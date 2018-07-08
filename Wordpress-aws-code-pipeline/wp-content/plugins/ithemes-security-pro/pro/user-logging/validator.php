<?php

class ITSEC_User_Logging_Validator extends ITSEC_Validator {
	public function get_id() {
		return 'user-logging';
	}
	
	protected function sanitize_settings() {
		$this->sanitize_setting( array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' ), 'role', __( 'Select Role for User Logging', 'it-l10n-ithemes-security-pro' ) );
	}
}

ITSEC_Modules::register_validator( new ITSEC_User_Logging_Validator() );
