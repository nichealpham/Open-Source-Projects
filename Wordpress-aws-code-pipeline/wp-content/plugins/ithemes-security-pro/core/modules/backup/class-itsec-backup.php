<?php

/**
 * Backup execution.
 *
 * Handles database backups at scheduled interval.
 *
 * @since   4.0.0
 *
 * @package iThemes_Security
 */
class ITSEC_Backup {

	/**
	 * The module's saved options
	 *
	 * @since  4.0.0
	 * @access private
	 * @var array
	 */
	private $settings;

	/**
	 * Setup the module's functionality.
	 *
	 * Loads the backup detection module's unpriviledged functionality including
	 * performing the scans themselves.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	function run() {

		$this->settings = ITSEC_Modules::get_settings( 'backup' );

		add_action( 'itsec_execute_backup_cron', array( $this, 'do_backup' ) );
		add_filter( 'itsec_logger_modules', array( $this, 'register_logger' ) );

		if ( defined( 'ITSEC_BACKUP_CRON' ) && true === ITSEC_BACKUP_CRON ) {
			if ( ! wp_next_scheduled( 'itsec_execute_backup_cron' ) ) {
				wp_schedule_event( time(), 'daily', 'itsec_execute_backup_cron' );
			}

			// When ITSEC_BACKUP_CRON is enabled, skip the regular scheduling system.
			return;
		}

		if ( ! $this->settings['enabled'] || $this->settings['interval'] <= 0 ) {
			// Don't run when scheduled backups aren't enabled or the interval is zero or less.
			return;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			// Don't run on AJAX requests.
			return;
		}

		if ( class_exists( 'pb_backupbuddy' ) ) {
			// Don't run when BackupBuddy is active.
			return;
		}


		$next_run = $this->settings['last_run'] + $this->settings['interval'] * DAY_IN_SECONDS;

		if ( $next_run <= ITSEC_Core::get_current_time_gmt() ) {
			add_action( 'init', array( $this, 'do_backup' ), 10, 0 );
		}
	}

	/**
	 * Public function to get lock and call backup.
	 *
	 * Attempts to get a lock to prevent concurrant backups and calls the backup function itself.
	 *
	 * @since 4.0.0
	 *
	 * @param  boolean $one_time whether this is a one time backup
	 *
	 * @return mixed false on error or nothing
	 */
	public function do_backup( $one_time = false ) {
		$itsec_files = ITSEC_Core::get_itsec_files();

		if ( ! $itsec_files->get_file_lock( 'backup' ) ) {
			return new WP_Error( 'itsec-backup-do-backup-already-running', __( 'Unable to create a backup at this time since a backup is currently being created. If you wish to create an additional backup, please wait a few minutes before trying again.', 'it-l10n-ithemes-security-pro' ) );
		}


		ITSEC_Lib::set_minimum_memory_limit( '256M' );
		$this->execute_backup( $one_time );
		$itsec_files->release_file_lock( 'backup' );

		switch ( $this->settings['method'] ) {

			case 0:
				return __( 'Backup complete. The backup was sent to the selected email recipients and was saved locally.', 'it-l10n-ithemes-security-pro' );
			case 1:
				return __( 'Backup complete. The backup was sent to the selected email recipients.', 'it-l10n-ithemes-security-pro' );
			default:
				return __( 'Backup complete. The backup was saved locally.', 'it-l10n-ithemes-security-pro' );

		}
	}

	/**
	 * Executes backup function.
	 *
	 * Handles the execution of database backups.
	 *
	 * @since 4.0.0
	 *
	 * @param bool $one_time whether this is a one-time backup
	 *
	 * @return void
	 */
	private function execute_backup( $one_time = false ) {
		global $wpdb, $itsec_logger;



		require_once( ITSEC_Core::get_core_dir() . 'lib/class-itsec-lib-directory.php' );

		$dir = $this->settings['location'];
		$result = ITSEC_Lib_Directory::create( $dir );

		if ( is_wp_error( $result ) ) {
			return $result;
		} else if ( ! $result ) {
			return new WP_Error( 'itsec-backup-failed-to-create-backup-dir', esc_html__( 'Unable to create the backup directory due to an unknown error.', 'it-l10n-ithemes-security-pro' ) );
		}

		$file = "$dir/backup-" . substr( sanitize_title( get_bloginfo( 'name' ) ), 0, 20 ) . '-' . current_time( 'Ymd-His' ) . '-' . wp_generate_password( 30, false ) . '.sql';

		if ( false === ( $fh = @fopen( $file, 'w' ) ) ) {
			return new WP_Error( 'itsec-backup-failed-to-write-backup-file', esc_html__( 'Unable to write the backup file. This may be due to a permissions or disk space issue.', 'it-l10n-ithemes-security-pro' ) );
		}


		if ( false === $one_time ) {
			ITSEC_Modules::set_setting( 'backup', 'last_run', ITSEC_Core::get_current_time_gmt() );
		}


		if ( $this->settings['all_sites'] ) {
			$tables = $wpdb->get_col( 'SHOW TABLES' );
		} else {
			$tables = $wpdb->get_col( 'SHOW TABLES LIKE "' . $wpdb->base_prefix . '%"' );
		}

		$max_rows_per_query = 1000;

		foreach ( $tables as $table ) {
			$create_table = $wpdb->get_var( "SHOW CREATE TABLE `$table`;", 1 ) . ';' . PHP_EOL . PHP_EOL;
			$create_table = preg_replace( '/^CREATE TABLE /', 'CREATE TABLE IF NOT EXISTS ', $create_table );
			@fwrite( $fh, $create_table );

			if ( in_array( substr( $table, strlen( $wpdb->prefix ) ), $this->settings['exclude'] ) ) {
				// User selected to exclude the data from this table.
				fwrite( $fh, PHP_EOL . PHP_EOL );
				continue;
			}


			$num_fields = count( $wpdb->get_results( "DESCRIBE `$table`;" ) );

			$offset = 0;
			$has_more_rows = true;

			while ( $has_more_rows ) {
				$rows = $wpdb->get_results( "SELECT * FROM `$table` LIMIT $offset, $max_rows_per_query;", ARRAY_N );

				foreach ( $rows as $row ) {
					$sql = "INSERT INTO `$table` VALUES (";

					for ( $j = 0; $j < $num_fields; $j ++ ) {
						if ( isset( $row[$j] ) ) {
							$row[$j] = addslashes( $row[$j] );

							if ( PHP_EOL !== "\n" ) {
								$row[$j] = preg_replace( '#' . PHP_EOL . '#', "\n", $row[$j] );
							}

							$sql .= '"' . $row[$j] . '"';
						} else {
							$sql .= '""';
						}

						if ( $j < ( $num_fields - 1 ) ) {
							$sql .= ',';
						}
					}

					$sql .= ");" . PHP_EOL;

					@fwrite( $fh, $sql );
				}

				if ( count( $rows ) < $max_rows_per_query ) {
					$has_more_rows = false;
				} else {
					$offset += $max_rows_per_query;
				}

			}

			@fwrite( $fh, PHP_EOL . PHP_EOL );

		}

		@fwrite( $fh, PHP_EOL . PHP_EOL );
		@fclose( $fh );

		if ( $this->settings['zip'] ) {
			if ( ! class_exists( 'PclZip' ) ) {
				require( ABSPATH . 'wp-admin/includes/class-pclzip.php' );
			}

			$zip_file = substr( $file, 0, -4 ) . '.zip';
			$pclzip = new PclZip( $zip_file );

			if ( 0 != $pclzip->create( $file, PCLZIP_OPT_REMOVE_PATH, $dir ) ) {
				@unlink( $file );
				$file = $zip_file;
			}
		}

		if ( 2 !== $this->settings['method'] || true === $one_time ) {
			$mail_success = $this->send_mail( $file );
		}

		if ( 1 === $this->settings['method'] ) {
			@unlink( $file );
		} else if ( $this->settings['retain'] > 0 ) {
			$files = scandir( $dir, 1 );

			if ( is_array( $files ) && count( $files ) > 0 ) {
				$count = 0;

				foreach ( $files as $file ) {
					if ( ! strstr( $file, 'backup' ) ) {
						continue;
					}

					if ( $count >= $this->settings['retain'] ) {
						@unlink( trailingslashit( $dir ) . $file );
					}

					$count++;
				}
			}
		}


		$status  = __( 'Success', 'it-l10n-ithemes-security-pro' );
		$details = __( 'saved locally', 'it-l10n-ithemes-security-pro' );

		if ( 0 === $this->settings['method'] ) {
			if ( false === $mail_success ) {
				$status  = __( 'Error', 'it-l10n-ithemes-security-pro' );
				$details = __( 'saved locally but email to backup recipients could not be sent.', 'it-l10n-ithemes-security-pro' );
			} else {
				$details = __( 'emailed to backup recipients and saved locally', 'it-l10n-ithemes-security-pro' );
			}
		} else if ( 1 === $this->settings['method'] ) {
			if ( false === $mail_success ) {
				$status  = __( 'Error', 'it-l10n-ithemes-security-pro' );
				$details = __( 'email to backup recipients could not be sent.', 'it-l10n-ithemes-security-pro' );
			} else {
				$details = __( 'emailed to backup recipients', 'it-l10n-ithemes-security-pro' );
			}
		}

		$data = compact( 'status', 'details' );
		$itsec_logger->log_event( 'backup', 3, array( $data ) );
	}

	private function send_mail( $file ) {
		require_once( ITSEC_Core::get_core_dir() . 'lib/class-itsec-mail.php' );

		$mail = new ITSEC_Mail();
		$mail->add_header( esc_html__( 'Database Backup', 'it-l10n-ithemes-security-pro' ), sprintf( wp_kses( __( 'Site Database Backup for <b>%s</b>', 'it-l10n-ithemes-security-pro' ), array( 'b' => array() ) ), date_i18n( get_option( 'date_format' ) ) ) );
		$mail->add_info_box( esc_html__( 'Attached is the database backup file for your site.', 'it-l10n-ithemes-security-pro' ), 'attachment' );


		$mail->add_section_heading( esc_html__( 'Website', 'it-l10n-ithemes-security-pro' ) );
		$mail->add_text( esc_html( network_home_url() ) );

		$mail->add_section_heading( esc_html__( 'Date', 'it-l10n-ithemes-security-pro' ) );
		$mail->add_text( esc_html( date_i18n( get_option( 'date_format' ) ) ) );

		$mail->add_footer();


		$recipients = ITSEC_Modules::get_setting( 'global', 'backup_email' );
		$mail->set_recipients( $recipients );

		$subject = sprintf( esc_html__( '[%s] Database Backup', 'it-l10n-ithemes-security-pro' ), esc_url( network_home_url() ) );
		$subject = apply_filters( 'itsec_backup_email_subject', $subject );
		$mail->set_subject( $subject, false );

		$mail->add_attachment( $file );

		return $mail->send();
	}

	/**
	 * Register backups for logger.
	 *
	 * Adds the backup module to ITSEC_Logger.
	 *
	 * @since 4.0.0
	 *
	 * @param  array $logger_modules array of logger modules
	 *
	 * @return array                   array of logger modules
	 */
	public function register_logger( $logger_modules ) {

		$logger_modules['backup'] = array(
			'type'     => 'backup',
			'function' => __( 'Database Backup Executed', 'it-l10n-ithemes-security-pro' ),
		);

		return $logger_modules;

	}

}
