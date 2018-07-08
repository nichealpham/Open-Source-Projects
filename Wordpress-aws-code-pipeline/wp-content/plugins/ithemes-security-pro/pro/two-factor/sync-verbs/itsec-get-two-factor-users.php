<?php

class Ithemes_Sync_Verb_ITSEC_Get_Two_Factor_Users extends Ithemes_Sync_Verb {

	public static $name        = 'itsec-get-two-factor-users';
	public static $description = 'Retrieve a list of all users using two-factor authorization';

	public $default_arguments = array();

	public function run( $arguments ) {

		$itsec_two_factor = ITSEC_Two_Factor::get_instance();
		$two_factor_users = array();
		$users            = get_users();

		foreach ( $users as $user ) {
			$enabled          = $itsec_two_factor->is_user_using_two_factor( $user->ID );
			$override         = intval( get_user_option( 'itsec_two_factor_override', $user->ID ) ) === 1 ? true : false;
			$override_expires = intval( get_user_option( 'itsec_two_factor_override_expires', $user->ID ) );

			if ( $enabled == 'on' ) {

				$two_factor_users[$user->user_login] = array(
					'ID'               => $user->ID,
					'user_login'       => $user->user_login,
					'override'         => $override,
					'override_expires' => $override_expires,
				);

			}

		}

		return $two_factor_users;

	}

}
