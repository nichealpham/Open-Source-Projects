<?php

if ( ! class_exists( 'ITSEC_Four_Oh_Four_Setup' ) ) {

	class ITSEC_Four_Oh_Four_Setup {

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
		}

		/**
		 * Execute module uninstall
		 *
		 * @return void
		 */
		public function execute_uninstall() {

			$this->execute_deactivate();

			delete_site_option( 'itsec_four_oh_four' );

		}

		/**
		 * Execute module upgrade
		 *
		 * @return void
		 */
		public function execute_upgrade( $itsec_old_version ) {

			if ( $itsec_old_version < 4000 ) {

				global $itsec_bwps_options;

				$current_options = get_site_option( 'itsec_four_oh_four' );

				// Don't do anything if settings haven't already been set, defaults exist in the module system and we prefer to use those
				if ( false !== $current_options ) {

					$current_options['enabled']         = isset( $itsec_bwps_options['id_enabled'] ) && $itsec_bwps_options['id_enabled'] == 1 ? true : false;
					$current_options['check_period']    = isset( $itsec_bwps_options['id_checkinterval'] ) ? intval( $itsec_bwps_options['id_checkinterval'] ) : 5;
					$current_options['error_threshold'] = isset( $itsec_bwps_options['id_threshold'] ) ? intval( $itsec_bwps_options['id_threshold'] ) : 20;

					if ( isset( $itsec_bwps_options['id_whitelist'] ) && ! is_array( $itsec_bwps_options['id_whitelist'] ) && strlen( $itsec_bwps_options['id_whitelist'] ) > 1 ) {

						$current_options['white_list'] .= explode( PHP_EOL, $itsec_bwps_options['id_whitelist'] );

					}

					update_site_option( 'itsec_four_oh_four', $current_options );
				}
			}

			if ( $itsec_old_version < 4041 ) {
				$current_options = get_site_option( 'itsec_four_oh_four' );

				// If there are no current options, go with the new defaults by not saving anything
				if ( is_array( $current_options ) ) {
					// Make sure the new module is properly activated or deactivated
					if ( $current_options['enabled'] ) {
						ITSEC_Modules::activate( '404-detection' );
					} else {
						ITSEC_Modules::deactivate( '404-detection' );
					}

					// remove 'enabled' which isn't use in the new module
					unset( $current_options['enabled'] );
					ITSEC_Modules::set_settings( '404-detection', $current_options );
				}
			}

		}

	}

}

new ITSEC_Four_Oh_Four_Setup();
