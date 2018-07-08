<?php

class ITSEC_SSL_Validator extends ITSEC_Validator {
	public function get_id() {
		return 'ssl';
	}
	
	protected function sanitize_settings() {
		$this->sanitize_setting( 'positive-int', 'frontend', __( 'Front End SSL Mode', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( array( 0, 1, 2 ), 'frontend', __( 'Front End SSL Mode', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'bool', 'admin', __( 'SSL for Dashboard', 'it-l10n-ithemes-security-pro' ) );
	}
	
	protected function validate_settings() {
		if ( ! $this->can_save() ) {
			return;
		}
		
		
		$previous_settings = ITSEC_Modules::get_settings( $this->get_id() );
		
		if ( $this->settings['admin'] !== $previous_settings['admin'] ) {
			ITSEC_Response::regenerate_wp_config();
			
			if ( $this->settings['admin'] ) {
				ITSEC_Response::force_logout();
			}
		}
	}
}

ITSEC_Modules::register_validator( new ITSEC_SSL_Validator() );
