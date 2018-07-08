<?php
class ITSEC_Zxcvbn_Regex_Match extends ITSEC_Zxcvbn_Match {

	protected static $regexen = array(
		'recent_year' => '/19\d\d|200\d|201\d/',
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

		foreach ( self::$regexen as $name => $regex ) {
			preg_match_all( $regex, $password, $regex_matches, PREG_OFFSET_CAPTURE );
			foreach ( $regex_matches[0] as $match ) {
				$result = array(
					'token'       => $match[0],
					'begin'       => $match[1],
					'end'         => $match[1] + strlen( $match[0] ) - 1,
					'regex_name'  => $name,
					'regex_match' => $match
				);
				$matches[] = new self( $password, $result );
			}
		}

		return $matches;
	}

	public function estimate_guesses() {
		$char_class_bases = array(
			'alpha_lower'  => 26,
			'alpha_upper'  => 26,
			'alpha'        => 52,
			'alphanumeric' => 62,
			'digits'       => 10,
			'symbols'      => 33,
		);

		if ( in_array( $this->regex_name, $char_class_bases ) ) {
			$this->guesses = pow( $char_class_bases[ $this->regex_name ], strlen( $this->token ) );
		} else {
			switch ( $this->regex_name ) {
				case 'recent_year':
					// conservative estimate of year space: num years from REFERENCE_YEAR.
					// if year is close to REFERENCE_YEAR, estimate a year space of MIN_YEAR_SPACE.
					$this->guesses = max( abs( $this->token - self::$reference_year ), self::$min_year_space );
			}
		}
		return $this->guesses;
	}

	public function get_feedback( $is_sole_match = true ) {
		$feedback = new stdClass();
		$feedback->warning = '';
		$feedback->suggestions = array();

		if ( 'recent_year' == $this->regex_name ) {
			$feedback->warning = 'Recent years are easy to guess';
			$feedback->suggestions[] = 'Avoid recent years';
			$feedback->suggestions[] = 'Avoid years that are associated with you';
		}

		return $feedback;
	}
}
