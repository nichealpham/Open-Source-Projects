<?php

class Ithemes_Sync_Verb_ITSEC_Get_Security_Check_Feedback_Response extends Ithemes_Sync_Verb {
	public static $name = 'itsec-get-security-check-feedback-response';
	public static $description = '';

	private $default_arguments = array(
		'data' => array(),
	);

	public function run( $arguments ) {
		$arguments = Ithemes_Sync_Functions::merge_defaults( $arguments, $this->default_arguments );

		require_once( dirname( dirname( __FILE__ ) ) . '/scanner.php' );

		ITSEC_Security_Check_Scanner::activate_network_brute_force( $arguments['data'] );

		return ITSEC_Response::get_raw_data();
	}
}
