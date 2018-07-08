<?php

if ( ! class_exists( 'ITSEC_Recaptcha_Setup' ) ) {

	class ITSEC_Recaptcha_Setup {

		public function __construct() {

			add_action( 'itsec_modules_do_plugin_activation',   array( $this, 'execute_activate'   )          );
			add_action( 'itsec_modules_do_plugin_deactivation', array( $this, 'execute_deactivate' )          );
			add_action( 'itsec_modules_do_plugin_uninstall',    array( $this, 'execute_uninstall'  )          );
			add_action( 'itsec_modules_do_plugin_upgrade',      array( $this, 'execute_upgrade'    ), null, 2 );

		}

		/**
		 * Execute module activation.
		 *
		 * @since 1.13
		 *
		 * @return void
		 */
		public function execute_activate() {
		}

		/**
		 * Execute module deactivation
		 *
		 * @since 1.13
		 *
		 * @return void
		 */
		public function execute_deactivate() {
		}

		/**
		 * Execute module uninstall
		 *
		 * @since 1.13
		 *
		 * @return void
		 */
		public function execute_uninstall() {

			$this->execute_deactivate();

			delete_site_option( 'itsec_recaptcha' );

		}

		/**
		 * Execute module upgrade
		 *
		 * @since 1.13
		 *
		 * @return void
		 */
		public function execute_upgrade( $itsec_old_version ) {

			if ( $itsec_old_version < 4041 ) {
				$current_options = get_site_option( 'itsec_recaptcha' );

				// If there are no current options, go with the new defaults by not saving anything
				if ( is_array( $current_options ) ) {
					// Make sure the new module is properly activated or deactivated
					if ( $current_options['enabled'] ) {
						ITSEC_Modules::activate( 'recaptcha' );
					} else {
						ITSEC_Modules::deactivate( 'recaptcha' );
					}

					$defaults = ITSEC_Modules::get_defaults( 'recaptcha' );
					$options = $defaults;

					foreach ( $defaults as $name => $value ) {
						if ( isset( $current_options[$name] ) ) {
							$options[$name] = $current_options[$name];
						}
					}


					ITSEC_Modules::set_settings( 'recaptcha', $options );
				}
			}

		}

	}

}

new ITSEC_Recaptcha_Setup();
