<?php

/**
 * Manage iThemes Security Pro functionality
 *
 * Provides command line access via WP-CLI: http://wp-cli.org/
 */
class ITSEC_WP_CLI_Command_ITSEC extends WP_CLI_Command {

	/**
	 * Performs a file change scan
	 *
	 * @since 1.12
	 *
	 * @return void
	 */
	public function filescan() {

		if ( ! class_exists( 'ITSEC_File_Change' ) ) {
			WP_CLI::error( __( 'File change scanning is not enabled. You must enable the module first.', 'it-l10n-ithemes-security-pro' ) );
		}

		ITSEC_Modules::load_module_file( 'scanner.php', 'file-change' );
		$response = ITSEC_File_Change_Scanner::run_scan( false, true );

		if ( false === $response ) {
			WP_CLI::success( __( 'File scan completed. No changes were detected.', 'it-l10n-ithemes-security-pro' ) );
			return;
		}
		
		if ( -1 === $response ) {
			WP_CLI::error( __( 'A scan is currently running. Please wait a few minutes before attempting a new file scan.', 'it-l10n-ithemes-security-pro' ) );
			return;
		}
		
		if ( ! is_array( $response ) ) {
			WP_CLI::error( __( 'There was an error in the scan operation. Please check the site logs or contact support.', 'it-l10n-ithemes-security-pro' ) );
			return;
		}
		
		if ( empty( $response['added'] ) && empty( $response['removed'] ) && empty( $response['changed'] ) ) {
			WP_CLI::success( __( 'File scan completed. No changes were detected.', 'it-l10n-ithemes-security-pro' ) );
			return;
		}
		
		
		$added    = array();
		$removed  = array();
		$modified = array();

		//process added files if we have them
		if ( isset( $response['added'] ) && sizeof( $response['added'] ) > 0 ) {

			foreach ( $response['added'] as $index => $data ) {

				$added[] = $this->format_filescan( __( 'added', 'it-l10n-ithemes-security-pro' ), $index, $data['h'], $data['d'] );

			}

		}

		//process removed files if we have them
		if ( isset( $response['removed'] ) && sizeof( $response['removed'] ) > 0 ) {

			foreach ( $response['removed'] as $index => $data ) {

				$removed[] = $this->format_filescan( __( 'removed', 'it-l10n-ithemes-security-pro' ), $index, $data['h'], $data['d'] );

			}

		}

		//process modified files if we have them
		if ( isset( $response['changed'] ) && sizeof( $response['changed'] ) > 0 ) {

			foreach ( $response['changed'] as $index => $data ) {

				$modified[] = $this->format_filescan( __( 'modified', 'it-l10n-ithemes-security-pro' ), $index, $data['h'], $data['d'] );

			}

		}

		$file_changes = array_merge( $added, $removed, $modified );

		$obj_type   = 'itsec_file_changes';
		$obj_fields = array(
			'type',
			'file',
			'hash',
			'date',
		);

		$defaults = array(
			'format' => 'table',
			'fields' => array( 'type', 'file', 'hash', 'date', ),
		);

		$formatter = $this->get_formatter( $defaults, $obj_fields, $obj_type );
		$formatter->display_items( $file_changes );

	}

	/**
	 * Standardize and sanitize output of file changes detected
	 *
	 * @since 1.12
	 *
	 * @param string $type the type of change
	 * @param string $file the file that changed
	 * @param string $hash the md5 hash of the file
	 * @param int    $date the timestamp detected on the file
	 *
	 * @return array presentable array of file information
	 */
	private function format_filescan( $type, $file, $hash, $date ) {

		global $itsec_globals;

		$file_info = array();

		$file = sanitize_text_field( $file );

		$file_info['type'] = sanitize_text_field( $type );
		$file_info['file']  = $file;
		$file_info['hash']  = substr( sanitize_text_field( $hash ), 0, 8 );
		$file_info['date']  = human_time_diff( ITSEC_Core::get_current_time_gmt(), intval( $date ) ) . ' ago';

		return $file_info;

	}

	/**
	 * Returns an instance of the wp-cli formatter for better information dissplay
	 *
	 * @since 1.12
	 *
	 * @param array  $assoc_args array of formatter options
	 * @param array  $obj_fields array of field titles for display
	 * @param string $obj_type   type of object being displayed
	 *
	 * @return \WP_CLI\Formatter
	 */
	private function get_formatter( $assoc_args, $obj_fields, $obj_type ) {

		return new \WP_CLI\Formatter( $assoc_args, $obj_fields, $obj_type );

	}

	/**
	 * Retrieve active lockouts
	 *
	 * @since 1.12
	 *
	 * @return void
	 */
	public function getlockouts() {

		/** @var ITSEC_Lockout $itsec_lockout */
		global $itsec_lockout, $itsec_globals;

		$host_locks = $itsec_lockout->get_lockouts( 'host', true );
		$user_locks = $itsec_lockout->get_lockouts( 'user', true );

		if ( empty( $host_locks ) && empty( $user_locks ) ) {

			WP_CLI::success( __( 'There are no current lockouts', 'it-l10n-ithemes-security-pro' ) );

		} else {

			if ( ! empty( $host_locks ) ) {

				foreach ( $host_locks as $index => $lock ) {

					$host_locks[ $index ]['type']           = __( 'host', 'it-l10n-ithemes-security-pro' );
					$host_locks[ $index ]['lockout_expire'] = isset( $lock['lockout_expire'] ) ? human_time_diff( $itsec_globals['current_time'], strtotime( $lock['lockout_expire'] ) ) : __( 'N/A', 'it-l10n-ithemes-security-pro' );

				}

			}

			if ( ! empty( $user_locks ) ) {

				foreach ( $user_locks as $index => $lock ) {

					$user_locks[ $index ]['type']           = __( 'user', 'it-l10n-ithemes-security-pro' );
					$user_locks[ $index ]['lockout_expire'] = isset( $lock['lockout_expire'] ) ? human_time_diff( $itsec_globals['current_time'], strtotime( $lock['lockout_expire'] ) ) : __( 'N/A', 'it-l10n-ithemes-security-pro' );

				}

			}

			$lockouts = array_merge( $host_locks, $user_locks );

			WP_CLI\Utils\format_items( 'table', $lockouts, array( 'lockout_id', 'type', 'lockout_host', 'lockout_username', 'lockout_expire' ) );

		}

	}

	/**
	 * Release a lockout using one or more ID's provided by getlockouts.
	 *
	 * ## OPTIONS
	 *
	 * [<id>...]
	 * : One or more active lockout ID's.
	 *
	 * [--id=<id>]
	 * : An active lockout ID.
	 *
	 * ## EXAMPLES
	 *
	 *     wp itsec releaselockout 14 21
	 *     wp itsec releaselockout --id=83
	 *
	 * @since 1.12
	 *
	 * @param array $args
	 * @param array $assoc_args
	 *
	 * @return void
	 */
	public function releaselockout( $args, $assoc_args ) {

		/** @var ITSEC_Lockout $itsec_lockout */
		global $itsec_lockout;

		$ids = array();
		
		//make sure they provided a valid ID
		if ( isset( $assoc_args['id'] ) ) {
			$ids[] = $assoc_args['id'];
		} else {
			$ids = $args;
		}
		
		if ( empty( $ids ) ) {
			WP_CLI::error( __( 'You must supply one or more lockout ID\'s to release.', 'it-l10n-ithemes-security-pro' ) );
		}
		
		foreach ( $ids as $id ) {
			if ( '' === $id ) {
				WP_CLI::error( __( 'Skipping empty ID.', 'it-l10n-ithemes-security-pro' ) );
			} else if ( (string) intval( $id ) !== (string) $id ) {
				WP_CLI::error( sprintf( __( 'Skipping invalid ID "%s". Please supply a valid ID.', 'it-l10n-ithemes-security-pro' ), $id ) );
			} else if ( ! $itsec_lockout->release_lockout( $id ) ) {
				WP_CLI::error( sprintf( __( 'Unable to remove lockout "%s".', 'it-l10n-ithemes-security-pro' ), $id ) );
			} else {
				WP_CLI::success( sprintf( __( 'Successfully removed lockout "%d".', 'it-l10n-ithemes-security-pro' ), $id ) );
			}
		}
	}

	/**
	 * List the most recent log items
	 *
	 * ## OPTIONS
	 *
	 * [<count>]
	 * : The number of log items to display.
	 * ---
	 * default: 10
	 * ---
	 *
	 * [--count=<count>]
	 * : The number of log items to display.
	 * ---
	 * default: 10
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp itsec getrecent 20
	 *     wp itsec getrecent --count=50
	 *
	 * @since 1.12
	 *
	 * @param array $args
	 * @param array $assoc_args
	 *
	 * @return void
	 */
	public function getrecent( $args, $assoc_args ) {

		global $itsec_logger, $itsec_globals;

		//make sure they provided a valid ID
		if ( isset( $assoc_args['count'] ) ) {

			$count = intval( $assoc_args['count'] );

		} elseif ( isset( $args[0] ) ) {

			$count = intval( $args[0] );

		} else {

			$count = 10;

		}

		$log_items = $itsec_logger->get_events( 'all', array(), $count, null, 'log_date' );

		if ( ! is_array( $log_items ) || empty( $log_items ) ) {

			WP_CLI::success( __( 'The Security logs are empty.', 'it-l10n-ithemes-security-pro' ) );

		} else {

			foreach ( $log_items as $index => $item ) {

				$log_items[ $index ] = array(
					'Time'     => human_time_diff( $itsec_globals['current_time_gmt'], strtotime( $item['log_date_gmt'] ) ) . ' ' . __( 'ago', 'it-l10n-ithemes-security-pro' ),
					'Type'     => sanitize_text_field( $item['log_function'] ),
					'Priority' => absint( $item['log_priority'] ),
					'IP'       => sanitize_text_field( $item['log_host'] ),
					'Username' => sanitize_text_field( $item['log_username'] ),
					'URL'      => esc_url( $item['log_url'] ),
					'Referrer' => esc_url( $item['log_referrer'] ),
				);

			}

			WP_CLI\Utils\format_items( 'table', $log_items, array( 'Time', 'Type', 'Priority', 'IP', 'Username', 'URL', 'Referrer' ) );

		}

	}

}

