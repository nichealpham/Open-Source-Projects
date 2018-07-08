<?php

final class ITSEC_Version_Management_Settings extends ITSEC_Settings {
	public function get_id() {
		return 'version-management';
	}

	public function get_defaults() {
		return array(
			'wordpress_automatic_updates'   => false,
			'plugin_automatic_updates'      => false,
			'theme_automatic_updates'       => false,
			'strengthen_when_outdated'      => false,
			'scan_for_old_wordpress_sites'  => false,
			'email_contacts'                => array(),
			'update_details'                => array(),
			'is_software_outdated'          => false,
			'old_site_details'              => array(),
		);
	}
}

ITSEC_Modules::register_settings( new ITSEC_Version_Management_Settings() );
