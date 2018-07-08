<?php

final class ITSEC_VM_Utility {
	private static $wordpress_release_dates = false;

	public static function get_email_addresses() {
		$settings = ITSEC_Modules::get_settings( 'version-management' );
		$contacts = $settings['email_contacts'];

		if ( empty( $contacts ) ) {
			// Select all roles that can manage the plugin when the setting is empty.

			$validator = ITSEC_Modules::get_validator( 'version-management' );

			$users_and_roles = $validator->get_available_admin_users_and_roles();
			$contacts = array_keys( $users_and_roles['roles'] );
		}


		$addresses = array();

		foreach ( $contacts as $contact ) {
			if ( (string) $contact === (string) intval( $contact ) ) {
				$users = array( get_userdata( $contact ) );
			} else {
				list( $prefix, $role ) = explode( ':', $contact, 2 );

				if ( empty( $role ) ) {
					continue;
				}

				$users = get_users( array( 'role' => $role ) );
			}

			foreach ( $users as $user ) {
				if ( is_object( $user ) && ! empty( $user->user_email ) ) {
					$addresses[] = $user->user_email;
				}
			}
		}

		$addresses = array_unique( $addresses );

		if ( ! empty( $addresses ) ) {
			return $addresses;
		}


		if ( is_callable( 'wp_roles' ) ) {
			$roles = wp_roles();
		} else {
			$roles = new WP_Roles();
		}

		foreach ( $roles->roles as $role => $details ) {
			if ( isset( $details['capabilities']['manage_options'] ) && ( true === $details['capabilities']['manage_options'] ) ) {
				$users = get_users( array( 'role' => $role ) );

				foreach ( $users as $user ) {
					if ( ! empty( $user->user_email ) ) {
						$addresses[] = $user->user_email;
					}
				}
			}
		}

		return $addresses;
	}

	public static function is_wordpress_version_outdated( $version = false ) {
		if ( false === $version ) {
			$version = self::get_wordpress_version();
		}

		$version = self::get_clean_version( $version );

		if ( false === $version ) {
			// If the version is invalid, assume that it is outdated since the version file has likely been modified.
			return true;
		}

		$release_dates = self::get_wordpress_release_dates();

		if ( empty( $release_dates ) ) {
			// If release data is missing, the tests cannot proceed.
			return false;
		}

		uksort( $release_dates, 'version_compare' );

		$latest_timestamp = end( $release_dates );
		$latest_version = key( $release_dates );

		$previous_timestamp = prev( $release_dates );
		$previous_version = key( $release_dates );

		// If this version is the previous release version and the latest release version has been out for less than a
		// month, do not list this version as outdated.
		if ( $version === $previous_version && $latest_timestamp > time() - MONTH_IN_SECONDS ) {
			return false;
		}

		if ( ! isset( $release_dates[$version] ) ) {
			$latest_major_version = self::get_major_version( $latest_version );
			$current_major_version = self::get_major_version( $version );

			if ( $latest_major_version === $current_major_version && version_compare( $version, $latest_version, '>=' ) ) {
				// Looks like a new minor release that hasn't come through in the release dates details yet.
				return false;
			}

			$next_major_version = self::get_next_major_version( $latest_version );

			if ( false !== $next_major_version && ( $version === $next_major_version || $version === "$next_major_version.0" ) ) {
				// This version is a development version.
				return false;
			}

			$next_development_version = self::get_next_major_version( $next_major_version );

			if ( false !== $next_development_version && ( $version === $next_development_version || $version === "$next_development_version.0" ) ) {
				// This version is the latest development version shortly after a new major version is released. It's
				// also possible that it is a fake version, but we'll assume that it's a development version.
				return false;
			}

			// Return true since the version is likely fake to fool automatic upgrades.
			return true;
		}

		if ( version_compare( $version, $latest_version, '>=' ) ) {
			// Running a current version.
			return false;
		}

		$current_version_timestamp = $release_dates[$version];
		$timestamp_diff = $latest_timestamp - $current_version_timestamp;

		if ( $timestamp_diff >= MONTH_IN_SECONDS ) {
			// If a month or more of time spans between the release of this version and the latest version, this version
			// is outdated.
			return true;
		}

		$latest_major_version = self::get_major_version( $latest_version );

		// Tests when the version is an older major version.
		if ( false !== $latest_major_version && version_compare( $version, $latest_major_version, '<' ) ) {
			if ( isset( $release_dates[$latest_major_version] ) ) {
				$latest_major_timestamp = $release_dates[$latest_major_version];
			} else if ( isset( $release_dates["$latest_major_version.0"] ) ) {
				$latest_major_timestamp = $release_dates["$latest_major_version.0"];
			}

			$latest_major_age = time() - $latest_major_timestamp;

			if ( isset( $latest_major_timestamp ) && $latest_major_age >= MONTH_IN_SECONDS ) {
				// If the latest major version has been out for a month or more and this version is an older major
				// major version, this version is outdated.
				return true;
			}

			return false;
		}

		// This version is not the latest release, but it is not old enough to be considered outdated.
		return false;
	}

	public static function get_major_version( $version ) {
		if ( ! preg_match( '/^(\d+)\.(\d+)/', $version, $match ) ) {
			return false;
		}

		return $match[1] . '.' . $match[2];
	}

	public static function get_next_major_version( $version ) {
		if ( ! preg_match( '/^(\d+)\.(\d+)/', $version, $match ) ) {
			return false;
		}

		if ( $match[2] > 8 ) {
			return ( $match[1] + 1 ) . '.0';
		}

		return $match[1] . '.' . ( $match[2] + 1 );
	}

	public static function get_clean_version( $version ) {
		if ( preg_match( '/^(\d+\.\d+(?:\.\d+)?)/', $version, $match ) ) {
			return $match[1];
		}

		return false;
	}

	public static function get_wordpress_version( $version_file_path = false ) {
		if ( false === $version_file_path ) {
			$version_file_path = ABSPATH . WPINC . '/version.php';
		}

		$fh = fopen( $version_file_path, 'r' );

		if ( false === $fh || feof( $fh ) ) {
			return false;
		}

		$content = fread( $fh, 2048 );
		fclose( $fh );

		if ( preg_match( '/\\$wp_version = \'([^\']+)\';/', $content, $match ) ) {
			return $match[1];
		}

		return false;
	}

	public static function get_wordpress_release_dates() {
		if ( is_array( self::$wordpress_release_dates ) ) {
			return self::$wordpress_release_dates;
		}

		$data = get_site_option( 'itsec_vm_wp_releases' );

		if ( is_array( $data ) && isset( $data['expires'] ) && $data['expires'] > time() && isset( $data['dates'] ) ) {
			self::$wordpress_release_dates = $data['dates'];
			return $data['dates'];
		}

		$data = array(
			'expires' => time() + DAY_IN_SECONDS,
			'dates'   => isset( $data['dates'] ) ? $data['dates'] : array(),
		);

		$https_url = 'https://s3.amazonaws.com/downloads.ithemes.com/public/wordpress-release-dates.json';
		$http_url = 'http://downloads.ithemes.com/public/wordpress-release-dates.json';

		if ( wp_http_supports( array( 'ssl' ) ) ) {
			$response = wp_remote_get( $https_url );
		}

		if ( ! isset( $response ) || is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$response = wp_remote_get( $http_url );
		}

		if ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) {
			$dates = json_decode( $response['body'], true );

			if ( is_array( $dates ) ) {
				uksort( $dates, 'version_compare' );
				$data['dates'] = $dates;
			}
		}

		// Refresh more quickly if something went wrong with loading the data.
		if ( empty( $data['dates'] ) ) {
			$data['expires'] = time() + HOUR_IN_SECONDS;
		}

		update_site_option( 'itsec_vm_wp_releases', $data );

		self::$wordpress_release_dates = $data['dates'];

		return $data['dates'];
	}
}
