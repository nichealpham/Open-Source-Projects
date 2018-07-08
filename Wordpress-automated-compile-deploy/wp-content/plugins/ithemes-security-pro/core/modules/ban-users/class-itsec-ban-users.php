<?php

class ITSEC_Ban_Users {
	private static $instance = false;
	
	private $hooks_added = false;
	
	
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
		
		add_filter( 'itsec_filter_blacklisted_ips', array( $this, 'filter_blacklisted_ips' ) );
		
		add_filter( 'itsec_filter_apache_server_config_modification', array( $this, 'filter_apache_server_config_modification' ) );
		add_filter( 'itsec_filter_nginx_server_config_modification', array( $this, 'filter_nginx_server_config_modification' ) );
		add_filter( 'itsec_filter_litespeed_server_config_modification', array( $this, 'filter_litespeed_server_config_modification' ) );
		
		$this->hooks_added = true;
	}
	
	public function remove_hooks() {
		remove_filter( 'itsec_filter_blacklisted_ips', array( $this, 'filter_blacklisted_ips' ) );
		
		remove_filter( 'itsec_filter_apache_server_config_modification', array( $this, 'filter_apache_server_config_modification' ) );
		remove_filter( 'itsec_filter_nginx_server_config_modification', array( $this, 'filter_nginx_server_config_modification' ) );
		remove_filter( 'itsec_filter_litespeed_server_config_modification', array( $this, 'filter_litespeed_server_config_modification' ) );
		
		$this->hooks_added = false;
	}
	
	public function init() {
		$this->add_hooks();
	}
	
	public function filter_blacklisted_ips( $blacklisted_ips ) {
		if ( ITSEC_Modules::get_setting( 'ban-users', 'enable_ban_lists' ) ) {
			$blacklisted_ips = array_merge( $blacklisted_ips, ITSEC_Modules::get_setting( 'ban-users', 'host_list', array() ) );
		}
		
		return $blacklisted_ips;
	}
	
	public function filter_apache_server_config_modification( $modification ) {
		require_once( dirname( __FILE__ ) . '/config-generators.php' );
		
		if ( ITSEC_Modules::get_setting( 'ban-users', 'default' ) ) {
			$modification .= ITSEC_Ban_Users_Config_Generators::get_server_config_default_blacklist_rules( 'apache' );
		}
		
		if ( ITSEC_Modules::get_setting( 'ban-users', 'enable_ban_lists' ) ) {
			$modification .= ITSEC_Ban_Users_Config_Generators::get_server_config_ban_hosts_rules( 'apache' );
			$modification .= ITSEC_Ban_Users_Config_Generators::get_server_config_ban_user_agents_rules( 'apache' );
		}
		
		return $modification;
	}
	
	public function filter_nginx_server_config_modification( $modification ) {
		require_once( dirname( __FILE__ ) . '/config-generators.php' );
		
		if ( ITSEC_Modules::get_setting( 'ban-users', 'default' ) ) {
			$modification .= ITSEC_Ban_Users_Config_Generators::get_server_config_default_blacklist_rules( 'nginx' );
		}
		
		if ( ITSEC_Modules::get_setting( 'ban-users', 'enable_ban_lists' ) ) {
			$modification .= ITSEC_Ban_Users_Config_Generators::get_server_config_ban_hosts_rules( 'nginx' );
			$modification .= ITSEC_Ban_Users_Config_Generators::get_server_config_ban_user_agents_rules( 'nginx' );
		}
		
		return $modification;
	}
	
	public function filter_litespeed_server_config_modification( $modification ) {
		require_once( dirname( __FILE__ ) . '/config-generators.php' );
		
		if ( ITSEC_Modules::get_setting( 'ban-users', 'default' ) ) {
			$modification .= ITSEC_Ban_Users_Config_Generators::get_server_config_default_blacklist_rules( 'litespeed' );
		}
		
		if ( ITSEC_Modules::get_setting( 'ban-users', 'enable_ban_lists' ) ) {
			$modification .= ITSEC_Ban_Users_Config_Generators::get_server_config_ban_hosts_rules( 'litespeed' );
			$modification .= ITSEC_Ban_Users_Config_Generators::get_server_config_ban_user_agents_rules( 'litespeed' );
		}
		
		return $modification;
	}
}


ITSEC_Ban_Users::get_instance();
