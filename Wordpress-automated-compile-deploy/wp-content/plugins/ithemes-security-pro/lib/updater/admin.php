<?php

/*
Set up admin interface elements.
Written by Chris Jean for iThemes.com
Version 1.2.2

Version History
	1.0.0 - 2013-09-19 - Chris Jean
		Split off from the old Ithemes_Updater_Init class.
	1.1.0 - 2013-10-02 - Chris Jean
		Added support for themes through the filter_plugins_api function (since themes don't have a "View version *** details" feature.
	1.2.0 - 2013-10-23 - Chris Jean
		Changed how the licensing page is registered for multisite. It now will only load on multisite sites if the user is a super user (network admin).
		Removed the code that handled the setting to show or hide the licensing page on multisite sites.
	1.2.1 - 2013-10-25 - Chris Jean
		Added "License" links to Network Admin Plugins and Themes pages.
	1.2.2 - 2014-10-23 - Chris Jean
		Updated code formating to WordPress coding standards.
*/


class Ithemes_Updater_Admin {
	private $page_name = 'ithemes-licensing';
	
	private $package_details = false;
	private $registration_link = false;
	
	private $page_ref;
	
	
	public function __construct() {
		require_once( $GLOBALS['ithemes_updater_path'] . '/settings.php' );
		
		if ( ! is_multisite() || is_super_admin() ) {
			add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );
		}
		
		add_action( 'network_admin_menu', array( $this, 'add_network_admin_pages' ) );
		
		add_action( 'admin_head-plugins.php', array( $this, 'show_activation_message' ) );
		add_action( 'admin_head-themes.php', array( $this, 'show_activation_message' ) );
		add_action( 'deactivated_plugin', array( $this, 'clear_activation_package' ) );
		
		add_filter( 'upgrader_pre_install', array( $this, 'filter_upgrader_pre_install' ) );
		add_filter( 'upgrader_post_install', array( $this, 'filter_upgrader_post_install' ), 10, 3 );
		add_filter( 'plugins_api', array( $this, 'filter_plugins_api' ), 10, 3 );
		
		if ( ! is_multisite() || is_super_admin() ) {
			add_filter( 'plugin_action_links', array( $this, 'filter_plugin_action_links' ), 10, 4 );
			add_filter( 'theme_action_links', array( $this, 'filter_theme_action_links' ), 10, 2 );
		}
		
		add_filter( 'network_admin_plugin_action_links', array( $this, 'filter_plugin_action_links' ), 10, 4 );
		add_filter( 'network_admin_theme_action_links', array( $this, 'filter_theme_action_links' ), 10, 2 );
	}
	
	public function filter_plugins_api( $value, $action, $args ) {
		$options = $GLOBALS['ithemes-updater-settings']->get_options();
		
		if ( ! isset( $args->slug ) ) {
			return $value;
		}
		
		foreach ( (array) $options['update_plugins'] as $path => $data ) {
			if ( $data->slug == $args->slug ) {
				require_once( $GLOBALS['ithemes_updater_path'] . '/information.php' );
				return Ithemes_Updater_Information::get_plugin_information( $path );
			}
		}
		
		foreach ( (array) $options['update_themes'] as $path => $data ) {
			if ( $path == $args->slug ) {
				require_once( $GLOBALS['ithemes_updater_path'] . '/information.php' );
				return Ithemes_Updater_Information::get_theme_information( $path );
			}
		}
		
		return $value;
	}
	
	public function filter_upgrader_pre_install( $value ) {
		$this->set_package_details();
		
		return $value;
	}
	
	public function filter_upgrader_post_install( $value, $hook_extra, $result ) {
		$options = $GLOBALS['ithemes-updater-settings']->queue_flush();
		
		return $value;
	}
	
	public function clear_activation_package( $deactivated_path ) {
		$packages = $GLOBALS['ithemes-updater-settings']->get_packages();
		$options = $GLOBALS['ithemes-updater-settings']->get_options();
		
		$deactivated_path = WP_PLUGIN_DIR . "/$deactivated_path";
		
		foreach ( $packages as $package => $paths ) {
			if ( ! in_array( $deactivated_path, $paths ) || ( count( $paths ) > 1 ) ) {
				continue;
			}
			
			$index = array_search( $package, $options['packages'] );
			
			if ( false === $index ) {
				return;
			}
			
			unset( $options['packages'][$index] );
			$GLOBALS['ithemes-updater-settings']->update_options( $options );
			
			return;
		}
	}
	
	public function show_activation_message() {
		$new_packages = $GLOBALS['ithemes-updater-settings']->get_new_packages();
		
		if ( empty( $new_packages ) ) {
			return;
		}
		
		
		natcasesort( $new_packages );
		require_once( $GLOBALS['ithemes_updater_path'] . '/functions.php' );
		$names = array();
		
		foreach ( $new_packages as $package ) {
			$names = Ithemes_Updater_Functions::get_package_name( $package );
		}
		
		if ( is_multisite() && is_network_admin() ) {
			$url = network_admin_url( 'settings.php' ) . "?page={$this->page_name}";
		} else {
			$url = admin_url( 'options-general.php' ) . "?page={$this->page_name}";
		}
		
		echo '<div class="updated fade"><p>' . wp_sprintf( __( 'To receive automatic updates for %l, use the <a href="%s">iThemes Licensing</a> page found in the Settings menu.', 'it-l10n-ithemes-security-pro' ), $names, $url ) . '</p></div>';
		
		
		$GLOBALS['ithemes-updater-settings']->update_packages();
	}
	
	public function add_admin_pages() {
		$this->page_ref = add_options_page( __( 'iThemes Licensing', 'it-l10n-ithemes-security-pro' ), __( 'iThemes Licensing', 'it-l10n-ithemes-security-pro' ), 'manage_options', $this->page_name, array( $this, 'settings_index' ) );
		
		add_action( "load-{$this->page_ref}", array( $this, 'load_settings_page' ) );
	}
	
	public function add_network_admin_pages() {
		$this->page_ref = add_submenu_page( 'settings.php', __( 'iThemes Licensing', 'it-l10n-ithemes-security-pro' ), __( 'iThemes Licensing', 'it-l10n-ithemes-security-pro' ), 'manage_options', $this->page_name, array( $this, 'settings_index' ) );
		
		add_action( "load-{$this->page_ref}", array( $this, 'load_settings_page' ) );
	}
	
	public function load_settings_page() {
		require( $GLOBALS['ithemes_updater_path'] . '/settings-page.php' );
	}
	
	public function settings_index() {
		do_action( 'ithemes_updater_settings_page_index' );
	}
	
	private function set_package_details() {
		if ( false !== $this->package_details ) {
			return;
		}
		
		require_once( $GLOBALS['ithemes_updater_path'] . '/packages.php' );
		$this->package_details = Ithemes_Updater_Packages::get_local_details();
	}
	
	private function set_registration_link() {
		if ( false !== $this->registration_link ) {
			return;
		}
		
		$url = admin_url( 'options-general.php' ) . "?page={$this->page_name}";
		$this->registration_link = sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', $url, __( 'Manage iThemes product licenses to receive automatic upgrade support', 'it-l10n-ithemes-security-pro' ), __( 'License', 'it-l10n-ithemes-security-pro' ) );
	}
	
	public function filter_plugin_action_links( $actions, $plugin_file, $plugin_data, $context ) {
		$this->set_package_details();
		$this->set_registration_link();
		
		if ( isset( $this->package_details[$plugin_file] ) ) {
			$actions[] = $this->registration_link;
		}
		
		return $actions;
	}
	
	public function filter_theme_action_links( $actions, $theme ) {
		$this->set_package_details();
		$this->set_registration_link();
		
		if ( is_object( $theme ) ) {
			$path = basename( $theme->get_stylesheet_directory() ) . '/style.css';
		} else if ( is_array( $theme ) && isset( $theme['Stylesheet Dir'] ) ) {
			$path = $theme['Stylesheet Dir'] . '/style.css';
		} else {
			$path = '';
		}
		
		if ( isset( $this->package_details[$path] ) ) {
			$actions[] = $this->registration_link;
		}
		
		return $actions;
	}
}

new Ithemes_Updater_Admin();
