<?php

final class ITSEC_System_Tweaks {
	private static $instance = false;

	private $hooks_added = false;


	private function __construct() {
		$this->add_hooks();
	}

	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public static function activate() {
		$self = self::get_instance();

		$self->add_hooks();
		ITSEC_Response::regenerate_server_config();
	}

	public static function deactivate() {
		$self = self::get_instance();

		$self->remove_hooks();
		ITSEC_Response::regenerate_server_config();
	}

	public function add_hooks() {
		if ( $this->hooks_added ) {
			return;
		}

		add_filter( 'itsec_filter_apache_server_config_modification', array( $this, 'filter_apache_server_config_modification' ) );
		add_filter( 'itsec_filter_nginx_server_config_modification', array( $this, 'filter_nginx_server_config_modification' ) );
		add_filter( 'itsec_filter_litespeed_server_config_modification', array( $this, 'filter_litespeed_server_config_modification' ) );

		if ( ITSEC_Modules::get_setting( 'system-tweaks', 'long_url_strings' ) ) {
			add_action( 'itsec_initialized', array( $this, 'block_long_urls' ) );
		}

		$this->hooks_added = true;
	}

	public function remove_hooks() {
		remove_filter( 'itsec_filter_apache_server_config_modification', array( $this, 'filter_apache_server_config_modification' ) );
		remove_filter( 'itsec_filter_nginx_server_config_modification', array( $this, 'filter_nginx_server_config_modification' ) );
		remove_filter( 'itsec_filter_litespeed_server_config_modification', array( $this, 'filter_litespeed_server_config_modification' ) );

		remove_action( 'itsec_initialized', array( $this, 'block_long_urls' ) );

		$this->hooks_added = false;
	}

	public function filter_apache_server_config_modification( $modification ) {
		require_once( dirname( __FILE__ ) . '/config-generators.php' );

		return ITSEC_System_Tweaks_Config_Generators::filter_apache_server_config_modification( $modification );
	}

	public function filter_nginx_server_config_modification( $modification ) {
		require_once( dirname( __FILE__ ) . '/config-generators.php' );

		return ITSEC_System_Tweaks_Config_Generators::filter_nginx_server_config_modification( $modification );
	}

	public function filter_litespeed_server_config_modification( $modification ) {
		require_once( dirname( __FILE__ ) . '/config-generators.php' );

		return ITSEC_System_Tweaks_Config_Generators::filter_litespeed_server_config_modification( $modification );
	}

	/**
	 * Block long URLs very early in the request cycle on the front-end.
	 */
	public function block_long_urls() {
		if ( strlen( $_SERVER['REQUEST_URI'] ) <= 255 ) {
			return;
		}

		if ( is_admin() ) {
			return;
		}

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			return;
		}

		if ( ITSEC_Core::is_iwp_call() ) {
			return;
		}

		if ( strpos( $_SERVER['REQUEST_URI'], 'infinity=scrolling&action=infinite_scroll' ) ) {
			return;
		}

		@header( 'HTTP/1.1 414 Request-URI Too Long' );
		@header( 'Status: 414 Request-URI Too Long' );
		@header( 'Cache-Control: no-cache, must-revalidate' );
		@header( 'Expires: Thu, 22 Jun 1978 00:28:00 GMT' );
		@header( 'Connection: Close' );
		@exit;
	}
}


ITSEC_System_Tweaks::get_instance();
