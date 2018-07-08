<?php

final class ITSEC_Online_Files_Comparison_Engine {
	private $hashes = array();
	
	
	public function __construct() {
		add_action( 'itsec-file-change-end-hash-comparisons', array( $this, 'do_cleanup' ) );
		
		add_action( 'itsec_process_added_files', array( $this, 'itsec_process_added_files' ) );
		add_action( 'itsec_process_changed_file', array( $this, 'itsec_process_changed_file' ), 10, 3 );
		
		$this->load_hashes();
	}
	
	public function load_hashes() {
		$this->hashes = array_merge( $this->get_core_hashes(), $this->get_plugin_hashes(), $this->get_theme_hashes() );
	}
	
	public function do_cleanup() {
		$this->hashes = array();
	}
	
	private function get_core_hashes() {
		global $wp_version;
		
		$transient_name = 'itsec-wp-hashes-' . sanitize_text_field( $wp_version ) . '-' . sanitize_text_field( get_locale() );
		$core_hashes = get_site_transient( $transient_name );
		
		if ( ! is_array( $core_hashes ) ) {
			$core_hashes = $this->get_wordpress_file_hashes( $wp_version, get_locale() );
			
			if ( empty( $core_hashes ) ) {
				set_site_transient( $transient_name, $core_hashes, HOUR_IN_SECONDS );
			} else {
				set_site_transient( $transient_name, $core_hashes, WEEK_IN_SECONDS );
			}
		}
		
		return $core_hashes;
	}
	
	private function get_cached_ithemes_package_file_hashes( $package, $version ) {
		$transient_name = "itsec-ithemes-package-hashes-$package-$version";
		$hashes = get_site_transient( $transient_name );
		
		if ( ! is_array( $hashes ) ) {
			$hashes = $this->get_ithemes_package_file_hashes( $package, $version );
			
			if ( empty( $hashes ) ) {
				set_site_transient( $transient_name, $hashes, HOUR_IN_SECONDS );
			} else {
				set_site_transient( $transient_name, $hashes, WEEK_IN_SECONDS );
			}
		}
		
		return $hashes;
	}
	
	private function get_plugin_hashes() {
		if ( ! is_callable( 'get_plugins' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		
		if ( ! is_callable( 'get_plugins' ) ) {
			return array();
		}
		
		$plugins = get_plugins();
		$plugin_hashes = array();
		
		$headers = array(
			'package' => 'iThemes Package',
			'version' => 'Version',
		);
		
		$base_dir = preg_replace( '/^'. preg_quote( ABSPATH, '/' ) . '/', '', WP_PLUGIN_DIR );
		$base_dir = trailingslashit( $base_dir );
		
		foreach ( $plugins as $plugin => $data ) {
			$data = get_file_data( trailingslashit( WP_PLUGIN_DIR ) . $plugin, $headers );
			
			if ( empty( $data['package'] ) ) {
				// Skip any plugins that do not have an iThemes Package listed.
				continue;
			}
			
			
			$hashes = $this->get_cached_ithemes_package_file_hashes( $data['package'], $data['version'] );
			$plugin_dir = dirname( $base_dir . $plugin );
			
			foreach ( $hashes as $file => $hash ) {
				$file_parts = explode( '/', $file );
				unset( $file_parts[0] );
				$file = "$plugin_dir/" . implode( '/', $file_parts );
				
				$plugin_hashes[$file] = $hash;
			}
		}
		
		return $plugin_hashes;
	}
	
	private function get_theme_hashes() {
		$themes = wp_get_themes( null );
		$theme_hashes = array();
		
		$headers = array(
			'package' => 'iThemes Package',
			'version' => 'Version',
		);
		
		foreach ( $themes as $theme => $data ) {
			$theme_file = trailingslashit( $data->theme_root ) . "$theme/style.css";
			$data = get_file_data( $theme_file, $headers );
			
			if ( empty( $data['package'] ) ) {
				// Skip any themes that do not have an iThemes Package listed.
				continue;
			}
			
			
			$hashes = $this->get_cached_ithemes_package_file_hashes( $data['package'], $data['version'] );
			$theme_dir = dirname( $theme_file );
			$theme_dir = preg_replace( '/^'. preg_quote( ABSPATH, '/' ) . '/', '', $theme_dir );
			
			foreach ( $hashes as $file => $hash ) {
				$file_parts = explode( '/', $file );
				unset( $file_parts[0] );
				$file = "$theme_dir/" . implode( '/', $file_parts );
				
				$theme_hashes[$file] = $hash;
			}
		}
		
		return $theme_hashes;
	}
	
	private function get_wordpress_file_hashes( $version, $locale ) {
		$url = "https://api.wordpress.org/core/checksums/1.0/?version=$version&locale=$locale";
		$results = wp_remote_get( "https://api.wordpress.org/core/checksums/1.0/?version=$version&locale=$locale" );

		if ( is_wp_error( $results ) ) {
			return array();
		}
		
		$body = json_decode( $results['body'], true );
		
		if ( empty( $body['checksums'] ) || ! is_array( $body['checksums'] ) ) {
			return array();
		}
		
		return $body['checksums'];
	}
	
	private function get_ithemes_package_file_hashes( $package, $version ) {
		$results = wp_remote_get( "https://s3.amazonaws.com/package-hash.ithemes.com/$package/$version.json" );

		if ( is_wp_error( $results ) ) {
			return array();
		}
		
		$body = json_decode( $results['body'], true );
		
		if ( empty( $body ) || ! is_array( $body ) ) {
			return array();
		}
		
		return $body;
	}

	/**
	 * Compare files added with remote repository.
	 *
	 * Looks at all new files found by the local file scan and compares them to
	 * the appropriate remove hash if available.
	 *
	 * @since 1.10.0
	 *
	 * @param array $files_added Array of files added since last local check
	 *
	 * @return mixed false or array of files confirmed changed
	 */
	public function itsec_process_added_files( $files_added ) {

		foreach ( $files_added as $file => $attr ) {

			if ( isset( $this->hashes[$file] ) && isset( $file['h'] ) && $file['h'] === $this->hashes[$file] ) {
				unset( $files_added[$file] );
			}

		}

		return ( $files_added );

	}

	/**
	 * Compare a file that has been marked as changed since the last local scan.
	 *
	 * Looks at all the changed files found by the local scan and compares them
	 * to their remote hashes if they're available.
	 *
	 * @since 1.10.0
	 *
	 * @param bool   $changed whether the file has been changed or not
	 * @param string $file    The name of the file to check
	 * @param string $hash    the md5 to check
	 *
	 * @return bool whether a remote difference is detected or false
	 */
	public function itsec_process_changed_file( $changed, $file, $hash ) {

		if ( isset( $this->hashes[$file] ) && $hash === $this->hashes[$file] ) {
			$changed = false;
		}

		return $changed;

	}

}

new ITSEC_Online_Files_Comparison_Engine();
