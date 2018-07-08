<?php

class ITSEC_Core_Validator extends ITSEC_Validator {
	public function get_id() {
		return 'core';
	}
	
	protected function preprocess_settings() {
		
	}
	
	protected function validate_settings() {
		
	}
}

ITSEC_Modules::register_validator( new ITSEC_Core_Validator() );
