<?php

if ( ! class_exists( 'ITSEC_Malware_Setup' ) ) {

	class ITSEC_Malware_Setup {

		private
			$defaults;

		public function __construct() {

			add_action( 'itsec_modules_do_plugin_activation',   array( $this, 'execute_activate'   )          );
			add_action( 'itsec_modules_do_plugin_deactivation', array( $this, 'execute_deactivate' )          );
			add_action( 'itsec_modules_do_plugin_uninstall',    array( $this, 'execute_uninstall'  )          );
			add_action( 'itsec_modules_do_plugin_upgrade',      array( $this, 'execute_upgrade'    ), null, 2 );

			$this->defaults = array(
				'enabled' => false,
				'api_key' => '',
			);

		}

		/**
		 * Execute module activation.
		 *
		 * @since 4.0
		 *
		 * @return void
		 */
		public function execute_activate() {}

		/**
		 * Execute module deactivation
		 *
		 * @return void
		 */
		public function execute_deactivate() {
			delete_site_transient( 'itsec_cached_sucuri_scan' );
		}

		/**
		 * Execute module uninstall
		 *
		 * @return void
		 */
		public function execute_uninstall() {

			$this->execute_deactivate();

			delete_site_option( 'itsec_malware' );

		}

		/**
		 * Execute module upgrade
		 *
		 * @return void
		 */
		public function execute_upgrade( $itsec_old_version ) {
			if ( $itsec_old_version < 4065 ) {
				delete_site_option( 'itsec_malware' );
			}
		}

	}

}

new ITSEC_Malware_Setup();
