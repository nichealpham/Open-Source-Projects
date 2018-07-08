<?php

final class ITSEC_Lock {
	public static function get( $name, $expiration = HOUR_IN_SECONDS, $allow_api_request = false ) {
		global $wpdb;

		if ( ! $allow_api_request && ITSEC_Core::is_api_request() ) {
			return false;
		}

		$lock = "itsec-lock-$name";
		$now = time();

		if ( ! empty( $wpdb->sitemeta ) ) {
			$result = $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO `$wpdb->sitemeta` (`site_id`, `meta_key`, `meta_value`) VALUES (%d, %s, %s) /* LOCK */", $wpdb->siteid, $lock, $now ) );
		} else {
			$result = $wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO `$wpdb->options` (`option_name`, `option_value`, `autoload`) VALUES (%s, %s, 'no') /* LOCK */", $lock, $now ) );
		}

		if ( ! $result ) {
			// The lock exists. See if it has expired.

			$locked = get_site_option( $lock );

			if ( ! $locked ) {
				// Can't write or read the lock. Bail due to an unknown and hopefully temporary error.
				return false;
			}

			if ( $locked > $now - $expiration ) {
				// The lock still exists and has not expired.
				return false;
			}
		}

		// Ensure that the lock is set properly by triggering all the regular actions and filters.
		update_site_option( $lock, $now );

		return true;
	}

	public static function remove( $name ) {
		$lock = "itsec-lock-$name";

		delete_site_option( $lock );
	}
}
