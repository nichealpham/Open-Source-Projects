<?php

class ITSEC_Privilege {

	function run() {

		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'edit_user_profile', array( $this, 'edit_user_profile' ) );
		add_action( 'edit_user_profile_update', array( $this, 'edit_user_profile_update' ) );

		add_action( 'plugins_loaded', array( $this, 'escalate_user' ), 1 );
		add_action( 'switch_blog', array( $this, 'escalate_user' ) );

	}

	/**
	 * Process resetting form
	 *
	 * @since 1.11
	 *
	 * @return void
	 */
	public function admin_init() {

		//if they've clicked a button hide the notice
		if ( isset( $_GET['itsec-clear-privilege'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'itsec_clear_privilege' ) ) {

			delete_user_meta( absint( $_GET['itsec-clear-privilege'] ), 'itsec_privilege_role' );
			delete_user_meta( absint( $_GET['itsec-clear-privilege'] ), 'itsec_privilege_expires' );

			wp_redirect( admin_url( 'user-edit.php' ) . '?user_id=' . absint( $_GET['itsec-clear-privilege'] ), '302' );
			exit();

		}

	}

	/**
	 * Converts saved role integer to appropriate string
	 *
	 * @since 1.11
	 *
	 * @param int $role the current role
	 *
	 * @return bool|string the current role string of false if invalid input
	 */
	private function convert_current_role( $role ) {

		switch ( $role ) {

			case 1:
				return 'editor';
				break;
			case 2:
				return 'administrator';
				break;
			case 3:
				return 'super-admin';
				break;
			default:
				return false;

		}

	}

	/**
	 * Display user options field to allow override
	 *
	 * @since 1.11
	 *
	 * @param mixed $user user
	 *
	 * @return void
	 */
	public function edit_user_profile( $user ) {

		global $itsec_globals;

		if ( $user->ID != get_current_user_id() && ITSEC_Core::current_user_can_manage() ) {

			$temp_role         = intval( get_user_meta( $user->ID, 'itsec_privilege_role', true ) );
			$temp_role_expires = trim( get_user_meta( $user->ID, 'itsec_privilege_expires', true ) );
			$current_role      = $this->get_current_role( $user );

			echo '<h3>' . __( 'Temporary Privilege Escalation', 'it-l10n-ithemes-security-pro' ) . '</h3>';

			echo '<table class="form-table">';
			echo '<tbody>';

			echo '<tr>';
			echo '<th scope="row">' . __( 'Set Temporary Role', 'it-l10n-ithemes-security-pro' ) . '</th>';
			echo '<td>';

			if ( $temp_role != 0 && $temp_role_expires > $itsec_globals['current_time'] ) {

				switch ( $temp_role ) {

					case 3:
						$temp_role_text = __( 'Network Administrator', 'it-l10n-ithemes-security-pro' );
						break;

					case 2:
						$temp_role_text = __( 'Administrator', 'it-l10n-ithemes-security-pro' );
						break;

					case 1:
						$temp_role_text = __( 'Editor', 'it-l10n-ithemes-security-pro' );
						break;

				}

				printf(
					'%s <strong>%s</strong>. %s <strong>%s</strong>. <a class="itsec-clear-privilege" href="%s?itsec-clear-privilege=%s&_wpnonce=%s">%s</a>',
					__( 'The user has already been temporarily upgraded to the role of ', 'it-l10n-ithemes-security-pro' ),
					$temp_role_text,
					__( 'This upgrade expires at', 'it-l10n-ithemes-security-pro' ),
					date( 'l F jS, Y \a\t g:i a', $temp_role_expires ),
					admin_url( 'user-edit.php' ),
					$user->ID,
					wp_create_nonce( 'itsec_clear_privilege' ),
					__( 'Click here to clear', 'it-l10n-ithemes-security-pro' )
				);

			} elseif ( ( is_multisite() && $current_role < 3 ) || $current_role < 2 ) {

				echo '<table>';
				echo '<tr>';
				echo '<td>';

				echo '<select name="itsec_privilege_profile[role]" id="itsec_privelege_role">';
				echo '<option value="0" ' . selected( $temp_role, 0, false ) . '>' . __( 'Select', 'it-l10n-ithemes-security-pro' ) . '</option>';
				echo ( is_multisite() && $current_role < 3 ) ? '<option value="3" ' . selected( $temp_role, 3, false ) . '>' . __( 'Network Administrator', 'it-l10n-ithemes-security-pro' ) . '</option>' : '';
				echo $current_role < 2 ? '<option value="2" ' . selected( $temp_role, 2, false ) . '>' . __( 'Administrator', 'it-l10n-ithemes-security-pro' ) . '</option>' : '';
				echo $current_role < 1 ? '<option value="1" ' . selected( $temp_role, 1, false ) . '>' . __( 'Editor', 'it-l10n-ithemes-security-pro' ) . '</option>' : '';
				echo '</select>';

				echo '</td>';
				echo '<td>';

				echo '<input class="small-text" name="itsec_privilege_profile[expires]" id="itsec_privilege_expires" value="1" type="text">';
				echo '<label for="itsec_privilege_expires"> ' . __( 'Day(s)', 'it-l10n-ithemes-security-pro' ) . '</label>';

				echo '</td>';
				echo '</tr>';
				echo '</table>';

				echo '<p class="description"> ' . __( 'Set the role which you would like to assign to the user temporarily and for how long you would like it to last.', 'it-l10n-ithemes-security-pro' ) . '</p>';

			} else {

				echo __( 'This user has already been permanently upgraded to the maximum level. No further action can be taken.', 'it-l10n-ithemes-security-pro' );

			}

			echo '</td>';
			echo '</tr>';

			echo '</tbody>';
			echo '</table>';

		}

	}

	/**
	 * Sanitize and update user option for override
	 *
	 * @since 1.11
	 *
	 * @param int $user_id user id
	 *
	 * @return void
	 */
	public function edit_user_profile_update( $user_id ) {

		global $itsec_globals;

		if ( isset( $_POST['itsec_privilege_profile'] ) ) {

			$role = isset( $_POST['itsec_privilege_profile']['role'] ) && $_POST['itsec_privilege_profile']['role'] != 0 ? intval( $_POST['itsec_privilege_profile']['role'] ) : false;
			$exp  = isset( $_POST['itsec_privilege_profile']['expires'] ) && intval( $_POST['itsec_privilege_profile']['expires'] ) > 0 ? $itsec_globals['current_time'] + ( intval( $_POST['itsec_privilege_profile']['expires'] ) * 60 * 60 * 24 ) : false;

			if ( $role !== false && $exp !== false ) {

				update_user_meta( $user_id, 'itsec_privilege_role', $role );
				update_user_meta( $user_id, 'itsec_privilege_expires', $exp );

			} elseif ( $exp === false ) {

				add_action( 'user_profile_update_errors', array( $this, 'user_profile_update_errors' ), 10, 3 );

			}

		}

	}

	/**
	 * Returns the role of the current user
	 *
	 * @since 1.11
	 *
	 * @param wp_user $user WP_User object
	 *
	 * @return int current role
	 */
	private function get_current_role( $user ) {

		if ( is_multisite() && $user->has_cap( 'manage_network_options' ) ) {

			return 3;

		} elseif ( $user->has_cap( 'manage_options' ) ) {

			return 2;

		} elseif ( $user->has_cap( 'moderate_comments' ) ) {

			return 1;

		}

		return 0;

	}

	/**
	 * Process the user role upgrade
	 *
	 * @since 1.11
	 *
	 * @return void
	 */
	public function escalate_user() {

		global $itsec_globals, $wp_roles, $super_admins;

		if ( ! is_callable( 'wp_get_current_user' ) ) {
			return;
		}

		$current_user = wp_get_current_user();

		if ( ! is_object( $current_user ) || ! isset( $current_user->ID ) ) {
			return;
		}

		$temp_role         = intval( get_user_meta( $current_user->ID, 'itsec_privilege_role', true ) );
		$temp_role_expires = intval( get_user_meta( $current_user->ID, 'itsec_privilege_expires', true ) );

		if ( $temp_role > 0 && $temp_role_expires > 0 ) {
			if ( $itsec_globals['current_time'] > $temp_role_expires ) {

				delete_user_meta( $current_user->ID, 'itsec_privilege_role' );
				delete_user_meta( $current_user->ID, 'itsec_privilege_expires' );

			} else {
				$temp_role_converted    = $this->convert_current_role( $temp_role );
				$current_role_converted = $this->convert_current_role( $this->get_current_role( $current_user ) );

				if ( $temp_role === 3 ) {
					$temp_role_converted = 'administrator';
				}

				if ( ! is_array( $super_admins ) ) {
					$super_admins = array( $current_user->user_login );
				}

				$current_user->allcaps  = $wp_roles->roles[ $temp_role_converted ]['capabilities']; //Set new capabilities
				$current_user->roles[0] = strtolower( $temp_role_converted ); //Set new role
				unset( $current_user->caps[ $current_role_converted ] ); //Delete old capabilities
				$current_user->caps[ $temp_role_converted ] = true; //Turn on current capabilities
			}

		}

	}

	/**
	 * Requires a unique nicename on profile update or activate.
	 *
	 * @since 1.11
	 *
	 * @param \WP_Error $errors Profile entry errors.
	 *
	 * @return void
	 */
	public function user_profile_update_errors( $errors ) {

		$errors->add( 'user_error', __( 'You must select a valid number of days (greater than 0) for temporary role expiration.', 'it-l10n-ithemes-security-pro' ) );

	}

}
