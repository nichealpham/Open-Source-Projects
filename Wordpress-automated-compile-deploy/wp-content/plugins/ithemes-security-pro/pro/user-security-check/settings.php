<?php

final class ITSEC_User_Security_Check_Settings extends ITSEC_Settings {
	public function get_id() {
		return 'user-security-check';
	}

	public function get_defaults() {
		return array(
		);
	}
}

ITSEC_Modules::register_settings( new ITSEC_User_Security_Check_Settings() );
