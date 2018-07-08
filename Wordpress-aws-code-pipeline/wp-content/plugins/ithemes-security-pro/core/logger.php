<?php

/**
 * Handles the writing, maintenance and display of log files
 *
 * @package iThemes-Security
 * @since   4.0
 */
final class ITSEC_Logger {

	private
		$log_file,
		$logger_displays,
		$logger_modules,
		$module_path;

	/**
	 * @access private
	 *
	 * @var array Events that need to be logged to a file but couldn't
	 */
	private $events_to_log_to_file = array();

	function __construct() {

		$this->logger_modules  = array(); //array to hold information on modules using this feature
		$this->logger_displays = array(); //array to hold metabox information
		$this->module_path     = ITSEC_Lib::get_module_path( __FILE__ );

		add_action( 'plugins_loaded', array( $this, 'register_modules' ) );
		add_action( 'plugins_loaded', array( $this, 'write_pending_events_to_file' ) );

		//Run database cleanup daily with cron
		if ( ! wp_next_scheduled( 'itsec_purge_logs' ) ) {
			wp_schedule_event( time(), 'daily', 'itsec_purge_logs' );
		}

		add_action( 'itsec_purge_logs', array( $this, 'purge_logs' ) );

	}

	/**
	 * Gets events from the logs for a specified module
	 *
	 * @param string $module    module or type of events to fetch
	 * @param array  $params    array of extra query parameters
	 * @param int    $limit     the maximum number of rows to retrieve
	 * @param int    $offset    the offset of the data
	 * @param string $order     order by column
	 * @param bool   $direction false for descending or true for ascending
	 *
	 * @return bool|mixed false on error, null if no events or array of events
	 */
	public function get_events( $module, $params = array(), $limit = null, $offset = null, $order = null, $direction = false ) {

		global $wpdb;

		if ( isset( $module ) !== true || strlen( $module ) < 1 ) {
			return false;
		}

		if ( sizeof( $params ) > 0 || $module != 'all' ) {
			$where = " WHERE ";
		} else {
			$where = '';
		}

		$param_search = '';

		if ( $module == 'all' ) {

			$module_sql = '';
			$and        = '';

		} else {

			$module_sql = "`log_type` = '" . esc_sql( $module ) . "'";
			$and        = ' AND ';

		}

		if ( $direction === false ) {

			$order_direction = ' DESC';

		} else {

			$order_direction = ' ASC';

		}

		if ( $order !== null ) {

			$order_statement = ' ORDER BY `' . esc_sql( $order ) . '`';

		} else {

			$order_statement = ' ORDER BY `log_id`';

		}

		if ( $limit !== null ) {

			if ( $offset !== null ) {

				$result_limit = ' LIMIT ' . absint( $offset ) . ', ' . absint( $limit );

			} else {

				$result_limit = ' LIMIT ' . absint( $limit );

			}

		} else {

			$result_limit = '';

		}

		if ( sizeof( $params ) > 0 ) {

			foreach ( $params as $field => $value ) {

				if ( gettype( $value ) != 'integer' ) {
					$param_search .= $and . "`" . esc_sql( $field ) . "`='" . esc_sql( $value ) . "'";
				} else {
					$param_search .= $and . "`" . esc_sql( $field ) . "`=" . esc_sql( $value ) . "";
				}

			}

		}

		$items = $wpdb->get_results( "SELECT * FROM `" . $wpdb->base_prefix . "itsec_log`" . $where . $module_sql . $param_search . $order_statement . $order_direction . $result_limit . ";", ARRAY_A );

		return $items;

	}

	/**
	 * Logs events sent by other modules or systems
	 *
	 * @param string $module   the module requesting the log entry
	 * @param int    $priority the priority of the log entry (1-10)
	 * @param array  $data     extra data to log (non-indexed data would be good here)
	 * @param string $host     the remote host triggering the event
	 * @param string $username the username triggering the event
	 * @param string $user     the user id triggering the event
	 * @param string $url      the url triggering the event
	 * @param string $referrer the referrer to the url (if applicable)
	 *
	 * @return void
	 */
	public function log_event( $module, $priority = 5, $data = array(), $host = '', $username = '', $user = '', $url = '', $referrer = '' ) {

		if ( isset( $this->logger_modules[ $module ] ) ) {
			$type = ITSEC_Modules::get_setting( 'global', 'log_type' );

			if ( 'database' === $type || 'both' === $type ) {
				$this->log_event_to_db( $module, $priority, $data, $host, $username, $user, $url, $referrer );
			}

			if ( 'file' === $type || 'both' === $type ) {
				$this->log_event_to_file( $module, $priority, $data, $host, $username, $user, $url, $referrer );
			}

		}

		do_action( 'itsec_log_event', $module, $priority, $data, $host, $username, $user, $url, $referrer );

	}

	private function log_event_to_db( $module, $priority, $data, $host, $username, $user, $url, $referrer ) {
		global $wpdb, $itsec_globals;

		$options = $this->logger_modules[ $module ];

		$values = array(
			'log_type'     => $options['type'],
			'log_priority' => intval( $priority ),
			'log_function' => $options['function'],
			'log_date'     => date( 'Y-m-d H:i:s', $itsec_globals['current_time'] ),
			'log_date_gmt' => date( 'Y-m-d H:i:s', $itsec_globals['current_time_gmt'] ),
			'log_host'     => sanitize_text_field( $host ),
			'log_username' => sanitize_text_field( $username ),
			'log_user'     => intval( $user ),
			'log_url'      => $url,
			'log_referrer' => $referrer,
			'log_data'     => serialize( $data ),
		);

		$columns = '`' . implode( '`, `', array_keys( $values ) ) . '`';
		$placeholders = '%s, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s';

		$query_format = "INSERT INTO `{$wpdb->base_prefix}itsec_log` ($columns) VALUES ($placeholders)";

		$cached_show_errors_setting = $wpdb->hide_errors();
		$result = $wpdb->query( $wpdb->prepare( $query_format, $values ) );

		if ( ! $result ) {
			$wpdb->show_errors();

			ITSEC_Lib::create_database_tables();

			// Attempt the query again. Since errors will now be shown, a remaining issue will be display an error.
			$result = $wpdb->query( $wpdb->prepare( $query_format, $values ) );
		}

		// Set $wpdb->show_errors back to its original setting.
		$wpdb->show_errors( $cached_show_errors_setting );
	}

	private function log_event_to_file( $module, $priority = 5, $data = array(), $host = '', $username = '', $user = '', $url = '', $referrer = '' ) {
		global $itsec_globals;

		// If the file can't be prepared, store the events up to write later (at plugins_loaded)
		if ( false === $this->prepare_log_file() ) {
			$this->events_to_log_to_file[] = compact( 'module', 'priority', 'data', 'host', 'username', 'user', 'url', 'referrer' );
			return;
		}

		$options = $this->logger_modules[ $module ];

		$file_data = $this->sanitize_array( $data, true );

		$message =
			$options['type'] . ',' .
			intval( $priority ) . ',' .
			$options['function'] . ',' .
			date( 'Y-m-d H:i:s', $itsec_globals['current_time'] ) . ',' .
			date( 'Y-m-d H:i:s', $itsec_globals['current_time_gmt'] ) . ',' .
			sanitize_text_field( $host ) . ',' .
			sanitize_text_field( $username ) . ',' .
			( intval( $user ) === 0 ? '' : intval( $user ) ) . ',' .
			esc_sql( $url ) . ',' .
			esc_sql( $referrer ) . ',' .
			maybe_serialize( $file_data );

		$this->add_to_log_file( $message );

	}

	public function write_pending_events_to_file() {
		foreach ( $this->events_to_log_to_file as $event ) {
			call_user_func_array( array( $this, 'log_event_to_file' ), $event );
		}
	}

	/**
	 * Displays into box for logs page
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function metabox_all_logs() {

		$log_filter = isset( $_GET['itsec_log_filter'] ) ? sanitize_text_field( $_GET['itsec_log_filter'] ) : 'all-log-data';
		$callback   = null;

		echo '<p>' . __( 'To adjust logging options visit the global settings page.', 'it-l10n-ithemes-security-pro' ) . '</p>';

		echo '<label for="itsec_log_filter"><strong>' . __( 'Select Filter: ', 'it-l10n-ithemes-security-pro' ) . '</strong></label>';
		echo '<select id="itsec_log_filter" name="itsec_log_filter">';
		echo '<option value="all-log-data" ' . selected( $log_filter, 'all-log-data' ) . '>' . __( 'All Log Data', 'it-l10n-ithemes-security-pro' ) . '</option>';

		if ( sizeof( $this->logger_displays ) > 0 ) {

			foreach ( $this->logger_displays as $display ) {

				if ( $display['module'] === $log_filter ) {
					$callback = $display['callback'];
				}

				echo '<option value="' . $display['module'] . '" ' . selected( $log_filter, $display['module'] ) . '>' . $display['title'] . '</option>';

			}

		}

		echo '</select>';

		if ( $log_filter === 'all-log-data' || $callback === null ) {

			$this->all_logs_content();

		} else {

			call_user_func_array( $callback, array() );

		}

	}

	/**
	 * A better print array function to display array data in the logs
	 *
	 * @since 4.2
	 *
	 * @param array $array_items array to print or return
	 * @param bool  $return      true to return the data false to echo it
	 */
	public function print_array( $array_items, $return = true ) {

		$items = '';

		//make sure we're working with an array
		if ( ! is_array( $array_items ) ) {
			return false;
		}

		if ( sizeof( $array_items ) > 0 ) {

			$items .= '<ul>';

			foreach ( $array_items as $key => $item ) {

				if ( is_array( $item ) ) {

					$items .= '<li>';

					if ( ! is_numeric( $key ) ) {
						$items .= '<h3>' . esc_html( $key ) . '</h3>';
					}

					$items .= $this->print_array( $item, true ) . PHP_EOL;

					$items .= '</li>';

				} else {

					if ( strlen( trim( $item ) ) > 0 ) {
						$items .= '<li><h3>' . esc_html( $key ) . ' = ' . esc_html( $item ) . '</h3></li>' . PHP_EOL;
					}

				}

			}

			$items .= '</ul>';

		}

		return $items;

	}

	/**
	 * Purges database logs and rotates file logs (when needed)
	 *
	 * @return void
	 */
	public function purge_logs( $purge_all = false ) {

		global $wpdb, $itsec_globals, $itsec_clear_all_logs;

		if ( true === $purge_all ) {

			if ( ! wp_verify_nonce( $_POST['wp_nonce'], 'itsec_clear_logs' ) ) {
				return;
			}

			$wpdb->query( "DELETE FROM `" . $wpdb->base_prefix . "itsec_log`;" );

		} else {

			//Clean up the database log first
			$type = ITSEC_Modules::get_setting( 'global', 'log_type' );

			if ( 'database' === $type || 'both' === $type ) {

				$wpdb->query( "DELETE FROM `" . $wpdb->base_prefix . "itsec_log` WHERE `log_date_gmt` < '" . date( 'Y-m-d H:i:s', $itsec_globals['current_time_gmt'] - ( ITSEC_Modules::get_setting( 'global', 'log_rotation' ) * DAY_IN_SECONDS ) ) . "';" );

			} else {

				$wpdb->query( "DELETE FROM `" . $wpdb->base_prefix . "itsec_log`;" );

			}

			$this->rotate_log();

		}

	}

	/**
	 * Register modules that will use the logger service
	 *
	 * @return void
	 */
	public function register_modules() {

		$this->logger_modules  = apply_filters( 'itsec_logger_modules', $this->logger_modules );
		$this->logger_displays = apply_filters( 'itsec_logger_displays', $this->logger_displays );

	}

	/**
	 * Rotates the event-log.log file when called
	 *
	 * Adapted from http://www.phpclasses.org/browse/file/49471.html
	 *
	 * @return void
	 */
	private function rotate_log() {
		$log_file = $this->get_log_file();
		$max_size = 1024 * 1024 * 10; // 10MiB

		if ( ! @file_exists( $log_file ) || @filesize( $log_file ) < $max_size ) {
			return;
		}

		// rotate
		$path_info      = pathinfo( $log_file );
		$base_directory = $path_info['dirname'];
		$base_name      = $path_info['basename'];
		$num_map        = array();

		foreach ( new DirectoryIterator( $base_directory ) as $fInfo ) {

			if ( $fInfo->isDot() || ! $fInfo->isFile() ) {
				continue;
			}

			if ( preg_match( '/^' . $base_name . '\.?([0-9]*)$/', $fInfo->getFilename(), $matches ) ) {

				$num      = $matches[1];
				$old_file = $fInfo->getFilename();

				if ( $num == '' ) {
					$num = - 1;
				}

				$num_map[ $num ] = $old_file;

			}

		}

		krsort( $num_map );

		foreach ( $num_map as $num => $old_file ) {

			$new_file = $num + 1;
			@rename( $base_directory . DIRECTORY_SEPARATOR . $old_file, $log_file . '.' . $new_file );

		}

		$this->prepare_log_file();

	}

	/**
	 * Sanitizes strings in a given array recursively
	 *
	 * @param  array $array     array to sanitize
	 * @param  bool  $to_string true if output should be string or false for array output
	 *
	 * @return mixed             sanitized array or string
	 */
	private function sanitize_array( $array, $to_string = false ) {

		$sanitized_array = array();
		$string          = '';

		//Loop to sanitize each piece of data
		foreach ( $array as $key => $value ) {

			if ( is_array( $value ) ) {

				if ( $to_string === false ) {
					$sanitized_array[ esc_sql( $key ) ] = $this->sanitize_array( $value );
				} else {
					$string .= esc_sql( $key ) . '=' . $this->sanitize_array( $value, true );
				}

			} else {

				$sanitized_array[ esc_sql( $key ) ] = esc_sql( $value );

				$string .= esc_sql( $key ) . '=' . esc_sql( $value );

			}

		}

		if ( $to_string === false ) {
			return $sanitized_array;
		} else {
			return $string;
		}

	}

	private function get_log_file() {
		if ( isset( $this->log_file ) ) {
			return $this->log_file;
		}

		$log_location = ITSEC_Modules::get_setting( 'global', 'log_location' );
		$log_info = ITSEC_Modules::get_setting( 'global', 'log_info' );

		if ( empty( $log_info ) ) {
			// We need wp_generate_password() to create a cryptographically secure file name
			if ( ! function_exists( 'wp_generate_password' ) ) {
				$this->log_file = false;
				return false;
			}

			$log_info = substr( sanitize_title( get_bloginfo( 'name' ) ), 0, 20 ) . '-' . wp_generate_password( 30, false );

			ITSEC_Modules::set_setting( 'global', 'log_info', $log_info );
		}

		$this->log_file = "$log_location/event-log-$log_info.log";

		return $this->log_file;
	}

	/**
	 * Creates a new log file and adds header information (if needed)
	 *
	 * @return void
	 */
	private function prepare_log_file() {
		$log_file = $this->get_log_file();

		// We can't prepare a file if we can't get the file name
		if ( false === $log_file ) {
			return false;
		}

		if ( ! file_exists( $log_file ) ) { //only if current log file doesn't exist

			$header = 'log_type,log_priority,log_function,log_date,log_date_gmt,log_host,log_username,log_user,log_url,log_referrer,log_data';

			$this->add_to_log_file( $header );

		}

		return true;

	}

	private function add_to_log_file( $details ) {
		$log_file = $this->get_log_file();

		if ( false === $log_file ) {
			return false;
		}

		@error_log( $details . PHP_EOL, 3, $log_file );

		return true;
	}
}
