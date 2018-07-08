<?php

final class ITSEC_Recaptcha_Settings extends ITSEC_Settings {
	public function get_id() {
		return 'recaptcha';
	}

	public function get_defaults() {
		return array(
			'type'            => 'v2',
			'site_key'        => '',
			'secret_key'      => '',
			'login'           => false,
			'register'        => false,
			'comments'        => false,
			'language'        => '',
			'theme'           => false,
			'error_threshold' => 7,
			'check_period'    => 5,
			'validated'       => false,
			'last_error'      => '',
		);
	}
}

ITSEC_Modules::register_settings( new ITSEC_Recaptcha_Settings() );
