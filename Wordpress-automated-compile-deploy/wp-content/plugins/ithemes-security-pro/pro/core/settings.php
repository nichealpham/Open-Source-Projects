<?php

final class ITSEC_Core_Settings extends ITSEC_Settings {
	public function get_id() {
		return 'core';
	}
	
	public function get_defaults() {
		return array();
	}
}

ITSEC_Modules::register_settings( new ITSEC_Core_Settings() );
