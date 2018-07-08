<?php
/**
 * iThemes Security directory library.
 *
 * Contains the ITSEC_Lib_Directory class.
 *
 * @package iThemes_Security
 */

if ( ! class_exists( 'ITSEC_Lib_Directory' ) ) {
	/**
	 * iThemes Security Directory Library class.
	 *
	 * Utility class for managing directories.
	 *
	 * @package iThemes_Security
	 * @since 1.15.0
	 */
	class ITSEC_Lib_Directory {
		/**
		 * Get a listing of files and subdirectories contained in the supplied directory.
		 *
		 * @since 1.15.0
		 *
		 * @return array|WP_Error An array of the files and directories on success or a WP_Error object if an error occurs.
		 */
		public static function read( $dir ) {
			$callable = array();

			if ( ITSEC_Lib_Utility::is_callable_function( 'opendir' ) && ITSEC_Lib_Utility::is_callable_function( 'readdir' ) && ITSEC_Lib_Utility::is_callable_function( 'closedir' ) ) {
				$callable[] = 'opendir';
			}
			if ( ITSEC_Lib_Utility::is_callable_function( 'glob' ) ) {
				$callable[] = 'glob';
			}

			if ( empty( $callable ) ) {
				return new WP_Error( 'itsec-lib-directory-read-no-callable-functions', sprintf( __( '%s could not be read. Both the opendir/readdir/closedir and glob functions are disabled on the server.', 'it-l10n-ithemes-security-pro' ), $dir ) );
			}


			if ( in_array( 'opendir', $callable ) ) {
				if ( false !== ( $dh = opendir( $dir ) ) ) {
					$files = array();

					while ( false !== ( $file = readdir( $dh ) ) ) {
						if ( in_array( basename( $file ), array( '.', '..' ) ) ) {
							continue;
						}

						$files[] = "$dir/$file";
					}

					closedir( $dh );

					sort( $files );

					return $files;
				}
			}

			if ( ITSEC_Lib_Utility::is_callable_function( 'glob' ) ) {
				$visible = glob( "$dir/*" );
				$hidden = glob( "$dir/.*" );

				if ( false !== $visible || false !== $hidden ) {
					if ( false === $visible ) {
						$visible = array();
					}
					if ( false === $hidden ) {
						$hidden = array();
					}

					$files = array_merge( $visible, $hidden );

					foreach ( $files as $index => $file ) {
						if ( in_array( basename( $file ), array( '.', '..' ) ) ) {
							unset( $files[$index] );
						}
					}

					sort( $files );

					return $files;
				}
			}


			return new WP_Error( 'itsec-lib-directory-read-cannot-read', sprintf( __( '%s could not be read due to an unknown error.', 'it-l10n-ithemes-security-pro' ), $dir ) );
		}

		/**
		 * Determine if the supplied directory exists.
		 *
		 * @since 1.15.0
		 *
		 * @param string $dir Full path to the directory to test.
		 * @return bool|WP_Error Boolean true if it exists, false if it does not
		 */
		public static function is_dir( $dir ) {
			$dir = rtrim( $dir, '/' );

			if ( empty( $dir ) ) {
				return true;
			}

			@clearstatcache( true, $dir );

			return @is_dir( $dir );
		}

		/**
		 * Create the supplied directory.
		 *
		 * @since 1.15.0
		 *
		 * @param string $dir Full path to the directory to create.
		 * @return bool|WP_Error Boolean true if it is created successfully, WP_Error object otherwise.
		 */
		public static function create( $dir ) {
			$dir = rtrim( $dir, '/' );

			if ( self::is_dir( $dir ) ) {
				self::add_file_listing_protection( $dir );

				return true;
			}

			if ( ITSEC_Lib_File::exists( $dir ) ) {
				return new WP_Error( 'itsec-lib-directory-create-file-exists', sprintf( __( 'The directory %s could not be created as a file with that name already exists.', 'it-l10n-ithemes-security-pro' ), $dir ) );
			}

			if ( ! ITSEC_Lib_Utility::is_callable_function( 'mkdir' ) ) {
				return new WP_Error( 'itsec-lib-directory-create-mkdir-is-disabled', sprintf( __( 'The directory %s could not be created as the mkdir() function is disabled. This is a system configuration issue.', 'it-l10n-ithemes-security-pro' ), $dir ) );
			}


			$parent = dirname( $dir );

			while ( ! empty( $parent ) && ( ! self::is_dir( $parent ) ) ) {
				$parent = dirname( $parent );
			}

			if ( empty( $parent ) ) {
				return new WP_Error( 'itsec-lib-directory-create-unable-to-find-parent', sprintf( __( 'The directory %s could not be created as an existing parent directory could not be found.', 'it-l10n-ithemes-security-pro' ), $dir ) );
			}


			$perms = self::get_permissions( $parent );

			if ( ! is_int( $perms ) ) {
				$perms = 0755;
			}

			$cached_umask = umask( 0 );
			$result = @mkdir( $dir, $perms, true );
			umask( $cached_umask );

			if ( $result ) {
				self::add_file_listing_protection( $dir );

				return true;
			}

			return new WP_Error( 'itsec-lib-directory-create-failed', sprintf( __( 'The directory %s could not be created due to an unknown error. This could be due to a permissions issue.', 'it-l10n-ithemes-security-pro' ), $dir ) );
		}

		/**
		 * Recursively remove the supplied directory.
		 *
		 * @since 1.15.0
		 *
		 * @return bool|WP_Error Boolean true on success or a WP_Error object if an error occurs.
		 */
		public static function remove( $dir ) {
			if ( ! ITSEC_Lib_File::exists( $dir ) ) {
				return true;
			}


			if ( ! ITSEC_Lib_Utility::is_callable_function( 'rmdir' ) ) {
				return new WP_Error( 'itsec-lib-directory-remove-rmdir-is-disabled', sprintf( __( 'The directory %s could not be removed as the rmdir() function is disabled. This is a system configuration issue.', 'it-l10n-ithemes-security-pro' ), $dir ) );
			}


			$files = self::read( $dir );

			if ( is_wp_error( $files ) ) {
				return new WP_Error( 'itsec-lib-directory-remove-read-error', sprintf( __( 'Unable to remove %1$s due to the following error: %2$s', 'it-l10n-ithemes-security-pro' ), $dir, $files->get_error_message() ) );
			}

			foreach ( $files as $file ) {
				if ( ITSEC_Lib_File::is_file( $file ) ) {
					ITSEC_Lib_File::remove( $file );
				} else if ( self::is_dir( $file ) ) {
					self::remove( $file );
				}
			}

			$result = rmdir( $dir );
			@clearstatcache( true, $dir );

			if ( $result ) {
				return true;
			}

			return new WP_Error( 'itsec-lib-directory-remove-unknown-error', sprintf( __( 'Unable to remove %s due to an unknown error.', 'it-l10n-ithemes-security-pro' ), $dir ) );
		}

		/**
		 * Test to see if the requested directory is writable.
		 *
		 * @since 2.3.0
		 *
		 * @param string $dir Full path to the directory to test.
		 * @return bool True if the directory is writable, false otherwise.
		 */
		public static function is_writable( $dir ) {
			$dir = rtrim( $dir, '/' );

			if ( ! self::is_dir( $dir ) ) {
				return false;
			}

			$test_count = 0;

			do {
				$test_file = 'itsec-test-file-' . wp_generate_password( 10, false ) . '.txt';
			} while ( $test_count++ < 10 && ITSEC_Lib_File::exists( "$dir/$test_file" ) );

			if ( ITSEC_Lib_File::exists( "$dir/$test_file" ) ) {
				return false;
			}

			$result = ITSEC_Lib_File::write( "$dir/$test_file", __( 'This is a test file generated by iThemes Security. It can be removed.', 'it-l10n-ithemes-security-pro' ) );
			ITSEC_Lib_File::remove( "$dir/$test_file" );

			if ( true === $result ) {
				return $result;
			}

			return false;
		}

		/**
		 * Add an index.php file to the directory to prevent file listing.
		 *
		 * @since 2.3.0
		 *
		 * @param string $dir Full path to the directory to protect.
		 * @return bool|WP_Error Boolean true if the file could be created or already exists, WP_Error object otherwise.
		 */
		public static function add_file_listing_protection( $dir ) {
			$dir = rtrim( $dir, '/' );

			if ( ! self::is_dir( $dir ) ) {
				return new WP_Error( 'itsec-lib-directory-add-file-listing-protection-directory-does-not-exist', sprintf( __( 'The directory %s could not be protected from file listing as the directory does not exist.', 'it-l10n-ithemes-security-pro' ), $dir ) );
			}

			if ( ITSEC_Lib_File::exists( "$dir/index.php" ) ) {
				return true;
			}

			return ITSEC_Lib_File::write( "$dir/index.php", "<?php\n// Silence is golden." );
		}

		/**
		 * Get file permissions from the requested directory.
		 *
		 * If the directory permissions cannot be read, a default value of 0644 will be returned.
		 *
		 * @since 1.15.0
		 *
		 * @param string $dir Full path to the file to retrieve permissions from.
		 * @return int|WP_Error The permissions as an int or a WP_Error object if an error occurs.
		 */
		public static function get_permissions( $dir ) {
			if ( ! self::is_dir( $dir ) ) {
				return new WP_Error( 'itsec-lib-dir-get-permissions-missing-dir', sprintf( __( 'Permissions for the directory %s could not be read as the directory could not be found.', 'it-l10n-ithemes-security-pro' ), $dir ) );
			}

			if ( ! ITSEC_Lib_Utility::is_callable_function( 'fileperms' ) ) {
				return new WP_Error( 'itsec-lib-directory-get-permissions-fileperms-is-disabled', sprintf( __( 'Permissions for the directory %s could not be read as the fileperms() function is disabled. This is a system configuration issue.', 'it-l10n-ithemes-security-pro' ), $dir ) );
			}


			$dir = rtrim( $dir, '/' );
			@clearstatcache( true, $dir );

			return fileperms( $dir ) & 0777;
		}

		/**
		 * Get default file permissions to use for new files.
		 *
		 * @since 1.15.0
		 * @uses FS_CHMOD_DIR Define that sets default file permissions.
		 *
		 * @return int|WP_Error The default permissions as an int or a WP_Error object if an error occurs.
		 */
		public static function get_default_permissions() {
			if ( defined( 'FS_CHMOD_DIR' ) ) {
				return FS_CHMOD_DIR;
			}

			$perms = self::get_permissions( ABSPATH );

			if ( ! is_wp_error( $perms ) ) {
				return $perms;
			}

			return 0755;
		}

		/**
		 * Change directory permissions.
		 *
		 * @since 1.15.0
		 *
		 * @param string $dir   Full path to the directory to change permissions for.
		 * @param int    $perms New permissions to set.
		 * @return bool|WP_Error Boolean true if successful, false if not successful, or WP_Error if the chmod() function
		 *                       is unavailable.
		 */
		public static function chmod( $dir, $perms ) {
			return ITSEC_Lib_File::chmod( $dir, $perms );
		}
	}


	require_once( dirname( __FILE__ ) . '/class-itsec-lib-utility.php' );
	require_once( dirname( __FILE__ ) . '/class-itsec-lib-file.php' );
}
