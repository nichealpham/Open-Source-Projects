<?php
class ITSEC_Zxcvbn_Bruteforce_Match extends ITSEC_Zxcvbn_Match {
	private static $cardinality = 10;
	private static $min_submatch_guesses_single_char = 10;
	private static $min_submatch_guesses_multi_char = 50;

	private $entropy;

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
		// Matches entire string.
		$match = new self( $password, array( 'begin' => 0, 'end' => strlen( $password ) - 1, 'token' => $password ) );
		return array($match);
	}

	public function estimate_guesses() {
		$guesses = pow( self::$cardinality, strlen( $this->token ) );
		$min_guesses = ( 1 === strlen( $this->token ) )? self::$min_submatch_guesses_single_char : self::$min_submatch_guesses_multi_char;
		$this->guesses = max( $guesses, $min_guesses );
		return $this->guesses;
	}

	public function get_feedback( $is_sole_match = true ) {
		$feedback              = new stdClass();
		$feedback->warning     = '';
		$feedback->suggestions = array();

		return $feedback;
	}
	/**
	 * @return int
	 */
	/*
	public function get_cardinality( $token ) {
		if ( ! is_null( $this->cardinality ) ) {
			return $this->cardinality;
		}
		$lower = $upper = $digits = $symbols = $unicode = 0;

		foreach ( str_split( $token ) as $char ) {
			$ord = ord( $char );

			if ($this->is_digit($ord)) {
				$digits = 10;
			} elseif ($this->is_upper($ord)) {
				$upper = 26;
			} elseif ($this->is_lower($ord)) {
				$lower = 26;
			} elseif ($this->is_symbol($ord)) {
				$symbols = 33;
			} else {
				$unicode = 100;
			}
		}
		$this->cardinality = $lower + $digits + $upper + $symbols + $unicode;
		return $this->cardinality;
	}
	/**/
}
