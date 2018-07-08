<?php

final class ITSEC_Password_Expiration_Settings extends ITSEC_Settings {
	public function get_id() {
		return 'password-expiration';
	}
	
	public function get_defaults() {
		return array(
			'expire_role'  => 'administrator',
			'expire_force' => 0,
			'expire_max'   => 120,
		);
	}
}

ITSEC_Modules::register_settings( new ITSEC_Password_Expiration_Settings() );
