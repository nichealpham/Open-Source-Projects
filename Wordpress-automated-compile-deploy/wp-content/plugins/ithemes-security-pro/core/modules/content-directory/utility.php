<?php

final class ITSEC_Content_Directory_Utility {
	public static function change_content_directory( $dir_name ) {
		$dir_name = sanitize_file_name( $dir_name );

		if ( empty( $dir_name ) ) {
			return new WP_Error( 'itsec-content-directory-utility-change-content-directory-empty-directory-name', __( 'The content directory cannot be changed to a blank directory name.', 'it-l10n-ithemes-security-pro' ) );
		}

		if ( preg_match( '{^(?:/|\\|[a-z]:)}i', $dir_name ) ) {
			return new WP_Error( 'itsec-content-diraectory-utility-change-content-directory-received-absolute-path', sprintf( __( 'The new directory name cannot be an absolute path. Please supply a path that is relative to <code>ABSPATH</code> (<code>%s</code>).', 'it-l10n-ithemes-security-pro' ), esc_html( ABSPATH ) ) );
		}

		if ( 0 === strpos( WP_CONTENT_DIR, ABSPATH ) ) {
			$old_name = substr( WP_CONTENT_DIR, strlen( ABSPATH ) );
			$new_name = $dir_name;
		} else {
			$old_name = WP_CONTENT_DIR;
			$new_name = ABSPATH . $dir_name;
		}

		$old_dir = WP_CONTENT_DIR;
		$new_dir = ABSPATH . $dir_name;

		if ( $old_dir === $new_dir ) {
			return new WP_Error( 'itsec-content-directory-utility-change-content-directory-received-same-directory', __( 'The new directory name cannot be the same as the current directory name. Please supply a new directory name.', 'it-l10n-ithemes-security-pro' ) );
		}

		if ( file_exists( $new_dir ) ) {
			return new WP_Error( 'itsec-content-directory-utility-change-content-directory-path-already-exists', sprintf( __( 'A file or directory already exists at <code>%s</code>. No Directory Name changes have been made. Please choose a new Directory Name or remove the existing file or directory and try again.', 'it-l10n-ithemes-security-pro' ), esc_html( $new_dir ) ) );
		}


		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-config-file.php' );


		$old_permissions = ITSEC_Lib_Directory::get_permissions( $old_dir );
		$result = rename( $old_dir, $new_dir );

		if ( ! $result ) {
			/* translators: 1: Old directory path, 2: New directory path */
			return new WP_Error( 'itsec-content-directory-utility-change-content-directory-cannot-rename-directory', sprintf( __( 'Unable to rename the <code>%1$s</code> directory to <code>%2$s</code>. This could indicate a file permission issue or that your server does not support the supplied name as a valid directory name. No config file or directory changes have been made.', 'it-l10n-ithemes-security-pro' ), esc_html( $old_name ), esc_html( $new_name ) ) );
		}

		// Make sure ITSEC_Core knows it's in a different place
		$plugin_file = str_replace( $old_dir, $new_dir, ITSEC_Core::get_plugin_file() );
		ITSEC_Core::set_plugin_file( $plugin_file );
		ITSEC_Core::update_wp_upload_dir( $old_dir, $new_dir );
		ITSEC_Modules::update_module_paths( $old_dir, $new_dir );


		$new_permissions = ITSEC_Lib_Directory::get_permissions( $new_dir );

		if ( is_int( $old_permissions) && is_int( $new_permissions ) && ( $old_permissions != $new_permissions ) ) {
			$result = ITSEC_Lib_Directory::chmod( $new_dir, $old_permissions );

			if ( is_wp_error( $result ) ) {
				/* translators: 1: Directory path, 2: Directory permissions */
				return new WP_Error( 'itsec-content-directory-utility-change-content-directory-unable-to-change-permissions', sprintf( __( 'Unable to set the permissions of the new Directory Name (<code>%1$s</code>) to match the permissions of the old Directory Name. You may have to manually change the permissions of the directory to <code>%2$s</code> in order for your site to function properly.', 'it-l10n-ithemes-security-pro' ), esc_html( $new_name ), esc_html( $old_permissions ) ) );
			}
		}


		if ( 'wp-content' === $dir_name ) {
			// We're undoing the change.
			$expression = self::get_wp_config_define_expression();
			$expression = substr( $expression, 0, -1 );
			$expression .= "[\r\n]*|";

			$modification_result = ITSEC_Lib_Config_File::remove_from_wp_config( $expression );
		} else {
			$modification = self::get_wp_config_modification( $new_dir, get_option( 'siteurl' ) . "/$dir_name" );

			$modification_result = ITSEC_Lib_Config_File::append_wp_config( $modification, true );
		}


		if ( is_wp_error( $modification_result ) ) {
			$rename_result = rename( $new_dir, $old_dir );

			if ( $rename_result ) {
				// Reset the ITSEC_Core plugin file back to its old setting.
				$plugin_file = str_replace( $new_dir, $old_dir, ITSEC_Core::get_plugin_file() );
				ITSEC_Core::set_plugin_file( $plugin_file );
				ITSEC_Core::update_wp_upload_dir( $new_dir, $old_dir );
				ITSEC_Modules::update_module_paths( $new_dir, $old_dir );


				ITSEC_Lib_Directory::chmod( $old_dir, $old_permissions );

				/* translators: 1: Specific error details */
				return new WP_Error( $modification_result->get_error_code(), sprintf( __( 'Unable to update the <code>wp-config.php</code> file. No directory or config file changes have been made. The error that prevented the file from updating is as follows: %1$s', 'it-l10n-ithemes-security-pro' ), $modification_result->get_error_message() ) );
			} else {
				/* translators: 1: Old directory path, 2: New directory path, 3: Specific error details */
				return new WP_Error( $modification_result->get_error_code(), sprintf( __( 'CRITICAL ERROR: The <code>%1$s</code> directory was successfully renamed to the new name (<code>%2$s</code>). However, an error occurred when updating the <code>wp-config.php</code> file to configure WordPress to use the new content directory. iThemes Security attempted to rename the directory back to its original name, but an unknown error prevented the rename from working as expected. In order for your site to function properly, you will either need to manually rename the <code>%2$s</code> directory back to <code>%1$s</code> or manually update the <code>wp-config.php</code> file with the necessary modifications. The error that prevented the file from updating is as follows: %3$s', 'it-l10n-ithemes-security-pro' ), $old_name, $new_name, $modification_result->get_error_message() ) );
			}
		}


		$backups_location = ITSEC_Modules::get_setting( 'backup', 'location' );
		$backups_location = str_replace( $old_dir, $new_dir, $backups_location );
		ITSEC_Modules::set_setting( 'backup', 'location', $backups_location );

		$log_location = ITSEC_Modules::get_setting( 'global', 'log_location' );
		$log_location = str_replace( $old_dir, $new_dir, $log_location );
		ITSEC_Modules::set_setting( 'global', 'log_location', $log_location );

		$nginx_file = ITSEC_Modules::get_setting( 'global', 'nginx_file' );
		$nginx_file = str_replace( $old_dir, $new_dir, $nginx_file );
		ITSEC_Modules::set_setting( 'global', 'nginx_file', $nginx_file );


		return $dir_name;
	}

	public static function get_wp_config_define_warning() {
		return __( 'Do not remove. Removing this line could break your site. Added by Security > Settings > Change Content Directory.', 'it-l10n-ithemes-security-pro' );
	}

	public static function get_wp_config_define( $name, $value, $include_warning_comment = true ) {
		$name = str_replace( "'", "\\'", $name );
		$value = str_replace( "'", "\\'", $value );
		$line = "define( '$name', '$value' );";

		if ( $include_warning_comment ) {
			$line .= ' // ' . self::get_wp_config_define_warning();
		}

		return $line;
	}

	public static function get_wp_config_modification( $dir, $url, $include_warning_comment = true ) {
		$modification  = self::get_wp_config_define( 'WP_CONTENT_DIR', $dir, $include_warning_comment ) . "\n";
		$modification .= self::get_wp_config_define( 'WP_CONTENT_URL', $url, $include_warning_comment );

		return $modification;
	}

	public static function get_wp_config_define_expression( $include_warning_comment = true ) {
		$expression = self::get_wp_config_modification( 'WILDCARD', 'WILDCARD', $include_warning_comment );
		$expression = preg_quote( $expression, '|' );
		$expression = str_replace( ' ', '\s*', $expression );
		$expression = str_replace( 'WILDCARD', "[^']+", $expression );
		$expression = "|$expression|";

		if ( $include_warning_comment ) {
			$expression = str_replace( "\n", "\s*[\r\n]+\s*", $expression );
		} else {
			$expression = str_replace( "\n", "\s*", $expression );
		}

		return $expression;
	}

	public static function is_custom_directory() {
		if ( isset( $GLOBALS['__itsec_content_directory_is_custom_directory'] ) ) {
			return $GLOBALS['__itsec_content_directory_is_custom_directory'];
		}

		if ( ABSPATH . 'wp-content' !== WP_CONTENT_DIR ) {
			$GLOBALS['__itsec_content_directory_is_custom_directory'] = true;
		} else if ( get_option( 'siteurl' ) . '/wp-content' !== WP_CONTENT_URL ) {
			$GLOBALS['__itsec_content_directory_is_custom_directory'] = true;
		} else {
			$GLOBALS['__itsec_content_directory_is_custom_directory'] = false;
		}

		return $GLOBALS['__itsec_content_directory_is_custom_directory'];
	}

	public static function is_modified_by_it_security() {
		if ( isset( $GLOBALS['__itsec_content_directory_is_modified_by_it_security'] ) ) {
			return $GLOBALS['__itsec_content_directory_is_modified_by_it_security'];
		}

		$GLOBALS['__itsec_content_directory_is_modified_by_it_security'] = false;


		if ( ! self::is_custom_directory() ) {
			return false;
		}


		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-config-file.php' );

		$wp_config_file = ITSEC_Lib_Config_File::get_wp_config_file_path();

		if ( empty( $wp_config_file ) ) {
			return false;
		}

		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-file.php' );

		$wp_config = ITSEC_Lib_File::read( $wp_config_file );

		if ( is_wp_error( $wp_config ) ) {
			return false;
		}

		$define_expression = self::get_wp_config_define_expression();

		if ( ! preg_match( $define_expression, $wp_config ) ) {
			return false;
		}

		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-utility.php' );

		$wp_config_without_comments = ITSEC_Lib_Utility::strip_php_comments( $wp_config );

		if ( is_wp_error( $wp_config_without_comments ) ) {
			return false;
		}

		$define_expression_without_comment = self::get_wp_config_define_expression( false );

		if ( ! preg_match( $define_expression_without_comment, $wp_config_without_comments ) ) {
			return false;
		}


		$GLOBALS['__itsec_content_directory_is_modified_by_it_security'] = true;

		return true;
	}
}
