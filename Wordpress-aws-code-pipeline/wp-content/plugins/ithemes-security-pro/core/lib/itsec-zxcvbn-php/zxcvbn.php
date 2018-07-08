<?php
require_once( 'matcher.php' );
require_once( 'scorer.php' );
require_once( 'results.php' );

class ITSEC_Zxcvbn {
	/**
	 * @var ITSEC_Zxcvbn_Matcher
	 */
	private $matcher;

	/**
	 * @var ITSEC_Zxcvbn_Scorer
	 */
	private $scorer;

	public function __construct() {
		$this->matcher   = new ITSEC_Zxcvbn_Matcher();
		$this->scorer    = new ITSEC_Zxcvbn_Scorer();
	}

	/**
	 * @param string $password         The Password to test.
	 * @param array  $penalty_strings  Strings that should be penalized if in the password. This should be things like the username, first and last name, etc.
	 *
	 * @return ITSEC_Zxcvbn_Results
	 */
	public function test_password( $password, $penalty_strings = array() ) {
		$start = microtime( true );

		$penalty_strings = array_map( 'strtolower', $penalty_strings );

		$this->matcher->set_user_input_dictionary( $penalty_strings );
		$matches = $this->matcher->omnimatch( $password );

		$result = $this->scorer->most_guessable_match_sequence( $password, $matches );
		$result->calc_time = microtime( true ) - $start;

		return $result;
	}
}
