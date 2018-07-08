<?php
class ITSEC_Zxcvbn_Dictionary_Match extends ITSEC_Zxcvbn_Match {

	/**
	 * @var array|mixed|object
	 */
	private static $frequency_lists;

	protected static $start_upper_regex = '/^[A-Z][^A-Z]+$/';
	protected static $end_upper_regex   = '/^[^A-Z]+[A-Z]$/';
	protected static $all_upper_regex   = '/^[A-Z]+$/';

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
	public static function match( $password, array $penalty_strings = array(), $class = null ) {
		if ( ! isset( $class ) ) {
			$class = __CLASS__;
		}
		$matches = array();
		$dictionary_names = array(
			'english_wikipedia',
			'female_names',
			'male_names',
			'passwords',
			'surnames',
			'us_tv_and_film',
		);
		foreach ( $dictionary_names as $dictionary_name ) {
			$dictionaries = call_user_func( array( $class, 'get_ranked_dictionary' ), $dictionary_name );
			foreach ( $dictionaries as $name => $dictionary ) {
				$matches = array_merge( $matches, call_user_func( array( $class, 'get_dictionary_matches' ), $dictionary, $name, $password, $class ) );
			}
			if ( ! empty( $penalty_strings ) ) {
				$dictionary = array();
				foreach ( $penalty_strings as $rank => $input ) {
					if ( empty( $input ) ) {
						continue;
					}

					$input_lower                = strtolower( $input );
					$dictionary[ $input_lower ] = $rank;
				}
				$matches = array_merge( $matches, call_user_func( array( $class, 'get_dictionary_matches' ), $dictionary, 'user_inputs', $password, $class ) );
			}
		}
		return $matches;
	}

	public static function get_dictionary_matches( $dictionary, $name, $password, $class ) {
		$matches = array();
		$results = call_user_func( array( $class, 'dictionary_match' ), $password, $dictionary );
		foreach ( $results as $result ) {
			$result['dictionary_name'] = $name;
			$result['reversed']        = false;
			$matches[]                 = new $class( $password, $result );
		}
		return $matches;

	}


	/**
	 * Match password in a single dictionary.
	 *
	 * @param string $password
	 * @param array  $dictionary
	 *
	 * @return array
	 */
	protected static function dictionary_match( $password, $dictionary ) {
		$result = array();
		$length = strlen($password);

		$pw_lower = strtolower( $password );

		for ( $i = 0; $i < $length; $i++ ) {
			for ( $j = $i; $j < $length; $j++ ) {
				$word = substr( $pw_lower, $i, $j - $i + 1 );

				if ( isset( $dictionary->{$word} ) ) {
					$result[] = array(
						'begin'        => $i,
						'end'          => $j,
						'token'        => substr( $password, $i, $j - $i + 1 ),
						'matched_word' => $word,
						'rank'         => $dictionary->{$word},
					);
				}
			}
		}

		return $result;
	}

	/**
	 * Load ranked frequency dictionaries.
	 *
	 * @return object
	 */
	protected static function get_ranked_dictionary( $dictionary_name ) {
		return json_decode( file_get_contents( dirname( __FILE__ ) . sprintf( '/ranked_frequency_list-%s.json', $dictionary_name ) ) );
	}

	public function estimate_guesses() {
		$this->guesses = $this->rank * $this->get_uppercase_variations() * $this->get_l33t_variations() * $this->get_reversed_variations();
		return $this->guesses;
	}

	/**
	 * @return float
	 */
	protected function get_uppercase_variations() {
		$token = $this->token;
		// Return if token is all lowercase.
		if ( $token === strtolower( $token ) ) {
			return 1;
		}

		// a capitalized word is the most common capitalization scheme,
		// so it only doubles the search space (uncapitalized + capitalized).
		// allcaps and end-capitalized are common enough too, underestimate as 2x factor to be safe.
		foreach ( array( self::$start_upper_regex, self::$end_upper_regex, self::$all_upper_regex ) as $regex ) {
			if ( preg_match( $regex, $token ) ) {
				return 2;
			}
		}
		$variations = $upper = $lower = 0;

		foreach ( str_split( $token ) as $c ) {
			$ord = ord( $c );

			if ( $this->is_upper( $ord ) ) {
				++$upper;
			} elseif ( $this->is_lower( $ord ) ) {
				++$lower;
			}
		}

		// otherwise calculate the number of ways to capitalize U+L uppercase+lowercase letters
		// with U uppercase letters or less. or, if there's more uppercase than lower (for eg. PASSwORD),
		// the number of ways to lowercase U+L letters with L lowercase letters or less.
		for ( $i = 1; $i <= min( $upper, $lower ); $i++ ) {
			$variations += $this->binomial_coefficient( $upper + $lower, $i );
		}
		return $variations;
	}

	protected function get_l33t_variations() {
		return 1;
	}

	protected function get_reversed_variations() {
		return 1;
	}

	public function get_feedback( $is_sole_match = true ) {
		$feedback = new stdClass();
		$feedback->warning = '';
		$feedback->suggestions = array();

		switch ( $this->dictionary_name ) {
			case 'passwords':
				if ( $is_sole_match ) {
					if ( $this->rank < 10 ) {
						$feedback->warning = 'This is a top-10 common password';
					} elseif ( $this->rank < 100 ) {
						$feedback->warning = 'This is a top-100 common password';
					} else {
						$feedback->warning = 'This is a very common password';
					}
				} elseif ( log10( $this->estimate_guesses() ) <= 4 ) {
					$feedback->warning = 'This is similar to a commonly used password';
				}
				break;
			case 'english':
				if ( $is_sole_match ) {
					$feedback->warning = 'A word by itself is easy to guess';
				}
				break;
			case 'surnames':
			case 'male_names':
			case 'female_names':
				if ( $is_sole_match ) {
					$feedback->warning = 'Names and surnames by themselves are easy to guess';
				} else {
					$feedback->warning = 'Common names and surnames are easy to guess';
				}
				break;
		}

		if ( preg_match( self::$start_upper_regex, $this->token ) ) {
			$feedback->suggestions[] = "Capitalization doesn't help very much";
		} elseif ( preg_match( self::$all_upper_regex, $this->token ) ) {
			$feedback->suggestions[] = "All-uppercase is almost as easy to guess as all-lowercase";
		}

		return $feedback;
	}
}
