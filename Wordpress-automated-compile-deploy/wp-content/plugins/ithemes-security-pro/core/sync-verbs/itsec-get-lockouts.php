<?php

class Ithemes_Sync_Verb_ITSEC_Get_Lockouts extends Ithemes_Sync_Verb {

	public static $name = 'itsec-get-lockouts';
	public static $description = 'Retrieve a list of current lockouts in iThemes Security.';

	public $default_arguments = array();

	public function run( $arguments ) {

		global $itsec_lockout;

		$lockouts = $itsec_lockout->get_lockouts( 'all', true ); //Gets all lockouts, host and user

		//Send the user name or false
		foreach ( $lockouts as $key => $lockout ) {

			$userdata = get_userdata( intval( $lockout['lockout_user'] ) );

			if ( $userdata === false ) {

				$lockout['lockout_user'] = false;

			} else {

				$lockout['lockout_username'] = $userdata->user_login;

			}

			$lockouts[$key] = $lockout;

		}

		return array(
			'api'      => '0',
			'lockouts' => $lockouts,
		);

	}

}
