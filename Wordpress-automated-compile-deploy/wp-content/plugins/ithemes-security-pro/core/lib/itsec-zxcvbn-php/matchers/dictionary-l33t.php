<?php
class ITSEC_Zxcvbn_Dictionary_L33t_Match extends ITSEC_Zxcvbn_Dictionary_Match {

	private static $l33t_table = array(
		'a' => array( '4', '@' ),
		'b' => array( '8' ),
		'c' => array( '(', '{', '[', '<' ),
		'e' => array( '3' ),
		'g' => array( '6', '9' ),
		'i' => array( '1', '!', '|' ),
		'l' => array( '1', '|', '7' ),
		'o' => array( '0' ),
		's' => array( '$', '5' ),
		't' => array( '+', '7' ),
		'x' => array( '%' ),
		'z' => array( '2' )
	);

	private static $l33t_table_reverse_single = array(
		'!' => 'i',
		'@' => 'a',
		'$' => 's',
		'%' => 'x',
		'(' => 'c',
		'+' => 't',
		'{' => 'c',
		'[' => 'c',
		'<' => 'c',
		'0' => 'o',
		'2' => 'z',
		'3' => 'e',
		'4' => 'a',
		'5' => 's',
		'6' => 'g',
		'8' => 'b',
		'9' => 'g',
	);

	private static $l33t_table_reverse_multi = array(
		'|' => array( 'i', 'l' ),
		'1' => array( 'i', 'l' ),
		'7' => array( 'l', 't' ),
	);

	/**
	 * Finds matches in the password.
	 * @param string $password         Password to check for match
	 * @param array  $penalty_strings  Strings that should be penalized if in the password. This should be things like the username, first and last name, etc.
	 *
	 * @return ITSEC_Zxcvbn_Match[]    Array of Match objects
	 */
	public static function match( $password, array $penalty_strings = array(), $class = null ) {
		$plain_passes = self::l33t_sub( $password );
		$matches = array();
		foreach ( $plain_passes as $plain_pass ) {
			$match_set = parent::match( $plain_pass, $penalty_strings, __CLASS__ );
			foreach ( $match_set as &$match ) {
				$match->l33t     = true;
				$match->token    = substr( $password, $match->begin, strlen( $match->token ) );
				$match->password = $password;
			}
			$matches = array_merge( $matches, $match_set );
		}
		return $matches;
	}

	protected function get_l33t_variations() {
		return 2;
	}

	public function get_feedback( $is_sole_match = true ) {
		$feedback = parent::get_feedback( $is_sole_match );
		$feedback->suggestions[] = "Predictable substitutions like '@' instead of 'a' don't help very much";

		if ( 'passwords' == $this->dictionary_name ) {
			if ( log10( $this->estimate_guesses() ) <= 4 ) {
				$feedback->warning = 'This is similar to a commonly used password';
			} else {
				$feedback->warning = '';
			}
		}

		return $feedback;
	}

	protected static function l33t_sub( $password ) {
		// Handle all single character replacements
		$password = str_replace( array_keys( self::$l33t_table_reverse_single ), self::$l33t_table_reverse_single, $password );
		$passwords = array( $password );
		//Loop through the more complicated replacements (multiple opssible replacements per character, such as | being l or i)
		foreach ( self::$l33t_table_reverse_multi as $char => $replace_array ) {
			// If the character doesn't exist, don't worry about it
			if ( false !== $pos = strpos( $password, (string) $char ) ) {
				$new_passwords = array();
				// Loop through each current password and merge all returns into $new_passwords
				foreach( $passwords as $password ) {
					$new_passwords = array_merge( $new_passwords, self::l33t_sub_char( $password, $char, $replace_array ) );
				}
				// Replace the old passwords with the newer, bigger set
				$passwords = $new_passwords;
			}
		}
		return $passwords;
	}

	protected static function l33t_sub_char( $password, $char, $replace_array ) {
		$passwords = array();
		foreach( $replace_array as $replace_char ) {
			$pos = strpos( $password, (string) $char );
			$passwords[] = substr_replace( $password, $replace_char, $pos, 1 );
		}
		return $passwords;
	}
}
