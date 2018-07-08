<?php

if ( ! class_exists( 'ITSEC_Version_Management_Setup' ) ) {
	final class ITSEC_Version_Management_Setup {
		public function __construct() {
			add_action( 'itsec_modules_do_plugin_activation', array( $this, 'execute_activate' ) );
			add_action( 'itsec_modules_do_plugin_deactivation', array( $this, 'execute_deactivate' ) );
			add_action( 'itsec_modules_do_plugin_uninstall', array( $this, 'execute_uninstall' ) );
		}

		/**
		 * Execute module activation.
		 *
		 * @since 2.9.0
		 *
		 * @return void
		 */
		public function execute_activate() {
			require_once( dirname( __FILE__ ) . '/class-itsec-version-management.php' );

			ITSEC_Version_Management::activate();
		}

		/**
		 * Execute module deactivation
		 *
		 * @since 2.9.0
		 *
		 * @return void
		 */
		public function execute_deactivate() {
			delete_site_option( 'itsec_vm_wp_releases' );

			require_once( dirname( __FILE__ ) . '/class-itsec-version-management.php' );
			ITSEC_Version_Management::deactivate();
		}

		/**
		 * Execute module uninstall
		 *
		 * @since 2.9.0
		 *
		 * @return void
		 */
		public function execute_uninstall() {
			$this->execute_deactivate();
		}
	}
}

new ITSEC_Version_Management_Setup();
