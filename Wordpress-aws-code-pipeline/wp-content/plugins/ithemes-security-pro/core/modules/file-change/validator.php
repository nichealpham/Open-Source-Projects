<?php

class ITSEC_File_Change_Validator extends ITSEC_Validator {
	public function get_id() {
		return 'file-change';
	}

	protected function sanitize_settings() {
		$previous_settings = ITSEC_Modules::get_settings( $this->get_id() );

		if ( ! isset( $this->settings['last_run'] ) ) {
			$this->settings['last_run'] = $previous_settings['last_run'];
		}
		if ( ! isset( $this->settings['last_chunk'] ) ) {
			$this->settings['last_chunk'] = $previous_settings['last_chunk'];
		}
		if ( ! isset( $this->settings['show_warning'] ) ) {
			$this->settings['show_warning'] = $previous_settings['show_warning'];
		}

		$this->set_previous_if_empty( array( 'latest_changes' ) );
		$this->vars_to_skip_validate_matching_types[] = 'last_chunk';

		$this->sanitize_setting( 'bool', 'split', __( 'Split File Scanning', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( array( 'exclude', 'include' ), 'method', __( 'Include/Exclude Files and Folders', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'newline-separated-array', 'file_list', __( 'Files and Folders List', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'newline-separated-extensions', 'types', __( 'Ignore File Types', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'email', __( 'Email File Change Notifications', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'notify_admin', __( 'Display File Change Admin Warning', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'positive-int', 'last_run', __( 'Last Run', 'it-l10n-ithemes-security-pro' ), false );

		$this->settings = apply_filters( 'itsec-file-change-sanitize-settings', $this->settings );
	}

	protected function validate_settings() {
		$current_time = ITSEC_Core::get_current_time();

		if ( defined( 'ITSEC_DOING_FILE_CHECK' ) && true === ITSEC_DOING_FILE_CHECK ) {
			$this->settings['last_run'] = $current_time;
		} else {
			if ( $this->settings['split'] ) {
				$interval = 12282;
			} else {
				$interval = 86340;
			}

			if ( $this->settings['last_run'] <= $current_time - $interval ) {
				$this->settings['last_run'] = $current_time - $interval + 120;
			}
		}
	}
}

ITSEC_Modules::register_validator( new ITSEC_File_Change_Validator() );
