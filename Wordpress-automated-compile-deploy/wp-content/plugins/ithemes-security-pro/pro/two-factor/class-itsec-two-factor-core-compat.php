<?php

/**
 * Two-Factor Administrative Screens
 *
 * Sets up all administrative functions for the two-factor authentication feature
 * including fields, sanitation and all other privileged functions.
 *
 * @since   1.2.0
 *
 * @package iThemes_Security
 */
if ( ! class_exists( 'Two_Factor_Core' ) ):
class Two_Factor_Core {

	public static function get_enabled_providers_for_user( $user = null ) {
		require_once( dirname( __FILE__ ) . '/class-itsec-two-factor.php' );
		$itsec_two_factor = ITSEC_Two_Factor::get_instance();
		return $itsec_two_factor->get_enabled_providers_for_user( $user );
	}

	public static function is_user_using_two_factor( $user_id = null ) {
		require_once( dirname( __FILE__ ) . '/class-itsec-two-factor.php' );
		$itsec_two_factor = ITSEC_Two_Factor::get_instance();
		return $itsec_two_factor->is_user_using_two_factor( $user_id );
	}

}
endif;
