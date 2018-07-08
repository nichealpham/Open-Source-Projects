<?php

class Ithemes_Sync_Verb_ITSEC_Get_Security_Check_Modules extends Ithemes_Sync_Verb {
	public static $name = 'itsec-get-security-check-modules';
	public static $description = '';

	public function run( $arguments ) {
		require_once( dirname( dirname( __FILE__ ) ) . '/scanner.php' );

		return ITSEC_Security_Check_Scanner::get_supported_modules();
	}
}
