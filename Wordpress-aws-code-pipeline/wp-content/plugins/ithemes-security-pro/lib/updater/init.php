<?php

/*
Load the updater and licensing system without loading unneeded parts.
Written by Chris Jean for iThemes.com
Version 1.2.1

Version History
	1.0.0 - 2013-04-11 - Chris Jean
		Release ready
	1.0.1 - 2013-05-01 - Chris Jean
		Fixed a bug where some plugins caused the filter_update_plugins and filter_update_themes to run when load hadn't run, causing errors.
	1.1.0 - 2013-09-19 - Chris Jean
		Complete restructuring of this file as most of the code has been relocated to other files.
	1.2.0 - 2013-12-13 - Chris Jean
		Added the ability to force clear the server timeout hold by adding a query variable named ithemes-updater-force-clear-server-timeout-hold to the URL.
	1.2.1 - 2014-10-23 - Chris Jean
		Removed ithemes-updater-force-clear-server-timeout-hold code.
*/


if ( defined( 'ITHEMES_UPDATER_DISABLE' ) && ITHEMES_UPDATER_DISABLE ) {
	return;
}


$GLOBALS['ithemes_updater_path'] = dirname( __FILE__ );


if ( is_admin() ) {
	require( $GLOBALS['ithemes_updater_path'] . '/admin.php' );
}


function ithemes_updater_filter_update_plugins( $update_plugins ) {
	if ( ! class_exists( 'Ithemes_Updater_Settings' ) ) {
		require( $GLOBALS['ithemes_updater_path'] . '/settings.php' );
	}
	
	return $GLOBALS['ithemes-updater-settings']->filter_update_plugins( $update_plugins );
}
add_filter( 'site_transient_update_plugins', 'ithemes_updater_filter_update_plugins' );
add_filter( 'transient_update_plugins', 'ithemes_updater_filter_update_plugins' );


function ithemes_updater_filter_update_themes( $update_themes ) {
	if ( ! class_exists( 'Ithemes_Updater_Settings' ) ) {
		require( $GLOBALS['ithemes_updater_path'] . '/settings.php' );
	}
	
	return $GLOBALS['ithemes-updater-settings']->filter_update_themes( $update_themes );
}
add_filter( 'site_transient_update_themes', 'ithemes_updater_filter_update_themes' );
add_filter( 'transient_update_themes', 'ithemes_updater_filter_update_themes' );
