<?php

class Ithemes_Sync_Verb_ITSEC_Override_Two_Factor_User extends Ithemes_Sync_Verb {

	public static $name        = 'itsec-override-two-factor-user';
	public static $description = 'Adds or removes a ten minute override for two-factor authentication for a given user';

	public $default_arguments = array(
		'id'        => 'add',
		'direction' => '',
	);

	public function run( $arguments ) {

		global $itsec_globals;

		if ( ! isset( $arguments['id'] ) ) {
			return false; //User not provided
		}

		$user = get_user_by( 'id', intval( $arguments['id'] ) );

		if ( $user === false ) {
			return false; //user doesn't exist
		}

		$direction        = isset( $arguments['direction'] ) ? $arguments['direction'] : 'add';
		$override         = intval( get_user_option( 'itsec_two_factor_override', $user->ID ) ) === 1 ? true : false;
		$override_expires = intval( get_user_option( 'itsec_two_factor_override_expires', $user->ID ) );

		if ( $direction === 'add' ) {

			if ( $override !== 1 && $itsec_globals['current_time'] < $override_expires ) {
				return false; //Override already active
			}

			$override         = true;
			$override_expires = $itsec_globals['current_time'] + 600;

			$response = array(
				'ID'               => $user->ID,
				'user_login'       => $user->user_login,
				'override'         => $override,
				'override_expires' => $override_expires,
			);

			update_user_option( $user->ID, 'itsec_two_factor_override', $override, true );
			update_user_option( $user->ID, 'itsec_two_factor_override_expires', $override_expires, true );

			return $response;

		} elseif ( $direction === 'remove' ) {

			delete_user_option( $user->ID, 'itsec_two_factor_override', true );
			delete_user_option( $user->ID, 'itsec_two_factor_override_expires', true );

			return true;

		}

		return false;

	}

}
