<?php

class Ithemes_Sync_Verb_ITSEC_Release_Lockout extends Ithemes_Sync_Verb {

	public static $name = 'itsec-release-lockout';
	public static $description = 'Release a lockout set by iThemes Security.';

	public $default_arguments = array(
		'id' => '', //lockout id to release
	);

	public function run( $arguments ) {

		/** @var ITSEC_Lockout $itsec_lockout */
		global $itsec_lockout;

		$id     = intval( $arguments['id'] );
		$result = $itsec_lockout->release_lockout( $id );

		if ( $result === false ) {

			$status = 'error';

		} else {

			$status = 'ok';

		}

		return array(
			'api'    => '0',
			'status' => $status
		);

	}

}