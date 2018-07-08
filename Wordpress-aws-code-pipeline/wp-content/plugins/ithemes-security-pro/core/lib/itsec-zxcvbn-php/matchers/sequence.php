<?php
class ITSEC_Zxcvbn_Sequence_Match extends ITSEC_Zxcvbn_Match {

	protected static $max_delta = 5;

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
		// Sequences only matter on passwords with at least two characters
		if ( strlen( $password ) < 2 ) {
			return array();
		}
		$matches = array();
		$begin = 0;
		for ( $k = 1; $k < strlen( $password ); $k++ ) {
			$delta = ord( $password[ $k ] ) - ord( $password[ $k - 1 ] );
			if ( ! isset( $last_delta ) ) {
				$last_delta = $delta;
			}
			if ( $delta === $last_delta ) {
				continue;
			}
			$end = $k - 1;
			$result = self::update( $password, $begin, $end, $last_delta );
			if ( $result ) {
				$matches[] = new self( $password, $result );
			}
			$begin = $end;
			$last_delta = $delta;
		}
		$result = self::update( $password, $begin, strlen( $password ) - 1, $last_delta );
		if ( $result ) {
			$matches[] = new self( $password, $result );
		}
		return $matches;
	}

	protected static function update( $password, $begin, $end, $delta ) {
		$abs_delta = abs( $delta );
		if ( $end - $begin > 0 || $abs_delta == 1 ) {
			if ( 0 < $abs_delta && $abs_delta <= self::$max_delta ) {
				$token = substr( $password, $begin, $end - $begin + 1 );
				if ( preg_match( '/^[a-z]+$/', $token ) ) {
					$sequence_name = 'lower';
					$sequence_space = 26;
				} elseif ( preg_match( '/^[A-Z]+$/', $token ) ) {
					$sequence_name = 'upper';
					$sequence_space = 26;
				} elseif ( preg_match( '/^\d+$/', $token ) ) {
					$sequence_name = 'digits';
					$sequence_space = 10;
				} else {
					$sequence_name = 'unicode';
					$sequence_space = 26;
				}
				return array(
					'begin'         => $begin,
					'end'           => $end,
					'token'         => $token,
					'sequence_name' => $sequence_name,
					'sequence_space'=> $sequence_space,
					'ascending'     => ( $delta > 0 ),
				);
			}
		}
		// We don't want this as a match
		return false;
	}

	public function estimate_guesses() {
		// lower guesses for obvious starting points
		if ( in_array( $this->token[0], array( 'a', 'A', 'z', 'Z', '0', '1', '9' ) ) ) {
			$base_guesses = 4;
		} elseif ( $this->is_digit( $this->token[0] ) ) {
			$base_guesses = 10;
		} else {
			$base_guesses = 26;
		}
		if ( ! $this->ascending ) {
			$base_guesses *= 2;
		}

		$this->guesses = $base_guesses * strlen( $this->token );
		return $this->guesses;
	}

	public function get_feedback( $is_sole_match = true ) {
		$feedback = new stdClass();
		$feedback->warning = 'Sequences like abc or 6543 are easy to guess';
		$feedback->suggestions = array( 'Avoid sequences' );

		return $feedback;
	}
}
