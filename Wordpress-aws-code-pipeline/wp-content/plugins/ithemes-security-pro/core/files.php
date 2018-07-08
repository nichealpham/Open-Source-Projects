<?php

/**
 * iThemes file handler.
 *
 * Writes to core files including wp-config.php, htaccess and nginx.conf.
 *
 * @package iThemes_Security
 *
 * @since   4.0.0
 */
final class ITSEC_Files {
	static $instance = false;

	private function __construct() {

		add_action( 'itsec-new-blacklisted-ip', array( $this, 'quick_ban' ) );

	}

	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Check the setting that allows writing files.
	 *
	 * @since 1.15.0
	 *
	 * @return bool True if files can be written to, false otherwise.
	 */
	public static function can_write_to_files() {
		$can_write = (bool) ITSEC_Modules::get_setting( 'global', 'write_files' );
		$can_write = apply_filters( 'itsec_filter_can_write_to_files', $can_write );

		return $can_write;
	}

	public static function regenerate_wp_config( $add_responses = true ) {
		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-config-file.php' );

		$result = ITSEC_Lib_Config_File::update_wp_config();
		$success = ! is_wp_error( $result );

		if ( $add_responses && is_wp_error( $result ) ) {
			ITSEC_Response::add_error( $result );
		}

		return $success;
	}

	public static function regenerate_server_config( $add_responses = true ) {
		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-config-file.php' );

		$result = ITSEC_Lib_Config_File::update_server_config();
		$success = ! is_wp_error( $result );
		$server = ITSEC_Lib_Utility::get_web_server();

		if ( $add_responses ) {
			if ( is_wp_error( $result ) ) {
				ITSEC_Response::add_error( $result );

				$file = ITSEC_Lib_Config_File::get_server_config_file_path();
			} else if ( 'nginx' === $server ) {
				ITSEC_Response::add_message( __( 'You must restart your NGINX server for the changes to take effect.', 'it-l10n-ithemes-security-pro' ) );
			}
		}

		return $success;
	}

	/**
	 * Execute activation functions.
	 *
	 * Writes necessary information to wp-config and .htaccess upon plugin activation.
	 *
	 * @since  4.0.0
	 *
	 * @return void
	 */
	public function do_activate() {
		self::regenerate_wp_config( false );
		self::regenerate_server_config( false );
	}

	/**
	 * Execute deactivation functions.
	 *
	 * Writes necessary information to wp-config and .htaccess upon plugin deactivation.
	 *
	 * @since  4.0.0
	 *
	 * @return void
	 */
	public function do_deactivate() {
		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-config-file.php' );

		ITSEC_Lib_Config_File::reset_wp_config();
		ITSEC_Lib_Config_File::reset_server_config();
	}

	/**
	 * Process quick ban of host.
	 *
	 * Immediately adds the supplied host to the .htaccess file for banning.
	 *
	 * @since 4.0.0
	 *
	 * @param string $host the host to ban
	 *
	 * @return bool true on success or false on failure
	 */
	public function quick_ban( $host ) {
		$host = trim( $host );

		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-ip-tools.php' );

		if ( ! ITSEC_Lib_IP_Tools::validate( $host ) ) {
			return false;
		}


		$host_rule = '# ' . __( 'Quick ban IP. Will be updated on next formal rules save.', 'it-l10n-ithemes-security-pro' ) . "\n";

		if ( 'nginx' === ITSEC_Lib::get_server() ) {
			$host_rule .= "\tdeny $host;\n";
		} else if ( 'apache' === ITSEC_Lib::get_server() ) {
			$dhost = str_replace( '.', '\\.', $host ); //re-define $dhost to match required output for SetEnvIf-RegEX

			$host_rule .= "SetEnvIF REMOTE_ADDR \"^$dhost$\" DenyAccess\n"; //Ban IP
			$host_rule .= "SetEnvIF X-FORWARDED-FOR \"^$dhost$\" DenyAccess\n"; //Ban IP from Proxy-User
			$host_rule .= "SetEnvIF X-CLUSTER-CLIENT-IP \"^$dhost$\" DenyAccess\n"; //Ban IP for Cluster/Cloud-hosted WP-Installs
			$host_rule .= "<IfModule mod_authz_core.c>\n";
			$host_rule .= "\t<RequireAll>\n";
			$host_rule .= "\t\tRequire all granted\n";
			$host_rule .= "\t\tRequire not env DenyAccess\n";
			$host_rule .= "\t\tRequire not ip $host\n";
			$host_rule .= "\t</RequireAll>\n";
			$host_rule .= "</IfModule>\n";
			$host_rule .= "<IfModule !mod_authz_core.c>\n";
			$host_rule .= "\tOrder allow,deny\n";
			$host_rule .= "\tDeny from env=DenyAccess\n";
			$host_rule .= "\tDeny from $host\n";
			$host_rule .= "\tAllow from all\n";
			$host_rule .= "</IfModule>\n";
		}

		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-config-file.php' );
		$result = ITSEC_Lib_Config_File::append_server_config( $host_rule );

		if ( is_wp_error( $result ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Attempt to get a lock for atomic operations.
	 *
	 * Tries to get a more robust lock on the file in question. Useful in situations where automatic
	 * file locking doesn't work.
	 *
	 * @since  4.0.0
	 *
	 * @param string $lock_file file name of lock
	 * @param int    $exp       seconds until lock expires
	 *
	 * @return bool true if lock was achieved, else false
	 */
	public function get_file_lock( $lock_file, $exp = 180 ) {

		if ( ITSEC_Modules::get_setting( 'global', 'lock_file' ) ) {
			return true;
		}

		clearstatcache();

		$lock_file = ITSEC_Core::get_storage_dir() . '/' . sanitize_text_field( $lock_file ) . '.lock';
		$dir_age   = @filectime( $lock_file );

		if ( false === @mkdir( $lock_file ) ) {

			if ( false !== $dir_age ) {

				if ( ( time() - $dir_age ) > intval( $exp ) ) { //see if the lock has expired

					@rmdir( $lock_file );
					@mkdir( $lock_file );

				} else { //couldn't get the lock

					return false;

				}

			} else {

				return false;

			}

		}

		return true; //file lock was achieved

	}

	/**
	 * Release the lock.
	 *
	 * Releases a file lock to allow others to use it.
	 *
	 * @since  4.0.0
	 *
	 * @param string $lock_file file name of lock
	 *
	 * @return bool true if released, false otherwise
	 */
	public function release_file_lock( $lock_file ) {
		if ( ITSEC_Modules::get_setting( 'global', 'lock_file' ) ) {
			return true;
		}

		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-directory.php' );

		$lock_file = ITSEC_Core::get_storage_dir() . '/' . sanitize_text_field( $lock_file ) . '.lock';

		$result = ITSEC_Lib_Directory::remove( $lock_file );

		if ( is_wp_error( $result ) ) {
			return false;
		}

		return true;
	}
}
