<?php
class ITSEC_Zxcvbn_Scorer {

	/**
	 * @var int
	 */
	private $cardinality;

	private $optimal;

	private static $min_guesses_before_growing_sequence = 10000;


	public function most_guessable_match_sequence( $password, $matches ) {
		$password_length = strlen( $password );
		$this->optimal = array(
			// optimal.m[k][l] holds final match in the best length-l match sequence covering the
			// password prefix up to k, inclusive.
			// if there is no length-l sequence that scores better (fewer guesses) than
			// a shorter match sequence spanning the same prefix, optimal.m[k][l] is undefined.
			'm'  => array(),

			// same structure as optimal.m, except holds the product term Prod(m.guesses for m in sequence).
			// optimal.pi allows for fast (non-looping) updates to the minimization function.
			'pi' => array(),

			// optimal.g[k] holds the lowest guesses up to k according to the minimization function.
			'g'  => array(),

			// optimal.l[k] holds the length, l, of the optimal sequence covering up to k.
			// (this is also the largest key in optimal.m[k] and optimal.pi[k] objects)
			'l'  => array(),
		);

		// Count up through the characters
		for ( $i = 0; $i < $password_length; $i++ ) {
			// initialize the optimal array
			//$this->optimal['m'][$i] = $this->optimal['pi'][$i] = new stdClass();
			$this->optimal['g'][$i] = null;
			$this->optimal['l'][$i] = 0;
		}

		$matches_by_end = array();
		foreach ( $matches as $match ) {
			$matches_by_end[ $match->end ][] = $match;
		}

		// Count up through the characters
		for ( $i = 0; $i < $password_length; $i++ ) {
			// Loop through matches that end on the current character
			if ( ! empty( $matches_by_end[ $i ] ) ) {
				foreach ( $matches_by_end[$i] as $m ) {
					if ( $m->begin > 0 ) {
						// Process chunk
						foreach ( $this->optimal['m'][$m->begin - 1] as $l => $om ) {
							$this->update_optimal( $m, $l + 1 );
						}
					} else {
						$this->update_optimal( $m, 1 );
					}
				}
			}
			$this->bruteforce_update( $password, $i );
		}

		$optimal_match_sequence = $this->unwind( $password_length );
		if ( 0 === $password_length ) {
			$guesses = 1;
		} else {
			$guesses = $this->optimal['g'][ $password_length - 1 ];
		}

		$result = new ITSEC_Zxcvbn_Results( $password, $guesses, $optimal_match_sequence );
		return $result;
	}

	protected function bruteforce_update( $password, $i ) {
		$m = new ITSEC_Zxcvbn_Bruteforce_Match( $password, array( 'begin' => 0, 'end' => $i, 'token' => substr( $password, 0, $i + 1 ) ) );
		$this->update_optimal( $m, 1 );
		if ( 0 === $i ) {
			return;
		}
		foreach ( $this->optimal['m'][ $i - 1 ] as $l => $last_match ) {
			if ( is_a( $last_match, 'ITSEC_Zxcvbn_Bruteforce_Match' ) ) {
				$m = new ITSEC_Zxcvbn_Bruteforce_Match( $password, array( 'begin' => $last_match->begin, 'end' => $i, 'token' => substr( $password, $last_match->begin, $i + 1 - $last_match->begin ) ) );
				$this->update_optimal( $m, $l );
			} else {
				$m = new ITSEC_Zxcvbn_Bruteforce_Match( $password, array( 'begin' => $i, 'end' => $i, 'token' => substr( $password, $i, 1 ) ) );
				$this->update_optimal( $m, $l + 1 );
			}
		}
	}

	protected function factorial( $n ) {
		$n = abs( intval( $n ) );
		if ( $n < 2 ) {
			return 1;
		}

		for ( $i = $n - 1; $i > 0; $i-- ) {
			$n *= $i;
		}
		return $n;
	}

	protected function update_optimal( $m, $l ) {
		$end = $m->end;
		$pi = $m->estimate_guesses();

		if ( $l > 1 ) {
			$pi *= $this->optimal['pi'][ $m->begin - 1 ][ $l - 1 ];
		}
		$g = $this->factorial( $l ) * $pi;
		$g += pow( self::$min_guesses_before_growing_sequence, ( $l - 1 ) );

		if ( is_null( $this->optimal['g'][ $end ] ) || $g < $this->optimal['g'][ $end ] ) {
			$this->optimal['g'][ $end ]        = $g;
			$this->optimal['l'][ $end ]        = $l;
			$this->optimal['m'][ $end ][ $l ]  = $m;
			$this->optimal['pi'][ $end ][ $l ] = $pi;
		}
	}

	protected function unwind( $n ) {
		$optimal_match_sequence = array();
		$k = $n - 1;
		$l = $this->optimal['l'][ $k ];
		while ( $k >= 0 ) {
			$m = $this->optimal['m'][ $k ][ $l ];
			array_unshift( $optimal_match_sequence, $m );
			$k = $m->begin - 1;
			--$l;
		}
		return $optimal_match_sequence;
	}
}
