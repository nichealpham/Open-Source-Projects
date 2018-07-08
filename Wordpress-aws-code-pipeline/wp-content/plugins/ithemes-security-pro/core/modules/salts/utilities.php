<?php

final class ITSEC_WordPress_Salts_Utilities {
	public function generate_new_salts() {
		if ( ! ITSEC_Modules::get_setting( 'global', 'write_files' ) ) {
			return new WP_Error( 'itsec-wordpress-salts-utilities-write-files-disabled', __( 'The "Write to Files" setting is disabled in Global Settings. In order to use this feature, you must enable the "Write to Files" setting.', 'it-l10n-ithemes-security-pro' ) );
		}
		
		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-config-file.php' );
		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-file.php' );
		
		$config_file_path = ITSEC_Lib_Config_File::get_wp_config_file_path();
		$config = ITSEC_Lib_File::read( $config_file_path );
		
		if ( is_wp_error( $config ) ) {
			return new WP_Error( 'itsec-wordpress-salts-utilities-cannot-read-wp-config.php', sprintf( __( 'Unable to read the <code>wp-config.php</code> file in order to update the salts. You will need to manually update the file. Error details as follows: %1$s (%2$s)', 'it-l10n-ithemes-security-pro' ), $config->get_error_message(), $config->get_error_code() ) );
		}
		
		
		$defines = array(
			'AUTH_KEY',
			'SECURE_AUTH_KEY',
			'LOGGED_IN_KEY',
			'NONCE_KEY',
			'AUTH_SALT',
			'SECURE_AUTH_SALT',
			'LOGGED_IN_SALT',
			'NONCE_SALT',
		);
		
		foreach ( $defines as $define ) {
			if ( empty( $salts ) ) {
				$salts = self::get_new_salts();
			}
			
			$salt = array_pop( $salts );
			
			if ( empty( $salt ) ) {
				$salt = wp_generate_password( 64, true, true );
			}
			
			$salt = str_replace( '$', '\\$', $salt );
			$regex = "/(define\s*\(\s*(['\"])$define\\2\s*,\s*)(['\"]).+?\\3(\s*\)\s*;)/";
			$config = preg_replace( $regex, "\${1}'$salt'\${4}", $config );
		}
		
		$write_result = ITSEC_Lib_File::write( $config_file_path, $config );
		
		if ( is_wp_error( $write_result ) ) {
			return new WP_Error( 'itsec-wordpress-salts-utilities-cannot-save-wp-config.php', sprintf( __( 'Unable to update the <code>wp-config.php</code> file in order to update the salts. You will need to manually update the file. Error details as follows: %1$s (%2$s)', 'it-l10n-ithemes-security-pro' ), $config->get_error_message(), $config->get_error_code() ) );
		}
		
		return true;
	}
	
	public static function get_new_salts() {
		// From wp-admin/setup-config.php in WordPress 4.5.
		
		// Generate keys and salts using secure CSPRNG; fallback to API if enabled; further fallback to original wp_generate_password().
		try {
			$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|';
			$max = strlen($chars) - 1;
			for ( $i = 0; $i < 8; $i++ ) {
				$key = '';
				for ( $j = 0; $j < 64; $j++ ) {
					$key .= substr( $chars, random_int( 0, $max ), 1 );
				}
				$secret_keys[] = $key;
			}
		} catch ( Exception $ex ) {
			$secret_keys = wp_remote_get( 'https://api.wordpress.org/secret-key/1.1/salt/' );
			
			if ( is_wp_error( $secret_keys ) ) {
				$secret_keys = array();
				for ( $i = 0; $i < 8; $i++ ) {
					$secret_keys[] = wp_generate_password( 64, true, true );
				}
			} else {
				$secret_keys = explode( "\n", wp_remote_retrieve_body( $secret_keys ) );
				foreach ( $secret_keys as $k => $v ) {
					$secret_keys[$k] = substr( $v, 28, 64 );
				}
			}
		}
		
		return $secret_keys;
	}
}
