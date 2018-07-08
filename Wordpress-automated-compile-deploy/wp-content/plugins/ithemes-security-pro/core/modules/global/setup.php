<?php

if ( ! class_exists( 'ITSEC_Global_Setup' ) ) {

	class ITSEC_Global_Setup {

		public function __construct() {

			add_action( 'itsec_modules_do_plugin_activation',   array( $this, 'execute_activate'   )          );
			add_action( 'itsec_modules_do_plugin_deactivation', array( $this, 'execute_deactivate' )          );
			add_action( 'itsec_modules_do_plugin_uninstall',    array( $this, 'execute_uninstall'  )          );
			add_action( 'itsec_modules_do_plugin_upgrade',      array( $this, 'execute_upgrade'    ), null, 2 );

		}

		/**
		 * Execute module activation.
		 *
		 * @since 4.0
		 *
		 * @return void
		 */
		public function execute_activate() {
		}

		/**
		 * Execute module deactivation
		 *
		 * @return void
		 */
		public function execute_deactivate() {
		}

		/**
		 * Execute module uninstall
		 *
		 * @return void
		 */
		public function execute_uninstall() {
		}

		/**
		 * Execute module upgrade
		 *
		 * @return void
		 */
		public function execute_upgrade( $itsec_old_version ) {
			if ( $itsec_old_version < 4040 ) {
				$options = get_site_option( 'itsec_global' );

				if ( $options['log_info'] ) {
					$new_log_info = substr( sanitize_title( get_bloginfo( 'name' ) ), 0, 20 ) . '-' . wp_generate_password( 30, false );
					$old_file = path_join( $options['log_location'], 'event-log-' . $options['log_info'] . '.log' );
					$new_file = path_join( $options['log_location'], 'event-log-' . $new_log_info . '.log' );

					// If the file exists already, don't update the location unless we successfully move it.
					if ( file_exists( $old_file ) && rename( $old_file, $new_file ) ) {
						$options['log_info'] = $new_log_info;
						update_site_option( 'itsec_global', $options );
					}
				}

				// Make sure we have an index files to block directory listing in logs directory
				if ( is_dir( $options['log_location'] ) && ! file_exists( path_join( $options['log_location'], 'index.php' ) ) ) {
					file_put_contents( path_join( $options['log_location'], 'index.php' ), "<?php\n// Silence is golden." );
				}
			}

			if ( $itsec_old_version < 4041 ) {
				$current_options = get_site_option( 'itsec_global' );

				// If there are no current options, go with the new defaults by not saving anything
				if ( is_array( $current_options ) ) {
					// log_type used to be 0 for database, 1 for file, 2 for both
					switch ( $current_options['log_type'] ) {
						case 2:
							$current_options['log_type'] = 'both';
							break;
						case 1:
							$current_options['log_type'] = 'file';
							break;
						default:
							$current_options['log_type'] = 'database';
					}

					if ( isset( $current_options['log_location'] ) && ! is_dir( $current_options['log_location'] ) ) {
						unset( $current_options['log_location'] );
					}

					if ( isset( $current_options['nginx_file'] ) && ! is_dir( dirname( $current_options['nginx_file'] ) ) ) {
						unset( $current_options['nginx_file'] );
					}

					$settings = ITSEC_Modules::get_defaults( 'global' );

					foreach ( $settings as $index => $setting ) {
						if ( isset( $current_options[ $index ] ) ) {
							$settings[ $index ] = $current_options[ $index ];
						}
					}

					ITSEC_Modules::set_settings( 'global', $settings );
				}
			}

			if ( $itsec_old_version < 4059 ) {
				$message_queue = get_site_option( 'itsec_message_queue' );

				if ( false !== $message_queue ) {
					if ( isset( $message_queue['last_sent'] ) ) {
						ITSEC_Modules::set_setting( 'global', 'digest_last_sent', $message_queue['last_sent'] );
					}

					if ( isset( $message_queue['messages'] ) ) {
						ITSEC_Modules::set_setting( 'global', 'digest_messages', $message_queue['messages'] );
					}

					delete_site_option( 'itsec_message_queue' );
				}
			}

			if ( $itsec_old_version < 4064 ) {
				delete_site_option( 'itsec_global' );
			}
		}

	}

}

new ITSEC_Global_Setup();
