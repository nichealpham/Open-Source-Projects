<?php

final class ITSEC_Hide_Backend_Settings extends ITSEC_Settings {
	public function get_id() {
		return 'hide-backend';
	}
	
	public function get_defaults() {
		return array(
			'enabled'           => false,
			'slug'              => 'wplogin',
			'register'          => 'wp-register.php',
			'theme_compat'      => true,
			'theme_compat_slug' => 'not_found',
			'post_logout_slug'  => '',
		);
	}
}

ITSEC_Modules::register_settings( new ITSEC_Hide_Backend_Settings() );
