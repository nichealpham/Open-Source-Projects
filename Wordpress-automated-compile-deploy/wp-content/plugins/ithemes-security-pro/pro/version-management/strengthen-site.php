<?php

final class ITSEC_Version_Management_Strengthen_Site {
	public static function is_software_outdated() {
		require_once( dirname( __FILE__ ) . '/utility.php' );

		if ( ITSEC_VM_Utility::is_wordpress_version_outdated() ) {
			return true;
		}

		$details = ITSEC_Modules::get_setting( 'version-management', 'update_details' );
		$outdated_time = time() - MONTH_IN_SECONDS;

		if ( isset( $details['core'] ) && $details['core']['time'] < $outdated_time ) {
			return true;
		}

		if ( isset( $details['plugins'] ) ) {
			foreach ( $details['plugins'] as $plugin_details ) {
				if ( $plugin_details['time'] < $outdated_time ) {
					return true;
				}
			}
		}

		if ( isset( $details['themes'] ) ) {
			foreach ( $details['themes'] as $theme_details ) {
				if ( $theme_details['time'] < $outdated_time ) {
					return true;
				}
			}
		}

		return false;
	}
}
