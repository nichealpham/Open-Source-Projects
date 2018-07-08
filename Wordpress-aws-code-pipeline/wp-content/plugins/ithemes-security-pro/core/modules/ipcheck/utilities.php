<?php

final class ITSEC_Network_Brute_Force_Utilities {
	private static $network_endpoint = 'http://ipcheck-api.ithemes.com/';

	/**
	 * Retrieve an API key from the IPCheck server
	 *
	 * @since 4.5
	 *
	 * @param string $email the email address to associate with the key
	 * @param bool   $optin true to optin to mailing list else false
	 *
	 * @return string|WP_Error The API key or a WP_Error object.
	 */
	public static function get_api_key( $email, $optin ) {
		$email = sanitize_text_field( trim( $email ) );

		if ( ! is_email( $email ) ) {
			return new WP_Error( 'itsec-network-brute-force-utilities-get-api-key-bad-email', sprintf( __( 'The supplied email address (%s) is invalid. A valid email address is required in order to sign up for the Network Bruteforce Protection by iThemes.', 'it-l10n-ithemes-security-pro' ), $email ) );
		}

		$args = array(
			'action' => 'request-key',
			'email'  => $email,
			'optin'  => $optin,
		);

		$url = add_query_arg( $args, self::$network_endpoint );

		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( ! isset( $response['body'] ) ) {
			return new WP_Error( 'itsec-network-brute-force-utilities-get-api-key-failed-get-request', __( 'An unknown error prevented the API key request from succeeding. This problem could be due to a server configuration or plugin compatibility issue. Please wait a few minutes and try again.', 'it-l10n-ithemes-security-pro' ) );
		}

		$body = json_decode( $response['body'], true );

		if ( ! is_array( $body ) || ! isset( $body['apikey'] ) ) {
			return new WP_Error( 'itsec-network-brute-force-utilities-get-api-key-bad-response', __( 'An unknown error prevented the API key request from succeeding. The request for an API key returned an unrecognized response. Please wait a few minutes and try again.', 'it-l10n-ithemes-security-pro' ) );
		}

		$key = trim( sanitize_text_field( $body['apikey'] ) );

		if ( empty( $key ) ) {
			return new WP_Error( 'itsec-network-brute-force-utilities-get-api-key-bad-response', __( 'An unknown error prevented the API key request from succeeding. The request for an API key returned an empty key. Please wait a few minutes and try again.', 'it-l10n-ithemes-security-pro' ) );
		}

		return $key;
	}

	/**
	 * Activate an IPCheck API Key
	 *
	 * @since 4.5
	 *
	 * @param string $api_key the API key to activate
	 *
	 * @return string|WP_Error IPCheck activation secret or a WP_Error object.
	 */
	public static function activate_api_key( $api_key ) {
		$api_key = sanitize_text_field( trim( $api_key ) );

		if ( empty( $api_key ) ) {
			return new WP_Error( 'itsec-network-brute-force-utilities-activate-api-key-empty-key', __( 'An unknown error prevented the API key secret request from succeeding. The request for an API key submitted an empty key. Please wait a few minutes and try again.', 'it-l10n-ithemes-security-pro' ) );
		}

		$args = array(
			'action' => 'activate-key',
			'apikey' => $api_key,
			'site'   => home_url( '', 'http' ),
		);

		$url = add_query_arg( $args, self::$network_endpoint );

		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		if ( ! isset( $response['body'] ) ) {
			return new WP_Error( 'itsec-network-brute-force-utilities-activate-api-key-failed-get-request', __( 'An unknown error prevented the API key secret request from succeeding. This problem could be due to a server configuration or plugin compatibility issue. Please wait a few minutes and try again.', 'it-l10n-ithemes-security-pro' ) );
		}

		$body = json_decode( $response['body'], true );

		if ( ! is_array( $body ) || ! isset( $body['secret'] ) ) {
			// If this is an error with a message, show that to the user
			if ( ! empty( $body['error'] ) && ! empty( $body['error']['message'] ) ) {
				return new WP_Error( 'itsec-network-brute-force-utilities-activate-api-key-error-response', sprintf( __( 'There was an error returned from the Network Brute Force Protection API: %1$s', 'it-l10n-ithemes-security-pro' ), $body['error']['message'] ) );
			}
			return new WP_Error( 'itsec-network-brute-force-utilities-activate-api-key-bad-response', __( 'An unknown error prevented the API key secret request from succeeding. The request for an API key secret returned an unrecognized response. Please wait a few minutes and try again.', 'it-l10n-ithemes-security-pro' ) );
		}

		$secret = trim( sanitize_text_field( $body['secret'] ) );

		if ( empty( $secret ) ) {
			return new WP_Error( 'itsec-network-brute-force-utilities-activate-api-key-bad-response', __( 'An unknown error prevented the API key secrete request from succeeding. The request for an API key secret returned an empty key secret. Please wait a few minutes and try again.', 'it-l10n-ithemes-security-pro' ) );
		}

		return $secret;
	}
}
