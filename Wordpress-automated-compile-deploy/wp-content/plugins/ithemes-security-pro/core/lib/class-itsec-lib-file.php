<?php
/**
 * iThemes Security file library.
 *
 * Contains the ITSEC_Lib_File class.
 *
 * @package iThemes_Security
 */

/**
 * iThemes Security File Library class.
 *
 * Utility class for managing files.
 *
 * @package iThemes_Security
 * @since 1.15.0
 */
class ITSEC_Lib_File {
	/**
	 * Read requested file and return the contents.
	 *
	 * @since 1.15.0
	 *
	 * @param string $file Full path to config file to update.
	 * @return string|WP_Error String of the file contents or a WP_Error object otherwise.
	 */
	public static function read( $file ) {
		if ( ! self::is_file( $file ) ) {
			return new WP_Error( 'itsec-lib-file-read-non-file', sprintf( __( '%s could not be read. It does not appear to be a file.', 'it-l10n-ithemes-security-pro' ), $file ) );
		}
		
		
		$callable = array();
		
		if ( ITSEC_Lib_Utility::is_callable_function( 'file_get_contents' ) ) {
			$callable[] = 'file_get_contents';
		}
		if ( ITSEC_Lib_Utility::is_callable_function( 'fopen' ) && ITSEC_Lib_Utility::is_callable_function( 'feof' ) && ITSEC_Lib_Utility::is_callable_function( 'fread' ) && ITSEC_Lib_Utility::is_callable_function( 'flock' ) ) {
			$callable[] = 'fopen';
		}
		
		if ( empty( $callable ) ) {
			return new WP_Error( 'itsec-lib-file-read-no-callable-functions', sprintf( __( '%s could not be read. Both the fopen/feof/fread/flock and file_get_contents functions are disabled on the server.', 'it-l10n-ithemes-security-pro' ), $file ) );
		}
		
		
		$contents = false;
		
		// Different permissions to try in case the starting set of permissions are prohibiting read.
		$trial_perms = array(
			false,
			0644,
			0664,
			0666,
		);
		
		
		foreach ( $trial_perms as $perms ) {
			if ( false !== $perms ) {
				if ( ! isset( $original_file_perms ) ) {
					$original_file_perms = self::get_permissions( $file );
				}
				
				self::chmod( $file, $perms );
			}
			
			
			
			
			if ( in_array( 'fopen', $callable ) ) {
				if ( false !== ( $fh = fopen( $file, 'rb' ) ) ) {
					flock( $fh, LOCK_SH );
					
					$contents = '';
					
					while ( ! feof( $fh ) ) {
						$contents .= fread( $fh, 1024 );
					}
					
					flock( $fh, LOCK_UN );
					fclose( $fh );
				}
			}
			
			if ( ( false === $contents ) && in_array( 'file_get_contents', $callable ) ) {
				$contents = file_get_contents( $file );
			}
			
			
			if ( false !== $contents ) {
				if ( isset( $original_file_perms ) && is_int( $original_file_perms ) ) {
					// Reset the original file permissions if they were modified.
					self::chmod( $file, $original_file_perms );
				}
				
				return $contents;
			}
		}
		
		
		return new WP_Error( 'itsec-lib-file-read-cannot-read', sprintf( __( '%s could not be read due to an unknown error.', 'it-l10n-ithemes-security-pro' ), $file ) );
	}
	
	/**
	 * Update or append the requested file with the supplied contents.
	 *
	 * @since 1.15.0
	 *
	 * @param string $file     Full path to config file to update.
	 * @param string $contents Contents to write to the file.
	 * @param bool   $append   Optional. Set to true to append contents to the file. Defaults to false.
	 * @return bool|WP_Error Boolean true on success, WP_Error object otherwise.
	 */
	public static function write( $file, $contents, $append = false ) {
		$callable = array();
		
		if ( ITSEC_Lib_Utility::is_callable_function( 'fopen' ) && ITSEC_Lib_Utility::is_callable_function( 'fwrite' ) && ITSEC_Lib_Utility::is_callable_function( 'flock' ) ) {
			$callable[] = 'fopen';
		}
		if ( ITSEC_Lib_Utility::is_callable_function( 'file_put_contents' ) ) {
			$callable[] = 'file_put_contents';
		}
		
		if ( empty( $callable ) ) {
			return new WP_Error( 'itsec-lib-file-write-no-callable-functions', sprintf( __( '%s could not be written. Both the fopen/fwrite/flock and file_put_contents functions are disabled on the server. This is a server configuration issue that must be resolved before iThemes Security can write files.', 'it-l10n-ithemes-security-pro' ), $file ) );
		}
		
		
		if ( ITSEC_Lib_Directory::is_dir( $file ) ) {
			return new WP_Error( 'itsec-lib-file-write-path-exists-as-directory', sprintf( __( '%s could not be written as a file. The requested path already exists as a directory. The directory must be removed or a new file name must be chosen before the file can be written.', 'it-l10n-ithemes-security-pro' ), $file ) );
		}
		
		if ( ! ITSEC_Lib_Directory::is_dir( dirname( $file ) ) ) {
			$result = ITSEC_Lib_Directory::create( dirname( $file ) );
			
			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}
		
		
		$file_existed = self::is_file( $file );
		$success = false;
		
		// Different permissions to try in case the starting set of permissions are prohibiting write.
		$trial_perms = array(
			false,
			0644,
			0664,
			0666,
		);
		
		
		foreach ( $trial_perms as $perms ) {
			if ( false !== $perms ) {
				if ( ! isset( $original_file_perms ) ) {
					$original_file_perms = self::get_permissions( $file );
				}
				
				self::chmod( $file, $perms );
			}
			
			if ( in_array( 'fopen', $callable ) ) {
				if ( $append ) {
					$mode = 'ab';
				} else {
					$mode = 'wb';
				}
				
				if ( false !== ( $fh = @fopen( $file, $mode ) ) ) {
					flock( $fh, LOCK_EX );
					
					mbstring_binary_safe_encoding();
					
					$data_length = strlen( $contents );
					$bytes_written = @fwrite( $fh, $contents );
					
					reset_mbstring_encoding();
					
					@flock( $fh, LOCK_UN );
					@fclose( $fh );
					
					if ( $data_length === $bytes_written ) {
						$success = true;
					}
				}
			}
			
			if ( ! $success && in_array( 'file_put_contents', $callable ) ) {
				if ( $append ) {
					$flags = FILE_APPEND;
				} else {
					$flags = 0;
				}
				
				mbstring_binary_safe_encoding();
				
				$data_length = strlen( $contents );
				$bytes_written = @file_put_contents( $file, $contents, $flags );
				
				reset_mbstring_encoding();
				
				if ( $data_length === $bytes_written ) {
					$success = true;
				}
			}
			
			if ( $success ) {
				if ( ! $file_existed ) {
					// Set default file permissions for the new file.
					self::chmod( $file, self::get_default_permissions() );
				} else if ( isset( $original_file_perms ) && ! is_wp_error( $original_file_perms ) ) {
					// Reset the original file permissions if they were modified.
					self::chmod( $file, $original_file_perms );
				}
				
				return true;
			}
			
			if ( ! $file_existed ) {
				// If the file is new, there is no point attempting different permissions.
				break;
			}
		}
		
		
		return new WP_Error( 'itsec-lib-file-write-file-put-contents-failed', sprintf( __( '%s could not be written. This could be due to a permissions issue. Ensure that PHP runs as a user that has permission to write to this location.', 'it-l10n-ithemes-security-pro' ), $file ) );
	}
	
	/**
	 * Create or append the requested file with the supplied contents.
	 *
	 * @since 1.15.0
	 *
	 * @param string $file     Full path to config file to update.
	 * @param string $contents Contents to append to the file.
	 * @return bool|WP_Error Boolean true on success, WP_Error object otherwise.
	 */
	public static function append( $file, $contents ) {
		return self::write( $file, $contents, true );
	}
	
	/**
	 * Remove the supplied file.
	 *
	 * @since 1.15.0
	 *
	 * @return bool|WP_Error Boolean true on success or a WP_Error object if an error occurs.
	 */
	public static function remove( $file ) {
		if ( ! self::exists( $file ) ) {
			return true;
		}
		
		if ( ! ITSEC_Lib_Utility::is_callable_function( 'unlink' ) ) {
			return new WP_Error( 'itsec-lib-file-remove-unlink-is-disabled', sprintf( __( 'The file %s could not be removed as the unlink() function is disabled. This is a system configuration issue.', 'it-l10n-ithemes-security-pro' ), $file ) );
		}
		
		
		$result = @unlink( $file );
		@clearstatcache( true, $file );
		
		if ( $result ) {
			return true;
		}
		
		return new WP_Error( 'itsec-lib-file-remove-unknown-error', sprintf( __( 'Unable to remove %s due to an unknown error.', 'it-l10n-ithemes-security-pro' ), $file ) );
	}
	
	/**
	 * Change file permissions.
	 *
	 * @since 1.15.0
	 *
	 * @param string $file  Full path to the file to change permissions for.
	 * @param int    $perms New permissions to set.
	 * @return bool|WP_Error Boolean true if successful, false if not successful, or WP_Error if the chmod() function
	 *                       is unavailable.
	 */
	public static function chmod( $file, $perms ) {
		if ( ! is_int( $perms ) ) {
			return new WP_Error( 'itsec-lib-file-chmod-invalid-perms', sprintf( __( 'The file %1$s could not have its permissions updated as non-integer permissions were sent: (%2$s) %3$s', 'it-l10n-ithemes-security-pro' ), $file, gettype( $perms ), $perms ) );
		}
		
		if ( ! ITSEC_Lib_Utility::is_callable_function( 'chmod' ) ) {
			return new WP_Error( 'itsec-lib-file-chmod-chmod-is-disabled', sprintf( __( 'The file %s could not have its permissions updated as the chmod() function is disabled. This is a system configuration issue.', 'it-l10n-ithemes-security-pro' ), $file ) );
		}
		
		return @chmod( $file, $perms );
	}
	
	/**
	 * Determine if a file or directory exists.
	 *
	 * @since 1.15.0
	 *
	 * @param string $file Full path to test for existence.
	 * @return bool|WP_Error Boolean true if it exists, false if it does not.
	 */
	public static function exists( $file ) {
		@clearstatcache( true, $file );
		
		return @file_exists( $file );
	}
	
	/**
	 * Determine if a file exists.
	 *
	 * @since 1.15.0
	 *
	 * @param string $file Full path to file to test for existence.
	 * @return bool|WP_Error Boolean true if it exists, false if it does not.
	 */
	public static function is_file( $file ) {
		@clearstatcache( true, $file );
		
		return @is_file( $file );
	}
	
	/**
	 * Get file permissions from the requested file.
	 *
	 * @since 1.15.0
	 *
	 * @param string $file Full path to the file to retrieve permissions from.
	 * @return int|WP_Error The permissions as an int or a WP_Error object if an error occurs.
	 */
	public static function get_permissions( $file ) {
		if ( ! self::is_file( $file ) ) {
			return new WP_Error( 'itsec-lib-file-get-permissions-missing-file', sprintf( __( 'Permissions for the file %s could not be read as the file could not be found.', 'it-l10n-ithemes-security-pro' ), $file ) );
		}
		
		if ( ! ITSEC_Lib_Utility::is_callable_function( 'fileperms' ) ) {
			return new WP_Error( 'itsec-lib-file-get-permissions-fileperms-is-disabled', sprintf( __( 'Permissions for the file %s could not be read as the fileperms() function is disabled. This is a system configuration issue.', 'it-l10n-ithemes-security-pro' ), $file ) );
		}
		
		
		@clearstatcache( true, $file );
		
		return fileperms( $file ) & 0777;
	}
	
	/**
	 * Get default file permissions to use for new files.
	 *
	 * @since 1.15.0
	 * @uses FS_CHMOD_FILE Define that sets default file permissions.
	 *
	 * @return int|WP_Error The default permissions as an int or a WP_Error object if an error occurs.
	 */
	public static function get_default_permissions() {
		if ( defined( 'FS_CHMOD_FILE' ) ) {
			return FS_CHMOD_FILE;
		}
		
		$perms = self::get_permissions( ABSPATH . 'index.php' );
		
		if ( ! is_wp_error( $perms ) ) {
			return $perms;
		}
		
		return 0644;
	}
}


require_once( dirname( __FILE__ ) . '/class-itsec-lib-utility.php' );
require_once( dirname( __FILE__ ) . '/class-itsec-lib-directory.php' );
