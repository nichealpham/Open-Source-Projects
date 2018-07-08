<?php

if ( ! class_exists( 'ITSEC_SSL_Setup' ) ) {

	class ITSEC_SSL_Setup {

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
		public function execute_deactivate() {}

		/**
		 * Execute module uninstall
		 *
		 * @return void
		 */
		public function execute_uninstall() {

			delete_site_option( 'itsec_ssl' );

			delete_metadata( 'post', null, 'itsec_enable_ssl', null, true );
			delete_metadata( 'post', null, 'bwps_enable_ssl', null, true );

		}

		/**
		 * Execute module upgrade
		 *
		 * @return void
		 */
		public function execute_upgrade( $itsec_old_version ) {

			if ( $itsec_old_version < 4000 ) {

				global $itsec_bwps_options;

				$current_options = get_site_option( 'itsec_ssl' );

				// Don't do anything if settings haven't already been set, defaults exist in the module system and we prefer to use those
				if ( false !== $current_options ) {

					$current_options['frontend'] = isset( $itsec_bwps_options['ssl_frontend'] ) ? intval( $itsec_bwps_options['ssl_frontend'] ) : 0;

					update_site_option( 'itsec_ssl', $current_options );
					ITSEC_Response::regenerate_wp_config();

				}
			}

			if ( $itsec_old_version < 4041 ) {
				$current_options = get_site_option( 'itsec_ssl' );

				// If there are no current options, go with the new defaults by not saving anything
				if ( is_array( $current_options ) ) {
					// If anything in this module is being used activate it, otherwise deactivate it
					$activate = false;
					foreach ( $current_options as $on ) {
						if ( $on ) {
							$activate = true;
							break;
						}
					}
					if ( $activate ) {
						ITSEC_Modules::activate( 'ssl' );
					} else {
						ITSEC_Modules::deactivate( 'ssl' );
					}

					// remove 'enabled' which isn't used in the new module
					unset( $current_options['enabled'] );

					ITSEC_Modules::set_settings( 'ssl', $current_options );
				}
			}

		}

	}

}

new ITSEC_SSL_Setup();
