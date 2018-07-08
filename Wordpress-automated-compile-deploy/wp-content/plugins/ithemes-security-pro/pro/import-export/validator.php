<?php

final class ITSEC_Import_Export_Validator extends ITSEC_Validator {
	protected $run_validate_matching_fields = false;
	protected $run_validate_matching_types = false;

	public function get_id() {
		return 'import-export';
	}
}

ITSEC_Modules::register_validator( new ITSEC_Import_Export_Validator() );
