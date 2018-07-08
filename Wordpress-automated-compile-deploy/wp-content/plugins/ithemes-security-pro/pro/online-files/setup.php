<?php

if ( ! class_exists( 'ITSEC_Online_Files_Setup' ) ) {

	class ITSEC_Online_Files_Setup {

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

			global $wpdb;

			$wpdb->query( "DELETE FROM `" . $wpdb->base_prefix . "options` WHERE `option_name` LIKE ('%_itsec_plugin_hashes%')" );
			$wpdb->query( "DELETE FROM `" . $wpdb->base_prefix . "options` WHERE `option_name` LIKE ('%_itsec_theme_hashes%')" );

			delete_site_transient( 'itsec_online_files_remote_checksums' );
			delete_site_transient( 'itsec_online_files_core_hashes' );

		}

		/**
		 * Execute module uninstall
		 *
		 * @return void
		 */
		public function execute_uninstall() {

			$this->execute_deactivate();
			delete_site_option( 'itsec_online_files' );

		}

		/**
		 * Execute module upgrade
		 *
		 * @return void
		 */
		public function execute_upgrade( $itsec_old_version ) {

			if ( $itsec_old_version < 4036 ) {

				delete_site_transient( 'itsec_online_files_remote_checksums' );

			}

			if ( $itsec_old_version < 4041 ) {
				$current_options = get_site_option( 'itsec_online_files' );
				// If there are no current options, go with the new defaults by not saving anything
				if ( is_array( $current_options ) ) {
					ITSEC_Modules::set_settings( 'online-files', array( 'compare_file_hashes' => (bool) $current_options['enabled'] ) );
				}
			}

		}

	}

}

new ITSEC_Online_Files_Setup();
