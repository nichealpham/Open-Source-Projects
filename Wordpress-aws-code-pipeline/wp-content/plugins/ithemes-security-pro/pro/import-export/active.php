<?php

function itsec_import_export_register_sync_verbs( $api ) {
	$api->register( 'itsec-get-settings-export', 'Ithemes_Sync_Verb_ITSEC_Get_Settings_Export', dirname( __FILE__ ) . '/sync-verbs/itsec-get-settings-export.php' );
	$api->register( 'itsec-import-settings', 'Ithemes_Sync_Verb_ITSEC_Import_Settings', dirname( __FILE__ ) . '/sync-verbs/itsec-import-settings.php' );
}

add_action( 'ithemes_sync_register_verbs', 'itsec_import_export_register_sync_verbs' );
