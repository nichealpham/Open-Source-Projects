<?php

final class ITSEC_Application_Passwords {
	public static function add_hooks() {
		add_filter( 'authenticate', array( __CLASS__, 'authenticate' ), 50, 3 );
		add_filter( 'determine_current_user', array( __CLASS__, 'filter_current_user' ), 20 );
		add_action( 'show_user_security_settings', array( __CLASS__, 'show_user_profile' ) );
		add_action( 'wp_ajax_itsec_application_password_create', array( __CLASS__, 'handle_ajax_request' ) );
		add_action( 'wp_ajax_itsec_application_password_revoke', array( __CLASS__, 'handle_ajax_request' ) );
		add_action( 'wp_ajax_itsec_application_password_revoke_all', array( __CLASS__, 'handle_ajax_request' ) );
	}

	public static function handle_ajax_request() {
		require_once( dirname( __FILE__ ) . '/application-passwords-util.php' );

		ITSEC_Application_Passwords_Util::handle_ajax_request();
	}

	public static function filter_current_user( $input_user ) {
		// Don't authenticate twice
		if ( ! empty( $input_user ) ) {
			return $input_user;
		}

		// Check that we're trying to authenticate
		if ( ! isset( $_SERVER['PHP_AUTH_USER'] ) && ! isset( $_SERVER['PHP_AUTH_PW'] ) ) {
			return $input_user;
		}

		$user = self::authenticate( $input_user, $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] );

		if ( is_a( $user, 'WP_User' ) ) {
			return $user->ID;
		}

		// If it wasn't a user what got returned, just pass on what we had received originally.
		return $input_user;
	}

	public static function authenticate( $input_user, $username, $password ) {
		$xml_rpc_request = ITSEC_Core::is_xmlrpc_request();
		$rest_api_request = ITSEC_Core::is_rest_api_request();
		$api_request = apply_filters( 'application_password_is_api_request', $xml_rpc_request || $rest_api_request );

		if ( ! $api_request ) {
			return $input_user;
		}


		$user = get_user_by( 'login', $username );

		if ( ! $user ) {
			// Don't try to process authentication for an invalid user.
			return $input_user;
		}


		/*
		 * Strip out anything non-alphanumeric. This is so passwords can be used with
		 * or without spaces to indicate the groupings for readability.
		 *
		 * Generated application passwords are exclusively alphanumeric.
		 */
		$password = preg_replace( '/[^a-z\d]/i', '', $password );

		require_once( dirname( __FILE__ ) . '/application-passwords-util.php' );
		$application_passwords = ITSEC_Application_Passwords_Util::get( $user->ID );

		foreach ( $application_passwords as $key => $item ) {
			if ( $rest_api_request ) {
				if ( ! in_array( 'rest-api', $item['enabled_for'] ) ) {
					continue;
				} else if ( ( 'read' === $item['rest_api_permissions'] ) && ( 'GET' !== $_SERVER['REQUEST_METHOD'] ) ) {
					continue;
				}
			} else if ( $xml_rpc_request && ! in_array( 'xml-rpc', $item['enabled_for'] ) ) {
				continue;
			} else {
				// All custom API requests due to the application_password_is_api_request filter returning true are checked
				// for an application password match.
			}

			if ( wp_check_password( $password, $item['password'], $user->ID ) ) {
				$item['last_used'] = time();
				$item['last_ip'] = $_SERVER['REMOTE_ADDR'];
				$application_passwords[$key] = $item;

				ITSEC_Application_Passwords_Util::set( $user->ID, $application_passwords );

				return $user;
			}
		}

		// If the user uses two factor and no valid API credentials were used, return an error
		if ( Two_Factor_Core::is_user_using_two_factor( $user->ID ) ) {
			return new WP_Error( 'invalid_application_credentials', __( '<strong>ERROR</strong>: Invalid API credentials provided.', 'it-l10n-ithemes-security-pro' ) );
		}

		// By default, return what we've been passed.
		return $input_user;
	}

	public static function show_user_profile( $user ) {
		require_once( dirname( __FILE__ ) . '/application-passwords-util.php' );

		ITSEC_Application_Passwords_Util::show_user_profile( $user );
	}
}
