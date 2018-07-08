<?php

class Ithemes_Sync_Verb_ITSEC_Perform_File_Scan extends Ithemes_Sync_Verb {
	public static $name = 'itsec-perform-file-scan';
	public static $description = 'Perform a one-time file scan';

	public function run( $arguments ) {
		require_once( dirname( dirname( __FILE__ ) ) . '/scanner.php' );

		return ITSEC_File_Change_Scanner::run_scan( false, true );
	}
}
