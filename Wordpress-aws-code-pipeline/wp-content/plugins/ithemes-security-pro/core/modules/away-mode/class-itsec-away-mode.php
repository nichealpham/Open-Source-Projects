<?php

final class ITSEC_Away_Mode {

	public function run() {

		//Execute away mode functions on admin init
		add_filter( 'itsec_logger_modules', array( $this, 'register_logger' ) );
		add_action( 'itsec_admin_init', array( $this, 'run_active_check' ) );
		add_action( 'login_init', array( $this, 'run_active_check' ) );

		add_action( 'ithemes_sync_register_verbs', array( $this, 'register_sync_verbs' ) );
		add_filter( 'itsec-filter-itsec-get-everything-verbs', array( $this, 'register_sync_get_everything_verbs' ) );

	}

	/**
	 * Check if away mode is active
	 *
	 * @since 4.4
	 * @static
	 *
	 * @param bool $get_details Optional, defaults to false. True to receive details rather than a boolean response.
	 *
	 * @return mixed If $get_details is true, an array of status details. Otherwise, true if away and false otherwise.
	 */
	public static function is_active( $get_details = false ) {
		require_once( dirname( __FILE__ ) . '/utilities.php' );

		$settings = ITSEC_Modules::get_settings( 'away-mode' );

		if ( 'daily' === $settings['type'] ) {
			$details = ITSEC_Away_Mode_Utilities::is_current_time_active( $settings['start_time'], $settings['end_time'], true );
		} else {
			$details = ITSEC_Away_Mode_Utilities::is_current_timestamp_active( $settings['start'], $settings['end'], true );
		}

		$details['has_active_file'] = ITSEC_Away_Mode_Utilities::has_active_file();
		$details['override_type'] = $settings['override_type'];
		$details['override_end'] = $settings['override_end'];

		if ( empty( $settings['override_type'] ) || ( ITSEC_Core::get_current_time() > $settings['override_end'] ) ) {
			$details['override_active'] = false;
		} else {
			$details['override_active'] = true;

			if ( 'activate' === $details['override_type'] ) {
				$details['active'] = true;
			} else {
				$details['active'] = false;
			}
		}

		// If the active file does not exist, completely disable the away mode feature to allow an administrator
		// to regain access to their site.
		if ( ! $details['has_active_file'] ) {
			$details['active'] = false;
			$details['remaining'] = false;
			$details['next'] = false;
			$details['length'] = false;
		}

		if ( ! isset( $details['error'] ) ) {
			$details['error'] = false;
		}


		if ( $get_details ) {
			return $details;
		}

		return $details['active'];
	}

	/**
	 * Execute away mode functionality
	 *
	 * @return void
	 */
	public function run_active_check() {

		global $itsec_logger;

		//execute lockout if applicable
		if ( self::is_active() ) {

			$itsec_logger->log_event(
				'away_mode',
				5,
				array(
					__( 'A host was prevented from accessing the dashboard due to away-mode restrictions being in effect', 'it-l10n-ithemes-security-pro' ),
				),
				ITSEC_Lib::get_ip(),
				'',
				'',
				'',
				''
			);

			wp_redirect( get_option( 'siteurl' ) );
			wp_clear_auth_cookie();
			die();

		}

	}

	/**
	 * Register 404 and file change detection for logger
	 *
	 * @param  array $logger_modules array of logger modules
	 *
	 * @return array array of logger modules
	 */
	public function register_logger( $logger_modules ) {

		$logger_modules['away_mode'] = array(
			'type'     => 'away_mode',
			'function' => __( 'Away Mode Triggered', 'it-l10n-ithemes-security-pro' ),
		);

		return $logger_modules;

	}

	/**
	 * Register verbs for Sync.
	 *
	 * @since 3.6.0
	 *
	 * @param Ithemes_Sync_API $api API object.
	 */
	public function register_sync_verbs( $api ) {
		$api->register( 'itsec-get-away-mode', 'Ithemes_Sync_Verb_ITSEC_Get_Away_Mode', dirname( __FILE__ ) . '/sync-verbs/itsec-get-away-mode.php' );
		$api->register( 'itsec-override-away-mode', 'Ithemes_Sync_Verb_ITSEC_Override_Away_Mode', dirname( __FILE__ ) . '/sync-verbs/itsec-override-away-mode.php' );
	}

	/**
	 * Filter to add verbs to the response for the itsec-get-everything verb.
	 *
	 * @since 3.6.0
	 *
	 * @param  array Array of verbs.
	 *
	 * @return array Array of verbs.
	 */
	public function register_sync_get_everything_verbs( $verbs ) {
		$verbs['away_mode'][] = 'itsec-get-away-mode';

		return $verbs;
	}
}
