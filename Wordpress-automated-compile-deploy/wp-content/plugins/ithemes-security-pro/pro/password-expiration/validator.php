<?php

class ITSEC_Password_Expiration_Validator extends ITSEC_Validator {
	public function get_id() {
		return 'password-expiration';
	}
	
	protected function sanitize_settings() {
		$this->sanitize_setting( array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' ), 'expire_role', __( 'Select Minimum Role for Password Expiration', 'it-l10n-ithemes-security-pro' ) );
		$this->sanitize_setting( 'positive-int', 'expire_max', __( 'Maximum Password Age', 'it-l10n-ithemes-security-pro' ) );
		
		if ( $this->settings['expire_force'] ) {
			delete_metadata( 'user', null, 'itsec_last_password_change', null, true ); //delete existing last password change
			
			$this->settings['expire_force'] = ITSEC_Core::get_current_time_gmt();
		} else {
			$this->settings['expire_force'] = $this->previous_settings['expire_force'];
		}
	}
	
	protected function validate_settings() {
		if ( ! $this->can_save() ) {
			return;
		}
		
		ITSEC_Response::reload_module( $this->get_id() );
	}
}

ITSEC_Modules::register_validator( new ITSEC_Password_Expiration_Validator() );
