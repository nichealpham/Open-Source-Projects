<?php

/*
Provides a simple interface for connecting iThemes' packages with the updater API.
Written by Chris Jean for iThemes.com
Version 1.4.1

Version History
	1.0.0 - 2013-04-11 - Chris Jean
		Release ready
	1.0.1 - 2013-09-19 - Chris Jean
		Changed the logic in process_server_response to skip updatable packages that have the 'upgrade' data set to a true value.
		Updated requires to not use dirname().
		Updated ithemes-updater-object to ithemes-updater-settings.
	1.1.0 - 2013-10-02 - Chris Jean
		Updated 'url' data for themes to point to the plugin-install.php file in order to show changelog notes as plugins have.
	1.2.0 - 2013-10-04 - Chris Jean
		Added logic to handle skipped updates when force_minor_version_update is set.
	1.2.1 - 2013-10-04 - Chris Jean
		Added a fix to prevent the code from executing if it is loaded by an older updater version. This can happen when updating a theme or plugin.
	1.3.0 - 2013-10-23 - Chris Jean
		Enhancement: Added support for quick_releases setting to force an update to a quick release.
	1.4.0 - 2014-11-13 - Chris Jean
		Improved cache flush handling.
		Removed server-cache setting change handler.
		Added timeout-multiplier setting change handler.
	1.4.1 - 2015-04-23 - Chris Jean
		Added "plugin" entry for plugins in order to handle changes in WordPress 4.2.
		Added "theme" entry for themes in order to handle changes in WordPress 4.2.
		Added support for both "autoupdate" and "upgrade_notice" fields to be supplied from the server.
*/


class Ithemes_Updater_Updates {
	public static function run_update() {
		// Prevent the code from running if the code was loaded by an older updater version.
		if ( ! isset( $GLOBALS['ithemes_updater_path'] ) ) {
			return;
		}
		
		require_once( $GLOBALS['ithemes_updater_path'] . '/api.php' );
		require_once( $GLOBALS['ithemes_updater_path'] . '/packages.php' );
		require_once( $GLOBALS['ithemes_updater_path'] . '/keys.php' );
		
		
		$keys = Ithemes_Updater_Keys::get();
		$legacy_keys = Ithemes_Updater_Keys::get_legacy();
		
		if ( empty( $keys ) && empty( $legacy_keys ) ) {
			return;
		}
		
		
		Ithemes_Updater_API::get_package_details( false );
	}
	
	public static function process_server_response( $response, $cached = false ) {
		if ( empty( $response['packages'] ) ) {
			return;
		}
		
		
		require_once( $GLOBALS['ithemes_updater_path'] . '/keys.php' );
		require_once( $GLOBALS['ithemes_updater_path'] . '/packages.php' );
		
		
		$keys = array();
		
		foreach ( $response['packages'] as $package => $data ) {
			if ( isset( $data['key'] ) ) {
				$keys[$package] = $data['key'];
			} else if ( isset( $data['status'] ) && ( 'inactive' == $data['status'] ) ) {
				$keys[$package] = '';
			}
		}
		
		Ithemes_Updater_Keys::set( $keys );
		
		
		$details = Ithemes_Updater_Packages::get_full_details( $response );
		
		$updates = array(
			'update_themes'  => array(),
			'update_plugins' => array(),
			'expiration'     => $details['expiration'],
		);
		
		if ( ! $cached ) {
			$updates['timestamp'] = time();
		}
		
		
		if ( isset( $response['timeout_multiplier'] ) ) {
			$updates['timeout-multiplier'] = $response['timeout_multiplier'];
		}
		
		if ( ! isset( $updates['timeout-multiplier'] ) || ( $updates['timeout-multiplier'] < 1 ) ) {
			$updates['timeout-mulitplier'] = 1;
		} else if ( $updates['timeout-multiplier'] > 10 ) {
			$updates['timeout-mulitplier'] = 10;
		}
		
		$use_ssl = $GLOBALS['ithemes-updater-settings']->get_option( 'use_ssl' );
		
		
		foreach ( $details['packages'] as $path => $data ) {
			if ( empty( $data['package-url'] ) || version_compare( $data['installed'], $data['available'], '>=' ) ) {
				continue;
			}
			
			
			$force_minor_version_update = $GLOBALS['ithemes-updater-settings']->get_option( 'force_minor_version_update' );
			$quick_releases = $GLOBALS['ithemes-updater-settings']->get_option( 'quick_releases' );
			
			if ( ( isset( $data['upgrade'] ) && ! $data['upgrade'] ) && ! $force_minor_version_update && ! $quick_releases ) {
				continue;
			}
			
			if ( ! $use_ssl ) {
				$data['package-url'] = preg_replace( '/^https/', 'http', $data['package-url'] );
			}
			
			if ( 'plugin' == $data['type'] ) {
				$update = array(
					'id'          => 0,
					'slug'        => dirname( $path ),
					'plugin'      => $path,
					'new_version' => $data['available'],
					'url'         => $data['info-url'],
					'package'     => $data['package-url'],
				);
				
				if ( isset( $data['autoupdate'] ) ) {
					$update['autoupdate'] = $data['autoupdate'];
				}
				if ( isset( $data['upgrade_notice'] ) ) {
					$update['upgrade_notice'] = $data['upgrade_notice'];
				}
				
				$update = (object) $update;
			}
			else {
				$update = array(
					'theme'       => $path,
					'new_version' => $data['available'],
					'url'         => self_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . dirname( $path ) . '&section=changelog&TB_iframe=true&width=600&height=800' ),
					'package'     => $data['package-url'],
				);
				
				if ( isset( $data['autoupdate'] ) ) {
					$update['autoupdate'] = $data['autoupdate'];
				}
				if ( isset( $data['upgrade_notice'] ) ) {
					$update['upgrade_notice'] = $data['upgrade_notice'];
				}
				
				$path = dirname( $path );
			}
			
			$updates["update_{$data['type']}s"][$path] = $update;
		}
		
		
		$GLOBALS['ithemes-updater-settings']->update_options( $updates );
	}
}
