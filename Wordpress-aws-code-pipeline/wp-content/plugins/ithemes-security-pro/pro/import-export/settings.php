<?php

final class ITSEC_Import_Export_Settings extends ITSEC_Settings {
	public function get_id() {
		return 'import-export';
	}

	public function get_defaults() {
		return array();
	}
}

ITSEC_Modules::register_settings( new ITSEC_Import_Export_Settings() );
