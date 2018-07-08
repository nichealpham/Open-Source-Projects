<?php

final class ITSEC_Strong_Passwords_Settings extends ITSEC_Settings {
	public function get_id() {
		return 'strong-passwords';
	}
	
	public function get_defaults() {
		return array(
			'role' => 'administrator',
		);
	}
}

ITSEC_Modules::register_settings( new ITSEC_Strong_Passwords_Settings() );
