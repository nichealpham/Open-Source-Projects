<?php

final class ITSEC_User_Logging_Settings extends ITSEC_Settings {
	public function get_id() {
		return 'user-logging';
	}
	
	public function get_defaults() {
		return array(
			'role' => 'administrator',
		);
	}
}

ITSEC_Modules::register_settings( new ITSEC_User_Logging_Settings() );
