<?php

class Ithemes_Sync_Verb_ITSEC_Get_Away_Mode extends Ithemes_Sync_Verb {
	public static $name = 'itsec-get-away-mode';
	public static $description = 'Retrieve current away mode status.';
	
	public $default_arguments = array();
	
	public function run( $arguments ) {
		$details = ITSEC_Away_Mode::is_active( true );
		
		$response = array(
			'api'     => '1',
			'enabled' => $details['active'],
			'next'    => $details['next'],
			'details' => $details,
		);
		
		// For backwards compatibility with the api version 0 functionality, the next value represents either next or
		// remaining depending on the context. The context when this occurs is when away mode is either active or has
		// an active override, but not both.
		if ( $details['active'] xor $details['override_active'] ) {
			$response['next'] = $details['remaining'];
		}
		
		return $response;
	}
}
