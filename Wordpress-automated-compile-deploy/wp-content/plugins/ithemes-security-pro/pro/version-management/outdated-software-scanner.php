<?php

final class ITSEC_VM_Outdated_Software_Scanner {
	private static $instance;

	public static function run_scan() {
		if ( self::$instance ) {
			// Only allow one scan per page load.
			return;
		}

		self::$instance = new self;

		require_once( dirname( __FILE__ ) . '/utility.php' );
		require_once( ABSPATH . WPINC . '/update.php' );

		$details = ITSEC_Modules::get_setting( 'version-management', 'update_details' );
		$wp_version = ITSEC_VM_Utility::get_wordpress_version();

		if ( is_callable( 'wp_version_check' ) ) {
			wp_version_check( array(), true );
		}
		if ( is_callable( 'wp_update_plugins' ) ) {
			wp_update_plugins();
		}
		if ( is_callable( 'wp_update_themes' ) ) {
			wp_update_themes();
		}


		$core = get_site_transient( 'update_core' );
		$has_update = false;

		foreach ( $core->updates as $update ) {
			if ( 'development' === $update->response || 'upgrade' === $update->response ) {
				$has_update = true;
				break;
			}
		}

		if ( $has_update ) {
			if ( ! isset( $details['core'] ) || version_compare( $wp_version, $details['core']['current'], '>' ) ) {
				$details['core'] = array(
					'current' => $wp_version,
					'time'    => time(),
				);
			}
		} else if ( isset( $details['core'] ) ) {
			unset( $details['core'] );
		}


		$plugins = get_site_transient( 'update_plugins' );

		if ( isset( $details['plugins'] ) && is_array( $details['plugins'] ) ) {
			foreach ( $details['plugins'] as $plugin_slug => $plugin_details ) {
				if ( ! isset( $plugins->response[$plugin_slug] ) ) {
					unset( $details['plugins'][$plugin_slug] );
					continue;
				}

				if ( isset( $plugins->checked[$plugin_slug] ) && version_compare( $plugins->checked[$plugin_slug], $plugin_details['current'], '>' ) ) {
					unset( $details['plugins'][$plugin_slug] );
				}
			}
		} else {
			$details['plugins'] = array();
		}

		foreach ( $plugins->response as $plugin_slug => $plugin_details ) {
			if ( isset( $plugins->checked[$plugin_slug] ) && version_compare( $plugins->checked[$plugin_slug], $plugin_details->new_version, '>=' ) ) {
				unset( $details['plugins'][$plugin_slug] );
				continue;
			}

			if ( isset( $details['plugins'][$plugin_slug] ) || ! isset( $plugins->checked[$plugin_slug] ) ) {
				continue;
			}

			$details['plugins'][$plugin_slug] = array(
				'current' => $plugins->checked[$plugin_slug],
				'time'    => time(),
			);
		}

		if ( empty( $details['plugins'] ) ) {
			unset( $details['plugins'] );
		}


		$themes = get_site_transient( 'update_themes' );

		if ( isset( $details['themes'] ) && is_array( $details['themes'] ) ) {
			foreach ( $details['themes'] as $theme_slug => $theme_details ) {
				if ( ! isset( $themes->response[$theme_slug] ) ) {
					unset( $details['themes'][$theme_slug] );
					continue;
				}

				if ( isset( $themes->checked[$theme_slug] ) && version_compare( $themes->checked[$theme_slug], $theme_details['current'], '>' ) ) {
					unset( $details['themes'][$theme_slug] );
				}
			}
		} else {
			$details['themes'] = array();
		}

		foreach ( $themes->response as $theme_slug => $theme_details ) {
			if ( isset( $themes->checked[$theme_slug] ) && version_compare( $themes->checked[$theme_slug], $theme_details['new_version'], '>=' ) ) {
				unset( $details['themes'][$theme_slug] );
				continue;
			}

			if ( isset( $details['themes'][$theme_slug] ) || ! isset( $themes->checked[$theme_slug] ) ) {
				continue;
			}

			$details['themes'][$theme_slug] = array(
				'current' => $themes->checked[$theme_slug],
				'time'    => time(),
			);
		}

		if ( empty( $details['themes'] ) ) {
			unset( $details['themes'] );
		}


		ITSEC_Modules::set_setting( 'version-management', 'update_details', $details );
	}
}
