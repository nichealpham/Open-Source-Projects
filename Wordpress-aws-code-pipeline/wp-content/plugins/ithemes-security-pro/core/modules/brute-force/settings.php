<?php

final class ITSEC_Brute_Force_Settings extends ITSEC_Settings {
	public function get_id() {
		return 'brute-force';
	}
	
	public function get_defaults() {
		return array(
			'max_attempts_host' => 5,
			'max_attempts_user' => 10,
			'check_period'      => 5,
			'auto_ban_admin'    => false,
		);
	}
}

ITSEC_Modules::register_settings( new ITSEC_Brute_Force_Settings() );
