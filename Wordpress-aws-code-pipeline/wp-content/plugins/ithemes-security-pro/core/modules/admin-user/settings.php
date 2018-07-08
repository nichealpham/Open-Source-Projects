<?php

final class ITSEC_Admin_User_Settings extends ITSEC_Settings {
	public function get_id() {
		return 'admin-user';
	}
	
	public function get_defaults() {
		return array();
	}
}

ITSEC_Modules::register_settings( new ITSEC_Admin_User_Settings() );
