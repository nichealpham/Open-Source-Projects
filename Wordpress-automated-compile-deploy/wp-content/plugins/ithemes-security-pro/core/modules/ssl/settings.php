<?php

final class ITSEC_SSL_Settings extends ITSEC_Settings {
	public function get_id() {
		return 'ssl';
	}
	
	public function get_defaults() {
		return array(
			'frontend' => 0,
			'admin'    => false,
		);
	}
}

ITSEC_Modules::register_settings( new ITSEC_SSL_Settings() );
