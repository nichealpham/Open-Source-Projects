<?php

final class ITSEC_Lib_User_Activity {
	private static $instance;

	private $user_id = false;

	private function __construct() {
		if ( did_action( 'init' ) ) {
			$this->identify_user();
		} else {
			add_action( 'init', array( $this, 'identify_user' ) );
		}
	}

	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function get_last_seen( $user_id = false ) {
		if ( false === $user_id ) {
			$user_id = get_current_user_id();
		}

		if ( 0 === $user_id ) {
			return false;
		}

		return get_user_meta( $user_id, 'itsec_user_activity_last_seen', true );
	}

	public function identify_user() {
		$this->user_id = get_current_user_id();

		if ( 0 !== $this->user_id ) {
			add_action( 'shutdown', array( $this, 'update_last_seen' ), 0 );
		}
	}

	public function update_last_seen() {
		$last_seen = $this->get_last_seen( $this->user_id );

		if ( $last_seen < time() - HOUR_IN_SECONDS ) {
			update_user_meta( $this->user_id, 'itsec_user_activity_last_seen', time() );
			delete_user_meta( $this->user_id, 'itsec_user_activity_last_seen_notification_sent' );
		}
	}
}
ITSEC_Lib_User_Activity::get_instance();
