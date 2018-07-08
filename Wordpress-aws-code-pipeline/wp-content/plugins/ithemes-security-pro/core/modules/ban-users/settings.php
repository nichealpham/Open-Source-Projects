<?php

final class ITSEC_Ban_Users_Settings extends ITSEC_Settings {
	public function get_id() {
		return 'ban-users';
	}

	public function get_defaults() {
		return array(
			'default'          => false,
			'enable_ban_lists' => true,
			'host_list'        => array(),
			'agent_list'       => array(),
		);
	}
}

ITSEC_Modules::register_settings( new ITSEC_Ban_Users_Settings() );
