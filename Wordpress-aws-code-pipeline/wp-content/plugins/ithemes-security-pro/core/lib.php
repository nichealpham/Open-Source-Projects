<?php

/**
 * Miscellaneous plugin-wide functions.
 *
 * Various static functions to provide information to modules and other areas throughout the plugin.
 *
 * @package iThemes_Security
 *
 * @since   4.0.0
 */
final class ITSEC_Lib {
	/**
	 * Clear caches.
	 *
	 * Clears popular WordPress caching mechanisms.
	 *
	 * @since 4.0.0
	 *
	 * @param bool $page [optional] true to clear page cache
	 *
	 * @return void
	 */
	public static function clear_caches( $page = false ) {

		//clear APC Cache
		if ( function_exists( 'apc_store' ) ) {
			apc_clear_cache(); //Let's clear APC (if it exists) when big stuff is saved.
		}

		//clear w3 total cache or wp super cache
		if ( function_exists( 'w3tc_pgcache_flush' ) ) {

			if ( true == $page ) {
				w3tc_pgcache_flush();
				w3tc_minify_flush();
			}

			w3tc_dbcache_flush();
			w3tc_objectcache_flush();

		} else if ( function_exists( 'wp_cache_clear_cache' ) && true == $page ) {

			wp_cache_clear_cache();

		}


		do_action( 'itsec-lib-clear-caches' );
	}

	/**
	 * Creates appropriate database tables.
	 *
	 * Uses dbdelta to create database tables either on activation or in the event that one is missing.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public static function create_database_tables() {

		global $wpdb;

		$charset_collate = '';

		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}

		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}

		//Set up log table
		$tables = "CREATE TABLE " . $wpdb->base_prefix . "itsec_log (
				log_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				log_type varchar(20) NOT NULL DEFAULT '',
				log_function varchar(255) NOT NULL DEFAULT '',
				log_priority int(2) NOT NULL DEFAULT 1,
				log_date datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
				log_date_gmt datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
				log_host varchar(40),
				log_username varchar(60),
				log_user bigint(20) UNSIGNED,
				log_url varchar(255),
				log_referrer varchar(255),
				log_data longtext NOT NULL,
				PRIMARY KEY  (log_id),
				KEY log_type (log_type),
				KEY log_date_gmt (log_date_gmt)
				) " . $charset_collate . ";";

		//set up lockout table
		$tables .= "CREATE TABLE " . $wpdb->base_prefix . "itsec_lockouts (
				lockout_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				lockout_type varchar(20) NOT NULL,
				lockout_start datetime NOT NULL,
				lockout_start_gmt datetime NOT NULL,
				lockout_expire datetime NOT NULL,
				lockout_expire_gmt datetime NOT NULL,
				lockout_host varchar(40),
				lockout_user bigint(20) UNSIGNED,
				lockout_username varchar(60),
				lockout_active int(1) NOT NULL DEFAULT 1,
				PRIMARY KEY  (lockout_id),
				KEY lockout_expire_gmt (lockout_expire_gmt),
				KEY lockout_host (lockout_host),
				KEY lockout_user (lockout_user),
				KEY lockout_username (lockout_username),
				KEY lockout_active (lockout_active)
				) " . $charset_collate . ";";

		//set up temp table
		$tables .= "CREATE TABLE " . $wpdb->base_prefix . "itsec_temp (
				temp_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				temp_type varchar(20) NOT NULL,
				temp_date datetime NOT NULL,
				temp_date_gmt datetime NOT NULL,
				temp_host varchar(40),
				temp_user bigint(20) UNSIGNED,
				temp_username varchar(60),
				PRIMARY KEY  (temp_id),
				KEY temp_date_gmt (temp_date_gmt),
				KEY temp_host (temp_host),
				KEY temp_user (temp_user),
				KEY temp_username (temp_username)
				) " . $charset_collate . ";";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		@dbDelta( $tables );

	}

	/**
	 * Gets location of wp-config.php.
	 *
	 * Finds and returns path to wp-config.php
	 *
	 * @since 4.0.0
	 *
	 * @return string path to wp-config.php
	 * */
	public static function get_config() {
		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-config-file.php' );

		return ITSEC_Lib_Config_File::get_wp_config_file_path();
	}

	/**
	 * Gets current url
	 *
	 * Finds and returns current url.
	 *
	 * @since 4.3.0
	 *
	 * @return string current url
	 * */
	public static function get_current_url() {

		$page_url = 'http';

		if ( isset( $_SERVER["HTTPS"] ) ) {

			if ( 'on' == $_SERVER["HTTPS"] ) {
				$page_url .= "s";
			}

		}

		$page_url .= "://";

		if ( '80' != $_SERVER["SERVER_PORT"] ) {

			$page_url .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];

		} else {

			$page_url .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];

		}

		return esc_url( $page_url );
	}

	/**
	 * Return primary domain from given url.
	 *
	 * Returns primary domain name (without subdomains) of given URL.
	 *
	 * @since 4.0.0
	 *
	 * @param string $url          URL to filter
	 *
	 * @return string domain name or '*' on error or domain mapped multisite
	 * */
	public static function get_domain( $url ) {
		if ( is_multisite() && function_exists( 'domain_mapping_warning' ) ) {
			return '*';
		}


		$host = parse_url( $url, PHP_URL_HOST );

		if ( false === $host ) {
			return '*';
		}
		if ( 'www.' == substr( $host, 0, 4 ) ) {
			return substr( $host, 4 );
		}

		$host_parts = explode( '.', $host );

		if ( count( $host_parts ) > 2 ) {
			$host_parts = array_slice( $host_parts, -2, 2 );
		}

		return implode( '.', $host_parts );
	}

	/**
	 * Get path to WordPress install.
	 *
	 * Get the absolute filesystem path to the root of the WordPress installation.
	 *
	 * @since 4.3.0
	 *
	 * @return string Full filesystem path to the root of the WordPress installation
	 */
	public static function get_home_path() {

		$home    = set_url_scheme( get_option( 'home' ), 'http' );
		$siteurl = set_url_scheme( get_option( 'siteurl' ), 'http' );

		if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) {

			$wp_path_rel_to_home = str_ireplace( $home, '', $siteurl ); /* $siteurl - $home */
			$pos                 = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), trailingslashit( $wp_path_rel_to_home ) );

			if ( $pos === false ) {

				$home_path = dirname( $_SERVER['SCRIPT_FILENAME'] );

			} else {

				$home_path = substr( $_SERVER['SCRIPT_FILENAME'], 0, $pos );

			}

		} else {

			$home_path = ABSPATH;

		}

		return trailingslashit( str_replace( '\\', '/', $home_path ) );

	}

	/**
	 * Returns the root of the WordPress install.
	 *
	 * Gets the URI path to the WordPress installation.
	 *
	 * @since 4.0.6
	 *
	 * @return string the root folder
	 */
	public static function get_home_root() {
		if ( isset( $GLOBALS['__itsec_lib_get_home_root'] ) ) {
			return $GLOBALS['__itsec_lib_get_home_root'];
		}

		$url_parts = parse_url( site_url() );

		if ( isset( $url_parts['path'] ) ) {
			$GLOBALS['__itsec_lib_get_home_root'] = trailingslashit( $url_parts['path'] );
		} else {
			$GLOBALS['__itsec_lib_get_home_root'] = '/';
		}

		return $GLOBALS['__itsec_lib_get_home_root'];
	}

	/**
	 * Gets location of .htaccess
	 *
	 * Finds and returns path to .htaccess or nginx.conf if appropriate
	 *
	 * @since 4.0.0
	 *
	 * @return string path to .htaccess
	 */
	public static function get_htaccess() {
		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-config-file.php' );

		return ITSEC_Lib_Config_File::get_server_config_file_path();
	}

	/**
	 * Returns the actual IP address of the user.
	 *
	 * Determines the user's IP address by returning the forwarded IP address if present or
	 * the direct IP address if not.
	 *
	 * @since 4.0.0
	 *
	 * @return  String The IP address of the user
	 */
	public static function get_ip( $use_cache = true ) {
		if ( isset( $GLOBALS['__itsec_remote_ip'] ) && $use_cache ) {
			return $GLOBALS['__itsec_remote_ip'];
		}

		if ( ITSEC_Modules::get_setting( 'global', 'proxy_override' ) ) {
			$GLOBALS['__itsec_remote_ip'] = $_SERVER['REMOTE_ADDR'];
			return $GLOBALS['__itsec_remote_ip'];
		}

		$headers = array(
			'HTTP_CF_CONNECTING_IP', // CloudFlare
			'HTTP_X_FORWARDED_FOR',  // Squid and most other forward and reverse proxies
			'REMOTE_ADDR',           // Default source of remote IP
		);

		$headers = apply_filters( 'itsec_filter_remote_addr_headers', $headers );

		$headers = (array) $headers;

		if ( ! in_array( 'REMOTE_ADDR', $headers ) ) {
			$headers[] = 'REMOTE_ADDR';
		}

		// Loop through twice. The first run won't accept a reserved or private range IP. If an acceptable IP is not
		// found, try again while accepting reserved or private range IPs.
		for ( $x = 0; $x < 2; $x++ ) {
			foreach ( $headers as $header ) {
				if ( ! isset( $_SERVER[$header] ) ) {
					continue;
				}

				$ip = trim( $_SERVER[$header] );

				if ( empty( $ip ) ) {
					continue;
				}

				if ( false !== ( $comma_index = strpos( $_SERVER[$header], ',' ) ) ) {
					$ip = substr( $ip, 0, $comma_index );
				}

				if ( 0 === $x ) {
					// First run through. Only accept an IP not in the reserved or private range.
					$ip = filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE | FILTER_FLAG_NO_PRIV_RANGE );
				} else {
					$ip = filter_var( $ip, FILTER_VALIDATE_IP );
				}

				if ( ! empty( $ip ) ) {
					break;
				}
			}

			if ( ! empty( $ip ) ) {
				break;
			}
		}

		if ( empty( $ip ) ) {
			// If an IP is not found, force it to a localhost IP that would not be blacklisted as this typically
			// indicates a local request that does not provide the localhost IP.
			$ip = '127.0.0.1';
		}

		$GLOBALS['__itsec_remote_ip'] = (string) $ip;

		return $GLOBALS['__itsec_remote_ip'];
	}

	/**
	 * Gets PHP Memory Limit.
	 *
	 * Attempts to get the maximum amount of memory allowed for the application by the server.
	 *
	 * @since 4.0.0
	 *
	 * @return int php memory limit in megabytes
	 */
	public static function get_memory_limit() {

		return (int) ini_get( 'memory_limit' );

	}

	/**
	 * Returns the URL of the current module.
	 *
	 * Get's the full URL of the current module.
	 *
	 * @since 4.0.0
	 *
	 * @param string $file the module file from which to derive the path
	 *
	 * @return string the path of the current module
	 */
	public static function get_module_path( $file ) {

		$path = str_replace( ITSEC_Core::get_plugin_dir(), '', dirname( $file ) );
		$path = ltrim( str_replace( '\\', '/', $path ), '/' );

		$url_base = trailingslashit( plugin_dir_url( ITSEC_Core::get_plugin_file() ) );

		return trailingslashit( $url_base . $path );

	}

	/**
	 * Returns the server type of the plugin user.
	 *
	 * Attempts to figure out what http server the visiting user is running.
	 *
	 * @since 4.0.0
	 *
	 * @return string|bool server type the user is using of false if undetectable.
	 */
	public static function get_server() {
		require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-utility.php' );

		return ITSEC_Lib_Utility::get_web_server();
	}

	/**
	 * Determine whether the server supports SSL (shared cert not supported.
	 *
	 * Attempts to retrieve an HTML version of the homepage in an effort to determine if SSL is available.
	 *
	 * @since 4.0.0
	 *
	 * @return bool true if ssl is supported or false
	 */
	public static function get_ssl() {

		$url = str_ireplace( 'http://', 'https://', get_bloginfo( 'url' ) );

		if ( function_exists( 'wp_http_supports' ) && wp_http_supports( array( 'ssl' ), $url ) ) {

			return true;

		} elseif ( function_exists( 'curl_init' ) ) {

			//use a manual CURL request to better account for self-signed certificates
			$timeout    = 5; //timeout for the request
			$site_title = trim( get_bloginfo() );

			$request = curl_init();

			curl_setopt( $request, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $request, CURLOPT_VERBOSE, false );
			curl_setopt( $request, CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $request, CURLOPT_HEADER, true );
			curl_setopt( $request, CURLOPT_URL, $url );
			curl_setopt( $request, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $request, CURLOPT_CONNECTTIMEOUT, $timeout );

			$data = curl_exec( $request );

			$header_size = curl_getinfo( $request, CURLINFO_HEADER_SIZE );
			$http_code   = intval( curl_getinfo( $request, CURLINFO_HTTP_CODE ) );
			$body        = substr( $data, $header_size );

			preg_match( '/<title>(.+)<\/title>/', $body, $matches );

			if ( 200 == $http_code && isset( $matches[1] ) && false !== strpos( $matches[1], $site_title ) ) {

				return true;

			} else {

				return false;

			}

		}

		return false;

	}

	public static function get_whitelisted_ips() {
		return apply_filters( 'itsec_white_ips', array() );
	}

	/**
	 * Determines whether a given IP address is whiteliste
	 *
	 * @param  string  $ip              ip to check (can be in CIDR notation)
	 * @param  array   $whitelisted_ips ip list to compare to if not yet saved to options
	 * @param  boolean $current         whether to whitelist the current ip or not (due to saving, etc)
	 *
	 * @return boolean true if whitelisted or false
	 */
	public static function is_ip_whitelisted( $ip, $whitelisted_ips = null, $current = false ) {

		/** @var ITSEC_Lockout $itsec_lockout */
		global $itsec_lockout;

		$ip = sanitize_text_field( $ip );

		if ( ITSEC_Lib::get_ip() === $ip && $itsec_lockout->is_visitor_temp_whitelisted() ) {
			return true;
		}

		if ( ! class_exists( 'ITSEC_Lib_IP_Tools' ) ) {
			require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-ip-tools.php' );
		}

		if ( is_null( $whitelisted_ips ) ) {
			$whitelisted_ips = self::get_whitelisted_ips();
		}

		if ( $current ) {
			$whitelisted_ips[] = ITSEC_Lib::get_ip(); //add current user ip to whitelist
		}

		if ( ! empty( $_SERVER['SERVER_ADDR'] ) ) {
			$whitelisted_ips[] = $_SERVER['SERVER_ADDR'];
		}

		if ( ! empty( $_SERVER['LOCAL_ADDR'] ) ) {
			$whitelisted_ips[] = $_SERVER['LOCAL_ADDR'];
		}

		foreach ( $whitelisted_ips as $whitelisted_ip ) {
			if ( ITSEC_Lib_IP_Tools::intersect( $ip, ITSEC_Lib_IP_Tools::ip_wild_to_ip_cidr( $whitelisted_ip ) ) ) {
				return true;
			}
		}

		return false;

	}

	public static function get_blacklisted_ips() {
		return apply_filters( 'itsec_filter_blacklisted_ips', array() );
	}

	/**
	 * Determines whether a given IP address is blacklisted
	 *
	 * @param string $ip              ip to check (can be in CIDR notation)
	 * @param array  $blacklisted_ips ip list to compare to if not yet saved to options
	 *
	 * @return boolean true if blacklisted or false
	 */
	public static function is_ip_blacklisted( $ip = null, $blacklisted_ips = null ) {
		$ip = sanitize_text_field( $ip );

		if ( empty( $ip ) ) {
			$ip = ITSEC_Lib::get_ip();
		}

		if ( ! class_exists( 'ITSEC_Lib_IP_Tools' ) ) {
			require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-ip-tools.php' );
		}

		if ( is_null( $blacklisted_ips ) ) {
			$blacklisted_ips = self::get_blacklisted_ips();
		}

		foreach ( $blacklisted_ips as $blacklisted_ip ) {
			if ( ITSEC_Lib_IP_Tools::intersect( $ip, ITSEC_Lib_IP_Tools::ip_wild_to_ip_cidr( $blacklisted_ip ) ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Determine whether we're on the login page or not.
	 *
	 * Attempts to determine whether or not the user is on the WordPress dashboard login page.
	 *
	 * @since 4.0.0
	 *
	 * @return bool true if is login page else false
	 */
	public static function is_login_page() {

		return in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) );

	}

	/**
	 * Checks jQuery version.
	 *
	 * Checks if the jquery version saved is vulnerable to http://bugs.jquery.com/ticket/9521
	 *
	 * @since 4.0.0
	 *
	 * @return mixed|bool true if known safe false if unsafe or null if untested
	 */
	public static function is_jquery_version_safe() {

		$jquery_version = ITSEC_Modules::get_setting( 'wordpress-tweaks', 'jquery_version' );

		if ( ! empty( $jquery_version ) && version_compare( $jquery_version, '1.6.3', '>=' ) ) {

			return true;

		}

		return false;

	}

	/**
	 * Set a 404 error.
	 *
	 * Forces the given page to a WordPress 404 error.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public static function set_404() {

		global $wp_query;

		status_header( 404 );

		if ( function_exists( 'nocache_headers' ) ) {
			nocache_headers();
		}

		$wp_query->set_404();
		$page_404 = get_404_template();

		if ( 1 < strlen( $page_404 ) ) {

			include( $page_404 );

		} else {

			include( get_query_template( 'index' ) );

		}

		die();

	}

	/**
	 * Increases minimum memory limit.
	 *
	 * This function, adopted from builder, attempts to increase the minimum
	 * memory limit before heavy functions.
	 *
	 * @since 4.0.0
	 *
	 * @param int $new_memory_limit what the new memory limit should be
	 *
	 * @return void
	 */
	public static function set_minimum_memory_limit( $new_memory_limit ) {

		$memory_limit = @ini_get( 'memory_limit' );

		if ( - 1 < $memory_limit ) {

			$unit = strtolower( substr( $memory_limit, - 1 ) );
			$memory_limit = (int) $memory_limit;

			$new_unit = strtolower( substr( $new_memory_limit, - 1 ) );
			$new_memory_limit = (int) $new_memory_limit;

			if ( 'm' == $unit ) {

				$memory_limit *= 1048576;

			} else if ( 'g' == $unit ) {

				$memory_limit *= 1073741824;

			} else if ( 'k' == $unit ) {

				$memory_limit *= 1024;

			}

			if ( 'm' == $new_unit ) {

				$new_memory_limit *= 1048576;

			} else if ( 'g' == $new_unit ) {

				$new_memory_limit *= 1073741824;

			} else if ( 'k' == $new_unit ) {

				$new_memory_limit *= 1024;

			}

			if ( (int) $memory_limit < (int) $new_memory_limit ) {
				@ini_set( 'memory_limit', $new_memory_limit );
			}

		}

	}

	/**
	 * Checks if user exists.
	 *
	 * Checks to see if WordPress user with given id exists.
	 *
	 * @since 4.0.0
	 *
	 * @param int $user_id user id of user to check
	 *
	 * @return bool true if user exists otherwise false
	 *
	 * */
	public static function user_id_exists( $user_id ) {

		global $wpdb;

		//return false if username is null
		if ( '' == $user_id ) {
			return false;
		}

		//queary the user table to see if the user is there
		$saved_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM `" . $wpdb->users . "` WHERE ID='%s';", sanitize_text_field( $user_id ) ) );

		if ( $saved_id == $user_id ) {

			return true;

		} else {

			return false;

		}

	}

	/**
	 * Validates a file path
	 *
	 * Adapted from http://stackoverflow.com/questions/4049856/replace-phps-realpath/4050444#4050444 as a replacement for PHP's realpath
	 *
	 * @since 4.0.0
	 *
	 * @param string $path The original path, can be relative etc.
	 *
	 * @return bool true if the path is valid and writeable else false
	 */
	public static function validate_path( $path ) {

		// whether $path is unix or not
		$unipath = strlen( $path ) == 0 || $path{0} != '/';

		// attempts to detect if path is relative in which case, add cwd
		if ( false === strpos( $path, ':' ) && $unipath ) {
			$path = getcwd() . DIRECTORY_SEPARATOR . $path;
		}

		// resolve path parts (single dot, double dot and double delimiters)
		$path      = str_replace( array( '/', '\\' ), DIRECTORY_SEPARATOR, $path );
		$parts     = array_filter( explode( DIRECTORY_SEPARATOR, $path ), 'strlen' );
		$absolutes = array();

		foreach ( $parts as $part ) {

			if ( '.' == $part ) {
				continue;
			}

			if ( '..' == $part ) {

				array_pop( $absolutes );

			} else {

				$absolutes[] = $part;

			}

		}

		$path = implode( DIRECTORY_SEPARATOR, $absolutes );

		// resolve any symlinks
		if ( function_exists( 'linkinfo' ) ) { //linkinfo not available on Windows with PHP < 5.3.0

			if ( file_exists( $path ) && 0 < linkinfo( $path ) ) {
				$path = @readlink( $path );
			}

		} else {

			if ( file_exists( $path ) && 0 < linkinfo( $path ) ) {
				$path = @readlink( $path );
			}

		}

		// put initial separator that could have been lost
		$path = ! $unipath ? '/' . $path : $path;

		$test = @touch( $path . '/test.txt' );
		@unlink( $path . '/test.txt' );

		return $test;

	}

	/**
	 * Validates a URL
	 *
	 * Ensures the provided URL is a valid URL.
	 *
	 * @since 4.3.0
	 *
	 * @param string $url the url to validate
	 *
	 * @return bool true if valid url else false
	 */
	public static function validate_url( $url ) {

		$pattern = "/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i";

		return (bool) preg_match( $pattern, $url );

	}

	public static function show_status_message( $message ) {
		echo "<div class=\"updated fade\"><p><strong>$message</strong></p></div>\n";
	}

	public static function show_error_message( $message ) {
		if ( is_wp_error( $message ) ) {
			$message = $message->get_error_message();
		}

		if ( ! is_string( $message ) ) {
			return;
		}

		echo "<div class=\"error\"><p><strong>$message</strong></p></div>\n";
	}

	public static function show_inline_status_message( $message ) {
		echo "<div class=\"updated fade inline\"><p><strong>$message</strong></p></div>\n";
	}

	public static function show_inline_error_message( $message ) {
		if ( is_wp_error( $message ) ) {
			$message = $message->get_error_message();
		}

		if ( ! is_string( $message ) ) {
			return;
		}

		echo "<div class=\"error inline\"><p><strong>$message</strong></p></div>\n";
	}

	/**
	 * Get a WordPress user object.
	 *
	 * @param int|string|WP_User|bool $user Either the user ID ( must be an int ), the username, a WP_User object,
	 *                                      or false to retrieve the currently logged-in user.
	 *
	 * @return WP_User|false
	 */
	public static function get_user( $user = false ) {
		if ( $user instanceof WP_User ) {
			return $user;
		}

		if ( false === $user ) {
			$user = wp_get_current_user();
		} else if ( is_int( $user ) ) {
			$user = get_user_by( 'id', $user );
		} else if ( is_string( $user ) ) {
			$user = get_user_by( 'login', $user );
		} else {
			if ( is_object( $user ) ) {
				$type = 'object(' . get_class( $user ) . ')';
			} else {
				$type = gettype( $user );
			}

			trigger_error( "ITSEC_Lib::get_user() called with an invalid \$user argument. Received \$user variable of type: $type", E_USER_ERROR );

			return false;
		}

		if ( $user instanceof WP_User ) {
			return $user;
		}

		return false;
	}

	/**
	 * Evaluate a password's strength.
	 *
	 * @param string $password
	 * @param array  $penalty_strings Additional strings that if found within the password, will decrease the strength.
	 *
	 * @return ITSEC_Zxcvbn_Results
	 */
	public static function get_password_strength_results( $password, $penalty_strings = array() ) {
		if ( ! isset( $GLOBALS['itsec_zxcvbn'] ) ) {
			require_once( ITSEC_Core::get_core_dir() . '/lib/itsec-zxcvbn-php/zxcvbn.php' );
			$GLOBALS['itsec_zxcvbn'] = new ITSEC_Zxcvbn();
		}

		return $GLOBALS['itsec_zxcvbn']->test_password( $password, $penalty_strings );
	}

	/**
	 * Retrieve the URL to a website to lookup the location of an IP address.
	 *
	 * @param string|bool $ip IP address to lookup, or false to return a URL to their home page.
	 *
	 * @return string
	 */
	public static function get_trace_ip_link( $ip = false ) {
		if ( empty( $ip ) ) {
			return 'http://www.traceip.net/';
		} else {
			return 'http://www.traceip.net/?query=' . urlencode( $ip );
		}
	}

	/**
	 * Whenever a login fails, collect details of the attempt, and forward them to modules.
	 *
	 * @param string $username
	 */
	public static function handle_wp_login_failed( $username ) {
		$authentication_types = array();

		if ( isset( $_SERVER['HTTP_AUTHORIZATION'] ) ) {
			$http_auth_type = substr( $_SERVER['HTTP_AUTHORIZATION'], 0, 6 );

			if ( 'Basic ' === $http_auth_type ) {
				$authentication_types[] = 'header_http_basic_auth';
			} else if ( 'OAuth ' === $http_auth_type ) {
				$authentication_types[] = 'header_http_oauth';
			}
		}

		if ( isset( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] ) ) {
			$authentication_types[] = 'header_http_basic_auth';
		}

		if ( ! empty( $_GET['oauth_consumer_key'] ) ) {
			$authentication_types[] = 'query_oauth';
		}

		if ( ! empty( $_POST['oauth_consumer_key'] ) ) {
			$authentication_types[] = 'post_oauth';
		}

		if ( defined('XMLRPC_REQUEST') && XMLRPC_REQUEST ) {
			$source = 'xmlrpc';
			$authentication_types = array( 'username_and_password' );
		} else if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			$source = 'rest_api';
			$authentication_types[] = 'cookie';
		} else {
			$source = 'wp-login.php';
			$authentication_types = array( 'username_and_password' );
		}

		$details = compact( 'source', 'authentication_types' );
		$details = apply_filters( 'itsec-filter-failed-login-details', $details );

		do_action( 'itsec-handle-failed-login', $username, $details );
	}

	/**
	 * Reliably provides the URL path.
	 *
	 * It optionally takes a prefix that will be stripped from the path, if present. This is useful for use to get site
	 * URL paths without the site's subdirectory.
	 *
	 * Trailing slashes are not preserved.
	 *
	 * @param string $url    The URL to pull the path from.
	 * @param string $prefix [optional] A string prefix to be removed from the path.
	 *
	 * @return string The URL path.
	 */
	public static function get_url_path( $url, $prefix = '' ) {
		$path = (string) parse_url( $url, PHP_URL_PATH );
		$path = untrailingslashit( $path );

		if ( ! empty( $prefix ) && 0 === strpos( $path, $prefix ) ) {
			return substr( $path, strlen( $prefix ) );
		}

		return '';
	}

	/**
	 * Returns the current request path without the protocol, domain, site subdirectories, or query args.
	 *
	 * This function returns "wp-login.php" when requesting http://example.com/site-path/wp-login.php?action=register.
	 *
	 * @return string The requested site path.
	 */
	public static function get_request_path() {
		if ( ! isset( $GLOBALS['__itsec_lib_get_request_path'] ) ) {
			$GLOBALS['__itsec_lib_get_request_path'] = self::get_url_path( $_SERVER['REQUEST_URI'], self::get_home_root() );
		}

		return $GLOBALS['__itsec_lib_get_request_path'];
	}
}
