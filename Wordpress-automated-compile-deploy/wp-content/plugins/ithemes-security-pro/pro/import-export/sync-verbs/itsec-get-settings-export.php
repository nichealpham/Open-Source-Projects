<?php

final class Ithemes_Sync_Verb_ITSEC_Get_Settings_Export extends Ithemes_Sync_Verb {
	public static $name        = 'itsec-get-settings-export';
	public static $description = 'Get an export of settings for iThemes Security';

	public function run( $arguments ) {
		require_once( dirname( dirname( __FILE__ ) ) . '/exporter.php' );

		$settings = ITSEC_Import_Export_Exporter::get_export_content();
		$settings = json_encode( $settings );

		return $settings;
	}
}
