<?php

final class ITSEC_Multisite_Tweaks_Settings extends ITSEC_Settings {
	public function get_id() {
		return 'multisite-tweaks';
	}
	
	public function get_defaults() {
		return array(
			'theme_updates'  => false,
			'plugin_updates' => false,
			'core_updates'   => false,
		);
	}
}

ITSEC_Modules::register_settings( new ITSEC_Multisite_Tweaks_Settings() );
