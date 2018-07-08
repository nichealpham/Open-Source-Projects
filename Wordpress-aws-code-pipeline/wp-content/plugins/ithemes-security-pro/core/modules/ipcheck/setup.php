<?php

if ( ! class_exists( 'ITSEC_IPCheck_Setup' ) ) {

	class ITSEC_IPCheck_Setup {

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

			global $wpdb;

			$wpdb->query( "DELETE FROM `" . $wpdb->base_prefix . "options` WHERE `option_name` LIKE ('%_itsec_ip_cache%')" );

		}

		/**
		 * Execute module uninstall
		 *
		 * @return void
		 */
		public function execute_uninstall() {

			$this->execute_deactivate();

			delete_site_option( 'itsec_ipcheck' );

		}

		/**
		 * Execute module upgrade
		 *
		 * @return void
		 */
		public function execute_upgrade( $itsec_old_version ) {
			if ( $itsec_old_version < 4041 ) {
				$current_options = get_site_option( 'itsec_ipcheck' );

				// If there are no current options, go with the new defaults by not saving anything
				if ( is_array( $current_options ) ) {
					$settings = ITSEC_Modules::get_defaults( 'network-brute-force' );

					if ( isset( $current_options['api_ban'] ) ) {
						$settings['enable_ban'] = $current_options['api_ban'];
					}

					// Make sure the new module is properly activated or deactivated
					if ( $settings['enable_ban'] ) {
						ITSEC_Modules::activate( 'network-brute-force' );
					} else {
						ITSEC_Modules::deactivate( 'network-brute-force' );
					}

					if ( ! empty( $current_options['api_key'] ) ) {
						$settings['api_key'] = $current_options['api_key'];
						// Don't ask users to sign up if they already have
						$settings['api_nag'] = false;
					}

					if ( ! empty( $current_options['api_s'] ) ) {
						$settings['api_secret'] = $current_options['api_s'];
					}

					if ( ! empty( $current_options['optin'] ) ) {
						$settings['updates_optin'] = $current_options['optin'];
					}

					ITSEC_Modules::set_settings( 'network-brute-force', $settings );
				}
			}

			if ( $itsec_old_version < 4056 ) {
				delete_site_option( 'itsec_ipcheck' );
			}
		}

	}

}

new ITSEC_IPCheck_Setup();
