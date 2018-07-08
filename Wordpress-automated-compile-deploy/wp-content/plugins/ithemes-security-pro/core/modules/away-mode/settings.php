<?php

final class ITSEC_Away_Mode_Settings extends ITSEC_Settings {
	public function get_id() {
		return 'away-mode';
	}
	
	public function get_defaults() {
		return array(
			'type'          => 'daily',
			'start'         => 1,
			'start_time'    => 100000,
			'end'           => 1,
			'end_time'      => 100000,
			'override_type' => '',
			'override_end'  => 0,
		);
	}
	
	protected function after_save() {
		require_once( dirname( __FILE__ ) . '/utilities.php' );
		
		ITSEC_Away_Mode_Utilities::create_active_file();
	}
}

ITSEC_Modules::register_settings( new ITSEC_Away_Mode_Settings() );
