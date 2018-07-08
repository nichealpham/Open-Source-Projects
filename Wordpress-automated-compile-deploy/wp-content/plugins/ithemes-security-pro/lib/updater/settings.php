<?php

/*
Central management of options storage and registered packages.
Written by Chris Jean for iThemes.com
Version 1.3.0

Version History
	1.0.0 - 2013-09-19 - Chris Jean
		Split off from the old Ithemes_Updater_Init class.
	1.0.1 - 2013-09-20 - Chris Jean
		Fixed bug where the old ithemes-updater-object global was being referenced.
	1.1.0 - 2013-10-04 - Chris Jean
		Enhancement: Added handler for GET query variable: ithemes-updater-force-minor-update.
		Bug Fix: Changed URL regex for applying the CA patch to only apply to links for api.ithemes.com and not the S3 links.
		Bug Fix: A check to ensure that the $GLOBALS['ithemes_updater_path'] variable is set properly.
		Misc: Updated file reference for ca/cacert.crt to ca/roots.crt.
	1.2.0 - 2013-10-23 - Chris Jean
		Enhancement: Added the quick_releases setting.
		Enhancement: Added an explicit flush when the ithemes-updater-force-minor-update query variable is used
		Misc: Removed the show_on_sites setting as it is no longer needed.
	1.3.0 - 2014-10-23 - Chris Jean
		Improved flushing system.
		Reduced cache timeout durations.
		Added timeout multiplier.
		Removed CA patch code as it's now handled in the server code.
		Updated code to meet WordPress coding standards.
*/


class Ithemes_Updater_Settings {
	private $option_name = 'ithemes-updater-cache';
	
	private $packages = array();
	private $new_packages = array();
	private $options = false;
	private $options_modified = false;
	private $do_flush = false;
	private $initialized = false;
	private $plugins_cleaned = false;
	private $themes_cleaned = false;
	
	private $default_options = array(
		'timeout-multiplier' => 1,
		'expiration'         => 0,
		'timestamp'          => 0,
		'packages'           => array(),
		'update_plugins'     => array(),
		'update_themes'      => array(),
		'use_ca_patch'       => false,
		'use_ssl'            => true,
		'quick_releases'     => false,
	);
	
	
	public function __construct() {
		$GLOBALS['ithemes-updater-settings'] = $this;
		
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'shutdown', array( $this, 'shutdown' ) );
	}
	
	public function init() {
		if ( $this->initialized ) {
			return;
		}
		
		$this->initialized = true;
		
		if ( ! isset( $GLOBALS['ithemes_updater_path'] ) ) {
			$GLOBALS['ithemes_updater_path'] = dirname( __FILE__ );
		}
		
		$this->load();
		
		do_action( 'ithemes_updater_register', $this );
		
		$this->new_packages = array_diff( array_keys( $this->packages ), $this->options['packages'] );
		
		
		if ( isset( $_GET['ithemes-updater-force-quick-release-update'] ) && ! isset( $_GET['ithemes-updater-force-minor-update'] ) ) {
			$_GET['ithemes-updater-force-minor-update'] = $_GET['ithemes-updater-force-quick-release-update'];
		}
		
		
		$flushed = false;
		
		
		if ( isset( $_GET['ithemes-updater-force-minor-update'] ) && current_user_can( 'manage_options' ) ) {
			if ( $_GET['ithemes-updater-force-minor-update'] ) {
				$this->options['force_minor_version_update'] = time() + 3600;
				$this->update_options( $this->options );
				
				$this->flush( 'forced minor version update' );
				$flushed = true;
			} else {
				unset( $this->options['force_minor_version_update'] );
				$this->update_options( $this->options );
				
				$this->flush( 'unset forced minor version update' );
				$flushed = true;
			}
		} else if ( isset( $this->options['force_minor_version_update'] ) && ( $this->options['force_minor_version_update'] < time() ) ) {
			unset( $this->options['force_minor_version_update'] );
			$this->update_options( $this->options );
		}
		
		
		if ( ! $flushed ) {
			if ( ! empty( $_GET['ithemes-updater-force-refresh'] ) && current_user_can( 'manage_options' ) ) {
				$this->flush( 'forced' );
			} else if ( empty( $this->options['expiration'] ) || ( $this->options['expiration'] <= time() ) ) {
				$this->flush( 'expired' );
			} else if ( $this->is_expired( $this->options['timestamp'] ) ) {
				$this->flush( 'got stale' );
			} else if ( ! empty( $this->new_packages ) ) {
				$this->flush( 'new packages' );
			}
		}
	}
	
	public function load() {
		if ( false !== $this->options ) {
			return;
		}
		
		$this->options = get_site_option( $this->option_name, false );
		
		if ( ( false === $this->options ) || ! is_array( $this->options ) ) {
			$this->options = array();
		}
		
		$this->options = array_merge( $this->default_options, $this->options );
		
		if ( 0 == $this->options['timestamp'] ) {
			$this->update();
		}
	}
	
	public function shutdown() {
		$this->flush();
		
		if ( $this->options_modified ) {
			update_site_option( $this->option_name, $this->options );
		}
	}
	
	public function queue_flush() {
		$this->do_flush = true;
	}
	
	public function flush( $reason = '' ) {
		if ( empty( $reason ) && ! $this->do_flush ) {
			return;
		}
		
		$this->do_flush = false;
		
		$this->update();
	}
	
	public function update() {
		$this->init();
		
		require_once( $GLOBALS['ithemes_updater_path'] . '/updates.php' );
		
		Ithemes_Updater_Updates::run_update();
	}
	
	public function get_options() {
		$this->init();
		
		return $this->options;
	}
	
	public function get_option( $var ) {
		$this->init();
		
		if ( isset( $this->options[$var] ) ) {
			return $this->options[$var];
		}
		
		return null;
	}
	
	public function update_options( $updates ) {
		$this->init();
		
		$this->options = array_merge( $this->options, $updates );
		$this->options_modified = true;
	}
	
	public function update_packages() {
		$this->update_options( array( 'packages' => array_keys( $this->packages ) ) );
	}
	
	public function get_packages() {
		return $this->packages;
	}
	
	public function get_new_packages() {
		return $this->new_packages;
	}
	
	public function filter_update_plugins( $update_plugins ) {
		if ( ! is_object( $update_plugins ) ) {
			return $update_plugins;
		}
		
		if ( ! isset( $update_plugins->response ) || ! is_array( $update_plugins->response ) ) {
			$update_plugins->response = array();
		}
		
		$this->flush();
		
		if ( ! is_array( $this->options ) || ! isset( $this->options['update_plugins'] ) ) {
			$this->load();
		}
		
		if ( isset( $this->options['update_plugins'] ) && is_array( $this->options['update_plugins'] ) ) {
			if ( ! $this->plugins_cleaned ) {
				@include_once( ABSPATH . '/wp-admin/includes/plugin.php' );
				
				if ( is_callable( 'get_plugin_data' ) ) {
					foreach ( $this->options['update_plugins'] as $plugin => $update_data ) {
						$plugin_data = get_plugin_data( WP_PLUGIN_DIR . "/$plugin", false, false );
						
						if ( $plugin_data['Version'] == $update_data->new_version ) {
							unset( $this->options['update_plugins'][$plugin] );
							$this->plugins_cleaned = true;
						}
					}
				}
				
				if ( $this->plugins_cleaned ) {
					$this->options_modified = true;
				}
				
				$this->plugins_cleaned = true;
			}
			
			$update_plugins->response = array_merge( $update_plugins->response, $this->options['update_plugins'] );
		}
		
		return $update_plugins;
	}
	
	public function filter_update_themes( $update_themes ) {
		if ( ! is_object( $update_themes ) ) {
			return $update_themes;
		}
		
		if ( ! isset( $update_themes->response ) || ! is_array( $update_themes->response ) ) {
			$update_themes->response = array();
		}
		
		$this->flush();
		
		if ( ! is_array( $this->options ) || ! isset( $this->options['update_themes'] ) ) {
			$this->load();
		}
		
		if ( isset( $this->options['update_themes'] ) && is_array( $this->options['update_themes'] ) ) {
			if ( ! $this->themes_cleaned ) {
				foreach ( $this->options['update_themes'] as $theme => $update_data ) {
					$theme_data = wp_get_theme( $theme );
					
					if ( $theme_data->get( 'Version' ) == $update_data['new_version'] ) {
						unset( $this->options['update_themes'][$theme] );
						$this->themes_cleaned = true;
					}
				}
				
				if ( $this->themes_cleaned ) {
					$this->options_modified = true;
				}
				
				$this->themes_cleaned = true;
			}
			
			$update_themes->response = array_merge( $update_themes->response, $this->options['update_themes'] );
		}
		
		return $update_themes;
	}
	
	public function register( $slug, $file ) {
		$this->packages[$slug][] = $file;
	}
	
	private function is_expired( $timestamp ) {
		$multiplier = $this->get_option( 'timeout-multiplier' );
		
		if ( $multiplier < 1 ) {
			$multiplier = 1;
		} else if ( $multiplier > 10 ) {
			$multiplier = 10;
		}
		
		
		if ( current_user_can( 'update_themes' ) || current_user_can( 'update_plugins' ) ) {
			if ( ! empty( $_POST['action'] ) && ( 'heartbeat' == $_POST['action'] ) ) {
				$timeout = 43200;
			} else {
				$page = empty( $_GET['page'] ) ? $GLOBALS['pagenow'] : $_GET['page'];
				
				switch ( $page ) {
					case 'update.php' :
					case 'update-core.php' :
					case 'ithemes-licensing' :
						$timeout = 60;
						break;
					case 'plugins.php' :
					case 'themes.php' :
						$timeout = 600;
						break;
					default :
						$timeout = 3600;
				}
			}
		} else {
			$timeout = 7200;
		}
		
		$timeout *= $multiplier;
		
		
		if ( $timestamp <= ( time() - $timeout ) ) {
			return true;
		}
		
		return false;
	}
}

new Ithemes_Updater_Settings();
