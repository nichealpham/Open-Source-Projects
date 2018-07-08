<?php

class ITSEC_Version_Management_Validator extends ITSEC_Validator {
	private $scan_for_outdated_software_hook = 'itsec_vm_outdated_wp_check';

	public function get_id() {
		return 'version-management';
	}

	protected function sanitize_settings() {
		$this->vars_to_skip_validate_matching_fields[] = 'update_details';
		$this->vars_to_skip_validate_matching_fields[] = 'is_software_outdated';
		$this->vars_to_skip_validate_matching_fields[] = 'old_site_details';

		$this->sanitize_setting( 'bool', 'wordpress_automatic_updates', __( 'WordPress Automatic Updates', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'plugin_automatic_updates', __( 'Plugin Automatic Updates', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'theme_automatic_updates', __( 'Theme Automatic Updates', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'strengthen_when_outdated', __( 'Strengthen Site When Running Outdated Software', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'scan_for_old_wordpress_sites', __( 'Scan For Old WordPress Sites', 'it-l10n-ithemes-security-pro' ) );

		if ( $this->sanitize_setting( 'array', 'email_contacts', __( 'Email Contacts', 'it-l10n-ithemes-security-pro' ) ) ) {
			$users_and_roles = $this->get_available_admin_users_and_roles();
			$users = array_keys( $users_and_roles['users'] );
			$roles = array_keys( $users_and_roles['roles'] );

			$this->valid_contacts = array_merge( $users, $roles );

			$this->sanitize_setting( array( $this, 'get_validated_contact' ), 'email_contacts', __( 'Email Contacts', 'it-l10n-ithemes-security-pro' ) );
		}
	}

	protected function validate_settings() {
		if ( empty( $this->settings['email_contacts'] ) ) {
			$this->add_error( new WP_Error( 'itsec-version-management-empty-email-contacts', __( 'You must select at least one email contact.', 'it-l10n-ithemes-security-pro' ) ) );
			$this->set_can_save( false );
		}

		if ( ! $this->can_save() ) {
			return;
		}

		if ( $this->settings['strengthen_when_outdated'] ) {
			if ( ! wp_next_scheduled( $this->scan_for_outdated_software_hook ) ) {
				wp_schedule_event( time(), 'daily', $this->scan_for_outdated_software_hook );
			}
		} else if ( wp_next_scheduled( $this->scan_for_outdated_software_hook ) ) {
			wp_clear_scheduled_hook( $this->scan_for_outdated_software_hook );
		}

		if ( $this->settings['scan_for_old_wordpress_sites'] ) {
			if ( ! wp_next_scheduled( 'itsec_vm_scan_for_old_sites' ) ) {
				wp_schedule_event( time() + ( 5 * MINUTE_IN_SECONDS ), 'daily', 'itsec_vm_scan_for_old_sites' );
			}
		} else if ( wp_next_scheduled( 'itsec_vm_scan_for_old_sites' ) ) {
			wp_clear_scheduled_hook( 'itsec_vm_scan_for_old_sites' );
		}
	}

	public function get_validated_contact( $contact ) {
		if ( in_array( $contact, $this->valid_contacts ) ) {
			return $contact;
		}

		return false;
	}

	public function get_available_admin_users_and_roles() {
		if ( is_callable( 'wp_roles' ) ) {
			$roles = wp_roles();
		} else {
			$roles = new WP_Roles();
		}

		$available_roles = array();
		$available_users = array();

		foreach ( $roles->roles as $role => $details ) {
			if ( isset( $details['capabilities']['manage_options'] ) && ( true === $details['capabilities']['manage_options'] ) ) {
				$available_roles["role:$role"] = translate_user_role( $details['name'] );

				$users = get_users( array( 'role' => $role ) );

				foreach ( $users as $user ) {
					/* translators: 1: user display name, 2: user login */
					$available_users[$user->ID] = sprintf( __( '%1$s (%2$s)', 'it-l10n-ithemes-security-pro' ), $user->display_name, $user->user_login );
				}
			}
		}

		natcasesort( $available_users );

		return array(
			'users' => $available_users,
			'roles' => $available_roles,
		);
	}
}

ITSEC_Modules::register_validator( new ITSEC_Version_Management_Validator() );
