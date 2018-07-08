<?php
/**
 * iThemes Security IP tools library.
 *
 * Contains the ITSEC_Lib_IP_Tools class.
 *
 * @package iThemes_Security
 */

/**
 * iThemes Security IP Tools Library class.
 *
 * Utility class for validating and comparing IPs, as well as converting ranges. Supports IPv4 and IPv6.
 *
 * @package iThemes_Security
 * @since 2.2.0
 */
class ITSEC_Lib_IP_Tools {
	/**
	 * Stores max cidr (number of bits) for each IP version.
	 *
	 * @static
	 * @access private
	 *
	 * @var array
	 */
	private static $_max_cidr = array(
		4 => 32,
		6 => 128,
	);

	/**
	 * Validates an IP or an IP Range using CIDR notation
	 *
	 * @since 2.2.0
	 *
	 * @static
	 * @access public
	 *
	 * @param string $ip The IP address to validate, can be given in CIDR notation
	 *
	 * @return bool|int False for an invalid IP or range, and the IP version (4 or 6) on for a valid one
	 */
	public static function validate( $ip ) {
		$ip_parts = self::_ip_cidr( $ip );

		if ( filter_var( $ip_parts->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
			if ( ! isset( $ip_parts->cidr ) || self::_is_valid_cidr( $ip_parts->cidr, 4 ) ) {
				return 4;
			}

			// Invalid CIDR
			return false;
		} elseif ( filter_var( $ip_parts->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
			if ( ! isset( $ip_parts->cidr ) || self::_is_valid_cidr( $ip_parts->cidr, 6 ) ) {
				return 6;
			}

			// Invalid CIDR
			return false;
		}

		// IP is not valid v4 or v6 IP
		return false;
	}

	/**
	 * Converts an IP or an IP Range using CIDR notation, to it's parts (IP and CIDR)
	 *
	 * @since 2.2.0
	 *
	 * @static
	 * @access private
	 *
	 * @param string $ip The IP address, can be given in CIDR notation
	 *
	 * @return object IP parts, ->ip and ->cidr
	 */
	private static function _ip_cidr( $ip ) {
		$ip_parts = new stdClass();
		if ( strpos( $ip, '/' ) ) {
			list( $ip_parts->ip, $ip_parts->cidr ) = explode( '/', $ip );
		} else {
			$ip_parts->ip   = $ip;
		}

		return $ip_parts;
	}

	/**
	 * Validates a CIDR value for an IP version
	 *
	 * @since 2.2.0
	 *
	 * @static
	 * @access private
	 *
	 * @param string $cidr The CIDR value to validate
	 * @param int $version The IP version to validate the CIDR for (4 or 6)
	 *
	 * @return bool
	 */
	private static function _is_valid_cidr( $cidr, $version ) {
		// $version needs to be valid
		if ( ! in_array( $version, array_keys( self::$_max_cidr ) ) ) {
			return false;
		}

		// The cidr needs to be numeric and between 0 and the max
		if ( isset( $cidr ) && ( ! ctype_digit( $cidr ) || $cidr > self::$_max_cidr[ $version ] ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Checks to see if a given IP/CIDR is a range
	 *
	 * @since 2.2.0
	 *
	 * @static
	 * @access public
	 *
	 * @param string $ip The IP address, can be given in CIDR notation
	 * @param int $version The IP version (4 or 6). This needs to be supplied if skipping validation (for efficiency)
	 * @param bool $validate True to validate the IP, and false to skip (version must be supplied to skip) (Default true)
	 *
	 * @return bool
	 */
	public static function is_range( $ip, $version = null, $validate = true ) {
		if ( $validate || ! isset( $version ) ) {
			$version = self::validate( $ip );

			// If the IP isn't valid, it's not a range.
			if ( ! $version ) {
				return false;
			}
		}

		$ip_parts = self::_ip_cidr( $ip );

		// If there is no cidr specified or if it's the max for this IP version, then this is not a range.
		return !( ! isset( $ip_parts->cidr ) || $ip_parts->cidr == self::$_max_cidr[ $version ] );
	}

	/**
	 * Gets the start and end IPs for a given range
	 *
	 * @static
	 * @access public
	 *
	 * @param string $ip The IP address, can be given in CIDR notation
	 *
	 * @return bool|array False if the IP is invalid, and an array containing start and end IPs for the range specified otherwise
	 */
	public static function get_ip_range( $ip ) {
		$version = self::validate( $ip );
		if ( ! $version ) {
			return false;
		}

		$ip_parts = self::_ip_cidr( $ip );

		// If this isn't a range, return a single address
		if ( ! self::is_range( $ip, $version, false ) ) {
			return array(
				'start' => $ip_parts->ip,
				'end'   => $ip_parts->ip,
			);
		}

		$mask = self::get_mask( $ip_parts->cidr, $version );

		$range = array();
		$range['start'] = inet_ntop( inet_pton( $ip_parts->ip ) & inet_pton( $mask ) );
		$range['end'] = inet_ntop( inet_pton( $ip_parts->ip ) | ~ inet_pton( $mask ) );
		return  $range;
	}

	/**
	 * Gets the mask from CIDR and IP version
	 *
	 * @static
	 * @access public
	 *
	 * @param string $cidr The CIDR value to validate
	 * @param int $version The IP version to validate the CIDR for (4 or 6)
	 *
	 * @return string IP Mask
	 */
	public static function get_mask( $cidr, $version ) {
		if ( ! in_array( $version, array( 4, 6 ) ) ) {
			return false;
		}
		$bin_mask = str_repeat( '1', $cidr ) . str_repeat( '0', self::$_max_cidr[ $version ] - $cidr );

		$bin2mask_method = '_bin2mask_v' . $version;

		return call_user_func( array( 'self', $bin2mask_method ), $bin_mask );
	}

	/**
	 * Gets the IPv4 mask from the binary representation
	 *
	 * @static
	 * @access private
	 *
	 * @param string $bin_mask The binary representation of the mask
	 *
	 * @return string IP Mask
	 */
	private static function _bin2mask_v4( $bin_mask ) {
		$mask = array();
		// Eight binary bits per number
		foreach ( str_split( $bin_mask, 8 ) as $num ) {
			// Convert from bin to dec and append
			$mask[] = base_convert( $num, 2, 10 );
		}

		// Explode our new hex mask into 4 character segments and implode with colons
		return implode( '.', $mask );
	}

	/**
	 * Gets the IPv6 mask from the binary representation
	 *
	 * @static
	 * @access private
	 *
	 * @param string $bin_mask The binary representation of the mask
	 *
	 * @return string IP Mask
	 */
	private static function _bin2mask_v6( $bin_mask ) {
		$mask = '';
		// Four binary bits per hex character
		foreach ( str_split( $bin_mask, 4 ) as $char ) {
			// Convert from bin to hex and append
			$mask .= base_convert( $char, 2, 16 );
		}

		// Explode our new hex mask into 4 character segments and implode with colons
		return implode( ':', str_split( $mask, 4 ) );
	}

	/**
	 * Checks to see if an IP or range is within another IP or range
	 *
	 * @static
	 * @access public
	 *
	 * @param string $ip The IP address to check to see if is contained, can be given in CIDR notation
	 * @param string $range The IP address to check to see if contains, can be given in CIDR notation
	 *
	 * @return bool False if the given IP or range is not completely contained in the supplied range. True if it is
	 */
	public static function in_range( $ip, $range ) {
		$ip_version = self::validate( $ip );
		// If the IP isn't valid, it's not in the range
		if ( ! $ip_version ) {
			return false;
		}

		$range_version = self::validate( $range );
		// If the range isn't valid or isn't the same IP version as the first IP, it's not in the range
		if ( $ip_version != $range_version ) {
			return false;
		}

		if ( ! self::is_range( $range, $range_version, false ) ) {
			if ( ! self::is_range( $ip, $ip_version, false ) ) {
				$ip_parts = self::_ip_cidr( $ip );
				$range_parts = self::_ip_cidr( $ip );

				// If neither is a range, just compare and return
				return $ip_parts->ip == $range_parts->ip;
			} else {
				// If the IP is a range and the specified range isn't, then return false
				return false;
			}
		}

		$ip_range = array_map( 'inet_pton', self::get_ip_range( $ip ) );
		$in_range = array_map( 'inet_pton', self::get_ip_range( $range ) );

		return ( $in_range['start'] <= $ip_range['start'] && $ip_range['end'] <= $in_range['end'] );
	}

	/**
	 * Checks to see if an IP or range intersects with another IP or range
	 *
	 * @static
	 * @access public
	 *
	 * @param string $ip1 IP address, can be given in CIDR notation
	 * @param string $ip2 IP address, can be given in CIDR notation
	 *
	 * @return bool
	 */
	public static function intersect( $ip1, $ip2 ) {
		$ip1_version = self::validate( $ip1 );
		// If the first IP isn't valid, there is no intersection
		if ( ! $ip1_version ) {
			return false;
		}

		$ip2_version = self::validate( $ip2 );
		// If the second IP isn't valid or isn't the same IP version as the first IP, there is no intersection
		if ( $ip1_version != $ip2_version ) {
			return false;
		}

		// If neither is a range, just compare and return
		if ( ! self::is_range( $ip1, $ip1_version, false ) && ! self::is_range( $ip2, $ip2_version, false ) ) {
			$ip1_parts = self::_ip_cidr( $ip1 );
			$ip2_parts = self::_ip_cidr( $ip2 );

			return $ip1_parts->ip == $ip2_parts->ip;
		}

		$ip1_range = array_map( 'inet_pton', self::get_ip_range( $ip1 ) );
		$ip2_range = array_map( 'inet_pton', self::get_ip_range( $ip2 ) );

		return (
			// $ip1_range start is in $ip2_range
			( $ip2_range['start'] <= $ip1_range['start'] && $ip1_range['start'] <= $ip2_range['end'] ) ||
			// $ip1_range end is in $ip2_range
			( $ip2_range['start'] <= $ip1_range['end'] && $ip1_range['end'] <= $ip2_range['end'] ) ||
			// $ip2_range start is in $ip1_range
			( $ip1_range['start'] <= $ip2_range['start'] && $ip2_range['start'] <= $ip1_range['end'] ) ||
			// $ip2_range end is in $ip1_range
			( $ip1_range['start'] <= $ip2_range['end'] && $ip2_range['end'] <= $ip1_range['end'] )
		);
	}

	/**
	 * Converts IP with * wildcards to CIDR format
	 *
	 * Limited to only contiguous wildcards at the end of an IP, and wildcards represent a whole segment not a single character or digit
	 *
	 * @since 2.2.0
	 *
	 * @static
	 * @access public
	 *
	 * @param string $ip The IP address, can be given in CIDR notation
	 * @param int $version The IP version (4 or 6). This needs to be supplied if skipping validation (for efficiency)
	 * @param bool $validate True to validate the IP, and false to skip (version must be supplied to skip) (Default true)
	 *
	 * @return string IP in CIDR format
	 */
	public static function ip_wild_to_ip_cidr( $ip, $version = null, $validate = true ) {
		if ( $validate || ! isset( $version ) ) {
			// Replace the wildcards with zeroes and test to get version
			$version = self::validate( self::_clean_wildcards( $ip ) );

			// If the IP isn't valid, it's not a range.
			if ( ! $version ) {
				return false;
			}
		}

		// Not meant for IPs already using CIDR notation and only works on wildcards
		if ( strpos( $ip, '/' ) || false === strpos( $ip, '*' ) ) {
			return $ip;
		}

		$wild_to_cidr_method = "_ipv{$version}_wild_to_ip_cidr";

		return call_user_func( array( 'self', $wild_to_cidr_method ), $ip );
	}

	/**
	 * Converts IPv4 IP with * wildcards to CIDR format
	 *
	 * Limited to only contiguous wildcards at the end of an IP, and wildcards represent a whole segment not a single character or digit
	 *
	 * @since 2.2.0
	 *
	 * @static
	 * @access private
	 *
	 * @param string $ip The IP address, can be given in CIDR notation
	 *
	 * @return string IP in CIDR format
	 */
	private static function _ipv4_wild_to_ip_cidr( $ip ) {
		$host_parts = array_reverse( explode( '.', trim( $ip ) ) );

		$mask = self::$_max_cidr[4];
		$ip   = self::_clean_wildcards( $ip );

		//convert hosts with wildcards to host with netmask and create rule lines
		foreach ( $host_parts as $part ) {
			if ( '*' === $part ) {
				$mask -= 8;
			} else {
				break; // We only want to deal with contiguous wildcards at the end of an IP
			}
		}

		return "{$ip}/{$mask}";
	}

	/**
	 * Converts IPv6 IP with * wildcards to CIDR format
	 *
	 * Limited to only contiguous wildcards at the end of an IP, and wildcards represent a whole segment not a single character or digit
	 *
	 * @since 2.2.0
	 *
	 * @static
	 * @access private
	 *
	 * @param string $ip The IP address, can be given in CIDR notation
	 *
	 * @return string IP in CIDR format
	 */
	private static function _ipv6_wild_to_ip_cidr( $ip ) {
		$host_parts = array_reverse( explode( ':', trim( $ip ) ) );

		$mask = self::$_max_cidr[6];
		$ip   = self::_clean_wildcards( $ip );

		//convert hosts with wildcards to host with netmask and create rule lines
		foreach ( $host_parts as $part ) {
			if ( '*' === $part ) {
				$mask -= 16;
			} else {
				break; // We only want to deal with contiguous wildcards at the end of an IP
			}
		}

		return "{$ip}/{$mask}";
	}

	/**
	 * Remove wildcards, but only those that represent an entire chunk or octets
	 *
	 * @param string $ip The IP to clean
	 *
	 * @return string IP address with wildcards replaced with 0s
	 */
	private static function _clean_wildcards( $ip ) {
		$search = array(
			'/([:\.])(\*(\1|$))+/', // Match all whole chunks in the middle with wildcards, or a wildcard as the whole chunk at the end
			'/^\*([:\.])/',         // Match a wildcard as the whole first chunk
		);
		return preg_replace_callback( $search, array( 'self', 'clean_wildcards_preg_replace_callback' ), $ip );
	}

	/**
	 * Used with preg_replace_callback() to replace wildcards with 0 ONLY in cases where the wildcard is the whole chunk
	 *
	 * @param array $matches The matches found by preg_replace_callback()
	 *
	 * @return string Replacement string
	 */
	public static function clean_wildcards_preg_replace_callback( $matches ) {
		return str_replace( '*', '0', $matches[0] );
	}

	/**
	 * Converts IP in CIDR notation to a regex
	 *
	 * @since 2.2.0
	 *
	 * @static
	 * @access public
	 *
	 * @param string $ip The IP address, can be given in CIDR notation
	 * @param int $version The IP version (4 or 6). This needs to be supplied if skipping validation (for efficiency)
	 * @param bool $validate True to validate the IP, and false to skip (version must be supplied to skip) (Default true)
	 *
	 * @return string The IP in regex format
	 */
	public static function ip_cidr_to_ip_regex( $ip, $version = null, $validate = true ) {
		// Not meant for IPs already using wildcards
		if ( strpos( $ip, '*' ) ) {
			return $ip;
		}

		if ( $validate || ! isset( $version ) ) {
			$version = self::validate( $ip );

			// If the IP isn't valid, it's not a range.
			if ( ! $version ) {
				return false;
			}
		}

		$ip_parts = self::_ip_cidr( $ip );

		$cidr_to_wild_method = "_ipv{$version}_cidr_to_ip_regex";

		return call_user_func( array( 'self', $cidr_to_wild_method ), $ip_parts );
	}

	/**
	 * Converts IPv4 in CIDR notation to a regex
	 *
	 * @since 2.2.0
	 *
	 * @static
	 * @access private
	 *
	 * @param object $ip_parts The IP address parts (->ip and ->cidr), generated using self::_ip_cidr()
	 *
	 * @return string The IP in regex format
	 */
	private static function _ipv4_cidr_to_ip_regex( $ip_parts ) {
		// Explode IP into octets and reverse them to work backwards
		$octets = array_reverse( explode( '.', $ip_parts->ip ) );

		if ( ! isset( $ip_parts->cidr ) ) {
			$ip_parts->cidr = self::$_max_cidr[4];
		}

		// How many bits are actually masked
		$masked_bits = self::$_max_cidr[4] - $ip_parts->cidr;

		$i = 0;
		// For each set of 8 masked bits, we match a whole octet (3 digits is good enough here)
		while ( $masked_bits >= 8 ) {
			$octets[ $i ] = '[0-9]{1,3}';
			$masked_bits -= 8;
			++$i;
		}

		// If there are still masked bits to deal with after handling all whole octets
		if ( $masked_bits ) {
			// The step is the gap between the low and high values for this octet
			$step = base_convert( str_repeat( '1', $masked_bits ), 2, 10 ) + 1;
			// $low is the low value for this octect, based on the step
			$low = $octets[ $i ] - ( $octets[ $i ] % $step );
			// The regex we use is simply a valid range of numbers in a group with alternation, ex: (0|1|2|3|4|5|6|7)
			$octets[ $i ] = '(' . implode( '|', range( $low, $low + $step - 1 ) ) . ')';
		}

		// Re-reverse the octets array to set things straight, and put the pieces back together
		// Escape the . for a literal .
		return implode( '\.', array_reverse( $octets ) );
	}

	/**
	 * Converts IPv6 in CIDR notation to a regex
	 *
	 * @since 2.2.0
	 *
	 * @static
	 * @access private
	 *
	 * @param object $ip_parts The IP address parts (->ip and ->cidr), generated using self::_ip_cidr()
	 *
	 * @return string The IP in regex format
	 */
	private static function _ipv6_cidr_to_ip_regex( $ip_parts ) {
		// If the IP address has a :: in it, we need to expand that out so we have all eight chuks to work with
		$colons = substr_count( $ip_parts->ip, ':' );
		if ( $colons < 7 ) {
			// Fill out all the chunks so we can properly mask them all
			$ip_parts->ip = str_replace( '::', str_repeat( ':0', 7 - $colons + 1 ) . ':', $ip_parts->ip );
		}

		// Explode IP into chunks and reverse them to work backwards
		$chunks = array_reverse( explode( ':', $ip_parts->ip ) );

		if ( ! isset( $ip_parts->cidr ) ) {
			$ip_parts->cidr = self::$_max_cidr[6];
		}
		$masked_bits = self::$_max_cidr[6] - $ip_parts->cidr;

		$i = 0;
		// For each set of 16 masked bits, we match a whole chunk (1-4 hex characters)
		while ( $masked_bits >= 16 ) {
			$chunks[ $i ] = '[0-f]{1,4}';
			$masked_bits -= 16;
			++$i;
		}


		// If there are still masked bits to deal with after handling all whole chunks, we start working in single hex characters
		if ( $masked_bits ) {
			// Explode the chunk into characters and reverse them to work backwards
			$characters = array_reverse( str_split( str_pad( $chunks[ $i ], 4, '0', STR_PAD_LEFT ) ) );

			$j = 0;
			// For each set of 4 masked bits, we match a single hex character
			while ( $masked_bits >= 4 ) {
				$characters[ $j ] = '[0-f]';
				$masked_bits -= 4;
				++$j;
			}

			// If there are still masked bits to deal with after handling all whole characters
			if ( $masked_bits ) {
				// $step is the gap between the low and high values for this hex character (we want this in base 10 for use in operations)
				$step = base_convert( str_repeat( '1', $masked_bits ), 2, 10 ) + 1;
				// $value is the current value of the character in base 10
				$value = base_convert( $characters[ $j ], 16, 10 );
				// $low is the base 10 representation of the low value for this character based on the step
				$low = $value - ( $value % $step );
				// $high is the hex value (redy for our regex) of the high value for this character
				$high = base_convert( $low + $step - 1, 10, 16 );
				// Convert $low to hex for our
				$low = base_convert( $low, 10, 16 );
				// For our regex we use a character set from low to high, ex: [4-7] or [8-b]
				$characters[ $j ] = "[{$low}-{$high}]";
			}

			// Re-reverse the characters array to set things straight, and put the pieces back together
			$chunks[ $i ] = implode( array_reverse( $characters ) );
			$zeroes = strlen( $chunks[ $i ] ) - strlen( ltrim( $chunks[ $i ], '0' ) );
			if ( $zeroes ) {
				$chunks[ $i ] = str_repeat( '0?', $zeroes ) . ltrim( $chunks[ $i ], '0' );
			}
		}

		for ( $i; $i < count( $chunks ); $i++ ) {
			$chunks[ $i ] = ltrim( $chunks[ $i ], '0' );
			$num_chars = strlen( $chunks[ $i ] );
			if ( $num_chars < 4 ) {
				$chunks[ $i ] = str_repeat( '0?', 4 - $num_chars ) . $chunks[ $i ];
			}
		}

		// Re-reverse the chunks array to set things straight, and put the pieces back together
		$regex = implode( ':', array_reverse( $chunks ) );

		// Replace multiple chunks of all zeros with a regular expression that makes them optional but still enforces accurate matching
		$regex = preg_replace_callback( '/0\?0\?0\?0\?(\:0\?0\?0\?0\?)+/', array( 'self', 'ipv6_regex_preg_replace_callback' ), $regex );

		return $regex;
	}

	/**
	 * Used with preg_replace_callback() to make chunks of all zeroes optional while still enforcing accurate matching
	 *
	 * @param array $matches The matches found by preg_replace_callback()
	 *
	 * @return string Replacement string
	 */
	public static function ipv6_regex_preg_replace_callback( $matches ) {
		// Get the number of colons (chunks - 1) that we are replacing so we make sure to match no more than the original number of chunks
		$colons = substr_count( $matches[0], ':' );
		return sprintf( '(0{0,4}:){0,%d}(0{0,4})?', $colons );
	}
}
