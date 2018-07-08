<?php

if ( ! class_exists( 'ITSEC_Ban_Users_Setup' ) ) {

	class ITSEC_Ban_Users_Setup {

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

			delete_site_option( 'itsec_ban_users' );

		}

		/**
		 * Execute module upgrade
		 *
		 * @return void
		 */
		public function execute_upgrade( $itsec_old_version ) {

			if ( $itsec_old_version < 4000 ) {

				global $itsec_bwps_options;

				$current_options = get_site_option( 'itsec_ban_users' );

				// Don't do anything if settings haven't already been set, defaults exist in the module system and we prefer to use those
				if ( false !== $current_options ) {

					$current_options['enabled'] = isset( $itsec_bwps_options['bu_enabled'] ) && $itsec_bwps_options['bu_enabled'] == 1 ? true : false;
					$current_options['default'] = isset( $itsec_bwps_options['bu_blacklist'] ) && $itsec_bwps_options['bu_blacklist'] == 1 ? true : false;

					if ( isset( $itsec_bwps_options['bu_banlist'] ) && ! is_array( $itsec_bwps_options['bu_banlist'] ) && strlen( $itsec_bwps_options['bu_banlist'] ) > 1 ) {

						$raw_hosts = explode( PHP_EOL, $itsec_bwps_options['bu_banlist'] );

						foreach ( $raw_hosts as $host ) {

							if ( strlen( $host ) > 1 ) {
								$current_options['host_list'][] = $host;
							}

						}

					}

					if ( isset( $itsec_bwps_options['bu_banagent'] ) && ! is_array( $itsec_bwps_options['bu_banagent'] ) && strlen( $itsec_bwps_options['bu_banagent'] ) > 1 ) {

						$current_options['agent_list'] = explode( PHP_EOL, $itsec_bwps_options['bu_banagent'] );

						$raw_agents = explode( PHP_EOL, $itsec_bwps_options['bu_banagent'] );

						foreach ( $raw_agents as $agent ) {

							if ( strlen( $agent ) > 1 ) {
								$current_options['agent_list'][] = $agent;
							}

						}

					}

					update_site_option( 'itsec_ban_users', $current_options );
				}
			}

			if ( $itsec_old_version < 4041 ) {
				$current_options = get_site_option( 'itsec_ban_users' );

				// If there are no current options, go with the new defaults by not saving anything
				if ( is_array( $current_options ) ) {
					$itsec_modules = ITSEC_Modules::get_instance();

					// 'enable_ban_lists' was previously just 'enabled'
					// Make sure the new module is properly activated or deactivated
					if ( $current_options['enabled'] ) {
						ITSEC_Modules::activate( 'backup' );
						$current_options['enable_ban_lists'] = true;
					} else {
						ITSEC_Modules::deactivate( 'backup' );
						$current_options['enable_ban_lists'] = false;
					}
					unset( $current_options['enabled'] );

					// Filter out invalid IPs
					$current_options['host_list'] = array_map( 'trim', $current_options['host_list'] );

					if ( ! class_exists( 'ITSEC_Lib_IP_Tools' ) ) {
						require_once( ITSEC_Core::get_core_dir() . '/lib/class-itsec-lib-ip-tools.php' );
					}

					foreach ( $current_options['host_list'] as $index => $ip ) {
						if ( '' === $ip || false === ITSEC_Lib_IP_Tools::ip_wild_to_ip_cidr( $ip ) ) {
							unset( $current_options['host_list'][ $index ] );
						}
					}

					$itsec_modules->set_settings( 'ban-users', $current_options );
				}
			}

			if ( $itsec_old_version < 4069 ) {
				delete_site_option( 'itsec_ban_users' );
			}
		}

	}

}

new ITSEC_Ban_Users_Setup();
