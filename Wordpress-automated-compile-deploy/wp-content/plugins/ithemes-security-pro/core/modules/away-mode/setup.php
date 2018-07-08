<?php

if ( ! class_exists( 'ITSEC_Away_Mode_Setup' ) ) {

	class ITSEC_Away_Mode_Setup {

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

			delete_site_option( 'itsec_away_mode_sync_override' );
			delete_site_transient( 'itsec_away' );
			delete_site_transient( 'itsec_away_mode' );

		}

		/**
		 * Execute module uninstall
		 *
		 * @return void
		 */
		public function execute_uninstall() {

			$this->execute_deactivate();

			delete_site_option( 'itsec_away_mode' );

		}

		/**
		 * Execute module upgrade
		 *
		 * @return void
		 */
		public function execute_upgrade( $itsec_old_version ) {

			if ( $itsec_old_version < 4000 ) {

				global $itsec_bwps_options;

				$current_options = get_site_option( 'itsec_away_mode' );
				$current_time    = ITSEC_Core::get_current_time();

				// Don't do anything if settings haven't already been set, defaults exist in the module system and we prefer to use those
				if ( false !== $current_options ) {
					$current_options['enabled'] = isset( $itsec_bwps_options['am_enabled'] ) && $itsec_bwps_options['am_enabled'] == 1 ? true : false;
					$current_options['type']    = isset( $itsec_bwps_options['am_type'] ) && $itsec_bwps_options['am_type'] == 1 ? 1 : 2;

					if ( isset( $itsec_bwps_options['am_startdate'] ) && isset( $itsec_bwps_options['am_starttime'] ) ) {

						$current_options['start'] = strtotime( date( 'Y-m-d', $itsec_bwps_options['am_startdate'] ) ) + intval( $itsec_bwps_options['am_starttime'] );

					} elseif ( isset( $current_options['am_starttime'] ) && $current_options['type'] == 1 ) {

						$current_options['start'] = strtotime( date( 'Y-m-d', $current_time ) ) + intval( $itsec_bwps_options['am_starttime'] );

					} else {

						$current_options['enabled'] = false; //didn't have the whole start picture so disable

					}

					if ( isset( $itsec_bwps_options['am_enddate'] ) && isset( $itsec_bwps_options['am_endtime'] ) ) {

						$current_options['end'] = strtotime( date( 'Y-m-d', $itsec_bwps_options['am_enddate'] ) ) + intval( $itsec_bwps_options['am_endtime'] );

					} elseif ( isset( $itsec_bwps_options['am_endtime'] ) && $itsec_bwps_options['type'] == 1 ) {

						$current_options['end'] = strtotime( date( 'Y-m-d', $current_time ) ) + intval( $itsec_bwps_options['am_endtime'] );

					} else {

						$current_options['enabled'] = false; //didn't have the whole start picture so disable

					}

					update_site_option( 'itsec_away_mode', $current_options );

					$away_file = ITSEC_Core::get_storage_dir() . '/itsec_away.confg'; //override file

					if ( $current_options['enabled'] === true && ! file_exists( $away_file ) ) {

						@file_put_contents( $away_file, 'true' );

					} else {

						@unlink( $away_file );

					}

				}
			}

			if ( $itsec_old_version < 4041 ) {
				$current_options = get_site_option( 'itsec_away_mode' );
				$current_override_options = get_site_option( 'itsec_away_mode_sync_override' );

				// If there are no current options, go with the new defaults by not saving anything
				if ( is_array( $current_options ) || is_array( $current_override_options ) ) {
					$settings = ITSEC_Modules::get_defaults( 'away-mode' );
					$original_settings = $settings;

					if ( is_array( $current_options ) ) {
						$settings['type']       = ( 1 == $current_options['type'] )? 'daily' : 'one-time';
						$settings['start']      = intval( $current_options['start'] - ITSEC_Core::get_time_offset() );
						$settings['start_time'] = $current_options['start'] - strtotime( date( 'Y-m-d', $current_options['start'] ) );
						$settings['end']        = intval( $current_options['end'] - ITSEC_Core::get_time_offset() );
						$settings['end_time']   = $current_options['end'] - strtotime( date( 'Y-m-d', $current_options['end'] ) );
					}

					if ( is_array( $current_override_options ) ) {
						$settings['override_type'] = $current_override_options['intention'];
						$settings['override_end']  = $current_override_options['expires'];
					}

					ITSEC_Modules::set_settings( 'away-mode', $settings );

					if ( isset( $current_options['enabled'] ) && $current_options['enabled'] ) {
						ITSEC_Modules::activate( 'away-mode' );
					} else {
						ITSEC_Modules::deactivate( 'away-mode' );
					}
				}
			}

		}

	}

}

new ITSEC_Away_Mode_Setup();
