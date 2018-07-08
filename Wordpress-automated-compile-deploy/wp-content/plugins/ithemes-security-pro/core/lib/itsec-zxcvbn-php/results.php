<?php
class ITSEC_Zxcvbn_Results {
	/**
	 * @var string Password
	 */
	public $password;

	/**
	 * @var int Estimated guesses needed to crack password
	 */
	public $guesses;

	/**
	 * @var int Order of magnitude of result.guesses
	 */
	public $guesses_log10;

	/**
	 * @var object dictionary of back-of-the-envelope crack time estimations, in seconds, based on a few scenarios {
	 *     @type int   $offline_fast_hashing_1e10_per_second  offline attack with user-unique salting but a fast hash function like SHA-1, SHA-256 or MD5. A wide range of reasonable numbers anywhere from one billion - one trillion guesses per second, depending on number of cores and machines. ballparking at 10B/sec.
	 *     @type int   $offline_slow_hashing_1e4_per_second   offline attack. assumes multiple attackers, proper user-unique salting, and a slow hash function w/ moderate work factor, such as bcrypt, scrypt, PBKDF2.
	 *     @type int   $online_no_throttling_10_per_second    online attack on a service that doesn't ratelimit, or where an attacker has outsmarted ratelimiting
	 *     @type int   $online_throttling_100_per_hour        online attack on a service that ratelimits password auth attempts.
	 * }
	 */
	public $crack_times_seconds = array();

	/**
	 * @var object same keys as result.crack_times_seconds, with friendlier display string values: "less than a second", "3 hours", "centuries", etc. {
	 *     @type int   $offline_fast_hashing_1e10_per_second  offline attack with user-unique salting but a fast hash function like SHA-1, SHA-256 or MD5. A wide range of reasonable numbers anywhere from one billion - one trillion guesses per second, depending on number of cores and machines. ballparking at 10B/sec.
	 *     @type int   $offline_slow_hashing_1e4_per_second   offline attack. assumes multiple attackers, proper user-unique salting, and a slow hash function w/ moderate work factor, such as bcrypt, scrypt, PBKDF2.
	 *     @type int   $online_no_throttling_10_per_second    online attack on a service that doesn't ratelimit, or where an attacker has outsmarted ratelimiting
	 *     @type int   $online_throttling_100_per_hour        online attack on a service that ratelimits password auth attempts.
	 * }
	 */
	public $crack_times_display = array();

	/**
	 * @var int from 0-4 (useful for implementing a strength bar)
	 *     0 # too guessable: risky password. (guesses < 10^3)
	 *     1 # very guessable: protection from throttled online attacks. (guesses < 10^6)
	 *     2 # somewhat guessable: protection from unthrottled online attacks. (guesses < 10^8)
     *     3 # safely unguessable: moderate protection from offline slow-hash scenario. (guesses < 10^10)
     *     4 # very unguessable: strong protection from offline slow-hash scenario. (guesses >= 10^10)
     */
	public $score;

	/**
	 * @var object verbal feedback to help choose better passwords. set when score <= 2. {
	 *     @type string $warning     explains what's wrong, eg. 'this is a top-10 common password'. not always set -- sometimes an empty string
	 *     @type array  $suggestions a possibly-empty list of suggestions to help choose a less guessable password. eg. 'Add another word or two'
	 * }
	 */
	public $feedback;

	/**
	 * @var array the list of patterns that zxcvbn based the guess calculation on.
	 */
	public $sequence;

	/**
	 * @var int how long it took zxcvbn to calculate an answer, in milliseconds
	 */
	public $calc_time;

	public function __construct( $password, $guesses, $optimal_match_sequence ) {
		$this->password      = $password;
		$this->guesses       = $guesses;
		$this->guesses_log10 = log10( $guesses );
		$this->sequence      = $optimal_match_sequence;

		$this->estimate_attack_times();
		$this->calc_score();
		$this->feedback();
	}

	protected function estimate_attack_times() {
		$this->crack_times_seconds = array(
			'online_throttling_100_per_hour'       => $this->guesses / ( 100 / 3600 ),
			'online_no_throttling_10_per_second'   => $this->guesses / 10,
			'offline_slow_hashing_1e4_per_second'  => $this->guesses / 1e4,
			'offline_fast_hashing_1e10_per_second' => $this->guesses / 1e10
		);

		foreach ( $this->crack_times_seconds as $key => $seconds ) {
			$this->crack_times_display[ $key ] = $this->display_time( $seconds );
		}
	}

	protected function calc_score() {
		$delta = 5;
		if ( $this->guesses < 1e3 + $delta ) {
			$this->score = 0;
		} elseif ( $this->guesses < 1e6 + $delta ) {
			$this->score = 1;
		} elseif ( $this->guesses < 1e8 + $delta ) {
			$this->score = 2;
		} elseif ( $this->guesses < 1e10 + $delta ) {
			$this->score = 3;
		} else {
			$this->score = 4;
		}
	}

	protected function display_time( $seconds ) {
		$minute = 60;
		$hour = $minute * 60;
		$day = $hour * 24;
		$month = $day * 31;
		$year = $month * 12;
		$century = $year * 100;
		if ( $seconds < 1 ) {
			return 'less than a second';
		} elseif( $seconds < $minute ) {
			return sprintf( '%d second%s', $seconds, ( $seconds > 1 )? 's':'' );
		} elseif( $seconds < $hour ) {
			$base = round( $seconds / $minute );
			return sprintf( '%d minute%s', $base, ( $base > 1 )? 's':'' );
		} elseif( $seconds < $day ) {
			$base = round( $seconds / $hour );
			return sprintf( '%d hour%s', $base, ( $base > 1 )? 's':'' );
		} elseif( $seconds < $month ) {
			$base = round( $seconds / $day );
			return sprintf( '%d day%s', $base, ( $base > 1 )? 's':'' );
		} elseif( $seconds < $year ) {
			$base = round( $seconds / $month );
			return sprintf( '%d month%s', $base, ( $base > 1 )? 's':'' );
		} elseif( $seconds < $century ) {
			$base = round( $seconds / $year );
			return sprintf( '%d year%s', $base, ( $base > 1 )? 's':'' );
		} else {
			return 'centuries';
		}
	}

	protected function feedback() {
		$this->feedback = new stdClass();
		$this->feedback->warning = '';
		$this->feedback->suggestions = array();

		if ( 0 === count( $this->sequence ) ) {
			$this->feedback->suggestions = array(
				'Use a few words, avoid common phrases',
				'No need for symbols, digits, or uppercase letters'
			);
			return;
		}

		if ( $this->score > 2 ) {
			return;
		}

		// tie feedback to the longest match for longer sequences
		$longest_match = $this->sequence[0];
		foreach ( $this->sequence as $m ) {
			if ( strlen( $m->token ) > strlen( $longest_match->token ) ) {
				$longest_match = $m;
			}
		}

		$is_sole_match = count( $this->sequence ) == 1;

		if ( is_object( $longest_match ) && is_callable( array( $longest_match, 'get_feedback' ) ) ) {
			$this->feedback = $longest_match->get_feedback( $is_sole_match );
		}
	}
}
