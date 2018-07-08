<?php
class ITSEC_Zxcvbn_Repeat_Match extends ITSEC_Zxcvbn_Match {

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
		$greedy = '/(.+)\1+/';
		$lazy = '/(.+?)\1+/';
		$lazy_anchored = '/^(.+?)\1+$/';
		$last_index = 0;

		$matched = false;
		while ( $last_index < strlen( $password ) ) {
			preg_match( $greedy, $password, $greedy_match );
			preg_match( $lazy, $password, $lazy_match );

			// in JS a regex only matches once, so we need to break if it doesn't match AND if it already matched before
			if ( $matched || empty( $greedy_match ) ) {
				break;
			}
			$matched = true;
			if ( strlen( $greedy_match[0] ) > strlen( $lazy_match[0] ) ) {
				// greedy beats lazy for 'aabaab'
				//   greedy: [aabaab, aab]
				//   lazy:   [aa,     a]
				$match = $greedy_match;

				// greedy's repeated string might itself be repeated, eg.
				// aabaab in aabaabaabaab.
				// run an anchored lazy match on greedy's repeated string
				// to find the shortest repeated string
				preg_match( $lazy_anchored, $match[0], $lazy_anchored_match );
				$base_token = $lazy_anchored_match[1];
			} else {
				// lazy beats greedy for 'aaaaa'
				//   greedy: [aaaa,  aa]
				//   lazy:   [aaaaa, a]
				$match = $lazy_match;
				$base_token = $match[1];
			}
			$begin = strpos( $password, $match[0] );
			$end = $begin + strlen( $match[0] ) - 1;

			$scorer  = new ITSEC_Zxcvbn_Scorer();
			$matcher = new ITSEC_Zxcvbn_Matcher();
			$results = $scorer->most_guessable_match_sequence( $base_token, $matcher->omnimatch( $base_token ) );

			$result = array(
				'begin' => $begin,
				'end' => $end,
				'token' => $match[0],
				'base_token' => $base_token,
				'base_guesses' => $results->guesses,
				'base_matches' => $results->sequence,
				'repeat_count' => strlen( $match[0] ) / strlen( $base_token ),
			);
			$matches[] = new self( $password, $result );
			$last_index = $end + 1;
		}

		return $matches;
	}

	public function estimate_guesses() {
		$this->guesses = $this->base_guesses * $this->repeat_count;
		return $this->guesses;
	}

	public function get_feedback( $is_sole_match = true ) {
		$feedback = new stdClass();
		$feedback->warning = ( 1 === strlen( $this->base_token ) )? 'Repeats like "aaa" are easy to guess' : 'Repeats like "abcabcabc" are only slightly harder to guess than "abc"';
		$feedback->suggestions = array( 'Avoid repeated words and characters' );

		return $feedback;
	}
}
