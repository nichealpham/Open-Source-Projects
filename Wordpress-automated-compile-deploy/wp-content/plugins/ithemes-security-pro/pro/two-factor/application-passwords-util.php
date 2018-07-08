<?php

final class ITSEC_Application_Passwords_Util {
	const USERMETA_KEY_APPLICATION_PASSWORDS = '_application_passwords';

	public static function handle_ajax_request() {
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'itsec_application_password_create' ) ) {
			wp_send_json_error( new WP_Error( 'itsec-application-passwords-util-ajax-request-nonce-failure', esc_html__( 'This request failed a security nonce check. Please reload the page and try again.', 'it-l10n-ithemes-security-pro' ) ) );
		}

		$user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;

		if ( empty( $user_id ) ) {
			wp_send_json_error( new WP_Error( 'itsec-application-passwords-util-ajax-invalid-user-id', esc_html__( 'An invalid request was received. The user ID was missing. Please reload the page and try again.', 'it-l10n-ithemes-security-pro' ) ) );
		} else if ( ! current_user_can( 'edit_user', $user_id ) ) {
			wp_send_json_error( new WP_Error( 'itsec-application-passwords-util-ajax-cannot-modify-user', esc_html__( 'Blocked attempt to modify a user without sufficient privileges. Please reload the page and try again.', 'it-l10n-ithemes-security-pro' ) ) );
		}

		if ( 'itsec_application_password_create' === $_POST['action'] ) {
			$name = isset( $_POST['name'] ) ? $_POST['name'] : '';
			$enabled_for = isset( $_POST['enabled_for'] ) ? $_POST['enabled_for'] : array();
			$rest_api_permissions = isset( $_POST['rest_api_permissions'] ) ? $_POST['rest_api_permissions'] : '';

			$result = self::create( $user_id, $name, $enabled_for, $rest_api_permissions );

			if ( is_wp_error( $result ) ) {
				wp_send_json_error( $result );
			} else {
				$row = self::get_table_row( $result['item'] );

				$message = sprintf( esc_html_x( 'Your new password for %1$s: %2$s', 'application, password', 'it-l10n-ithemes-security-pro' ), "<strong>{$result['item']['name']}</strong>", "<kbd>{$result['password']}</kbd>" );

				$retval = array(
					'add_row' => $row,
					'message' => $message,
				);

				wp_send_json_success( $retval );
			}
		} else if ( 'itsec_application_password_revoke' === $_POST['action'] ) {
			$slug = isset( $_POST['slug'] ) ? $_POST['slug'] : '';

			$result = self::revoke( $user_id, $slug );

			if ( is_wp_error( $result ) ) {
				wp_send_json_error( $result );
			} else {
				$retval = array(
					'remove_row' => $slug,
				);

				wp_send_json_success( $retval );
			}
		} else if ( 'itsec_application_password_revoke_all' === $_POST['action'] ) {
			$result = self::revoke_all( $user_id );

			if ( is_wp_error( $result ) ) {
				wp_send_json_error( $result );
			} else {
				$retval = array(
					'remove_all' => true,
				);

				wp_send_json_success( $retval );
			}
		} else {
			wp_send_json_error( new WP_Error( 'itsec-application-passwords-invalid-ajax-request', esc_html__( 'ITSEC_Application_Passwords_Util::handle_request() was triggered by an invalid action.', 'it-l10n-ithemes-security-pro' ) ) );
		}
	}

	public static function create( $user_id, $name, $enabled_for, $rest_api_permissions ) {
		if ( empty( $name ) ) {
			return new WP_Error( 'itsec-application-passwords-util-create-empty-name', esc_html__( 'You must supply a name for this application password.', 'it-l10n-ithemes-security-pro' ) );
		}


		$enabled_for = (array) $enabled_for;

		if ( empty( $enabled_for ) ) {
			return new WP_Error( 'itsec-application-passwords-util-create-empty-enabled_for-arg', esc_html__( 'An application password must be valid for either REST API requests, XML-RPC requests, or both.', 'it-l10n-ithemes-security-pro' ) );
		} else {
			$unrecognized_types = array_diff( $enabled_for, array( 'rest-api', 'xml-rpc' ) );

			if ( ! empty( $unrecognized_types ) ) {
				return new WP_Error( 'itsec-application-passwords-util-create-invalid-enabled_for-value', esc_html__( 'Received an invalid request type to be associated with this application password. Please reload this page and try again.', 'it-l10n-ithemes-security-pro' ) );
			}
		}


		if ( in_array( 'rest-api', $enabled_for ) ) {
			if ( empty( $rest_api_permissions ) ) {
				return new WP_Error( 'itsec-application-passwords-util-create-empty-rest_api_permissions-arg', esc_html__( 'You must select either "Read and write" or "Read-only".', 'it-l10n-ithemes-security-pro' ) );
			} else if ( ! in_array( $rest_api_permissions, array( 'read', 'write' ) ) ) {
					return new WP_Error( 'itsec-application-passwords-util-create-invalid-rest_api_permissions-value', esc_html__( 'Received invalid rest_api_permissions for this application password. Please reload this page and try again.', 'it-l10n-ithemes-security-pro' ) );
			}
		} else {
			$rest_api_permissions = '';
		}


		$password = wp_generate_password( 16, false );
		$hashed_password = wp_hash_password( $password );

		$item = array(
			'name'                 => $name,
			'enabled_for'          => $enabled_for,
			'rest_api_permissions' => $rest_api_permissions,
			'password'             => $hashed_password,
			'created'              => time(),
			'last_used'            => null,
			'last_ip'              => null,
		);

		$passwords = self::get( $user_id );
		$passwords[] = $item;

		self::set( $user_id, $passwords );

		$retval = array(
			'user_id'  => $user_id,
			'item'     => $item,
			'password' => $password,
		);

		return $retval;
	}

	public static function revoke( $user_id, $slug ) {
		$passwords = self::get( $user_id );

		foreach ( $passwords as $key => $item ) {
			if ( self::get_unique_slug( $item ) === $slug ) {
				unset( $passwords[ $key ] );
				self::set( $user_id, $passwords );
				return true;
			}
		}

		return new WP_Error( 'itsec-application-passwords-util-revoke-invalid-slug', esc_html__( 'Unable to find the requested application password. Please reload this page and try again.', 'it-l10n-ithemes-security-pro' ) );
	}

	public static function revoke_all( $user_id ) {
		$passwords = self::get( $user_id );

		if ( ! empty( $passwords ) ) {
			self::set( $user_id, array() );
			return sizeof( $passwords );
		}

		return 0;
	}

	public static function show_user_profile( $user ) {
		// WP List Tables can't be used on the front end
		if ( ! is_admin() ) {
			return;
		}

		if ( ITSEC_Modules::get_setting( 'global', 'show_error_codes' ) ) {
			$error_format = esc_html_x( '%1$s (%2$s)', '1: error message, 2: error code', 'it-l10n-ithemes-security-pro' );
		} else {
			$error_format = '%1$s';
		}

		$vars = array(
			'user_id'       => $user->ID,
			'nonce'         => wp_create_nonce( 'itsec_application_password_create' ),
			'emptyRow'      => '<tr class="no-items"><td class="colspanchange" colspan="' . count( self::get_table_columns() ) . '">' . esc_html__( 'No items found.' ) . '</td></tr>',
			'emptyRowClass' => 'no-items',
			'errorMessages' => array(
				'unknownResponse' => esc_html__( 'An unrecognized response was sent by the server. Please reload the page and try again.', 'it-l10n-ithemes-security-pro' ),
				'ajaxUnknown'     => esc_html__( 'The request to the server failed due to an unknown reason. Please reload the page and try again.', 'it-l10n-ithemes-security-pro' ),
				'ajaxTimeout'     => esc_html__( 'The server took too long to respond. This could indicate a temporary issue with the server. Please reload the page and try again.', 'it-l10n-ithemes-security-pro' ),
				'parseError'      => esc_html__( 'The response from the site could not be properly parsed. Please reload the page and try again.', 'it-l10n-ithemes-security-pro' ),
			),
			'errorFormat'   => $error_format,
		);

		wp_enqueue_script( 'itsec-application-passwords-script', plugin_dir_url( __FILE__ ) . 'js/application-passwords.js', array( 'jquery' ) );
		wp_localize_script( 'itsec-application-passwords-script', 'itsecApplicationPasswordsData', $vars );

?>
	<div class="itsec-application-passwords hide-if-no-js" id="itsec-application-passwords-section">
		<h3><?php esc_html_e( 'Application Passwords', 'it-l10n-ithemes-security-pro' ); ?></h3>
		<p><?php esc_html_e( 'Application Passwords are used to allow authentication via non-interactive systems, such as XML-RPC or the REST API, without providing your actual password. They can be easily revoked, and can never be used for traditional logins to your website.', 'it-l10n-ithemes-security-pro' ); ?></p>
		<p><input id="itsec-application-password-add" class="button" type="button" value="<?php esc_attr_e( 'Add a new application password', 'it-l10n-ithemes-security-pro' ); ?>" /></p>

		<div id="itsec-application-password-settings" class="hide-if-js">
			<p><input id="itsec-application-password-name" type="text" size="30" placeholder="<?php esc_attr_e( 'New Application Password Name', 'it-l10n-ithemes-security-pro' ); ?>" class="input" /></p>

			<h4><?php esc_html_e( 'API Types', 'it-l10n-ithemes-security-pro' ); ?></h4>
			<p>
				<label for="itsec-application-password-enabled-for-rest-api">
					<input id="itsec-application-password-enabled-for-rest-api" type="checkbox" checked />
					<?php esc_html_e( 'Valid for REST API requests', 'it-l10n-ithemes-security-pro' ); ?>
				</label>
			</p>
			<p>
				<label for="itsec-application-password-enabled-for-xml-rpc">
					<input id="itsec-application-password-enabled-for-xml-rpc" type="checkbox" checked />
					<?php esc_html_e( 'Valid for XML-RPC requests', 'it-l10n-ithemes-security-pro' ); ?>
				</label>
			</p>

			<div id="itsec-application-password-rest-api-permissions-section">
				<h4><?php esc_html_e( 'REST API Permissions', 'it-l10n-ithemes-security-pro' ); ?></h4>
				<p>
					<label for="itsec-application-password-rest-api-permissions-write">
						<input id="itsec-application-password-rest-api-permissions-write" type="radio" name="itsec-application-password-rest-api-permissions" value="write" checked />
						<?php echo wp_kses( __( '<strong>Read and Write:</strong> This application password can access and modify data.', 'it-l10n-ithemes-security-pro' ), array( 'strong' => array() ) ); ?>
					</label>
				</p>
				<p>
					<label for="itsec-application-password-permission-read">
						<input id="itsec-application-password-permission-read" type="radio" name="itsec-application-password-rest-api-permissions" value="read" />
						<?php echo wp_kses( __( '<strong>Read-Only:</strong> This application password can access data but cannot modify data.', 'it-l10n-ithemes-security-pro' ), array( 'strong' => array() ) ); ?>
					</label>
				</p>
			</div>

			<p>
				<?php submit_button( esc_html__( 'Create application password', 'it-l10n-ithemes-security-pro' ), 'secondary', 'itsec-application-password-create', false ); ?>
				<?php submit_button( esc_html__( 'Cancel', 'it-l10n-ithemes-security-pro' ), 'secondary', 'itsec-application-password-cancel', false ); ?>
			</p>
		</div>

		<div id="itsec-application-password-feedback"></div>

		<div id="itsec-application-passwords-list-table-wrapper">
			<?php
				require_once( dirname( __FILE__ ) . '/application-passwords-list-table.php' );
				$list_table = new ITSEC_Application_Passwords_List_Table();
				$list_table->items = self::get( $user->ID );
				$list_table->prepare_items();
				$list_table->display();
			?>
		</div>
	</div>
<?php

	}

	public static function get( $user_id ) {
		$items = get_user_meta( $user_id, self::USERMETA_KEY_APPLICATION_PASSWORDS, true );

		if ( ! is_array( $items ) ) {
			$items = array();
		}

		foreach ( $items as $index => $item ) {
			if ( empty( $item['enabled_for'] ) ) {
				$items[$index]['enabled_for'] = array( 'rest-api', 'xml-rpc' );
			}

			if ( empty( $item['rest_api_permissions'] ) ) {
				$items[$index]['rest_api_permissions'] = 'write';
			}
		}

		return $items;
	}

	public static function get_unique_slug( $item ) {
		$concat = $item['name'] . '|' . $item['password'] . '|' . $item['created'];
		$hash   = md5( $concat );
		return substr( $hash, 0, 12 );
	}

	public static function chunk_password( $raw_password ) {
		$raw_password = preg_replace( '/[^a-z\d]/i', '', $raw_password );
		return trim( chunk_split( $raw_password, 4, ' ' ) );
	}

	public static function set( $user_id, $passwords ) {
		return update_user_meta( $user_id, self::USERMETA_KEY_APPLICATION_PASSWORDS, $passwords );
	}

	private static function get_table_row( $item ) {
		$row = '<tr data-slug="' . self::get_unique_slug( $item ) . '">';

		$columns = self::get_table_columns();

		foreach ( array_keys( $columns ) as $column ) {
			$row .= '<td>' . self::get_table_column_entry( $item, $column ) . '</td>';
		}

		$row .= '</tr>';

		return $row;
	}

	public static function get_table_columns() {
		return array(
			'name'                 => esc_html__( 'Name', 'it-l10n-ithemes-security-pro' ),
			'enabled_for'          => esc_html__( 'API Types', 'it-l10n-ithemes-security-pro' ),
			'rest_api_permissions' => esc_html__( 'REST API Permissions', 'it-l10n-ithemes-security-pro' ),
			'created'              => esc_html__( 'Created', 'it-l10n-ithemes-security-pro' ),
			'last_used'            => esc_html__( 'Last Used', 'it-l10n-ithemes-security-pro' ),
			'last_ip'              => esc_html__( 'Last IP', 'it-l10n-ithemes-security-pro' ),
			'revoke'               => esc_html__( 'Revoke', 'it-l10n-ithemes-security-pro' ),
		);
	}

	public static function get_table_column_entry( $item, $column_name ) {
		switch ( $column_name ) {
			case 'name':
				return esc_html( $item['name'] );
			case 'enabled_for':
				if ( array( 'xml-rpc' ) === $item['enabled_for'] ) {
					return esc_html__( 'XML-RPC', 'it-l10n-ithemes-security-pro' );
				} else if ( array( 'rest-api' ) === $item['enabled_for'] ) {
					return esc_html__( 'REST API', 'it-l10n-ithemes-security-pro' );
				} else {
					return esc_html__( 'REST API and XML-RPC', 'it-l10n-ithemes-security-pro' );
				}
			case 'rest_api_permissions':
				if ( ! in_array( 'rest-api', $item['enabled_for'] ) ) {
					return '&mdash;';
				} else if ( 'read' === $item['rest_api_permissions'] ) {
					return esc_html__( 'Read-Only', 'it-l10n-ithemes-security-pro' );
				} else {
					return esc_html__( 'Read and Write', 'it-l10n-ithemes-security-pro' );
				}
			case 'created':
				if ( empty( $item['created'] ) ) {
					return '&mdash;';
				}
				return date( get_option( 'date_format', 'r' ), $item['created'] );
			case 'last_used':
				if ( empty( $item['last_used'] ) ) {
					return '&mdash;';
				}
				return date( get_option( 'date_format', 'r' ), $item['last_used'] );
			case 'last_ip':
				if ( empty( $item['last_ip'] ) ) {
					return '&mdash;';
				}
				return $item['last_ip'];
			case 'revoke':
				return '<input type="button" class="button delete itsec-application-password-revoke" value="' . esc_html__( 'Revoke', 'it-l10n-ithemes-security-pro' ) . '" />';
			default:
				return 'ERROR: Not Set';
		}
	}
}
