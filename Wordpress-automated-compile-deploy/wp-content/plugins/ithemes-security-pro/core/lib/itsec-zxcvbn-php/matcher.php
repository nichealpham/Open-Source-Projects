<?php
class ITSEC_Zxcvbn_Matcher {

	private $penalty_strings = array();

	/**
	 * @var array Registered Matchers, Key is class name, value is file to require_once
	 */
	private $matchers;

	public function __construct() {
		$this->matchers = array(
			'ITSEC_Zxcvbn_Dictionary_Match'         => dirname( __FILE__ ) . '/matchers/dictionary.php',
			'ITSEC_Zxcvbn_Dictionary_Reverse_Match' => dirname( __FILE__ ) . '/matchers/dictionary-reverse.php',
			'ITSEC_Zxcvbn_Dictionary_L33t_Match'    => dirname( __FILE__ ) . '/matchers/dictionary-l33t.php',
			'ITSEC_Zxcvbn_Spatial_Match'            => dirname( __FILE__ ) . '/matchers/spatial.php',
			'ITSEC_Zxcvbn_Repeat_Match'             => dirname( __FILE__ ) . '/matchers/repeat.php',
			'ITSEC_Zxcvbn_Sequence_Match'           => dirname( __FILE__ ) . '/matchers/sequence.php',
			'ITSEC_Zxcvbn_Regex_Match'              => dirname( __FILE__ ) . '/matchers/regex.php',
			'ITSEC_Zxcvbn_Date_Match'               => dirname( __FILE__ ) . '/matchers/date.php',
			'ITSEC_Zxcvbn_Bruteforce_Match'         => dirname( __FILE__ ) . '/matchers/bruteforce.php',
		);
		foreach ( $this->matchers as $file ) {
			require_once( $file );
		}
	}

	public function omnimatch( $password ) {
		$matches = array();
		foreach ( $this->matchers as $class => $file ) {
			$matched = call_user_func( array( $class, 'match'), $password, $this->penalty_strings );
			if ( ! empty( $matched ) && is_array( $matched ) ) {
				$matches = array_merge( $matches, $matched );
			}
		}
		return $matches;
	}

	public function set_user_input_dictionary( $list ) {
		// @todo process this properly
		$this->penalty_strings = $list;
	}

}

abstract class ITSEC_Zxcvbn_Match {
	protected $guesses;

	protected static $min_year_space = 20;
	/**
	 * @todo make this dynamic? Why isn't it?
	 */
	protected static $reference_year = 2016;

	/**
	 * Finds matches in the password.
	 * @param string $password         Password to check for match
	 * @param array  $penalty_strings  Strings that should be penalized if in the password. This should be things like the username, first and last name, etc.
	 *
	 * @return ITSEC_Zxcvbn_Match[]    Array of Match objects
	 */
	public static function match($password, array $penalty_strings = array()) {}

	protected function is_digit( $ord ) {
		return $ord >= 0x30 && $ord <= 0x39;
	}

	protected function is_upper( $ord ) {
		return $ord >= 0x41 && $ord <= 0x5a;
	}

	protected function is_lower( $ord ) {
		return $ord >= 0x61 && $ord <= 0x7a;
	}

	protected function is_symbol( $ord ) {
		return $ord <= 0x7f;
	}

	/**
	 * http://blog.plover.com/math/choose.html
	 *
	 * @param $n int
	 * @param $k int
	 *
	 * @return int
	 */
	protected function binomial_coefficient( $n, $k ) {
		if ( $k > $n ) {
			return 0;
		}
		if ( 0 === $k ) {
			return 1;
		}
		$r = 1;
		for ( $d = 1; $d <= $k; $d++ ) {
			$r *= $n--;
			$r /= $d;
		}
		return $r;
	}

}
