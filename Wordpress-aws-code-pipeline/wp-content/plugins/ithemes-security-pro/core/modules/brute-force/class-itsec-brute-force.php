<?php

class ITSEC_Brute_Force {

	private
		$settings,
		$username;

	function run() {

		$this->settings = ITSEC_Modules::get_settings( 'brute-force' );

		add_action( 'wp_login', array( $this, 'wp_login' ), 10, 2 );
		add_action( 'itsec-handle-failed-login', array( $this, 'handle_failed_login' ), 10, 2 );

		add_filter( 'itsec_logger_displays', array( $this, 'itsec_logger_displays' ) ); //adds logs metaboxes

		add_filter( 'authenticate', array( $this, 'authenticate' ), 10, 3 );
		add_filter( 'itsec_lockout_modules', array( $this, 'itsec_lockout_modules' ) );
		add_filter( 'itsec_logger_modules', array( $this, 'itsec_logger_modules' ) );
		add_filter( 'jetpack_get_default_modules', array( $this, 'jetpack_get_default_modules' ) ); //disable jetpack protect via Geoge Stephanis

	}

	/**
	 * Sends to lockout class when login form isn't completely filled out and process xml_rpc username
	 *
	 * @since 4.0
	 *
	 * @param object $user     user or wordpress error
	 * @param string $username username attempted
	 * @param string $password password attempted
	 *
	 * @return user object or WordPress error
	 */
	public function authenticate( $user, $username = '', $password = '' ) {

		/** @var ITSEC_Lockout $itsec_lockout */
		/** @var ITSEC_Logger $itsec_logger */
		global $itsec_lockout, $itsec_logger;

		//Look for the "admin" user name and ban it if it is set to auto-ban
		if ( isset( $this->settings['auto_ban_admin'] ) && $this->settings['auto_ban_admin'] === true && 'admin' === $username ) {

			$itsec_logger->log_event( 'brute_force', 5, array(), ITSEC_Lib::get_ip(), $username );

			$itsec_lockout->do_lockout( 'brute_force_admin_user', $username );

		}

		//Execute brute force if username or password are empty
		if ( isset( $_POST['wp-submit'] ) && ( empty( $username ) || empty( $password ) ) ) {

			$user_id = username_exists( $username );

			if ( $user_id === false || $user_id === null ) {

				$itsec_lockout->check_lockout( false, $username );

			} else {

				$itsec_lockout->check_lockout( $user_id );

			}

			$itsec_logger->log_event( 'brute_force', 5, array(), ITSEC_Lib::get_ip(), $username, intval( $user_id ) );

			$itsec_lockout->do_lockout( 'brute_force', $username );

		}

		return $user;

	}

	/**
	 * Register Brute Force for lockout
	 *
	 * @since 4.0
	 *
	 * @param  array $lockout_modules array of lockout modules
	 *
	 * @return array                   array of lockout modules
	 */
	public function itsec_lockout_modules( $lockout_modules ) {

		$lockout_modules['brute_force'] = array(
			'type'   => 'brute_force',
			'reason' => __( 'too many bad login attempts', 'it-l10n-ithemes-security-pro' ),
			'host'   => $this->settings['max_attempts_host'],
			'user'   => $this->settings['max_attempts_user'],
			'period' => $this->settings['check_period'],
		);

		$lockout_modules['brute_force_admin_user'] = array(
			'type'   => 'brute_force',
			'reason' => __( 'user tried to login as "admin."', 'it-l10n-ithemes-security-pro' ),
			'host'   => 1,
			'user'   => 1,
			'period' => $this->settings['check_period']
		);

		return $lockout_modules;

	}

	/**
	 * Register Brute Force for logger
	 *
	 * @since 4.0
	 *
	 * @param  array $logger_modules array of logger modules
	 *
	 * @return array                   array of logger modules
	 */
	public function itsec_logger_modules( $logger_modules ) {

		$logger_modules['brute_force'] = array(
			'type'     => 'brute_force',
			'function' => __( 'Invalid Login Attempt', 'it-l10n-ithemes-security-pro' ),
		);

		return $logger_modules;

	}

	/**
	 * Disables the jetpack protect module
	 *
	 * Sent by George Stephanis
	 *
	 * @since 4.5
	 *
	 * @param array $modules array of Jetpack modules
	 *
	 * @return array array of Jetpack modules
	 */
	public function jetpack_get_default_modules( $modules ) {

		return array_diff( $modules, array( 'protect' ) );

	}

	/**
	 * Make sure user isn't already locked out even on successful form submission
	 *
	 * @since 4.0
	 *
	 * @param string $username the username attempted
	 * @param        object    wp_user the user
	 *
	 * @return void
	 */
	public function wp_login( $username, $user = null ) {

		/** @var ITSEC_Lockout $itsec_lockout */
		global $itsec_lockout;

		if ( ! $user === null ) {

			$itsec_lockout->check_lockout( $user );

		} elseif ( is_user_logged_in() ) {

			$current_user = wp_get_current_user();

			$itsec_lockout->check_lockout( $current_user->ID );

		}

	}

	/**
	 * Sends to lockout class when username and password are filled out and wrong
	 *
	 * @since 4.0
	 *
	 * @param string $username the username attempted
	 *
	 * @return void
	 */
	public function handle_failed_login( $username, $details ) {

		/** @var ITSEC_Lockout $itsec_lockout */
		global $itsec_lockout, $itsec_logger;

		$user_id = username_exists( $username );

		if ( 'admin' === $username && $this->settings['auto_ban_admin'] && empty( $user_id ) ) {
			$itsec_logger->log_event( 'brute_force', 5, $details, ITSEC_Lib::get_ip(), $username );
			$itsec_lockout->do_lockout( 'brute_force_admin_user', $username );

			return;
		}

		if ( empty( $user_id ) ) {
			$itsec_lockout->check_lockout( false, $username );
		} else {
			$itsec_lockout->check_lockout( $user_id );
		};

		$itsec_logger->log_event( 'brute_force', 5, $details, ITSEC_Lib::get_ip(), $username, intval( $user_id ) );
		$itsec_lockout->do_lockout( 'brute_force', $username );
	}

	/**
	 * Array of metaboxes for the logs screen
	 *
	 * @since 4.0
	 *
	 * @param object $displays metabox array
	 *
	 * @return array metabox array
	 */
	public function itsec_logger_displays( $displays ) {

		$displays[] = array(
			'module'   => 'brute_force',
			'title'    => __( 'Invalid Login Attempts', 'it-l10n-ithemes-security-pro' ),
			'callback' => array( $this, 'logs_metabox_content' ),
		);

		return $displays;

	}

	/**
	 * Render the settings metabox
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function logs_metabox_content() {

		if ( ! class_exists( 'ITSEC_Brute_Force_Log' ) ) {
			require( dirname( __FILE__ ) . '/class-itsec-brute-force-log.php' );
		}

		$log_display = new ITSEC_Brute_Force_Log();
		$log_display->prepare_items();
		$log_display->display();

	}

}
