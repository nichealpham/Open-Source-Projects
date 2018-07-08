<?php

class ITSEC_User_Logging_Admin {
	function run() {
		add_filter( 'itsec_logger_displays', array( $this, 'register_logger_displays' ) ); //adds logs metaboxes
	}

	/**
	 * Array of metaboxes for the logs screen
	 *
	 * @since 4.0
	 *
	 * @param array $displays metabox array
	 *
	 * @return array metabox array
	 */
	public function register_logger_displays( $displays ) {

		$displays[] = array(
			'module'   => 'user_logging',
			'title'    => __( 'User Actions', 'it-l10n-ithemes-security-pro' ),
			'callback' => array( $this, 'logs_metabox_content' )
		);

		return $displays;

	}

	/**
	 * Render the settings metabox
	 *
	 * @return void
	 */
	public function logs_metabox_content() {

		if ( ! class_exists( 'ITSEC_User_Logging_Log' ) ) {
			require( dirname( __FILE__ ) . '/class-itsec-user-logging-log.php' );
		}

		$log_display = new ITSEC_User_Logging_Log();

		$log_display->prepare_items();
		$log_display->display();

	}

}
