<?php

final class ITSEC_Network_Brute_Force_Settings extends ITSEC_Settings {
	public function get_id() {
		return 'network-brute-force';
	}

	public function get_defaults() {
		return array(
			'api_key'       => '',
			'api_secret'    => '',
			'enable_ban'    => true,
			'updates_optin' => true,
			'api_nag'       => true,
		);
	}
}

ITSEC_Modules::register_settings( new ITSEC_Network_Brute_Force_Settings() );
