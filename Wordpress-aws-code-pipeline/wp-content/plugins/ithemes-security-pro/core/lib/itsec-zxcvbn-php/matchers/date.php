<?php
/**
 * a "date" is recognized as:
 *   any 3-tuple that starts or ends with a 2- or 4-digit year,
 *   with 2 or 0 separator chars (1.1.91 or 1191),
 *   maybe zero-padded (01-01-91 vs 1-1-91),
 *   a month between 1 and 12,
 *   a day between 1 and 31.
 *
 * note: this isn't true date parsing in that "feb 31st" is allowed,
 * this doesn't check for leap years, etc.
 *
 * recipe:
 * start with regex to find maybe-dates, then attempt to map the integers
 * onto month-day-year to filter the maybe-dates into dates.
 * finally, remove matches that are substrings of other matches to reduce noise.
 *
 * note: instead of using a lazy or greedy regex to find many dates over the full string,
 * this uses a ^...$ regex against every substring of the password -- less performant but leads
 * to every possible date match.
 */
class ITSEC_Zxcvbn_Date_Match extends ITSEC_Zxcvbn_Match {

	protected $has_full_year = false;

	protected static $date_max_year = 2050;
	protected static $date_min_year = 1000;

	protected static $date_splits = array(
		'4' => array(
			array( 1, 2 ),
			array( 2, 3 )
		),
		'5' => array(
			array( 1, 3 ),
			array( 2, 3 )
		),
		'6' => array(
			array( 1, 2 ),
			array( 2, 4 ),
			array( 4, 5 )
		),
		'7' => array(

			array( 1, 3 ),
			array( 2, 3 ),
			array( 4, 5 ),
			array( 4, 6 )
		),
		'8' => array(
			array( 2, 4 ),
			array( 4, 6 )
		),
	);


	public function __construct( $password, $result ) {
		$this->password = $password;
		foreach ( $result as $key => $value ) {
			$this->{$key} = $value;
		}
	}

	/**
	 * Finds matches in the password.
	 * @param string $password         Password to check for match
	 * @param array  $penalty_strings  Strings that should be penalized if in the password. This should be things like the username, first and last name, etc.
	 *
	 * @return ITSEC_Zxcvbn_Match[]    Array of Match objects
	 */
	public static function match( $password, array $penalty_strings = array() ) {
		$matches = array();

		$maybe_date_no_separator = '/^\d{4,8}$/';
		$maybe_date_with_separator =
	        '#^'            // Anchor to start
	        . '(\d{1,4})'   // day, month, year
	        . '([\s/\_.-])' // separator
			. '(\d{1,2})'   // day, month
			. '\2'          // same separator
			. '(\d{1,4})'   // day, month, year
			. '$#';         // Anchor to end

		// dates without separators are between length 4 '1191' and 8 '11111991'
		for ( $begin = 0; $begin <= strlen( $password ) - 4; $begin++ ) {
			for ( $end = $begin + 3; $end <= $begin + 7; $end++ ) {
				if ( $end >= strlen( $password ) ) {
					break;
				}

				$token = substr( $password, $begin, $end - $begin + 1 );

				if ( ! preg_match( $maybe_date_no_separator, $token ) ) {
					continue;
				}
				$candidates = array();
				for ( $q = 0; $q < count( self::$date_splits[ strlen( $token ) ] ); $q++ ) {
					$k = self::$date_splits[ strlen( $token ) ][ $q ][0];
					$l = self::$date_splits[ strlen( $token ) ][ $q ][1];
					$dmy = self::map_ints_to_dmy( array( substr( $token, 0, $k ), substr( $token, $k, $l - $k ), substr( $token, $l ) ) );
					if ( ! empty( $dmy ) ) {
						$candidates[] = $dmy;
					}
		        }
		        if ( empty( $candidates ) ) {
		        	continue;
		        }
				// at this point: different possible dmy mappings for the same i,j substring.
				// match the candidate date that likely takes the fewest guesses: a year closest to REFERENCE_YEAR.
				//
				// ie, considering '111504', prefer 11-15-04 to 1-1-1504
				// (interpreting '04' as 2004)

				$best_candidate = array_reduce( $candidates, array( __CLASS__, 'metric' ) );

				$result = array(
					'token'     => $token,
					'begin'     => $begin,
					'end'       => $end,
					'separator' => '',
					'year'      => $best_candidate['year'],
					'month'     => $best_candidate['month'],
					'day'       => $best_candidate['day'],
				);

				$matches[] = new self( $password, $result );
			}
		}

		// dates with separators are between length 6 '1/1/91' and 10 '11/11/1991'
		for ( $begin = 0; $begin <= strlen( $password ) - 6; $begin++ ) {
			for ( $end = $begin + 5; $end <= $begin + 9; $end ++ ) {
				if ( $end >= strlen( $password ) ) {
					break;
				}

				$token = substr( $password, $begin, $end - $begin + 1 );
				if ( ! preg_match( $maybe_date_with_separator, $token, $maybe_match ) ) {
					continue;
				}
				$dmy = self::map_ints_to_dmy( array( $maybe_match[1], $maybe_match[3], $maybe_match[4] ) );
				if ( empty( $dmy ) ) {
					continue;
				}

				$result = array(
					'token'     => $token,
					'begin'     => $begin,
					'end'       => $end,
					'separator' => $maybe_match[2],
					'year'      => $dmy['year'],
					'month'     => $dmy['month'],
					'day'       => $dmy['day'],
				);

				$matches[] = new self( $password, $result );
			}
		}
		// matches now contains all valid date strings in a way that is tricky to capture
		// with regexes only. while thorough, it will contain some unintuitive noise:
		//
		// '2015_06_04', in addition to matching 2015_06_04, will also contain
		// 5(!) other date matches: 15_06_04, 5_06_04, ..., even 2015 (matched as 5/1/2020)
		//
		// to reduce noise, remove date matches that are strict substrings of others

		foreach ( $matches as $key => $match ) {
			foreach ( $matches as $m ) {
				// Don't compare to self
				if ( $m === $match ) {
					continue;
				}
				if ( $m->begin <= $match->begin && $m->end >= $match->end ) {
					unset( $matches[ $key ] );
					break;
				}
			}
		}

		return $matches;
	}

	private static function metric( $a, $b ) {
		if ( empty( $a['year'] ) ) {
			return $b;
		}
		if ( empty( $b['year'] ) ) {
			return $a;
		}
		return ( abs( $a['year'] - self::$reference_year ) <= abs( $b['year'] - self::$reference_year ) )? $a : $b;
	}

	private static function map_ints_to_dmy( $ints ) {
		// given a 3-tuple, discard if:
		//   middle int is over 31 (for all dmy formats, years are never allowed in the middle)
		//   middle int is zero
		//   any int is over the max allowable year
		//   any int is over two digits but under the min allowable year
		//   2 ints are over 31, the max allowable day
		//   2 ints are zero
		//   all ints are over 12, the max allowable month
		if ( $ints[1] > 31 || $ints[1] <= 0 ) {
			return;
		}
		$over_12 = 0;
	    $over_31 = 0;
	    $under_1 = 0;
		foreach ( $ints as $int ) {
			// If this is the year and it's not valid, return nothing
			if ( ( 99 < $int && $int < self::$date_min_year ) || $int > self::$date_max_year ) {
				return;
			}
			if ( $int > 31 ) {
				$over_31++;
			}
			if ( $int > 12 ) {
				$over_12++;
			}
			if ( $int < 1 ) {
				$under_1 ++;
			}
		}
		if ( $over_31 >= 2 || $over_12 === 3 || $under_1 >= 2 ) {
			return;
		}

		// first look for a four digit year: yyyy + daymonth or daymonth + yyyy
		$possible_year_splits = array(
			array( 'year' => $ints[2], 'rest' => array( $ints[0], $ints[1] ) ), // year last
			array( 'year' => $ints[0], 'rest' => array( $ints[1], $ints[2] ) ), // year first
		);
		foreach ( $possible_year_splits as $split ) {
			if ( self::$date_min_year <= $split['year'] && $split['year'] <= self::$date_max_year ) {
				$dm = self::map_ints_to_dm( $split['rest'] );
				if ( $dm ) {
					return array(
						'year'  => $split['year'],
						'month' => $dm['month'],
						'day'   => $dm['day'],
					);
				} else {
					// for a candidate that includes a four-digit year,
					// when the remaining ints don't match to a day and month,
					// it is not a date.
					return;
				}
			}
		}

	    # given no four-digit year, two digit years are the most flexible int to match, so
	    # try to parse a day-month out of ints[0..1] or ints[1..0]
		foreach ( $possible_year_splits as $split ) {
			$dm = self::map_ints_to_dm( $split['rest'] );
			if ( $dm ) {
				$split['year'] = self::two_to_four_digit_year( $split['year'] );
				return array(
					'year'  => $split['year'],
					'month' => $dm['month'],
					'day'   => $dm['day'],
				);
			}
		}
	}

	private static function map_ints_to_dm( $ints ) {
		list( $d, $m ) = $ints;

		for ( $i = 0; $i < 2; $i++ ) {
			if ( ( 1 <= $d && $d <= 31 ) && ( 1 <= $m && $m <= 12 ) ) {
				return array(
					'day'   => $d,
					'month' => $m
				);
			}
			// Swap day and month
			$temp = $d;
			$d = $m;
			$m = $temp;
		}
		return;
	}

	private static function two_to_four_digit_year( $year ) {
		if ( $year > 99 ) {
			return $year;
		} elseif ( $year > 50 ) {
			# 87 -> 1987
			return $year + 1900;
        } else {
			# 15 -> 2015
			return $year + 2000;
		}
	}

	public function estimate_guesses() {
		// base guesses: (year distance from REFERENCE_YEAR) * num_days * num_years
		$this->guesses = max( abs( $this->year - self::$reference_year ), self::$min_year_space ) * 365;

		// double for four-digit years
		if ( $this->has_full_year ) {
			$this->guesses *= 2;
		}

		// add factor of 4 for separator selection (one of ~4 choices)
		if ( $this->separator ) {
			$this->guesses *= 4;
		}

		return $this->guesses;
	}

	public function get_feedback( $is_sole_match = true ) {
		$feedback = new stdClass();
		$feedback->warning = 'Dates are often easy to guess';
		$feedback->suggestions = array( 'Avoid dates and years that are associated with you' );

		return $feedback;
	}
}
