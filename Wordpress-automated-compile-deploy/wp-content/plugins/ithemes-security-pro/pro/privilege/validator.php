<?php

class ITSEC_Privilege_Validator extends ITSEC_Validator {
	public function get_id() {
		return 'privilege';
	}
	
	protected function preprocess_settings() {
		
	}
	
	protected function validate_settings() {
		
	}
}

ITSEC_Modules::register_validator( new ITSEC_Privilege_Validator() );
