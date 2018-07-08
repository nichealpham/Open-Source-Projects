<?php

class ITSEC_Backup_Validator extends ITSEC_Validator {
	public function get_id() {
		return 'backup';
	}

	protected function sanitize_settings() {
		$previous_settings = ITSEC_Modules::get_settings( $this->get_id() );

		if ( ! isset( $this->settings['interval'] ) ) {
			$this->settings['interval'] = $previous_settings['interval'];
		}
		if ( ! isset( $this->settings['last_run'] ) ) {
			$this->settings['last_run'] = $previous_settings['last_run'];
		}


		$this->sanitize_setting( 'bool', 'all_sites', __( 'Backup Full Database', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'positive-int', 'method', __( 'Backup Method', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( array( 0, 1, 2 ), 'method', __( 'Backup Method', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'writable-directory', 'location', __( 'Backup Location', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'positive-int', 'retain', __( 'Backups to Retain', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'zip', __( 'Compress Backup Files', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'newline-separated-array', 'exclude', __( 'Exclude Tables', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'enabled', __( 'Schedule Database Backups', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'positive-int', 'interval', __( 'Backup Interval', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'positive-int', 'last_run', __( 'Last Run', 'it-l10n-ithemes-security-pro' ), false );
	}
}

ITSEC_Modules::register_validator( new ITSEC_Backup_Validator() );
