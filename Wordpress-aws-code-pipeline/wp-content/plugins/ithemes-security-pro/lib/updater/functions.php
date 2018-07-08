<?php

/*
Misc functions to assist the updater code.
Written by Chris Jean for iThemes.com
Version 1.0.0

Version History
	1.0.0 - 2013-04-11 - Chris Jean
		Release ready
*/


class Ithemes_Updater_Functions {
	public static function get_url( $path ) {
		$path = str_replace( '\\', '/', $path );
		$wp_content_dir = str_replace( '\\', '/', WP_CONTENT_DIR );
		
		if ( 0 === strpos( $path, $wp_content_dir ) )
			return content_url( str_replace( $wp_content_dir, '', $path ) );
		
		$abspath = str_replace( '\\', '/', ABSPATH );
		
		if ( 0 === strpos( $path, $abspath ) )
			return site_url( str_replace( $abspath, '', $path ) );
		
		$wp_plugin_dir = str_replace( '\\', '/', WP_PLUGIN_DIR );
		$wpmu_plugin_dir = str_replace( '\\', '/', WPMU_PLUGIN_DIR );
		
		if ( 0 === strpos( $path, $wp_plugin_dir ) || 0 === strpos( $path, $wpmu_plugin_dir ) )
			return plugins_url( basename( $path ), $path );
		
		return false;
	}
	
	public static function get_package_name( $package ) {
		$name = str_replace( 'builderchild', 'Builder Child', $package );
		$name = str_replace( '-', ' ', $name );
		$name = ucwords( $name );
		$name = str_replace( 'buddy', 'Buddy', $name );
		$name = str_replace( 'Ithemes', 'iThemes', $name );
		
		return $name;
	}
	
	public static function get_post_data( $vars, $fill_missing = false ) {
		$data = array();
		
		foreach ( $vars as $var ) {
			if ( isset( $_POST[$var] ) ) {
				$clean_var = preg_replace( '/^it-updater-/', '', $var );
				$data[$clean_var] = $_POST[$var];
			}
			else if ( $fill_missing ) {
				$data[$var] = '';
			}
		}
		
		return stripslashes_deep( $data );
	}
}
