<?php
/**
 * iThemes Security config file library.
 *
 * Contains the ITSEC_Lib_Config_File class.
 *
 * @package iThemes_Security
 */

/**
 * iThemes Security Config File Library class.
 *
 * Utility class for adding, updating, and removing iThemes Security modifications from existing config files.
 *
 * @package iThemes_Security
 * @since 1.15.0
 */
class ITSEC_Lib_Config_File {
	/**
	 * The current version of the config file format.
	 *
	 * @since 1.15.0
	 * @var int
	 */
	const FORMAT_VERSION = 2;


	/**
	 * Get the server config to be written to the config file.
	 *
	 * The server config is retrieved by the itsec_filter_SERVER_server_config_modification filter where SERVER is a
	 * valid server name.
	 *
	 * @since 1.15.0
	 *
	 * @return string The server config.
	 */
	public static function get_server_config() {
		$server = ITSEC_Lib_Utility::get_web_server();
		$modification = apply_filters( "itsec_filter_{$server}_server_config_modification", '' );
		$comment_delimiter = self::get_comment_delimiter( $server );
		$modification = self::get_prepared_modification( $modification, $comment_delimiter );

		return $modification;
	}

	/**
	 * Get the minimal server config to be written to the config file.
	 *
	 * The server config is retrieved by the itsec_filter_SERVER_minimal_server_config_modification filter where SERVER
	 * is a valid server name.
	 *
	 * @since 1.15.0
	 *
	 * @return string The server config.
	 */
	public static function get_minimal_server_config() {
		$server = ITSEC_Lib_Utility::get_web_server();
		$modification = apply_filters( "itsec_filter_{$server}_minimal_server_config_modification", '' );

		if ( empty( $modification ) ) {
			return '';
		}

		$comment_delimiter = self::get_comment_delimiter( $server );
		$modification = "$comment_delimiter " . __( 'iThemes Security preserved the following settings as removing them could prevent the site from functioning correctly.', 'it-l10n-ithemes-security-pro' ) . "\n$modification";
		$modification = self::get_prepared_modification( $modification, $comment_delimiter );

		return $modification;
	}

	/**
	 * Update the server config file.
	 *
	 * @since 1.15.0
	 *
	 * @return bool|WP_Error Boolean true on success or a WP_Error object otherwise.
	 */
	public static function update_server_config() {
		$server = ITSEC_Lib_Utility::get_web_server();
		$modification = self::get_server_config();

		return self::write_server_config( $server, $modification );
	}

	/**
	 * Remove all removable settings from the server config file.
	 *
	 * @since 1.15.0
	 *
	 * @return bool|WP_Error Boolean true on success or a WP_Error object otherwise.
	 */
	public static function reset_server_config() {
		$server = ITSEC_Lib_Utility::get_web_server();
		$modification = self::get_minimal_server_config();

		return self::write_server_config( $server, $modification );
	}

	/**
	 * Add a modification to the server config file without having to rebuild all iThemes Security modifications.
	 *
	 * By default, this modification is wrapped in comments that allow for removal of the modification when future
	 * updates occur. Thus, this function should only be used if a future update will include the same changes. If the
	 * optional $permanent attribute is set to true, the comment wrapper will not be added to the modification.
	 *
	 * @since 1.15.0
	 *
	 * @param string $modification The modification to add to the server config file.
	 * @param bool   $permanent    Optional. Set to true to prevent iThemes Security from removing the change in the
	 *                             future. Defaults to false.
	 * @return bool|WP_Error Boolean true on success or a WP_Error object otherwise.
	 */
	public static function append_server_config( $modification, $permanent = false ) {
		$server = ITSEC_Lib_Utility::get_web_server();

		if ( true !== $permanent ) {
			$comment_delimiter = self::get_comment_delimiter( $server );
			$modification = self::get_prepared_modification( $modification, $comment_delimiter );
		}

		return self::write_server_config( $server, $modification, false );
	}

	/**
	 * Write the supplied modification to the appropriate server config file.
	 *
	 * @since 1.15.0
	 * @access protected
	 *
	 * @param string $server                       The server type of the server config file.
	 * @param string $modification                 The modification to add to the server config file.
	 * @param bool   $clear_existing_modifications Optional. Whether or not existing modifications should be removed
	 *                                             first. Defaults to true.
	 * @return bool|WP_Error Boolean true on success or a WP_Error object otherwise.
	 */
	protected static function write_server_config( $server, $modification, $clear_existing_modifications = true ) {
		$file_path = self::get_server_config_file_path();

		if ( empty( $file_path ) ) {
			return true;
//			return new WP_Error( 'itsec-lib-config-file-server-config-file-updates-disabled', __( 'Updates to the server config file are disabled via a filter.', 'it-l10n-ithemes-security-pro' ) );
		}

		return self::update( $file_path, $server, $modification, $clear_existing_modifications );
	}

	/**
	 * Get the config to be written to the wp-config.php file.
	 *
	 * The config is retrieved by the itsec_filter_wp_config_modification filter.
	 *
	 * @since 1.15.0
	 *
	 * @return string The wp-config.php config.
	 */
	public static function get_wp_config() {
		$modification = apply_filters( 'itsec_filter_wp_config_modification', '' );
		$comment_delimiter = self::get_comment_delimiter( 'wp-config' );
		$modification = self::get_prepared_modification( $modification, $comment_delimiter );

		return $modification;
	}

	/**
	 * Get the minimal config to be written to the wp-config.php file.
	 *
	 * The config is retrieved by the itsec_filter_minimal_wp_config_modification filter.
	 *
	 * @since 1.15.0
	 *
	 * @return string The wp-config.php config.
	 */
	public static function get_minimal_wp_config() {
		$modification = apply_filters( 'itsec_filter_minimal_wp_config_modification', '' );

		if ( empty( $modification ) ) {
			return '';
		}

		$comment_delimiter = self::get_comment_delimiter( 'wp-config' );
		$modification = "$comment_delimiter " . __( 'iThemes Security preserved the following settings as removing them could prevent the site from functioning correctly.', 'it-l10n-ithemes-security-pro' ) . "\n$modification";
		$modification = self::get_prepared_modification( $modification, $comment_delimiter );

		return $modification;
	}

	/**
	 * Update the wp-config.php file.
	 *
	 * The modification to be written to the file is retrieved by the itsec_filter_wp_config_modification filter.
	 *
	 * @since 1.15.0
	 *
	 * @return bool|WP_Error Boolean true on success or a WP_Error object otherwise.
	 */
	public static function update_wp_config() {
		$modification = self::get_wp_config();

		return self::write_wp_config( $modification );
	}

	/**
	 * Remove all removable settings from the wp-config.php file.
	 *
	 * It is possible that some settings must remain when the plugin is disabled and removed. This function retrieves
	 * those necessary settings via the itsec_filter_minimal_wp_config_modification filter and ensures that they are
	 * written to the server config file.
	 *
	 * @since 1.15.0
	 *
	 * @return bool|WP_Error Boolean true on success or a WP_Error object otherwise.
	 */
	public static function reset_wp_config() {
		$modification = self::get_minimal_wp_config();

		return self::write_wp_config( $modification );
	}

	/**
	 * Add a modification to the wp-config.php file without having to rebuild all iThemes Security modifications.
	 *
	 * By default, this modification is wrapped in comments that allow for removal of the modification when future
	 * updates occur. Thus, this function should only be used if a future update will include the same changes. If the
	 * optional $permanent attribute is set to true, the comment wrapper will not be added to the modification. This
	 * results in iThemes Security being unable to manage the modification in the future and should only be used for
	 * changes that are one-time in nature and should not be undone, such as changing the Content Directory.
	 *
	 * @since 1.15.0
	 *
	 * @param string $modification The modification to add to the wp-config.php file.
	 * @param bool   $permanent    Optional. Set to true to prevent iThemes Security from removing the change in the
	 *                             future. Defaults to false.
	 * @return bool|WP_Error Boolean true on success or a WP_Error object otherwise.
	 */
	public static function append_wp_config( $modification, $permanent = false ) {
		if ( true !== $permanent ) {
			$comment_delimiter = self::get_comment_delimiter( 'wp-config' );
			$modification = self::get_prepared_modification( $modification, $comment_delimiter );
		}

		return self::write_wp_config( $modification, false );
	}

	/**
	 * Remove matched content from the wp-config.php file.
	 *
	 * @since 1.17.0
	 *
	 * @param array|string $patterns An array of regular expression strings or a string for a regular expression to
	 *                               match in the file.
	 * @return int|WP_Error Number of matches removed or a WP_Error object on error.
	 */
	public static function remove_from_wp_config( $patterns ) {
		$file_path = self::get_wp_config_file_path();

		if ( empty( $file_path ) ) {
			return new WP_Error( 'itsec-lib-config-file-wp-config-file-updates-disabled', __( 'Updates to <code>wp-config.php</code> are disabled via a filter.', 'it-l10n-ithemes-security-pro' ) );
		}

		return self::remove( $file_path, $patterns );
	}

	/**
	 * Write the supplied modification to the wp-config.php file.
	 *
	 * @since 1.15.0
	 * @access protected
	 *
	 * @param string $modification                 The modification to add to the wp-config.php file.
	 * @param bool   $clear_existing_modifications Optional. Whether or not existing modifications should be removed
	 *                                             first. Defaults to true.
	 * @return bool|WP_Error Boolean true on success or a WP_Error object otherwise.
	 */
	protected static function write_wp_config( $modification, $clear_existing_modifications = true ) {
		$file_path = self::get_wp_config_file_path();

		if ( empty( $file_path ) ) {
			return new WP_Error( 'itsec-lib-config-file-wp-config-file-updates-disabled', __( 'Updates to <code>wp-config.php</code> are disabled via a filter.', 'it-l10n-ithemes-security-pro' ) );
		}

		return self::update( $file_path, 'wp-config', $modification, $clear_existing_modifications );
	}

	/**
	 * Returns the contents of the file.
	 *
	 * @since 1.15.0
	 * @access protected
	 *
	 * @param string $file Config file to read.
	 * @return string|WP_Error The contents of the file, an empty string if the file does not exist, or a WP_Error object on error.
	 */
	protected static function get_file_contents( $file ) {
		if ( ! ITSEC_Lib_File::exists( $file ) ) {
			return '';
		}

		$contents = ITSEC_Lib_File::read( $file );

		if ( is_wp_error( $contents ) ) {
			return new WP_Error( 'itsec-lib-config-file-cannot-read-file', sprintf( __( 'Unable to read %1$s due to the following error: %2$s', 'it-l10n-ithemes-security-pro' ), $file, $contents->get_error_message() ) );
		}

		return $contents;
	}

	/**
	 * Returns the contents of the file with the iThemes Security modifications removed.
	 *
	 * @since 1.15.0
	 * @access protected
	 *
	 * @param string $file Config file to read.
	 * @param string $type The type of config file. Valid options are apache, nginx, and wp-config.
	 * @return string|WP_Error The contents of the file with modifications removed, an empty string if the file does not exist, or a WP_Error object on error.
	 */
	protected static function get_file_contents_without_modification( $file, $type ) {
		$contents = self::get_file_contents( $file );

		if ( is_wp_error( $contents ) ) {
			return $contents;
		}


		// Contents of just whitespace are treated as empty.
		if ( preg_match( '/^\s+$/', $contents ) ) {
			return '';
		}


		$format_version = 0;

		// Attempt to retrieve config file details from the contents.
		if ( preg_match( '/iThemes\s+Security\s+Config\s+Details:\s+([^\s]+)/', $contents, $match ) ) {
			$details = explode( ':', $match[1] );

			if ( isset( $details[0] ) && ( (string) intval( $details[0] ) === $details[0] ) ) {
				$format_version = intval( $details[0] );
			}
		}


		$placeholder = self::get_placeholder();

		// Ensure that the generated placeholder can be uniquely identified in the contents.
		while ( false !== strpos( $contents, $placeholder ) ) {
			$placeholder = self::get_placeholder();
		}


		// Create a set of regex patterns to identify existing iThemes Security modifications.
		$comment_delimiter = self::get_comment_delimiter( $type );
		$quoted_comment_delimiter = preg_quote( $comment_delimiter, '/' );
		$line_ending = self::get_line_ending( $contents );

		$patterns = array(
			array(
				'begin' => "$quoted_comment_delimiter+\s*BEGIN\s+iThemes\s+Security",
				'end'   => "$quoted_comment_delimiter+\s*END\s+iThemes\s+Security",
			),
			array(
				'begin' => "$quoted_comment_delimiter+\s*BEGIN\s+Better\s+WP\s+Security",
				'end'   => "$quoted_comment_delimiter+\s*END\s+Better\s+WP\s+Security",
			),
		);

		// Remove matched content.
		foreach ( $patterns as $pattern ) {
			$contents = preg_replace( "/\s*{$pattern['begin']}.+?{$pattern['end']}[^\r\n]*\s*/is", "$line_ending$placeholder", $contents );
		}


		if ( 'wp-config' === $type ) {
			// Special treatment for wp-config.php config data.

			// If the format is old or could not be detected, assume that cleanup of old modifications is required.
			if ( version_compare( $format_version, self::FORMAT_VERSION, '<' ) ) {
				// This code is clumsy, but it's the only way to remove the modifications given that the start and end
				// were not indicated in older versions.

				$contents = preg_replace( '/(<\?(?:php)?)?.+BWPS_FILECHECK.+/', "$1$line_ending$placeholder", $contents );
				$contents = preg_replace( '/(<\?(?:php)?)?.+BWPS_AWAY_MODE.+/', "$1$line_ending$placeholder", $contents );

				if ( preg_match( '|//\s*The entry below were created by iThemes Security to disable the file editor|', $contents ) ) {
					$contents = preg_replace( '%(<\?(?:php)?)?.*//\s*The entry below were created by iThemes Security to disable the file editor.*(?:\r\n|\r|\n)%', "$1$line_ending$placeholder", $contents );
					$contents = preg_replace( '/(<\?(?:php)?)?.+DISALLOW_FILE_EDIT.+(?:\r\r\n|\r\n|\r|\n)/', "$1$line_ending$placeholder", $contents );
				}

				if ( preg_match( '|//\s*The entries below were created by iThemes Security to enforce SSL|', $contents ) ) {
					$contents = preg_replace( '%(<\?(?:php)?)?.*//\s*The entries below were created by iThemes Security to enforce SSL.*(?:\r\n|\r|\n)%', "$1$line_ending$placeholder", $contents );
					$contents = preg_replace( '/(<\?(?:php)?)?.+FORCE_SSL_LOGIN.+(?:\r\r\n|\r\n|\r|\n)/', "$1$line_ending$placeholder", $contents );
					$contents = preg_replace( '/(<\?(?:php)?)?.+FORCE_SSL_ADMIN.+(?:\r\r\n|\r\n|\r|\n)/', "$1$line_ending$placeholder", $contents );
				}
			}
		}


		// Remove adjacent placeholders.
		$contents = preg_replace( "/$placeholder(?:\s*$placeholder)+/", $placeholder, $contents );

		// Remove whitespace from around the placeholders.
		$contents = preg_replace( "/\s*$placeholder\s*/", $placeholder, $contents );

		// Placeholders at the beginning or end of the contents do not need to have newlines added.
		$contents = preg_replace( "/^$placeholder|$placeholder$/", '', $contents );

		// Remaining placeholders are replaced with two newlines to leave a gap between sections of remaining contents.
		$contents = preg_replace( "/$placeholder/", "$line_ending$line_ending", $contents );


		// Fix potentially damaged Windows-style newlines for W3 Total Cache modifications
		$translated_w3tc_comment = __( 'Added by W3 Total Cache', 'w3-total-cache' );

		if ( preg_match_all( '/[^\r\n]+(?:W3 Total Cache|' . preg_quote( $translated_w3tc_comment, '/' ) . ').*?(?:\r\n|\r|\n)/', $contents, $matches ) ) {
			foreach ( $matches[0] as $match ) {
				$new_line = rtrim( $match ) . "\r\n";

				if ( $new_line !== $match ) {
					$contents = str_replace( $match, $new_line, $contents );
				}
			}
		}


		return $contents;
	}

	/**
	 * Update modifications in the supplied configuration file.
	 *
	 * If a blank $contents argument is supplied, all modifications will be removed.
	 *
	 * @since 1.15.0
	 * @access protected
	 *
	 * @param string $file                         Config file to update.
	 * @param string $type                         The type of config file. Valid options are apache, nginx, and
	 *                                             wp-config.
	 * @param string $modification                 The contents to add or update the file with. If an empty string is
	 *                                             supplied, all iThemes Security modifications will be removed.
	 * @param bool   $clear_existing_modifications Optional. Whether or not existing modifications should be removed
	 *                                             first. Defaults to true.
	 * @return bool|WP_Error Boolean true on success or a WP_Error object otherwise.
	 */
	protected static function update( $file, $type, $modification, $clear_existing_modifications = true ) {
		// Check to make sure that the settings give permission to write files.
		if ( ! ITSEC_Files::can_write_to_files() ) {
			$display_file = str_replace( '\\', '/', $file );
			$abspath = str_replace( '\\', '/', ABSPATH );
			$display_file = preg_replace( '/^' . preg_quote( $abspath, '/' ) . '/', '', $display_file );
			$display_file = ltrim( $display_file, '/' );

			return new WP_Error( 'itsec-config-file-update-writes-files-disabled', sprintf( __( 'The "Write to Files" setting is disabled. Manual configuration for the <code>%s</code> file can be found on the Security > Settings page in the Advanced section.', 'it-l10n-ithemes-security-pro' ), $display_file ) );
		}


		if ( $clear_existing_modifications ) {
			$contents = self::get_file_contents_without_modification( $file, $type );
		} else {
			$contents = self::get_file_contents( $file );
		}

		if ( is_wp_error( $contents ) ) {
			return $contents;
		}


		$modification = ltrim( $modification, "\x0B\r\n\0" );
		$modification = rtrim( $modification, " \t\x0B\r\n\0" );

		if ( empty( $modification ) ) {
			// If there isn't a new modification, write the content without any modification and return the result.

			if ( empty( $contents ) ) {
				$contents = PHP_EOL;
			}

			return ITSEC_Lib_File::write( $file, $contents );
		}


		$placeholder = self::get_placeholder();

		// Ensure that the generated placeholder can be uniquely identified in the contents.
		while ( false !== strpos( $contents, $placeholder ) ) {
			$placeholder = self::get_placeholder();
		}


		if ( 'wp-config' === $type ) {
			// Put the placeholder at the beginning of the file, after the <?php tag.
			$contents = preg_replace( '/^(.*?<\?(?:php)?)\s*(?:\r\r\n|\r\n|\r|\n)/', "\${1}$placeholder", $contents, 1 );

			if ( false === strpos( $contents, $placeholder ) ) {
				$contents = preg_replace( '/^(.*?<\?(?:php)?)\s*(.+(?:\r\r\n|\r\n|\r|\n))/', "\${1}$placeholder$2", $contents, 1 );
			}

			if ( false === strpos( $contents, $placeholder ) ) {
				$contents = "<?php$placeholder?" . ">$contents";
			}
		} else {
			// Apache and nginx server config files.
			$contents = "$placeholder$contents";
		}


		// Pad away from existing sections when adding iThemes Security modifications.
		$line_ending = self::get_line_ending( $contents );

		while ( ! preg_match( "/(?:^|(?:(?<!\r)\n|\r(?!\n)|(?<!\r)\r\n|\r\r\n)(?:(?<!\r)\n|\r(?!\n)|(?<!\r)\r\n|\r\r\n))$placeholder/", $contents ) ) {
			$contents = preg_replace( "/$placeholder/", "$line_ending$placeholder", $contents );
		}
		while ( ! preg_match( "/$placeholder(?:$|(?:(?<!\r)\n|\r(?!\n)|(?<!\r)\r\n|\r\r\n)(?:(?<!\r)\n|\r(?!\n)|(?<!\r)\r\n|\r\r\n))/", $contents ) ) {
			$contents = preg_replace( "/$placeholder/", "$placeholder$line_ending", $contents );
		}


		// Ensure that the file ends in a newline if the placeholder is at the end.
		$contents = preg_replace( "/$placeholder$/", "$placeholder$line_ending", $contents );

		if ( ! empty( $modification ) ) {
			// Normalize line endings of the modification to match the file's line endings.
			$modification = ITSEC_Lib_Utility::normalize_line_endings( $modification, $line_ending );

			// Exchange the placeholder with the modification.
			$contents = preg_replace( "/$placeholder/", $modification, $contents );
		}

		// Write the new contents to the file and return the results.
		return ITSEC_Lib_File::write( $file, $contents );
	}

	/**
	 * Add the identifying comments to the modification to identify them as coming from iThemes Security.
	 *
	 * @since 1.15.0
	 * @access protected
	 *
	 * @param string $file         Config file to update.
	 * @param string $type         The type of config file. Valid options are apache, nginx, and wp-config.
	 * @param string $modification The contents to add or update the file with. If an empty string is supplied, all
	 *                             iThemes Security modifications will be removed.
	 * @return bool|WP_Error Boolean true on success or a WP_Error object otherwise.
	 */
	protected static function get_prepared_modification( $modification, $comment_delimiter ) {
		// Trim off unwanted whitespace around modification.
		$modification = ltrim( $modification, "\x0B\r\n\0" );
		$modification = rtrim( $modification, " \t\x0B\r\n\0" );

		if ( empty( $modification ) ) {
			// Do not wrap an empty modification.
			return '';
		}


		// Update the modification to have the beginning and ending comments in order to identify the section as being
		// added by iThemes Security.
		$supplied_modification = $modification;
		$modification  = "$comment_delimiter BEGIN iThemes Security - " . __( 'Do not modify or remove this line', 'it-l10n-ithemes-security-pro' ) . "\n";
		$modification .= "$comment_delimiter iThemes Security Config Details: " . self::FORMAT_VERSION . "\n";
		$modification .= "$supplied_modification\n";
		$modification .= "$comment_delimiter END iThemes Security - " . __( 'Do not modify or remove this line', 'it-l10n-ithemes-security-pro' );

		return $modification;
	}

	/**
	 * Remove matched content from the supplied file.
	 *
	 * @since 1.17.0
	 *
	 * @param string       $file     Config file to update.
	 * @param array|string $patterns An array of regular expression strings or a string for a regular expression to
	 *                               match in the file.
	 * @return int|WP_Error Number of matches removed or a WP_Error object on error.
	 */
	protected static function remove( $file, $patterns ) {
		$replacements = array();

		foreach ( (array) $patterns as $pattern ) {
			$replacements[$pattern] = '';
		}


		return self::replace( $file, $replacements );
	}

	/**
	 * Replace matched content in a file.
	 *
	 * @since 1.17.0
	 *
	 * @param string       $file         Config file to update.
	 * @param array|string $replacements An array of regular expression string indexes with replacement string values.
	 * @return int|WP_Error Number of replacements made or a WP_Error object on error.
	 */
	protected static function replace( $file, $replacements ) {
		$contents = self::get_file_contents( $file );

		if ( is_wp_error( $contents ) ) {
			return $contents;
		}


		$total = 0;

		foreach ( (array) $replacements as $pattern => $replacement ) {
			$contents = preg_replace( $pattern, $replacement, $contents, -1, $count );
			$total += $count;
		}

		// Write the new contents to the file and return the results.
		return ITSEC_Lib_File::write( $file, $contents );
	}

	/**
	 * Get the appropriate comment delimiter for a specific type of config.
	 *
	 * @since 1.15.0
	 * @access protected
	 *
	 * @param string $type The type of config that the comment will be used for.
	 * @return string The comment delimiter.
	 */
	protected static function get_comment_delimiter( $type ) {
		if ( 'wp-config' === $type ) {
			$delimiter = '//';
		} else {
			$delimiter = '#';
		}

		$delimiter = apply_filters( 'itsec_filter_server_config_file_comment_delimiter', $delimiter, $type );

		return $delimiter;
	}

	/**
	 * Internal class function to get the primary line ending found in the contents.
	 *
	 * @since 1.15.0
	 * @access protected
	 *
	 * @param string $contents The contents to count line endings in.
	 * @return string The line ending.
	 */
	protected static function get_line_ending( $contents ) {
		if ( empty( $contents ) ) {
			return PHP_EOL;
		}

		$count["\n"]     = preg_match_all( "/(?<!\r)\n/", $contents, $matches );
		$count["\r"]     = preg_match_all( "/\r(?!\n)/", $contents, $matches );
		$count["\r\n"]   = preg_match_all( "/(?<!\r)\r\n/", $contents, $matches );
		$count["\r\r\n"] = preg_match_all( "/\r\r\n/", $contents, $matches );

		if ( 0 == array_sum( $count ) ) {
			return PHP_EOL;
		}

		$maxes = array_keys( $count, max( $count ) );

		if ( in_array( "\r\r\n", $maxes ) ) {
			return "\r\r\n";
		}

		return $maxes[0];
	}

	/**
	 * Internal class function to get a random string to use as a placeholder.
	 *
	 * @since 1.15.0
	 * @access protected
	 *
	 * @return string Returns a random string of characters.
	 */
	protected static function get_placeholder() {
		$characters = str_split( 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789' );

		$string = '';

		for ( $x = 0; $x < 100; $x++ ) {
			$string .= array_rand( $characters );
		}

		return $string;
	}

	/**
	 * Get full file path to the server's config file for the site.
	 *
	 * Customize the returned value with the itsec_filter_server_config_file_path filter. Filter the value to a blank
	 * string ("") in order to disable modifications to this file.
	 *
	 * @since 1.15.0
	 *
	 * @return string Full path to the server config file or a blank string if modifications for the file are disabled.
	 */
	public static function get_server_config_file_path() {
		$server = ITSEC_Lib_Utility::get_web_server();


		if ( 'nginx' === $server ) {
			$file = ITSEC_Modules::get_setting( 'global', 'nginx_file' );
			$file_path = apply_filters( 'itsec_filter_server_config_file_path', $file, basename( $file ) );

			if ( ! empty( $file_path ) ) {
				return $file_path;
			}
		}


		$file = self::get_default_server_config_file_name();

		if ( empty( $file ) ) {
			return '';
		}


		require_once( ABSPATH . 'wp-admin/includes/file.php' );

		$home_path = get_home_path();
		$file_path = $home_path . $file;
		$file_path = apply_filters( 'itsec_filter_server_config_file_path', $file_path, $file );

		if ( $file_path === $home_path ) {
			return '';
		}

		return $file_path;
	}

	/**
	 * Get the default name for server config files based upon the web server.
	 *
	 * Customize the returned value with the itsec_filter_default_server_config_file_name filter. This filter can be
	 * used to change the name of the config file used for this server, add support for additional server types (Apache
	 * and nginx are supported by default), or to disable modifications for the active server type by returning a blank
	 * string ("").
	 *
	 * @since 1.15.0
	 * @access protected
	 *
	 * @return string|bool File name of the config file used for the server, a blank string if modifications for the
	 *                     server config file are disabled, or a boolean false if the server is not recognized.
	 */
	protected static function get_default_server_config_file_name() {
		$server = ITSEC_Lib_Utility::get_web_server();

		$defaults = array(
			'apache'    => '.htaccess',
			'litespeed' => '.htaccess',
			'nginx'     => 'nginx.conf',
		);

		if ( isset( $defaults[$server] ) ) {
			$name = $defaults[$server];
		} else {
			$name = false;
		}

		return apply_filters( 'itsec_filter_default_server_config_file_name', $name, $server );
	}

	/**
	 * Get full file path to the site's wp-config.php file.
	 *
	 * Customize the returned value with the itsec_filter_wp_config_file_path filter. Filter the value to a blank string
	 * ("") in order to disable modifications to this file.
	 *
	 * @since 1.15.0
	 *
	 * @return string Full path to the wp-config.php file or a blank string if modifications for the file are disabled.
	 */
	public static function get_wp_config_file_path() {
		if ( file_exists( ABSPATH . 'wp-config.php') ) {
			$path = ABSPATH . 'wp-config.php';
		} else if ( file_exists( dirname( ABSPATH ) . '/wp-config.php' ) && ! file_exists( dirname( ABSPATH ) . '/wp-settings.php' ) ) {
			$path = dirname( ABSPATH ) . '/wp-config.php';
		} else {
			$path = ABSPATH . 'wp-config.php';
		}

		return apply_filters( 'itsec_filter_wp_config_file_path', $path );
	}
}


require_once( dirname( __FILE__ ) . '/class-itsec-lib-utility.php' );
require_once( dirname( __FILE__ ) . '/class-itsec-lib-file.php' );
