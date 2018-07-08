<?php

if ( ! class_exists( 'ITSEC_Two_Factor_Setup' ) ) {

	class ITSEC_Two_Factor_Setup {

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

			delete_site_option( 'itsec_two_factor' );

			delete_metadata( 'user', null, 'itsec_two_factor_enabled', null, true );
			delete_metadata( 'user', null, 'itsec_two_factor_description', null, true );
			delete_metadata( 'user', null, 'itsec_two_factor_key', null, true );
			delete_metadata( 'user', null, 'itsec_two_factor_use_app', null, true );
			delete_metadata( 'user', null, 'itsec_two_factor_app_pass', null, true );
			delete_metadata( 'user', null, 'itsec_two_factor_last_login', null, true );
			delete_metadata( 'user', null, 'itsec_two_factor_override', null, true );
			delete_metadata( 'user', null, 'itsec_two_factor_override_expires', null, true );

		}

		/**
		 * Execute module upgrade
		 *
		 * @return void
		 */
		public function execute_upgrade( $old, $new ) {
			// Upgrade to new provider module system
			if ( $old < 4038 ) {

				global $wpdb;
				$settings = get_site_option( 'itsec_two_factor' );
				// If two-factor wasn't enabled or already has providers for some reason, don't worry about upgrading it
				if ( ! isset( $settings['enabled'] ) || ! $settings['enabled'] || ! empty( $settings['enabled-providers'] ) ) {
					return;
				}
				$settings = array(
					'enabled' => true,
					'enabled-providers' => array(
						'Two_Factor_Totp',
						'Two_Factor_Backup_Codes'
					)
				);
				// Instantiate enabled providers so we can handle all the updating
				$helper = ITSEC_Two_Factor_Helper::get_instance();
				$helper->get_enabled_provider_instances();

				/**
				 * Migrate all app passes to new system
				 */
				$meta_results = $wpdb->get_results( "SELECT * FROM `{$wpdb->usermeta}` WHERE `meta_key` = 'itsec_two_factor_app_pass'" );

				foreach ( $meta_results as $user_meta ) {
					// New Style Passwords, in case any exist from other compatible plugins
					$passwords = Application_Passwords::get_user_application_passwords( $user_meta->user_id );
					if ( ! $passwords ) {
						$passwords = array();
					}

					$app_passwords = maybe_unserialize( $user_meta->meta_value );
					if ( is_array( $app_passwords ) ) {
						foreach ( $app_passwords as $name => $app_password ) {
							$passwords[]  = array(
								'name'      => $name,
								'password'  => $app_password,
								'created'   => time(),
								'last_used' => null,
								'last_ip'   => null,
							);
						}
					}
					// Store them all
					Application_Passwords::set_user_application_passwords( $user_meta->user_id, $passwords );
					delete_user_meta( $user_meta->user_id, 'itsec_two_factor_app_pass' );

				}

				/**
				 * Enable the TOTP provider for any user that is already using two-factor
				 */
				$meta_results = $wpdb->get_results( "SELECT * FROM `{$wpdb->usermeta}` WHERE `meta_key` = 'itsec_two_factor_enabled'" );
				foreach ( $meta_results as $user_meta ) {
					// Out with the old
					delete_user_meta( $user_meta->user_id, 'itsec_two_factor_enabled' );
					// Enable TOTP
					update_usermeta( $user_meta->user_id, '_two_factor_enabled_providers', array( 'Two_Factor_Totp' ) );
					// Make TOTP default
					update_usermeta( $user_meta->user_id, '_two_factor_provider', 'Two_Factor_Totp' );
				}

				// Change meta key from old 'itsec_two_factor_key' to new '_two_factor_totp_key'
				$wpdb->update( $wpdb->usermeta, array( 'meta_key' => '_two_factor_totp_key' ), array( 'meta_key' => 'itsec_two_factor_key' ) );
			}

			if ( $old < 4041 ) {
				if ( ! isset( $settings ) ) {
					$settings = get_site_option( 'itsec_two_factor' );
				}

				// If there are no current options, go with the new defaults by not saving anything
				if ( is_array( $settings ) ) {
					// Make sure the new module is properly activated or deactivated
					if ( empty( $settings['enabled-providers'] ) ) {
						ITSEC_Modules::deactivate( 'two-factor' );
					} else {
						ITSEC_Modules::activate( 'two-factor' );
					}

					$defaults = ITSEC_Modules::get_defaults( 'two-factor' );
					$normalized_settings = $defaults;

					foreach ( $defaults as $name => $value ) {
						if ( isset( $settings[$name] ) ) {
							$normalized_settings[$name] = $settings[$name];
						}
					}

					$settings = $normalized_settings;
				}
			}

			if ( $old < 4056 ) {
				delete_site_option( 'itsec_two_factor' );

				if ( ! isset( $settings ) ) {
					$settings = ITSEC_Modules::get_settings( 'two-factor' );
				}

				$defaults = ITSEC_Modules::get_defaults( 'two-factor' );

				if ( isset( $settings['enabled-providers'] ) && $settings['enabled-providers'] !== $defaults['custom_available_methods'] ) {
					$settings['available_methods'] = 'custom';
					$settings['custom_available_methods'] = $settings['enabled-providers'];
				} else {
					$settings['available_methods'] = $defaults['available_methods'];
					$settings['custom_available_methods'] = $defaults['custom_available_methods'];
				}

				unset( $settings['enabled-providers'] );
			}


			if ( isset( $settings ) ) {
				ITSEC_Modules::set_settings( 'two-factor', $settings );
			}
		}

	}

}

new ITSEC_Two_Factor_Setup();
