<?php

class ITSEC_SSL {
	private static $instance = false;

	private $config_hooks_added = false;
	private $http_site_url;
	private $https_site_url;


	private function __construct() {
		$this->init();
	}

	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public static function activate() {
		$self = self::get_instance();

		$self->add_config_hooks();
		ITSEC_Response::regenerate_wp_config();
	}

	public static function deactivate() {
		$self = self::get_instance();

		$self->remove_config_hooks();
		ITSEC_Response::regenerate_wp_config();
	}

	public function add_config_hooks() {
		if ( $this->config_hooks_added ) {
			return;
		}

		add_filter( 'itsec_filter_wp_config_modification', array( $this, 'filter_wp_config_modification' ) );

		$this->config_hooks_added = true;
	}

	public function remove_config_hooks() {
		remove_filter( 'itsec_filter_wp_config_modification', array( $this, 'filter_wp_config_modification' ) );

		$this->config_hooks_added = false;
	}

	public function init() {
		$this->add_config_hooks();

		add_action( 'template_redirect', array( $this, 'do_conditional_ssl_redirect' ), 0 );

		if ( is_ssl() ) {
			$this->http_site_url = site_url( '', 'http' );
			$this->https_site_url = site_url( '', 'https' );

			add_filter( 'the_content', array( $this, 'replace_content_urls' ) );
			add_filter( 'script_loader_src', array( $this, 'script_loader_src' ) );
			add_filter( 'style_loader_src', array( $this, 'style_loader_src' ) );
			add_filter( 'upload_dir', array( $this, 'upload_dir' ) );
		}
	}

	/**
	 * Redirects to or from SSL where appropriate
	 *
	 * @since 4.0
	 *
	 * @return void
	 */
	public function do_conditional_ssl_redirect() {
		$hide_options = get_site_option( 'itsec_hide_backend', array() );

		if ( isset( $hide_options['enabled'] ) && ( $hide_options['enabled'] === true ) && ( $_SERVER['REQUEST_URI'] == ITSEC_Lib::get_home_root() . $hide_options['slug'] ) ) {
			return;
		}


		$settings = ITSEC_Modules::get_settings( 'ssl' );

		if ( 2 === $settings['frontend'] ) {
			$protocol = 'https';
		} else if ( ( 1 === $settings['frontend'] ) && is_singular() ) {
			global $post;

			$bwps_ssl = get_post_meta( $post->ID, 'bwps_enable_ssl' );

			if ( ! empty( $bwps_ssl ) ) {
				if ( $bwps_ssl[0] ) {
					$protocol = 'https';
					update_post_meta( $post->ID, 'itsec_enable_ssl', true );
				}

				delete_post_meta( $post->ID, 'bwps_enable_ssl' );
			}

			if ( ! isset( $protocol ) ) {
				$enable_ssl = get_post_meta( $post->ID, 'itsec_enable_ssl' );

				if ( ! empty( $enable_ssl ) ) {
					if ( $enable_ssl[0] ) {
						$protocol = 'https';
					} else {
						delete_post_meta( $post->ID, 'itsec_enable_ssl' );
					}
				}
			}
		} else {
			return;
		}

		if ( ! isset( $protocol ) ) {
			$protocol = 'http';
		}

		$is_ssl = is_ssl();

		if ( $is_ssl && ( 'http' == $protocol ) ) {
			$redirect = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
		} else if ( ! $is_ssl && ( 'https' == $protocol ) ) {
			$redirect = "https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
		}

		if ( isset( $redirect ) ) {
			wp_redirect( $redirect, 301 );
			exit();
		}
	}

	/**
	 * Replace urls in content with ssl
	 *
	 * @since 4.1
	 *
	 * @param string $content the content
	 *
	 * @return string the content
	 */
	public function replace_content_urls( $content ) {
		return str_replace( $this->http_site_url, $this->https_site_url, $content );
	}

	/**
	 * Replace urls in scripts with ssl
	 *
	 * @since 4.4
	 *
	 * @param string $script_loader_src the url
	 *
	 * @return string the url
	 */
	public function script_loader_src( $script_loader_src ) {
		return str_replace( $this->http_site_url, $this->https_site_url, $script_loader_src );
	}

	/**
	 * Replace urls in styles with ssl
	 *
	 * @since 4.4
	 *
	 * @param string $style_loader_src the url
	 *
	 * @return string the url
	 */
	public function style_loader_src( $style_loader_src ) {
		return str_replace( $this->http_site_url, $this->https_site_url, $style_loader_src );
	}

	/**
	 * filter uploads dir so that plugins using it to determine upload URL also work
	 *
	 * @since 4.0
	 *
	 * @param array $uploads
	 *
	 * @return array
	 */
	public function upload_dir( $upload_dir ) {
		$upload_dir['url'] = str_replace( $this->http_site_url, $this->https_site_url, $upload_dir['url'] );
		$upload_dir['baseurl'] = str_replace( $this->http_site_url, $this->https_site_url, $upload_dir['baseurl'] );

		return $upload_dir;
	}

	public function filter_wp_config_modification( $modification ) {
		if ( ITSEC_Modules::get_setting( 'ssl', 'admin' ) ) {
			$modification .= "define( 'FORCE_SSL_LOGIN', true ); // " . __( 'Force SSL for Dashboard - Security > Settings > Secure Socket Layers (SSL) > SSL for Dashboard', 'it-l10n-ithemes-security-pro' ) . "\n";
			$modification .= "define( 'FORCE_SSL_ADMIN', true ); // " . __( 'Force SSL for Dashboard - Security > Settings > Secure Socket Layers (SSL) > SSL for Dashboard', 'it-l10n-ithemes-security-pro' ) . "\n";
		}

		return $modification;
	}
}


ITSEC_SSL::get_instance();
