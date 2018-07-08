<?php

final class ITSEC_Admin_User_Validator extends ITSEC_Validator {
	protected $run_validate_matching_fields = false;
	protected $run_validate_matching_types = false;


	public function get_id() {
		return 'admin-user';
	}

	protected function sanitize_settings() {
		// Only validate it if it exists
		if ( ! empty( $this->settings['new_username'] ) ) {
			$this->sanitize_setting( 'valid-username', 'new_username', __( 'New Admin Username', 'it-l10n-ithemes-security-pro' ) );
		}

		// If the value wasn't sent for this, assume false (no change)
		if ( empty( $this->settings['change_id'] ) ) {
			$this->settings['change_id'] = false;
		} else {
			$this->sanitize_setting( 'bool', 'change_id', __( 'Change User ID 1', 'it-l10n-ithemes-security-pro' ) );
		}
	}
	
	protected function validate_settings() {
		if ( ! $this->can_save() ) {
			return;
		}
		
		if ( empty( $this->settings['new_username'] ) || 'admin' === $this->settings['new_username'] ) {
			$this->settings['new_username'] = null;
		}
		
		if ( is_null( $this->settings['new_username'] ) && false === $this->settings['change_id'] ) {
			return;
		}
		
		
		$result = $this->change_admin_user( $this->settings['new_username'], $this->settings['change_id'] );
		
		if ( $result ) {
			$this->add_message( __( 'The user was successfully updated.', 'it-l10n-ithemes-security-pro' ) );
			ITSEC_Response::set_show_default_success_message( false );
			
			ITSEC_Response::force_logout();
		} else {
			$this->set_can_save( false );
			$this->add_error( new WP_Error( 'itsec-admin-user-failed-change-admin-user', __( 'The user was unable to be successfully updated. This could be due to a plugin or server configuration conflict.', 'it-l10n-ithemes-security-pro' ) ) );
			ITSEC_Response::set_show_default_error_message( false );
		}
	}
	
	/**
	 * Changes Admin User
	 *
	 * Changes the username and id of the 1st user
	 *
	 * @param string $username the username to change if changing at the same time
	 * @param bool   $id       whether to change the id as well
	 *
	 * @return bool success or failure
	 *
	 **/
	private function change_admin_user( $username = null, $id = false ) {

		global $wpdb;

		$itsec_files = ITSEC_Core::get_itsec_files();

		if ( $itsec_files->get_file_lock( 'admin_user' ) ) { //make sure it isn't already running

			//sanitize the username
			$new_user = sanitize_text_field( $username );

			//Get the full user object
			$user_object = get_user_by( 'id', '1' );

			if ( ! is_null( $username ) && validate_username( $new_user ) && false === username_exists( $new_user ) ) { //there is a valid username to change

				if ( $id === true ) { //we're changing the id too so we'll set the username

					$user_login = $new_user;

				} else { // we're only changing the username

					//query main user table
					$wpdb->query( "UPDATE `" . $wpdb->users . "` SET user_login = '" . esc_sql( $new_user ) . "' WHERE user_login='admin';" );

					if ( is_multisite() ) { //process sitemeta if we're in a multi-site situation

						$oldAdmins = $wpdb->get_var( "SELECT meta_value FROM `" . $wpdb->sitemeta . "` WHERE meta_key = 'site_admins'" );
						$newAdmins = str_replace( '5:"admin"', strlen( $new_user ) . ':"' . esc_sql( $new_user ) . '"', $oldAdmins );
						$wpdb->query( "UPDATE `" . $wpdb->sitemeta . "` SET meta_value = '" . esc_sql( $newAdmins ) . "' WHERE meta_key = 'site_admins'" );

					}

					$itsec_files->release_file_lock( 'admin_user' );

					return true;

				}

			} elseif ( $username !== null ) { //username didn't validate
				$itsec_files->release_file_lock( 'admin_user' );

				return false;

			} else { //only changing the id

				$user_login = $user_object->user_login;

			}

			if ( $id === true ) { //change the user id

				$wpdb->query( "DELETE FROM `" . $wpdb->users . "` WHERE ID = 1;" );

				$wpdb->insert( $wpdb->users, array(
					'user_login'          => $user_login, 'user_pass' => $user_object->user_pass,
					'user_nicename'       => $user_object->user_nicename, 'user_email' => $user_object->user_email,
					'user_url'            => $user_object->user_url, 'user_registered' => $user_object->user_registered,
					'user_activation_key' => $user_object->user_activation_key,
					'user_status'         => $user_object->user_status, 'display_name' => $user_object->display_name
				) );

				if ( is_multisite() && $username !== null && validate_username( $new_user ) ) { //process sitemeta if we're in a multi-site situation

					$oldAdmins = $wpdb->get_var( "SELECT meta_value FROM `" . $wpdb->sitemeta . "` WHERE meta_key = 'site_admins'" );
					$newAdmins = str_replace( '5:"admin"', strlen( $new_user ) . ':"' . esc_sql( $new_user ) . '"', $oldAdmins );
					$wpdb->query( "UPDATE `" . $wpdb->sitemeta . "` SET meta_value = '" . esc_sql( $newAdmins ) . "' WHERE meta_key = 'site_admins'" );

				}

				$new_user = $wpdb->insert_id;

				$wpdb->query( "UPDATE `" . $wpdb->posts . "` SET post_author = '" . $new_user . "' WHERE post_author = 1;" );
				$wpdb->query( "UPDATE `" . $wpdb->usermeta . "` SET user_id = '" . $new_user . "' WHERE user_id = 1;" );
				$wpdb->query( "UPDATE `" . $wpdb->comments . "` SET user_id = '" . $new_user . "' WHERE user_id = 1;" );
				$wpdb->query( "UPDATE `" . $wpdb->links . "` SET link_owner = '" . $new_user . "' WHERE link_owner = 1;" );

				$itsec_files->release_file_lock( 'admin_user' );

				return true;

			}

		}

		return false;

	}
}

ITSEC_Modules::register_validator( new ITSEC_Admin_User_Validator() );
