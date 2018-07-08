<?php

final class ITSEC_Two_Factor_Settings extends ITSEC_Settings {
	public function get_id() {
		return 'two-factor';
	}

	public function get_defaults() {
		return array(
			'available_methods'        => 'all',
			'custom_available_methods' => array(
				'Two_Factor_Totp',
				'Two_Factor_Email',
				'Two_Factor_Backup_Codes',
			),
			'protect_user_type'        => 'disabled',
			'protect_user_type_roles'  => array(),
			'protect_vulnerable_users' => false,
			'protect_vulnerable_site'  => false,
		);
	}
}

ITSEC_Modules::register_settings( new ITSEC_Two_Factor_Settings() );
