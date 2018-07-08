<?php

class ITSEC_Four_Oh_Four {

	private $settings;

	function run() {

		$this->settings = ITSEC_Modules::get_settings( '404-detection' );

		add_filter( 'itsec_lockout_modules', array( $this, 'register_lockout' ) );
		add_filter( 'itsec_logger_modules', array( $this, 'register_logger' ) );
		add_filter( 'itsec_logger_displays', array( $this, 'register_logger_displays' ) );

		add_action( 'wp_head', array( $this, 'check_404' ) );

	}

	/**
	 * If the page is a WordPress 404 error log it and register for lockout
	 *
	 * @return void
	 */
	public function check_404() {

		/** @var ITSEC_Lockout $itsec_lockout */
		global $itsec_logger, $itsec_lockout;

		if ( ! is_404() ) {
			return;
		}

		$uri = explode( '?', $_SERVER['REQUEST_URI'] );

		if ( ! is_array( $this->settings['white_list'] ) || in_array( $uri[0], $this->settings['white_list'] ) ) {
			// Invalid settings or white listed page.
			return;
		}

		$itsec_logger->log_event(
			'four_oh_four',
			3,
			array(
				'query_string' => isset( $uri[1] ) ? esc_sql( $uri[1] ) : '',
			),
			ITSEC_Lib::get_ip(),
			'',
			'',
			esc_sql( $uri[0] ),
			isset( $_SERVER['HTTP_REFERER'] ) ? esc_sql( $_SERVER['HTTP_REFERER'] ) : ''
		);

		$path_info = pathinfo( $uri[0] );

		if ( ! isset( $path_info['extension'] ) || ( is_array( $this->settings['types'] ) && ! in_array( '.' . $path_info['extension'], $this->settings['types'] ) ) ) {

			$itsec_lockout->do_lockout( 'four_oh_four' );

		}

	}

	/**
	 * Register 404 detection for lockout
	 *
	 * @param  array $lockout_modules array of lockout modules
	 *
	 * @return array                   array of lockout modules
	 */
	public function register_lockout( $lockout_modules ) {

		$lockout_modules['four_oh_four'] = array(
			'type'   => 'four_oh_four',
			'reason' => __( 'too many attempts to access a file that does not exist', 'it-l10n-ithemes-security-pro' ),
			'host'   => $this->settings['error_threshold'],
			'period' => $this->settings['check_period']
		);

		return $lockout_modules;

	}

	/**
	 * Register 404 and file change detection for logger
	 *
	 * @param  array $logger_modules array of logger modules
	 *
	 * @return array                   array of logger modules
	 */
	public function register_logger( $logger_modules ) {

		$logger_modules['four_oh_four'] = array(
			'type'     => 'four_oh_four',
			'function' => __( '404 Error', 'it-l10n-ithemes-security-pro' ),
		);

		return $logger_modules;

	}

	/**
	 * Array of displays for the logs screen
	 *
	 * @since 4.0
	 *
	 * @param array $logger_displays metabox array
	 *
	 * @return array metabox array
	 */
	public function register_logger_displays( $logger_displays ) {

		$logger_displays[] = array(
			'module'   => 'four_oh_four',
			'title'    => __( '404 Errors Found', 'it-l10n-ithemes-security-pro' ),
			'callback' => array( $this, 'logs_metabox_content' )
		);

		return $logger_displays;

	}

	/**
	 * Render the settings metabox
	 *
	 * @return void
	 */
	public function logs_metabox_content() {

		if ( ! class_exists( 'ITSEC_Four_Oh_Four_Log' ) ) {
			require( dirname( __FILE__ ) . '/class-itsec-four-oh-four-log.php' );
		}

		$log_display = new ITSEC_Four_Oh_Four_Log();

		$log_display->prepare_items();
		$log_display->display();

	}

}
