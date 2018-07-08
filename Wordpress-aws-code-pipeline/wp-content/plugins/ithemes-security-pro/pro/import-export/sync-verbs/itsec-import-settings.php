<?php

final class Ithemes_Sync_Verb_ITSEC_Import_Settings extends Ithemes_Sync_Verb {
	public static $name        = 'itsec-import-settings';
	public static $description = 'Import settings for iThemes Security';

	public $default_arguments = array(
		'data' => array(),
	);

	public function run( $arguments ) {
		$arguments = Ithemes_Sync_Functions::merge_defaults( $arguments, $this->default_arguments );

		require_once( dirname( dirname( __FILE__ ) ) . '/importer.php' );

		return ITSEC_Import_Export_Importer::import( $arguments['data'] );
	}
}
