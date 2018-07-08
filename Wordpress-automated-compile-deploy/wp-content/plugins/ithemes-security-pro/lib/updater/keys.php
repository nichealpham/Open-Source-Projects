<?php

/*
Provides license key management.
Written by Chris Jean for iThemes.com
Version 1.0.2

Version History
	1.0.0 - 2013-04-11 - Chris Jean
		Release ready
	1.0.1 - 2013-09-19 - Chris Jean
		Updated requires to no longer use dirname().
	1.0.2 - 2014-10-23 - Chris Jean
		Updated code to meet WordPress coding standards.
*/


class Ithemes_Updater_Keys {
	private static $option_name = 'ithemes-updater-keys';
	
	
	public static function get( $packages = array() ) {
		$all_keys = get_site_option( self::$option_name, array() );
		
		if ( '__all__' == $packages ) {
			return $all_keys;
		}
		
		if ( empty( $packages ) ) {
			require_once( $GLOBALS['ithemes_updater_path'] . '/packages.php' );
			$packages = array_unique( array_values( Ithemes_Updater_Packages::get_all() ) );
		}
		
		
		$keys = array();
		
		foreach ( (array) $packages as $package ) {
			if ( ! empty( $all_keys[$package] ) ) {
				$keys[$package] = $all_keys[$package];
			}
		}
		
		
		if ( ! is_array( $packages ) ) {
			return $keys[$packages];
		}
		
		return $keys;
	}
	
	public static function set( $new_keys, $key = false ) {
		$keys = self::get( '__all__' );
		
		if ( false === $key ) {
			foreach ( $new_keys as $package => $key ) {
				$keys[$package] = $key;
			}
		} else {
			$keys[$new_keys] = $key;
		}
		
		update_site_option( self::$option_name, $keys );
	}
	
	private static function get_legacy_slug( $raw_slug ) {
		$slug = str_replace( '_', '-', $raw_slug );
		$slug = preg_replace( '/^(pluginbuddy|ithemes|it)-/', '', $slug );
		
		if ( 'boom-bar' == $slug ) {
			$slug = 'boombar';
		}
		
		return $slug;
	}
	
	public static function delete_legacy( $packages = array() ) {
		if ( ! is_array( $packages ) ) {
			$packages = array( $packages );
		}
		
		$data = get_site_option( 'pluginbuddy_plugins', false );
		
		
		$remaining_count = 0;
		
		foreach ( $data as $index => $entry ) {
			if ( ! is_object( $entry ) || empty( $entry->slug ) ) {
				continue;
			}
			
			$slug = self::get_legacy_slug( $entry->slug );
			
			if ( in_array( $slug, $packages ) ) {
				unset( $data[$index] );
			} else {
				$remaining_count++;
			}
		}
		
		if ( 0 == $remaining_count ) {
			$data = false;
		}
		
		
		update_site_option( 'pluginbuddy_plugins', $data );
	}
	
	public static function get_legacy( $packages = array() ) {
		$data = get_site_option( 'pluginbuddy_plugins', false );
		
		if ( empty( $data ) || ! is_array( $data ) ) {
			return array();
		}
		
		
		$keys = array();
		
		foreach ( $data as $index => $entry ) {
			if ( ! is_object( $entry ) || empty( $entry->slug ) || ! isset( $entry->key ) ) {
				continue;
			}
			
			$slug = self::get_legacy_slug( $entry->slug );
			$keys[$slug] = $entry->key;
		}
		
		
		foreach ( array_keys( $keys ) as $slug ) {
			if ( ! isset( $data[$slug] ) ) {
				continue;
			}
			
			$entry = $data[$slug];
			
			if ( ! is_object( $entry ) || empty( $entry->slug ) || empty( $entry->key ) ) {
				continue;
			}
			
			$keys[$slug] = $entry->key;
		}
		
		
		if ( empty( $packages ) ) {
			require_once( $GLOBALS['ithemes_updater_path'] . '/packages.php' );
			$packages = array_unique( array_values( Ithemes_Updater_Packages::get_all() ) );
		} else if ( is_string( $packages ) ) {
			if ( ! empty( $keys[$packages] ) ) {
				return $keys[$packages];
			}
			
			return false;
		}
		
		
		$package_keys = array();
		
		foreach ( $packages as $package ) {
			if ( ! empty( $keys[$package] ) ) {
				$package_keys[$package] = $keys[$package];
			}
		}
		
		return $package_keys;
	}
}
