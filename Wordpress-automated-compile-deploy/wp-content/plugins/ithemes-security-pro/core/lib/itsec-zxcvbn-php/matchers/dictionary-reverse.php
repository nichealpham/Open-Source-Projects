<?php
class ITSEC_Zxcvbn_Dictionary_Reverse_Match extends ITSEC_Zxcvbn_Dictionary_Match {

	/**
	 * Finds matches in the password.
	 * @param string $password         Password to check for match
	 * @param array  $penalty_strings  Strings that should be penalized if in the password. This should be things like the username, first and last name, etc.
	 *
	 * @return ITSEC_Zxcvbn_Match[]    Array of Match objects
	 */
	public static function match( $password, array $penalty_strings = array(), $class = null ) {
		$rev_pass = strrev( $password );
		$matches = parent::match( $rev_pass, $penalty_strings, __CLASS__ );
		foreach ( $matches as &$match ) {
			$match->reversed = true;
			$match->token = strrev( $match->token );
			$match->password = strrev( $match->password );
			$match->length = strlen( $match->password );
			list( $match->begin, $match->end ) = array( strlen( $match->password ) - 1 - $match->end, strlen( $match->password ) - 1 - $match->begin );
		}
		return $matches;
	}

	protected function get_reversed_variations() {
		return 2;
	}

	public function get_feedback( $is_sole_match = true ) {
		$feedback = parent::get_feedback( $is_sole_match );
		if ( strlen( $this->token ) >= 4 ) {
			$feedback->suggestions[] = "Reversed words aren't much harder to guess";
		}

		if ( 'passwords' == $this->dictionary_name ) {
			if ( log10( $this->estimate_guesses() ) <= 4 ) {
				$feedback->warning = 'This is similar to a commonly used password';
			} else {
				$feedback->warning = '';
			}
		}

		return $feedback;
	}
}
