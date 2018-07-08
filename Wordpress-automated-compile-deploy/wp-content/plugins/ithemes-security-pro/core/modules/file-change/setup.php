<?php

if ( ! class_exists( 'ITSEC_File_Change_Setup' ) ) {

	class ITSEC_File_Change_Setup {

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

			wp_clear_scheduled_hook( 'itsec_file_check' );

		}

		/**
		 * Execute module uninstall
		 *
		 * @return void
		 */
		public function execute_uninstall() {

			$this->execute_deactivate();

			delete_site_option( 'itsec_file_change' );
			delete_site_option( 'itsec_local_file_list' );
			delete_site_option( 'itsec_local_file_list_0' );
			delete_site_option( 'itsec_local_file_list_1' );
			delete_site_option( 'itsec_local_file_list_2' );
			delete_site_option( 'itsec_local_file_list_3' );
			delete_site_option( 'itsec_local_file_list_4' );
			delete_site_option( 'itsec_local_file_list_5' );
			delete_site_option( 'itsec_local_file_list_6' );
			delete_site_option( 'itsec_file_change_warning' );

		}

		/**
		 * Execute module upgrade
		 *
		 * @return void
		 */
		public function execute_upgrade( $itsec_old_version ) {

			if ( $itsec_old_version < 4000 ) {

				global $itsec_bwps_options;

				$current_options = get_site_option( 'itsec_file_change' );

				// Don't do anything if settings haven't already been set, defaults exist in the module system and we prefer to use those
				if ( false !== $current_options ) {

					$current_options['enabled']      = isset( $itsec_bwps_options['id_fileenabled'] ) && $itsec_bwps_options['id_fileenabled'] == 1 ? true : false;
					$current_options['email']        = isset( $itsec_bwps_options['id_fileemailnotify'] ) && $itsec_bwps_options['id_fileemailnotify'] == 0 ? false : true;
					$current_options['notify_admin'] = isset( $itsec_bwps_options['id_filedisplayerror'] ) && $itsec_bwps_options['id_filedisplayerror'] == 0 ? false : true;
					$current_options['method']       = isset( $itsec_bwps_options['id_fileincex'] ) && $itsec_bwps_options['id_fileincex'] == 0 ? false : true;

					if ( isset( $itsec_bwps_options['id_specialfile'] ) && ! is_array( $itsec_bwps_options['id_specialfile'] ) && strlen( $itsec_bwps_options['id_specialfile'] ) > 1 ) {

						$current_options['file_list'] .= explode( PHP_EOL, $itsec_bwps_options['id_specialfile'] );

					}

					update_site_option( 'itsec_file_change', $current_options );

				}
			}

			if ( $itsec_old_version < 4028 ) {

				if ( ! is_multisite() ) {

					$options = array(
						'itsec_local_file_list',
						'itsec_local_file_list_0',
						'itsec_local_file_list_1',
						'itsec_local_file_list_2',
						'itsec_local_file_list_3',
						'itsec_local_file_list_4',
						'itsec_local_file_list_5',
						'itsec_local_file_list_6',
					);

					foreach ( $options as $option ) {

						$list = get_site_option( $option );

						if ( $list !== false ) {

							delete_site_option( $option );
							add_option( $option, $list, '', 'no' );

						}

					}

				}

			}

			if ( $itsec_old_version < 4041 ) {
				$current_options = get_site_option( 'itsec_file_change' );

				// If there are no current options, go with the new defaults by not saving anything
				if ( is_array( $current_options ) ) {
					// Make sure the new module is properly activated or deactivated
					if ( $current_options['enabled'] ) {
						ITSEC_Modules::activate( 'file-change' );
					} else {
						ITSEC_Modules::deactivate( 'file-change' );
					}

					// remove 'enabled' which isn't use in the new module
					unset( $current_options['enabled'] );

					// This used to be boolean. Attempt to migrate to new string, falling back to default
					if ( ! is_array( $current_options['method'] ) ) {
						$current_options['method'] = ( $current_options['method'] )? 'exclude' : 'include';
					} elseif ( ! in_array( $current_options['method'], array( 'include', 'exclude' ) ) ) {
						$current_options['method'] = 'exclude';
					}

					ITSEC_Modules::set_settings( 'file-change', $current_options );
				}
			}

		}

	}

}

new ITSEC_File_Change_Setup();
