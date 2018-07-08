<?php

final class ITSEC_Privilege_Settings extends ITSEC_Settings {
	public function get_id() {
		return 'privilege';
	}
	
	public function get_defaults() {
		return array();
	}
}

ITSEC_Modules::register_settings( new ITSEC_Privilege_Settings() );
