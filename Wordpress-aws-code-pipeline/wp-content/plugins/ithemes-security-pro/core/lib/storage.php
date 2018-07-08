<?php

final class ITSEC_Storage {
	private $option = 'itsec-storage';

	private static $instance = false;
	private $changed = false;
	private $cache;
	private $shutdown_done = false;

	private function __construct() {
		$this->load();

		register_shutdown_function( array( $this, 'shutdown' ) );
		add_action( 'shutdown', array( $this, 'shutdown' ), -10 );

		add_action( 'itsec-lib-clear-caches', array( $this, 'save' ), -20 );
		add_action( 'itsec-lib-clear-caches', array( $this, 'load' ), -10 );
	}

	private static function get_instance() {
		if ( false === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function get( $name ) {
		$data = self::get_instance();

		if ( isset( $data->cache[$name] ) ) {
			return $data->cache[$name];
		}

		return null;
	}

	public static function get_all() {
		$data = self::get_instance();

		return $data->cache;
	}

	public static function set( $name, $value ) {
		$data = self::get_instance();
		$data->cache[$name] = $value;
		$data->changed = true;

		if ( $data->shutdown_done ) {
			self::save();
		}
	}

	public static function set_all( $value ) {
		$data = self::get_instance();
		$data->cache = $value;
		$data->changed = true;

		if ( $data->shutdown_done ) {
			self::save();
		}
	}

	public static function save() {
		$data = self::get_instance();

		if ( ! $data->changed ) {
			return true;
		}

		$data->changed = false;

		if ( is_multisite() ) {
			return update_site_option( $data->option, $data->cache );
		} else {
			return update_option( $data->option, $data->cache );
		}
	}

	public static function reload() {
		$data = self::get_instance();
		$data->load();
	}

	public function load() {
		$this->cache = get_site_option( $this->option );

		if ( ! is_array( $this->cache ) ) {
			$this->cache = array();
		}
	}

	public function shutdown() {
		self::save();

		$this->shutdown_done = true;
	}
}
