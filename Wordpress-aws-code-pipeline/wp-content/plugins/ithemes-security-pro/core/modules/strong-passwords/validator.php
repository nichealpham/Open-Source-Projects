<?php

class ITSEC_Strong_Passwords_Validator extends ITSEC_Validator {
	public function get_id() {
		return 'strong-passwords';
	}
	
	protected function sanitize_settings() {
		$this->sanitize_setting( array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' ), 'role', __( 'Select Role for Strong Passwords', 'it-l10n-ithemes-security-pro' ) );
	}
}

ITSEC_Modules::register_validator( new ITSEC_Strong_Passwords_Validator() );
