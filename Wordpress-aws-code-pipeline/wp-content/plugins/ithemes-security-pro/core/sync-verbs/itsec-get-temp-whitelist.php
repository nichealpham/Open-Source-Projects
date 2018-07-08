<?php

class Ithemes_Sync_Verb_ITSEC_Get_Temp_Whitelist extends Ithemes_Sync_Verb {
	public static $name = 'itsec-get-temp-whitelist';
	public static $description = 'Retrieve and report temporarily whitelisted IP.';

	public $default_arguments = array();


	public function run( $arguments ) {
		/** @var ITSEC_Lockout $itsec_lockout */
		global $itsec_lockout;

		$response = array(
			'version'        => 2,
			'temp_whitelist' => $itsec_lockout->get_temp_whitelist(),
		);

		return $response;
	}

}
