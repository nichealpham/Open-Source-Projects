<?php

final class ITSEC_User_Security_Check_Validator extends ITSEC_Validator {
	public function get_id() {
		return 'user-security-check';
	}

	protected function sanitize_settings() {
	}
}

ITSEC_Modules::register_validator( new ITSEC_User_Security_Check_Validator() );
