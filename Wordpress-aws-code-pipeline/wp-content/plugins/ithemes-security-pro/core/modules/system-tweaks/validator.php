<?php

class ITSEC_System_Tweaks_Validator extends ITSEC_Validator {
	public function get_id() {
		return 'system-tweaks';
	}

	protected function sanitize_settings() {
		$this->sanitize_setting( 'bool', 'protect_files', __( 'System Files', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'directory_browsing', __( 'Directory Browsing', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'request_methods', __( 'Request Methods', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'suspicious_query_strings', __( 'Suspicious Query Strings', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'non_english_characters', __( 'Non-English Characters', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'long_url_strings', __( 'Long URL Strings', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'write_permissions', __( 'File Writing Permissions', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'uploads_php', __( 'PHP in Uploads', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'plugins_php', __( 'PHP in Plugins', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'themes_php', __( 'PHP in Themes', 'it-l10n-ithemes-security-pro' ) );
	}

	protected function validate_settings() {
		if ( ! $this->can_save() ) {
			return;
		}


		$previous_settings = ITSEC_Modules::get_settings( $this->get_id() );


		$diff = array_diff_assoc( $this->settings, $previous_settings );

		if ( ! empty( $diff ) ) {
			ITSEC_Response::regenerate_server_config();
		}


		if ( $this->settings['write_permissions'] ) {
			// Always set permissions to 0444 when saving the settings.
			// This ensures that the file permissions are fixed each time the settings are saved.

			$new_permissions = 0444;
		} else if ( $this->settings['write_permissions'] !== $previous_settings['write_permissions'] ) {
			// Only revert the settings to the defaults when disabling the setting.
			// This avoids changing the file permissions when the setting has yet to be enabled and disabled.

			$new_permissions = 0664;
		}

		if ( isset( $new_permissions ) ) {
			// Only change the permissions when needed.

			require_once( ITSEC_Core::get_core_dir() . 'lib/class-itsec-lib-config-file.php' );
			require_once( ITSEC_Core::get_core_dir() . 'lib/class-itsec-lib-file.php' );

			$server_config_file = ITSEC_Lib_Config_File::get_server_config_file_path();
			$wp_config_file = ITSEC_Lib_Config_File::get_wp_config_file_path();

			ITSEC_Lib_File::chmod( $server_config_file, $new_permissions );
			ITSEC_Lib_File::chmod( $wp_config_file, $new_permissions );

			ITSEC_Response::reload_module( 'file-permissions' );
		}
	}

}

ITSEC_Modules::register_validator( new ITSEC_System_Tweaks_Validator() );
