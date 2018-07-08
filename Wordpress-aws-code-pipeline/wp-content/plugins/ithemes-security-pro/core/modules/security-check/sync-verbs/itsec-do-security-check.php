<?php

class Ithemes_Sync_Verb_ITSEC_Do_Security_Check extends Ithemes_Sync_Verb {
	public static $name = 'itsec-do-security-check';
	public static $description = '';

	public function run( $arguments ) {
		require_once( dirname( dirname( __FILE__ ) ) . '/scanner.php' );

		return ITSEC_Security_Check_Scanner::get_results();
	}
}
