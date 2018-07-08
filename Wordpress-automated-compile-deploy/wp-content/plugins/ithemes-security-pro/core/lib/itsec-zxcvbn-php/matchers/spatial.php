<?php
class ITSEC_Zxcvbn_Spatial_Match extends ITSEC_Zxcvbn_Match {

	/**
	 * @var array|mixed|object
	 */
	private static $adjacency_graphs;

	private static $shifted = '[~!@#$%^&*()_+QWERTYUIOP{}|ASDFGHJKL:"ZXCVBNM<>?]';

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
		$graphs = self::get_adjacency_graphs();

		foreach ( $graphs as $graph_name => $graph ) {
			$results = self::spatial_match( $password, $graph_name );
			foreach ($results as $result) {
				$matches[] = new self( $password, $result );
			}
		}
		return $matches;
	}

	protected static function spatial_match( $password, $graph_name ) {
		$results = array();
		$graph = self::$adjacency_graphs->{$graph_name};
		$password_length = strlen( $password );
		$i = 0;
		while ( $i < $password_length - 1 ) {
			$j = $i + 1;
			$turns = 0;
			$last_direction = '';
			// Is the first character shifted
			$shifted_count = ( in_array( $graph_name, array( 'qwerty', 'dvorak' ) ) && false !== strpos( self::$shifted, $password[ $i ] ) )? 1:0;
			while (true) {
				$prev_char = $password[ $j - 1 ];
				$found = false;
				$found_direction = -1;
				$cur_direction = -1;
				$adjacents = isset( $graph->{$prev_char} )? $graph->{$prev_char} : array();

				if ( $j < $password_length ) {
					$cur_char = $password[ $j ];
					foreach ( $adjacents as $adj ) {
						++$cur_direction;
						if ( $adj && false !== $pos = strpos( $adj, $cur_char ) ) {
							$found = true;
							$found_direction = $cur_direction;

							// The seconds character is shifted in our tables
							if ( 1 === $pos ) {
								++$shifted_count;
							}

							if ( $last_direction !== $found_direction ) {
								++$turns;
								$last_direction = $found_direction;
							}
							break;
						}
					}
				}
				// If we're still finding things, keep going
				if ( $found ) {
					$j++;
				} else {
					if ( $j - $i > 2 ) { // If we're done finding new things that we found 3+ characters in a row, we have a match
						$results[] = array(
							'begin'         => $i,
							'end'           => $j - 1,
							'token'         => substr( $password, $i, $j - $i ),
							'graph'         => $graph_name,
							'turns'         => $turns,
							'shifted_count' => $shifted_count,
						);
					}
					// Keep looking from where we left off
					$i = $j;
					// Break out of internal while(true)
					break;
				}
			}
		}
		return $results;
	}

	/**
	 * Load ranked frequency dictionaries.
	 *
	 * @return object
	 */
	protected static function get_adjacency_graphs() {
		if ( empty( self::$adjacency_graphs ) ) {
			self::$adjacency_graphs = json_decode( file_get_contents( dirname( __FILE__ ) . '/adjacency_graphs.json' ) );
		}
		return self::$adjacency_graphs;
	}

	public function estimate_guesses() {
		$s = count( (array) self::$adjacency_graphs->{$this->graph} );
		$d = $this->calc_average_degree( $this->graph );

		$this->guesses = 0;
		$l = strlen( $this->token );
		$t = $this->turns;

		for ( $i = 2; $i <= $l; $i++ ) {
			$possible_turns = min( $t, $i - 1 );
			for ( $j = 1; $j <= $possible_turns; $j++ ) {
				$this->guesses += $this->binomial_coefficient( $i - 1, $j - 1 ) * $s * pow( $d, $j );
			}
		}

		// add extra guesses for shifted keys. (% instead of 5, A instead of a.)
		// math is similar to extra guesses of l33t substitutions in dictionary matches.
		if ( $this->shifted_count ) {
			$S = $this->shifted_count;
			$this->unshifted_count = $l - $this->shifted_count;
			// If all shifted, just * 2
			if ( $this->unshifted_count === 0 ) {
				$this->guesses *= 2;
			} else {
				$shifted_variations = 0;
				for ( $i = 1; $i <= min( $this->shifted_count, $this->unshifted_count ); $i++ ) {
					$shifted_variations += $this->binomial_coefficient( $this->shifted_count + $this->unshifted_count, $i );
				}
				$this->guesses *= $shifted_variations;
			}
		}

		return $this->guesses;
	}

	/**
	 * Calculates the average number of adjacent keys for any given adjacency graph
	 *
	 * @param $graph_name
	 *
	 * @return float
	 */
	protected function calc_average_degree( $graph_name ) {
		$graph = (array) self::$adjacency_graphs->{$graph_name};

		foreach ( $graph as &$neighbors ) {
			$neighbors = count( array_filter( $neighbors ) );
		}
		return array_sum( $graph ) / count( $graph );
	}


	public function get_feedback( $is_sole_match = true ) {
		$feedback = new stdClass();
		$feedback->warning = ( 1 === $this->turns )? 'Straight rows of keys are easy to guess':'Short keyboard patterns are easy to guess';
		$feedback->suggestions = array( 'Use a longer keyboard pattern with more turns' );

		return $feedback;
	}
}
