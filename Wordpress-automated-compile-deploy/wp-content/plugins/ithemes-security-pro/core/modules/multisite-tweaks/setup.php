<?php

if ( ! class_exists( 'ITSEC_Multisite_Tweaks_Setup' ) ) {

	class ITSEC_Multisite_Tweaks_Setup {

		private
			$defaults;

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

			//Reset recommended file permissions
			@chmod( ITSEC_Lib::get_htaccess(), 0644 );
			@chmod( ITSEC_Lib::get_config(), 0644 );

		}

		/**
		 * Execute module uninstall
		 *
		 * @return void
		 */
		public function execute_uninstall() {

			$this->execute_deactivate();

			delete_site_option( 'itsec_tweaks' );

		}

		/**
		 * Execute module upgrade
		 *
		 * @since 4.0
		 *
		 * @return void
		 */
		public function execute_upgrade( $itsec_old_version ) {

			if ( $itsec_old_version < 4000 ) {

				global $itsec_bwps_options;

				ITSEC_Lib::create_database_tables();

				$current_options = get_site_option( 'itsec_tweaks' );

				// Don't do anything if settings haven't already been set, defaults exist in the module system and we prefer to use those
				if ( false !== $current_options ) {
					$current_options['theme_updates']            = isset( $itsec_bwps_options['st_themenot'] ) && $itsec_bwps_options['st_themenot'] == 1 ? true : false;
					$current_options['plugin_updates']           = isset( $itsec_bwps_options['st_pluginnot'] ) && $itsec_bwps_options['st_pluginnot'] == 1 ? true : false;
					$current_options['core_updates']             = isset( $itsec_bwps_options['st_corenot'] ) && $itsec_bwps_options['st_corenot'] == 1 ? true : false;

					update_site_option( 'itsec_tweaks', $current_options );
					ITSEC_Response::regenerate_server_config();
					ITSEC_Response::regenerate_wp_config();
				}

			}

			if ( $itsec_old_version < 4035 ) {
				ITSEC_Response::regenerate_server_config();
			}

			if ( $itsec_old_version < 4041 ) {
				$current_options = get_site_option( 'itsec_tweaks' );

				// If there are no current options, go with the new defaults by not saving anything
				if ( is_array( $current_options ) ) {
					$new_module_settings = ITSEC_Modules::get_settings( 'multisite-tweaks' );

					// Reduce to only settings in new module
					$current_options = array_intersect_key( $current_options, $new_module_settings );

					// Use new module settings as defaults for any missing settings
					$current_options = array_merge( $new_module_settings, $current_options );

					// If anything in this module is being used activate it, otherwise deactivate it
					$activate = false;
					foreach ( $current_options as $on ) {
						if ( $on ) {
							$activate = true;
							break;
						}
					}
					if ( $activate ) {
						ITSEC_Modules::activate( 'multisite-tweaks' );
					} else {
						ITSEC_Modules::deactivate( 'multisite-tweaks' );
					}

					ITSEC_Modules::set_settings( 'multisite-tweaks', $current_options );
				}
			}

		}

	}

}

new ITSEC_Multisite_Tweaks_Setup();
