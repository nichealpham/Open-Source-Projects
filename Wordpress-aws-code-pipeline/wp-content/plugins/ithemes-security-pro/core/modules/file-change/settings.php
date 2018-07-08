<?php

final class ITSEC_File_Change_Settings extends ITSEC_Settings {
	public function get_id() {
		return 'file-change';
	}

	public function get_defaults() {
		return array(
			'split'          => false,
			'method'         => 'exclude',
			'file_list'      => array(),
			'types'          => array(
				'.jpg',
				'.jpeg',
				'.png',
				'.log',
				'.mo',
				'.po'
			),
			'email'          => true,
			'notify_admin'   => true,
			'last_run'       => 0,
			'last_chunk'     => false,
			'show_warning'   => false,
			'latest_changes' => array(),
		);
	}
}

ITSEC_Modules::register_settings( new ITSEC_File_Change_Settings() );
