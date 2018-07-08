<?php

final class ITSEC_Online_Files_Settings extends ITSEC_Settings {
	public function get_id() {
		return 'online-files';
	}
	
	public function get_defaults() {
		return array(
			'compare_file_hashes' => false,
		);
	}
}

ITSEC_Modules::register_settings( new ITSEC_Online_Files_Settings() );
