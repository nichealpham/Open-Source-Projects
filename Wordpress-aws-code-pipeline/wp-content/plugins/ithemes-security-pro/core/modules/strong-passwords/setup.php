<?php

if ( ! class_exists( 'ITSEC_Strong_Passwords_Setup' ) ) {

	class ITSEC_Strong_Passwords_Setup {

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

			delete_site_option( 'itsec_strong_passwords' );

		}

		/**
		 * Execute module upgrade
		 *
		 * @return void
		 */
		public function execute_upgrade( $itsec_old_version ) {

			if ( $itsec_old_version < 4000 ) {

				global $itsec_bwps_options;

				$current_options = get_site_option( 'itsec_strong_passwords' );

				// Don't do anything if settings haven't already been set, defaults exist in the module system and we prefer to use those
				if ( false !== $current_options ) {

					$current_options['enabled'] = isset( $itsec_bwps_options['st_enablepassword'] ) && $itsec_bwps_options['st_enablepassword'] == 1 ? true : false;
					$current_options['roll']    = isset( $itsec_bwps_options['st_passrole'] ) ? $itsec_bwps_options['st_passrole'] : 'administrator';

					update_site_option( 'itsec_strong_passwords', $current_options );
				}

			}

			if ( $itsec_old_version < 4041 ) {
				$current_options = get_site_option( 'itsec_strong_passwords' );

				// If there are no current options, go with the new defaults by not saving anything
				if ( is_array( $current_options ) ) {
					// Make sure the new module is properly activated or deactivated
					if ( $current_options['enabled'] ) {
						ITSEC_Modules::activate( 'strong-passwords' );
					} else {
						ITSEC_Modules::deactivate( 'strong-passwords' );
					}

					$settings = array( 'role' => $current_options['roll'] );

					ITSEC_Modules::set_settings( 'strong-passwords', $settings );
				}
			}

		}

	}

}

new ITSEC_Strong_Passwords_Setup();
