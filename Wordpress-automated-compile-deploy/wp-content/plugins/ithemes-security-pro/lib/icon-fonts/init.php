<?php
/**
 * Custom Icons for iThemes Products
 *
 * @package icon-fonts
 * @author iThemes
 * @version 1.2.0
*/

if ( ! function_exists( 'it_icon_font_admin_enueue_scripts' ) ) {
	function it_icon_font_admin_enueue_scripts() {
		if ( version_compare( $GLOBALS['wp_version'], '3.7.10', '>=' ) ) {
			$dir = str_replace( '\\', '/', dirname( __FILE__ ) );

			$content_dir = rtrim( str_replace( '\\', '/', WP_CONTENT_DIR ), '/' );
			$abspath = rtrim( str_replace( '\\', '/', ABSPATH ), '/' );

			if ( empty( $content_dir ) || ( 0 === strpos( $dir, $content_dir ) ) ) {
				$url = WP_CONTENT_URL . str_replace( '\\', '/', preg_replace( '/^' . preg_quote( $content_dir, '/' ) . '/', '', $dir ) );
			} else if ( empty( $abspath ) || ( 0 === strpos( $dir, $abspath ) ) ) {
				$url = get_option( 'siteurl' ) . str_replace( '\\', '/', preg_replace( '/^' . preg_quote( $abspath, '/' ) . '/', '', $dir ) );
			}

			if ( empty( $url ) ) {
				$dir = realpath( $dir );

				if ( empty( $content_dir ) || ( 0 === strpos( $dir, $content_dir ) ) ) {
					$url = WP_CONTENT_URL . str_replace( '\\', '/', preg_replace( '/^' . preg_quote( $content_dir, '/' ) . '/', '', $dir ) );
				} else if ( empty( $abspath ) || ( 0 === strpos( $dir, $abspath ) ) ) {
					$url = get_option( 'siteurl' ) . str_replace( '\\', '/', preg_replace( '/^' . preg_quote( $abspath, '/' ) . '/', '', $dir ) );
				}
			}

			if ( is_ssl() ) {
				$url = preg_replace( '|^http://|', 'https://', $url );
			} else {
				$url = preg_replace( '|^https://|', 'http://', $url );
			}


			wp_enqueue_style( 'ithemes-icon-font', "$url/icon-fonts.css" );
		}
	}
	add_action( 'admin_enqueue_scripts', 'it_icon_font_admin_enueue_scripts' );
}
