<?php

if ( ! class_exists( 'ITSEC_Hide_Backend_Setup' ) ) {

	class ITSEC_Hide_Backend_Setup {

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

			delete_site_transient( 'ITSEC_SHOW_HIDE_BACKEND_TOOLTIP' );
			delete_site_option( 'itsec_hide_backend_new_slug' );

		}

		/**
		 * Execute module uninstall
		 *
		 * @return void
		 */
		public function execute_uninstall() {

			$this->execute_deactivate();

			delete_site_option( 'itsec_hide_backend' );

		}

		/**
		 * Execute module upgrade
		 *
		 * @return void
		 */
		public function execute_upgrade( $itsec_old_version ) {

			if ( $itsec_old_version < 4000 ) {

				global $itsec_bwps_options;

				$current_options = get_site_option( 'itsec_hide_backend' );

				if ( false !== $current_options ) {

					$current_options['enabled']  = isset( $itsec_bwps_options['hb_enabled'] ) && $itsec_bwps_options['hb_enabled'] == 1 ? true : false;
					$current_options['register'] = isset( $itsec_bwps_options['hb_register'] ) ? sanitize_text_field( $itsec_bwps_options['hb_register'] ) : 'wp-register.php';

					if ( $current_options['enabled'] === true ) {

						$current_options['show-tooltip'] = true;
						set_site_transient( 'ITSEC_SHOW_HIDE_BACKEND_TOOLTIP', true, 600 );

					} else {

						$current_options['show-tooltip'] = false;

					}

					$forbidden_slugs = array( 'admin', 'login', 'wp-login.php', 'dashboard', 'wp-admin', '' );

					if ( isset( $itsec_bwps_options['hb_login'] ) && ! in_array( trim( $itsec_bwps_options['hb_login'] ), $forbidden_slugs ) ) {

						$current_options['slug'] = $itsec_bwps_options['hb_login'];
						set_site_transient( 'ITSEC_SHOW_HIDE_BACKEND_TOOLTIP', true, 600 );

					} else {

						$current_options['enabled'] = false;
						set_site_transient( 'ITSEC_SHOW_HIDE_BACKEND_TOOLTIP', true, 600 );

					}

					update_site_option( 'itsec_hide_backend', $current_options );
					ITSEC_Response::regenerate_server_config();
				}
			}

			if ( $itsec_old_version < 4027 ) {

				$current_options = get_site_option( 'itsec_hide_backend' );

				if ( isset( $current_options['enabled'] ) && $current_options['enabled'] === true ) {

					add_action( 'admin_init', array( $this, 'flush_rewrite_rules' ) );

					ITSEC_Response::regenerate_server_config();

				}

			}

			if ( $itsec_old_version < 4041 ) {
				$current_options = get_site_option( 'itsec_hide_backend' );

				// If there are no current options, go with the new defaults by not saving anything
				if ( is_array( $current_options ) ) {
					// remove 'show-tooltip' which is old and not used in the new module
					unset( $current_options['show-tooltip'] );
					ITSEC_Modules::set_settings( 'hide-backend', $current_options );
				}
			}

			if ( $itsec_old_version < 4070 ) {
				delete_site_option( 'itsec_hide_backend' );
			}
		}

		/**
		 * Flush rewrite rules.
		 *
		 * @since 4.0.6
		 *
		 * @return void
		 */
		public function flush_rewrite_rules() {
			$config_file = ITSEC_Lib::get_htaccess();

			//Make sure we can write to the file
			$perms = substr( sprintf( '%o', @fileperms( $config_file ) ), - 4 );
			@chmod( $config_file, 0664 );

			flush_rewrite_rules();

			@chmod( $config_file, $perms );
		}

	}

}

new ITSEC_Hide_Backend_Setup();
