<?php

class ITSEC_User_Security_Check {
	public function run() {
		add_filter( 'manage_toplevel_page_itsec_columns', array( $this, 'add_columns' ) );
		add_action( 'manage_users_custom_column', array( $this, 'column_content' ), null, 3);
		add_action( 'wp_ajax_itsec-user-security-check-user-search', array( $this, 'user_search' ) );
		add_action( 'wp_ajax_itsec-set-user-role', array( $this, 'set_role' ) );
		add_action( 'wp_ajax_itsec-destroy-sessions', array( $this, 'destroy_sessions' ) );
		add_action( 'wp_ajax_itsec-send-2fa-email-reminder', array( $this, 'send_2fa_email_reminder' ) );
		add_action( 'wp_authenticate_user', array( $this, 'wp_authenticate_user' ), 999, 2 );
		add_filter( 'send_password_change_email', array( $this, 'send_password_change_email' ), 999, 3 );
		add_filter( 'user_profile_update_errors', array( $this, 'user_profile_update_errors' ), 999, 3 );
		add_action( 'password_reset', array( $this, 'password_reset' ), 999, 2 );
		add_action( 'itsec_check_inactive_accounts', array( $this, 'check_inactive_accounts' ) );

		if ( ! wp_next_scheduled( 'itsec_check_inactive_accounts' ) ) {
			wp_schedule_event( time(), 'daily', 'itsec_check_inactive_accounts' );
		}
	}

	/**
	 * Register columns for the user security check table.
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	function add_columns( $columns ) {
		require_once( ITSEC_Core::get_plugin_dir() . 'pro/two-factor/class-itsec-two-factor.php' );
		require_once( ITSEC_Core::get_plugin_dir() . 'pro/two-factor/class-itsec-two-factor-helper.php' );

		$columns = array(
			'username' => __( 'Username' ), // Uses core translation
		);

		if ( class_exists( 'ITSEC_Two_Factor' ) && class_exists( 'ITSEC_Two_Factor_Helper' ) ) {
			$two_factor_helper = ITSEC_Two_Factor_Helper::get_instance();
			if ( $two_factor_helper->get_enabled_providers() ) {
				$columns['itsec-two-factor'] = __( 'Two-Factor', 'it-l10n-ithemes-security-pro' );
			}
		}
		$columns['itsec-password'] = __( 'Password', 'it-l10n-ithemes-security-pro' );
		if ( class_exists( 'ITSEC_Lib_User_Activity' ) ) {
			$columns['itsec-last-active'] = __( 'Last Active', 'it-l10n-ithemes-security-pro' );
		}
		$columns['itsec-user-sessions'] = __( 'Sessions', 'it-l10n-ithemes-security-pro' );
		if ( current_user_can( 'promote_users' ) ) {
			$columns['itsec-user-role'] = __( 'Role', 'it-l10n-ithemes-security-pro' );
		}
		return $columns;
	}

	/**
	 * Render each column's content.
	 *
	 * @param string $value
	 * @param string $column_name
	 * @param int    $user_id
	 *
	 * @return string
	 */
	function column_content( $value, $column_name, $user_id ) {
		switch ( $column_name ) {
			case 'itsec-last-active':
				return $this->get_last_active_cell_contents( $user_id );
			case 'itsec-two-factor':
				$itsec_two_factor = ITSEC_Two_Factor::get_instance();
				if ( $itsec_two_factor->is_user_using_two_factor( $user_id ) ) {
					return '<span class="dashicons dashicons-lock" title="' . esc_attr__( 'Two Factor Enabled', 'it-l10n-ithemes-security-pro' ) . '"></span>';
				} else {
					$return = '<span class="dashicons dashicons-unlock" title="' . esc_attr__( 'Two Factor Not Enabled', 'it-l10n-ithemes-security-pro' ) . '"></span>';
					if ( current_user_can( 'edit_users', $user_id ) ) {
						$return .= sprintf( '<div class="row-actions"><span class="send-email"><a href="" data-nonce="%1$s" data-user_id="%2$d">%3$s</a></span></div>', esc_attr( wp_create_nonce( 'itsec-send-2fa-reminder-email-' . $user_id ) ), absint( $user_id ), __( 'Send E-Mail Reminder', 'it-l10n-ithemes-security-pro' ) );
					}
					return $return;
				}
			case 'itsec-password':
				return $this->get_password_cell_contents( $user_id );
			case 'itsec-user-sessions':
				return $this->get_user_session_cell_contents( $user_id );
			case 'itsec-user-role':
				if ( ! current_user_can( 'promote_users' ) ) {
					return '';
				}
				$user = get_userdata( $user_id );

				if ( empty( $user->roles ) ) {
					$role = '';
				} else {
					$role = current( $user->roles );
				}

				ob_start();
				?>
				<label class="screen-reader-text" for="<?php echo esc_attr( 'change_role-' . $user_id ); ?>"><?php _e( 'Change role to&hellip;' ) ?></label>
				<select name="<?php echo esc_attr( 'change_role-' . $user_id ); ?>" id="<?php echo esc_attr( 'change_role-' . $user_id ); ?>" data-user_id="<?php echo esc_attr( $user_id ); ?> " data-nonce="<?php echo esc_attr( wp_create_nonce( 'itsec-user-security-check-set-role-' . $user_id ) ); ?>">
					<option value="" disabled><?php _e( 'Change role to&hellip;' ) ?></option>
					<?php wp_dropdown_roles( $role ); ?>
				</select>
				<?php
				return ob_get_clean();
		}
		return $value;
	}

	/**
	 * Display the number of locations the user is logged-in at and a button to log out those locations.
	 *
	 * @param int $user_id
	 *
	 * @return string
	 */
	private function get_user_session_cell_contents( $user_id ) {
		$wp_sessions = WP_Session_Tokens::get_instance( $user_id );
		$sessions = $wp_sessions->get_all();
		if ( empty( $sessions ) ) {
			return __( 'Not currently logged in anywhere.', 'it-l10n-ithemes-security-pro' );
		} elseif( $user_id === get_current_user_id() && 1 === count( $sessions ) ) {
			return __( 'You are only logged in at this location.' );
		} else {
			$label = ( $user_id === get_current_user_id() )? __( 'Log Out Everywhere Else' ) : __( 'Log Out Everywhere' );// Uses code translation
			$return = sprintf( _n( 'Currently logged in at one location.', 'Currently logged in at %d locations.', count( $sessions ), 'it-l10n-ithemes-security-pro' ), count( $sessions ) );
			$return .= '<p><button type="button" class="destroy-sessions button button-secondary" data-nonce="' . esc_attr( wp_create_nonce( 'update-user_' . $user_id ) ) . '" data-user_id="' . esc_attr( $user_id ) . '">' . $label . '</button></p>';
			return $return;
		}
	}

	/**
	 * Display the time that the user has last been logged-in.
	 *
	 * @param int $user_id
	 *
	 * @return string
	 */
	public function get_last_active_cell_contents( $user_id ) {
		$ITSEC_Lib_User_Activity = ITSEC_Lib_User_Activity::get_instance();

		$time = intval( $ITSEC_Lib_User_Activity->get_last_seen( $user_id ) );

		$time_diff = time() - $time;
		if ( $time_diff > 0 && $time_diff < DAY_IN_SECONDS ) {
			return sprintf( __( '%s ago' ), human_time_diff( $time ) ); // Uses core translation
		} elseif ( empty( $time ) ) {
			return __( 'Unknown', 'it-l10n-ithemes-security-pro' );
		} else {
			return mysql2date( __( 'Y/m/d' ), date( 'Y-m-d H:i:s', $time ) ); // Uses core translation
		}

	}

	/**
	 * Display a notice about the strength of the user's password.
	 *
	 * @param int $user_id
	 *
	 * @return string
	 */
	public function get_password_cell_contents( $user_id ) {
		$password_strength = get_user_meta( $user_id, 'itsec-password-strength', true );

		// If the password strength wasn't retrieved or isn't 0-4, set it to -1 for "Unknown"
		if ( false === $password_strength || '' === $password_strength || ! in_array( $password_strength, range( 0, 4 ) ) ) {
			$password_strength = -1;
		}
		switch ( $password_strength ) {
			case 0:
			case 1:
				$strength_class = 'short';
				$strength_text = _x( 'Very weak', 'password strength' );
				break;
			case 2:
				$strength_class = 'bad';
				$strength_text = _x( 'Weak', 'password strength' );
				break;
			case 3:
				$strength_class = 'good';
				$strength_text = _x( 'Medium', 'password strength' );
				break;
			case 4:
				$strength_class = 'strong';
				$strength_text = _x( 'Strong', 'password strength' );
				break;
			default:
				$strength_class = '';
				$strength_text = _x( 'Unknown', 'password strength', 'it-l10n-ithemes-security-pro' );
		}

		$password_updated_time = ITSEC_Lib_Password_Requirements::password_last_changed( $user_id );

		if ( 0 === $password_updated_time ) {
			$age = __( 'Unknown', 'it-l10n-ithemes-security-pro' );
		} else {
			$age = human_time_diff( $password_updated_time );
		}

		return sprintf(
			__( '<strong>Strength:</strong> <span class="itsec-password-strength %1$s">%2$s</span></br><strong>Age:</strong> <span class="itsec-password-age">%3$s</span>' ),
			$strength_class,
			$strength_text,
			$age
		);

	}

	/**
	 * Ajax callback to display a table that has been filtered by the "search" input.
	 */
	public function user_search() {
		if ( wp_verify_nonce( $_POST['_nonce'], 'itsec-user-security-check-user-search' ) ) {
			$return = new stdClass();
			require_once( 'class-itsec-wp-users-list-table.php' );
			$wp_list_table = new ITSEC_WP_Users_List_Table( array( 'screen' => 'toplevel_page_itsec' ) );

			$wp_list_table->prepare_items();
			ob_start();
			$wp_list_table->views();
			$return->views = ob_get_clean();
			ob_start();
			$wp_list_table->search_box( __( 'Search Users' ), 'user' );
			$return->search_box = ob_get_clean();
			$return->search_nonce = wp_create_nonce( 'itsec-user-security-check-user-search' );
			ob_start();
			$wp_list_table->display();
			$return->users_table = ob_get_clean();
			wp_send_json_success( $return );
		}
		wp_send_json_error( array( 'message' => __( 'There was a problem searching.', 'it-l10n-ithemes-security-pro' ) ) );
	}

	/**
	 * Ajax callback to update a user's role.
	 */
	public function set_role() {
		$user_id = absint( $_POST['user_id'] );
		if ( wp_verify_nonce( $_POST['_nonce'], 'itsec-user-security-check-set-role-' . $user_id ) && ! empty( $_REQUEST['new_role'] ) ) {
			$user = get_userdata( $user_id );
			$user->set_role( $_REQUEST['new_role'] );

			$return = new stdClass();
			require_once( 'class-itsec-wp-users-list-table.php' );
			$wp_list_table = new ITSEC_WP_Users_List_Table( array( 'screen' => 'toplevel_page_itsec' ) );

			$wp_list_table->prepare_items();
			ob_start();
			$wp_list_table->views();
			$return->views = ob_get_clean();
			$return->message = __( 'Successfully updated role.', 'it-l10n-ithemes-security-pro' );

			wp_send_json_success( $return );
		} else {
			wp_send_json_error( array( 'message' => __( 'There was a problem updaing the user role.', 'it-l10n-ithemes-security-pro' ) ) );
		}
	}

	/**
	 * Ajax handler for destroying multiple open sessions for a user.
	 *
	 * Based on wp_ajax_destroy_sessions()
	 */
	public function destroy_sessions() {
		$user = get_userdata( (int) $_POST['user_id'] );
		if ( $user ) {
			if ( ! current_user_can( 'edit_user', $user->ID ) ) {
				$user = false;
			} elseif ( ! wp_verify_nonce( $_POST['nonce'], 'update-user_' . $user->ID ) ) {
				$user = false;
			}
		}

		if ( ! $user ) {
			wp_send_json_error( array(
				'message' => __( 'Could not log out user sessions. Please try again.' ),
			) );
		}

		$sessions = WP_Session_Tokens::get_instance( $user->ID );

		if ( $user->ID === get_current_user_id() ) {
			$sessions->destroy_others( wp_get_session_token() );
			$message = __( 'You are now logged out everywhere else.' );
		} else {
			$sessions->destroy_all();
			/* translators: 1: User's display name. */
			$message = sprintf( __( '%s has been logged out.' ), $user->display_name );
		}

		wp_send_json_success( array( 'message' => $message, 'session_cell_contents' => $this->get_user_session_cell_contents( $user->ID ) ) );
	}

	/**
	 * Ajax handler for sending a 2fa reminder E-Mail to a user.
	 */
	public function send_2fa_email_reminder() {
		$_POST['user_id'] = absint( $_POST['user_id'] );
		if ( ! current_user_can( 'edit_user', $_POST['user_id'] ) ) {
			wp_send_json_error( array(
				'message' => __( 'You do not have permission to send an E-Mail to that user.', 'it-l10n-ithemes-security-pro' ),
			) );
		}

		if ( ! wp_verify_nonce( $_POST['nonce'], 'itsec-send-2fa-reminder-email-' . $_POST['user_id'] ) ) {
			wp_send_json_error( array(
				'message' => __( 'There was a problem verifying your request. Please reload the page and try again.' ),
			) );
		}

		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		$configure_2fa_url = admin_url( 'index.php?itsec-action=configure-two-factor' );
		$user_requesting = wp_get_current_user();

		$merge_tags = array();
		$merge_tags['{{title}}']        = sprintf( __( 'Two Factor Authentication Reminder for <a href="%1$s" target="_blank" rel="noopener noreferrer" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%%;-webkit-text-size-adjust: 100%%;color: #FFFFFF;font-weight: bold;text-decoration: underline;">%2$s</a>', 'it-l10n-ithemes-security-pro' ), get_site_url(), $blogname );
		$merge_tags['{{heading}}']      = __( 'Two Factor Authentication Reminder', 'it-l10n-ithemes-security-pro' );
		$merge_tags['{{message}}']      = '<p>' . sprintf( __( '%1$s from %2$s has asked that you set up Two Factor Authentication.', 'it-l10n-ithemes-security-pro' ), $user_requesting->display_name, $blogname ) . '</p>';
		$merge_tags['{{setup_url}}']    = $configure_2fa_url;
		$merge_tags['{{setup_button}}'] = __( 'Setup now', 'it-l10n-ithemes-security-pro' );
		$merge_tags['{{explanation}}']  = '<li style="margin: 0; padding: 5px 10px; font-weight: bold;">' . __( 'Enabling two-factor authentication greatly increases the security of your user account on this site.', 'it-l10n-ithemes-security-pro' ) . '</li>';
		$merge_tags['{{explanation}}'] .= '<li style="margin: 0; padding: 5px 10px">'. __( 'With two-factor authentication enabled, after you login with your username and password, you will be asked for an authentication code before you can successfully log in.', 'it-l10n-ithemes-security-pro' ) . '</li>';
		$merge_tags['{{explanation}}'] .= '<li style="margin: 0; padding: 5px 10px">' . sprintf( __('<a href="%1$s">Learn more about Two Factor Authentication</a>, or <a href="%2$s">how to set it up</a>.', 'it-l10n-ithemes-security-pro' ), esc_url( 'https://ithemes.com/2015/07/28/two-factor-authentication/' ), esc_url( 'https://ithemes.com/2016/07/26/two-factor-authentication-ithemes-security-pro-plugin/' ) ) . '</li>';

		$subject = sprintf( __( '[%s] Please Set Up Two Factor Authentication', 'it-l10n-ithemes-security-pro' ), $blogname );
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		$message = file_get_contents( dirname( __FILE__ ) . '/email-templates/2fa-reminder.html' );
		$message = str_replace( array_keys( $merge_tags ), $merge_tags, $message );

		$user = get_userdata( $_POST['user_id'] );

		if ( wp_mail( $user->user_email, $subject, $message, $headers ) ) {
			wp_send_json_success( array(
				'message' => __( 'Reminder E-Mail has been sent.' ),
			) );
		} else {
			wp_send_json_error( array(
				'message' => __( 'There was a problem sending the E-Mail reminder. Please try again.' ),
			) );
		}
	}

	/**
	 * When a user's password is reset, store the new password's strength and set the last updated time.
	 *
	 * @param WP_User $user
	 * @param string  $new_pass
	 */
	public function password_reset( $user, $new_pass ) {
		if ( defined( 'ITSEC_DISABLE_PASSWORD_STRENGTH' ) && ITSEC_DISABLE_PASSWORD_STRENGTH ) {
			delete_user_meta( $user->ID, 'itsec-password-strength' );
		} else {
			update_user_meta( $user->ID, 'itsec-password-strength', $this->get_password_score( $new_pass, $user ) );
		}
	}

	/**
	 * When a user's password is updated, store the new password's strength and set the last updated time.
	 *
	 * @param bool  $send_email
	 * @param array $user       Old user data.
	 * @param array $userdata   New user data.
	 */
	public function send_password_change_email( $send_email, $user, $userdata ) {
		if ( ! empty( $this->user_pass ) && wp_check_password( $this->user_pass, $userdata['user_pass'] ) && ( ! defined( 'ITSEC_DISABLE_PASSWORD_STRENGTH' ) || ! ITSEC_DISABLE_PASSWORD_STRENGTH ) ) {
			// IF we have the correct password, check it's strength and store that
			update_user_meta( $user['ID'], 'itsec-password-strength', $this->get_password_score( $this->user_pass, $userdata ) );
			// No reason to keep this lingering
			unset( $this->user_pass );
		} else {
			// If we didn't find and intercept the password to test for strength, or if the password we got isn't right, remove the strength data and get it on next login
			delete_user_meta( $user['ID'], 'itsec-password-strength' );
		}
	}

	/**
	 * Action to catch the user's new password.
	 *
	 * @param WP_Error $errors
	 * @param bool     $update
	 * @param WP_User  $user
	 */
	public function user_profile_update_errors( $errors, $update, $user ) {
		if ( isset( $user->user_pass ) ) {
			$this->user_pass = $user->user_pass;
		}
	}

	/**
	 * When a user logs in, if their password does not have a recorded strength, calculate it.
	 *
	 * @todo Maybe move this to the Strong Password's module?
	 *
	 * @param WP_User|WP_Error $user
	 * @param string           $password
	 *
	 * @return WP_User|WP_Error
	 */
	public function wp_authenticate_user( $user, $password ) {
		// If this isn't a valid user, don't continue
		if ( is_wp_error($user) ) {
			return $user;
		}

		if ( defined( 'ITSEC_DISABLE_PASSWORD_STRENGTH' ) && ITSEC_DISABLE_PASSWORD_STRENGTH ) {
			return $user;
		}

		// If we've already stored the security of this user's password, don't continue
		$strength = get_user_meta( $user->ID, 'itsec-password-strength', true );

		if ( is_numeric( $strength ) && $strength >= 0 && $strength <= 4 ) {
			return $user;
		}

		// If the password doesn't match, don't continue
		if ( ! wp_check_password( $password, $user->user_pass, $user->ID ) ) {
			return $user;
		}

		$strength = $this->get_password_score( $password, $user );

		update_user_meta( $user->ID, 'itsec-password-strength', $strength );

		$min_role = ITSEC_Modules::get_setting( 'strong-passwords', 'role' );

		if ( $min_role && $strength < 4 ) {
			require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-canonical-roles.php' );

			if ( ITSEC_Lib_Canonical_Roles::is_user_at_least( $min_role, $user ) ) {
				ITSEC_Lib_Password_Requirements::flag_password_change_required( $user, 'strength' );
			}
		}

		return $user;
	}

	/**
	 * Calculate the strength of a user's password.
	 *
	 * @param string            $password
	 * @param int|array|WP_User $user
	 *
	 * @return int
	 */
	protected function get_password_score( $password, $user ) {
		if ( is_numeric( $user ) ) {
			$user = get_userdata( $user );
		} elseif ( ( is_array( $user ) && is_int( $user_id = $user['ID'] ) ) || ( is_object( $user ) && ! is_a( $user, 'WP_User' ) && is_int( $user_id = $user->ID ) ) ) {
			$user = get_userdata( $user_id );
		}

		$penalty_strings = array();
		if ( is_a( $user, 'WP_User' ) ) {
			$penalty_strings[] = $user->user_login;
			$penalty_strings[] = $user->user_login;
			$penalty_strings[] = $user->first_name;
			$penalty_strings[] = $user->last_name;
			$penalty_strings[] = $user->nickname;
			$penalty_strings[] = $user->display_name;
			$penalty_strings[] = $user->user_email;
			$penalty_strings[] = $user->user_url;
			$penalty_strings[] = $user->description;
			// Add site title? ;
			$penalty_strings[] = get_site_option( 'admin_email' );
		}
		// @todo filter these down by eliminating short words, etc as in password-strength-meter.js

		$results = ITSEC_Lib::get_password_strength_results( $password, $penalty_strings );
		return $results->score;
	}

	/**
	 * Iterate over all users who haven't been active in the last 30 days and email admins the results.
	 */
	public function check_inactive_accounts() {
		if ( defined( 'ITSEC_DISABLE_INACTIVE_USER_CHECK' ) && ITSEC_DISABLE_INACTIVE_USER_CHECK ) {
			return;
		}

		$max_days = apply_filters( 'itsec_inactive_user_days', 30 );
		$args = array(
			'meta_query' => array(
				'last-active' => array(
					'key'     => 'itsec_user_activity_last_seen',
					'value'   => time() - ( $max_days * DAY_IN_SECONDS ),
					'compare' => '<=',
				),
				'not-already-notified' => array(
					'key'     => 'itsec_user_activity_last_seen_notification_sent',
					'compare' => 'NOT EXISTS',
				),
			),
		);
		$users = get_users( $args );

		if ( empty( $users ) ) {
			return;
		}

		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );


		$merge_tags = array();
		$merge_tags['{{title}}']               = sprintf( __( 'Inactive User Warning for <a href="%1$s" target="_blank" rel="noopener noreferrer" style="mso-line-height-rule: exactly;-ms-text-size-adjust: 100%%;-webkit-text-size-adjust: 100%%;color: #FFFFFF;font-weight: bold;text-decoration: underline;">%2$s</a>', 'it-l10n-ithemes-security-pro' ), get_site_url(), $blogname );
		$merge_tags['{{heading}}']             = __( 'Inactive User Warning', 'it-l10n-ithemes-security-pro' );
		$merge_tags['{{warning-text}}']        = sprintf( _n( 'The following users have been inactive for more than %d day', 'The following users have been inactive for more than %d days', $max_days, 'it-l10n-ithemes-security-pro' ), $max_days );
		$merge_tags['{{explanation}}']         = __( 'Please take the time to review the users and demote or delete any where it makes sense.', 'it-l10n-ithemes-security-pro' );
		$merge_tags['{{username-heading}}']    = __( 'Username', 'it-l10n-ithemes-security-pro' );
		$merge_tags['{{last-active-heading}}'] = __( 'Last Active', 'it-l10n-ithemes-security-pro' );
		$merge_tags['{{caption}}']             = __( 'Inactive Users', 'it-l10n-ithemes-security-pro' );
		$merge_tags['{{setup_url}}']           = admin_url( 'admin.php?page=itsec&module=user-security-check' );
		$merge_tags['{{setup_cta_text}}']      = __( 'Edit Users', 'it-l10n-ithemes-security-pro' );
		$merge_tags['{{user-rows}}']           = '';

		foreach ( $users as $u ) {
			update_user_meta( $u->ID, 'itsec_user_activity_last_seen_notification_sent', true );
			$merge_tags['{{user-rows}}'] .= sprintf( '
			<tr class="file-change-table-row">
				<td class="file-change-table-cell file-name-cell" style="text-align:left; padding: 1em;word-break: break-word;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%%;-webkit-text-size-adjust: 100%%;font: 16px \'Helvetica\', sans-serif;">%1$s</td>
				<td class="file-change-table-cell" style="text-align:right; padding: 1em;word-break: break-word;mso-line-height-rule: exactly;-ms-text-size-adjust: 100%%;-webkit-text-size-adjust: 100%%;color: #505050;font: 16px \'Helvetica\', sans-serif;">%2$s</td>
			</tr>', $u->user_login, $this->get_last_active_cell_contents( $u->ID ) );
		}

		$subject = sprintf( __( '[%s] Inactive Users', 'it-l10n-ithemes-security-pro' ), $blogname );
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		$message = file_get_contents( dirname( __FILE__ ) . '/email-templates/inactive-users.html' );
		$message = str_replace( array_keys( $merge_tags ), $merge_tags, $message );


		$recipients  = ITSEC_Modules::get_setting( 'global', 'notification_email' );

		foreach ( $recipients as $recipient ) {
			if ( is_email( trim( $recipient ) ) ) {
				wp_mail( trim( $recipient ), $subject, $message, $headers );
			}
		}
	}
}
