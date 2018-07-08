<?php

final class ITSEC_Away_Mode_Utilities {

	/**
	 * Check if the config file signaling away mode is active exists.
	 *
	 * @return bool
	 */
	public static function has_active_file() {
		if ( @file_exists( self::get_active_file_name() ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Create a config file specifying that away mode is active.
	 *
	 * @return bool
	 */
	public static function create_active_file() {
		if ( self::has_active_file() ) {
			return true;
		}

		$file = self::get_active_file_name();

		$result = @file_put_contents( $file, 'true' );

		if ( false === $result ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Delete the config file specifying that away mode is active.
	 *
	 * @return bool
	 */
	public static function delete_active_file() {
		if ( ! self::has_active_file() ) {
			return true;
		}

		$file = self::get_active_file_name();

		return @unlink( $file );
	}

	/**
	 * Get the file name for the config file specifying that away mode is active,
	 *
	 * @return string
	 */
	public static function get_active_file_name() {
		$file_name = apply_filters( 'itsec_filer_away_mode_active_file', ITSEC_Core::get_storage_dir() . '/itsec_away.confg' );

		return $file_name;
	}

	/**
	 * Check if the current UTC time falls between the two specified times, inclusive.
	 *
	 * @param int  $start            The UTC timestamp signalling the beginning of the active window.
	 * @param int  $end              The UTC timestamp signalling the end of the active window.
	 * @param bool $include_details  Whether to include additional details about the active window.
	 *
	 * @return array|bool
	 */
	public static function is_current_timestamp_active( $start, $end, $include_details = false ) {
		$now = ITSEC_Core::get_current_time_gmt();

		$active = false;

		if ( $start <= $now && $now <= $end ) {
			$active = true;
		}

		if ( ! $include_details ) {
			return $active;
		}


		if ( $start > $end ) {
			$remaining = false;
			$next = false;
			$length = false;

			/* translators: 1: start timestamp, 2: end timestamp */
			$error = new WP_Error( 'itsec-away-mode-is-current-timestamp-in-range-start-after-end', sprintf( __( 'The supplied data is invalid. The supplied start (%1$s) is after the supplied end (%2$s).', 'it-l10n-ithemes-security-pro' ), $start, $end ) );
		} else {
			$remaining = $end - $now;
			$next = $start - $now;
			$length = $end - $start;
			$error = false;

			if ( $now < $start ) {
				$remaining = false;
			}

			if ( $next < 0 ) {
				$next = false;
			}
		}

		return compact( 'active', 'remaining', 'next', 'length', 'error' );
	}

	/**
	 * Check if the current local time falls between the two specified times, inclusive.
	 *
	 * @param int  $start            The local timestamp signalling the beginning of the active window.
	 * @param int  $end              The local timestamp signalling the end of the active window.
	 * @param bool $include_details  Whether to include additional details about the active window.
	 *
	 * @return array|bool
	 */
	public static function is_current_time_active( $start, $end, $include_details = false ) {
		$current_time = ITSEC_Core::get_current_time();
		$now = $current_time - strtotime( date( 'Y-m-d', $current_time ) );

		$active = false;

		if ( $start <= $end ) {
			if ( $start <= $now && $now <= $end ) {
				$active = true;
			}
		} else {
			if ( $start <= $now || $now <= $end ) {
				$active = true;
			}
		}

		if ( ! $include_details ) {
			return $active;
		}


		$remaining = $end - $now;
		$next = $start - $now;
		$length = $end - $start;

		if ( $active && $remaining < 0 ) {
			$remaining += DAY_IN_SECONDS;
		} else if ( ! $active && $remaining >= 0 ) {
			$remaining -= DAY_IN_SECONDS;
		}

		if ( $next < 0 ) {
			$next += DAY_IN_SECONDS;
		}

		if ( $length < 0 ) {
			$length += DAY_IN_SECONDS;
		}


		return compact( 'active', 'remaining', 'next', 'length' );
	}
}
