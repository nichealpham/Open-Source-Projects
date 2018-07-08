<?php

final class ITSEC_WordPress_Salts_Settings extends ITSEC_Settings {
	public function get_id() {
		return 'wordpress-salts';
	}
	
	public function get_defaults() {
		return array(
			'last_generated' => 0,
		);
	}
}

ITSEC_Modules::register_settings( new ITSEC_WordPress_Salts_Settings() );
