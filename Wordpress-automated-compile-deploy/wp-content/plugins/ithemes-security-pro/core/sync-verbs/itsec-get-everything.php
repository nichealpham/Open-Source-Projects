<?php

class Ithemes_Sync_Verb_ITSEC_Get_Everything extends Ithemes_Sync_Verb {
	public static $name        = 'itsec-get-everything';
	public static $description = 'Retrieve iThemes Security Status and other information.';

	public function run( $arguments ) {
		$api = ITSEC_Core::get_sync_api();
		$modules = apply_filters( 'itsec-filter-itsec-get-everything-verbs', array() );

		$results = array(
			'api' => '1',
		);

		foreach ( $modules as $name => $verbs ) {
			foreach ( $verbs as $verb ) {
				$results[$name][$verb] = $api->run( $verb );
			}
		}

		return $results;
	}
}
