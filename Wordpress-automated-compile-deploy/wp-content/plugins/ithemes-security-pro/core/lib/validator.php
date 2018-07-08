<?php

abstract class ITSEC_Validator {
	protected $run_validate_matching_fields = true;
	protected $run_validate_matching_types = true;

	protected $settings_obj;
	protected $defaults;
	protected $settings;
	protected $previous_settings;

	protected $can_save = true;
	protected $needs_refresh = false;
	protected $errors = array();
	protected $messages = array();
	protected $vars_to_skip_validate_matching_fields = array();
	protected $vars_to_skip_validate_matching_types = array();


	public function __construct() {
		$this->settings_obj = ITSEC_Modules::get_settings_obj( $this->get_id() );

		if ( ! is_callable( array( $this->settings_obj, 'get_defaults' ) ) ) {
			return;
		}

		$this->defaults = $this->settings_obj->get_defaults();
	}

	abstract public function get_id();
	protected function sanitize_settings() {}
	protected function validate_settings() {}

	public function validate( $settings ) {
		$this->settings = $settings;
		$this->previous_settings = ITSEC_Modules::get_settings( $this->get_id() );

		$this->sanitize_settings();

		if ( $this->run_validate_matching_fields ) {
			$this->validate_matching_fields();
		}

		if ( $this->run_validate_matching_types ) {
			$this->validate_matching_types();
		}

		$this->validate_settings();
	}

	protected function validate_matching_fields() {
		$id = $this->get_id();

		foreach ( array_keys( $this->defaults ) as $name ) {
			if ( ! isset( $this->settings[$name] ) && ! in_array( $name, $this->vars_to_skip_validate_matching_fields ) ) {
				$this->add_error( new WP_Error( "itsec-validator-$id-validate_matching_fields-missing-name-$name", sprintf( __( 'A validation function for %1$s received data that did not have the required entry for %2$s.', 'it-l10n-ithemes-security-pro' ), $id, $name ) ) );
				$this->set_can_save( false );
			}
		}

		foreach ( array_keys( $this->settings ) as $name ) {
			if ( ! isset( $this->defaults[$name] ) && ! in_array( $name, $this->vars_to_skip_validate_matching_fields ) ) {
				$this->add_error( new WP_Error( "itsec-validator-$id-validate_matching_fields-unknown-name-$name", sprintf( __( 'A validation function for %1$s received data that has an entry for %2$s when no such entry exists.', 'it-l10n-ithemes-security-pro' ), $id, $name ) ) );
				$this->set_can_save( false );
			}
		}
	}

	protected function validate_matching_types() {
		$id = $this->get_id();

		foreach ( $this->defaults as $name => $value ) {
			if ( in_array( $name, $this->vars_to_skip_validate_matching_types ) ) {
				// This is to prevent errors for a specific var appearing twice.
				continue;
			}

			if ( ! isset( $this->settings[$name] ) ) {
				// Skip missing entries to allow implementations that use validate_matching_types() but not
				// validate_matching_fields().
				continue;
			}

			if ( gettype( $value ) !== gettype( $this->settings[$name] ) ) {
				$this->add_error( new WP_Error( "itsec-validator-$id-validate_matching_types-inmatching-type-$name", sprintf( __( 'A validation function for %1$s received data that does not match the expected data type for the %2$s entry. A data type of %3$s was expected, but a data type of %4$s was received.', 'it-l10n-ithemes-security-pro' ), $id, $name, gettype( $value ), gettype( $this->settings[$name] ) ) ) );
				$this->set_can_save( false );
			}
		}
	}

	final protected function set_default_if_empty( $vars ) {
		foreach ( (array) $vars as $var ) {
			if ( ! isset( $this->settings[$var] ) || '' === $this->settings[$var] ) {
				$this->settings[$var] = $this->defaults[$var];
			}
		}
	}

	final protected function set_previous_if_empty( $vars ) {
		foreach ( (array) $vars as $var ) {
			if ( ! isset( $this->settings[$var] ) || '' === $this->settings[$var] ) {
				$this->settings[$var] = $this->previous_settings[$var];
			}
		}
	}

	final protected function sanitize_setting( $type, $var, $name, $prevent_save_on_error = true, $trim_value = true ) {
		$id = $this->get_id();

		if ( ! isset( $this->settings[$var] ) ) {
			$this->add_error( new WP_Error( "itsec-validator-missing-var-$id-$var", sprintf( __( 'A validation check for %1$s failed. The %2$s value is missing. This could be due to a problem with the iThemes Security installation or an invalid modification. Please reinstall iThemes Security and try again.', 'it-l10n-ithemes-security-pro' ), $id, $name ) ) );
			return false;
		}

		if ( $trim_value && is_string( $this->settings[$var] ) ) {
			$this->settings[$var] = trim( $this->settings[$var] );
		}

		$error = false;

		if ( 'string' === $type ) {
			$this->settings[$var] = (string) $this->settings[$var];
		} else if ( 'non-empty-string' === $type ) {
			$this->settings[$var] = (string) $this->settings[$var];

			if ( empty( $this->settings[$var] ) ) {
				$error = sprintf( __( 'The %1$s value cannot be empty.', 'it-l10n-ithemes-security-pro' ), $name );
			}
		} else if ( 'title' === $type ) {
			$this->settings[$var] = sanitize_title( $this->settings[$var] );
		} else if ( 'non-empty-title' === $type ) {
			$this->settings[$var] = sanitize_title( $this->settings[$var] );

			if ( empty( $this->settings[$var] ) ) {
				$error = sprintf( __( 'The %1$s value cannot be empty.', 'it-l10n-ithemes-security-pro' ), $name );
			}
		} else if ( 'array' === $type ) {
			if ( ! is_array( $this->settings[$var] ) ) {
				if ( empty( $this->settings[$var] ) ) {
					$this->settings[$var] = array();
				} else {
					$this->settings[$var] = array( $this->settings[$var] );
				}
			}
		} else if ( 'bool' === $type ) {
			if ( 'false' === $this->settings[$var] ) {
				$this->settings[$var] = false;
			} else if ( 'true' === $this->settings[$var] ) {
				$this->settings[$var] = true;
			} else {
				$this->settings[$var] = (bool) $this->settings[$var];
			}
		} else if ( 'int' === $type ) {
			$test_val = intval( $this->settings[$var] );
			if ( (string) $test_val === (string) $this->settings[$var] ) {
				$this->settings[$var] = $test_val;
			} else {
				$error = sprintf( __( 'The %1$s value must be an integer.', 'it-l10n-ithemes-security-pro' ), $name );
			}
		} else if ( 'positive-int' === $type ) {
			$test_val = intval( $this->settings[$var] );
			if ( (string) $test_val === (string) $this->settings[$var] && $test_val >= 0 ) {
				$this->settings[$var] = $test_val;
			} else {
				$error = sprintf( __( 'The %1$s value must be a positive integer.', 'it-l10n-ithemes-security-pro' ), $name );
			}
		} else if ( 'email' === $type ) {
			$this->settings[$var] = sanitize_text_field( $this->settings[$var] );

			if ( empty( $this->settings[$var] ) || ! is_email( $this->settings[$var] ) ) {
				$error = sprintf( __( 'The %1$s value must be a valid email address.', 'it-l10n-ithemes-security-pro' ), $name );
			}
		} else if ( 'valid-username' === $type ) {
			$this->settings[$var] = sanitize_text_field( $this->settings[$var] );

			if ( ! empty( $this->settings[$var] ) && ! validate_username( $this->settings[$var] ) ) {
				$error = sprintf( __( 'The %1$s value is not a valid username.', 'it-l10n-ithemes-security-pro' ), $name );
			}
		} else if ( 'date' === $type ) {
			$val = $this->settings[$var];

			$separator = '[\-/\. ]';

			if ( preg_match( "|^(\d\d\d\d)$separator(\d\d?)$separator(\d\d?)$|", $val, $match ) ) {
				$year = intval( $match[1] );
				$month = intval( $match[2] );
				$day = intval( $match[3] );

				if ( ! checkdate( $month, $day, $year ) ) {
					$error = sprintf( __( 'The %1$s value must be a valid date.', 'it-l10n-ithemes-security-pro' ), $name );
				}
			} else {
				$error = sprintf( __( 'The %1$s value must be a valid date in the format of YYYY-MM-DD.', 'it-l10n-ithemes-security-pro' ), $name );
			}
		} else if ( 'writable-directory' === $type ) {
			if ( ! is_string( $this->settings[$var] ) ) {
				$error = sprintf( __( 'The %1$s value must be a string.', 'it-l10n-ithemes-security-pro' ), $name );
			} else {
				require_once( ITSEC_Core::get_core_dir() . 'lib/class-itsec-lib-directory.php' );

				$this->settings[$var] = rtrim( $this->settings[$var], DIRECTORY_SEPARATOR );

				if ( ! ITSEC_Lib_Directory::is_dir( $this->settings[$var] ) ) {
					$result = ITSEC_Lib_Directory::create( $this->settings[$var] );

					if ( is_wp_error( $result ) ) {
						$error = sprintf( _x( 'The directory supplied in %1$s cannot be used as a valid directory. %2$s', '%1$s is the input name. %2$s is the error message.', 'it-l10n-ithemes-security-pro' ), $name, $result->get_error_message() );
					}
				}

				if ( empty( $error ) && ! ITSEC_Lib_Directory::is_writable( $this->settings[$var] ) ) {
					$error = sprintf( __( 'The directory supplied in %1$s is not writable. Please select a directory that can be written to.', 'it-l10n-ithemes-security-pro' ), $name );
				}

				if ( empty( $error ) ) {
					ITSEC_Lib_Directory::add_file_listing_protection( $this->settings[$var] );
				}
			}
		} else if ( 'writable-file' === $type ) {
			if ( ! is_string( $this->settings[$var] ) ) {
				$error = sprintf( __( 'The %1$s value must be a string.', 'it-l10n-ithemes-security-pro' ), $name );
			} else {
				require_once( ITSEC_Core::get_core_dir() . 'lib/class-itsec-lib-directory.php' );

				if ( ! ITSEC_Lib_File::is_file( $this->settings[$var] ) && ITSEC_Lib_File::exists( $this->settings[$var] ) ) {
					$error = sprintf( __( 'The file path supplied in %1$s cannot be used as it already exists but is not a file. Please supply a valid file path.', 'it-l10n-ithemes-security-pro' ), $name );
				} else {
					$result = ITSEC_Lib_Directory::create( dirname( $this->settings[$var] ) );

					if ( is_wp_error( $result ) ) {
						$error = sprintf( _x( 'The file path supplied in %1$s cannot be used as the parent directory cannot be created. %2$s', '%1$s is the input name. %2$s is the error message.', 'it-l10n-ithemes-security-pro' ), $name, $result->get_error_message() );
					} else if ( ! ITSEC_Lib_File::exists( $this->settings[$var] ) ) {
						$result = ITSEC_Lib_File::write( $this->settings[$var], '' );

						if ( is_wp_error( $result ) ) {
							$error = sprintf( __( 'The file path supplied in %1$s could not be created. Please supply a file path that can be written to.', 'it-l10n-ithemes-security-pro' ), $name );
						} else if ( ! is_writable( $this->settings[$var] ) ) {
							$error = sprintf( __( 'The file path supplied in %1$s was successfully created, but it cannot be updated. Please supply a file path that can be written to.', 'it-l10n-ithemes-security-pro' ), $name );
						}
					} else if ( ! is_writable( $this->settings[$var] ) ) {
						$error = sprintf( __( 'The file path supplied in %1$s is not writable. Please supply a file path that can be written to.', 'it-l10n-ithemes-security-pro' ), $name );
					}
				}
			}
		} else if ( is_array( $type ) && 2 === count( $type ) && $this === $type[0] ) {
			$this->settings[$var] = $this->convert_string_to_array( $this->settings[$var] );

			if ( ! is_array( $this->settings[$var] ) ) {
				$error = sprintf( __( 'The %1$s value must be a string with each entry separated by a new line.', 'it-l10n-ithemes-security-pro' ), $name );
			} else {
				$invalid_entries = array();

				foreach ( $this->settings[$var] as $index => $entry ) {
					$entry = sanitize_text_field( trim( $entry ) );
					$this->settings[$var][$index] = $entry;

					if ( empty( $entry ) ) {
						unset( $this->settings[$var][$index] );
					} else {
						$result = call_user_func( $type, $entry );

						if ( false === $result ) {
							$invalid_entries[] = $entry;
						} else {
							$this->settings[$var][$index] = $result;
						}
					}
				}

				$this->settings[$var] = array_unique( $this->settings[$var] );

				if ( ! empty( $invalid_entries ) ) {
					$error = wp_sprintf( _n( 'The following entry in %1$s is invalid: %2$l', 'The following entries in %1$s are invalid: %2$l', count( $invalid_entries ), 'it-l10n-ithemes-security-pro' ), $name, $invalid_entries );
				}
			}
		} else if ( is_array( $type ) ) {
			if ( is_array( $this->settings[$var] ) ) {
				$invalid_entries = array();

				foreach ( $this->settings[$var] as $index => $entry ) {
					$entry = sanitize_text_field( trim( $entry ) );
					$this->settings[$var][$index] = $entry;

					if ( empty( $entry ) ) {
						unset( $this->settings[$var][$index] );
					} else if ( ! in_array( $entry, $type, true ) ) {
						$invalid_entries[] = $entry;
					}
				}

				$this->settings[$var] = array_unique( $this->settings[$var] );

				if ( ! empty( $invalid_entries ) ) {
					$error = wp_sprintf( _n( 'The following entry in %1$s is invalid: %2$l', 'The following entries in %1$s are invalid: %2$l', count( $invalid_entries ), 'it-l10n-ithemes-security-pro' ), $name, $invalid_entries );
				}
			} else if ( ! in_array( $this->settings[$var], $type, true ) ) {
				$error = wp_sprintf( _n( 'The valid value for %1$s is: %2$l.', 'The valid values for %1$s are: %2$l.', count( $type ), 'it-l10n-ithemes-security-pro' ), $name, $type );
				$type = 'array';
			}
		} else if ( 'newline-separated-array' === $type ) {
			$this->settings[$var] = $this->convert_string_to_array( $this->settings[$var] );

			if ( ! is_array( $this->settings[$var] ) ) {
				$error = sprintf( __( 'The %1$s value must be a string with each entry separated by a new line.', 'it-l10n-ithemes-security-pro' ), $name );
			}
		} else if ( 'newline-separated-emails' === $type ) {
			$this->settings[$var] = $this->convert_string_to_array( $this->settings[$var] );

			if ( ! is_array( $this->settings[$var] ) ) {
				$error = sprintf( __( 'The %1$s value must be a string with each entry separated by a new line.', 'it-l10n-ithemes-security-pro' ), $name );
			} else {
				$invalid_emails = array();

				foreach ( $this->settings[$var] as $index => $email ) {
					$email = sanitize_text_field( trim( $email ) );
					$this->settings[$var][$index] = $email;

					if ( empty( $email ) ) {
						unset( $this->settings[$var][$index] );
					} else if ( ! is_email( $email ) ) {
						$invalid_emails[] = $email;
					}
				}

				$this->settings[$var] = array_unique( $this->settings[$var] );

				if ( ! empty( $invalid_emails ) ) {
					$error = wp_sprintf( _n( 'The following email in %1$s is invalid: %2$l', 'The following emails in %1$s are invalid: %2$l', count( $invalid_emails ), 'it-l10n-ithemes-security-pro' ), $name, $invalid_emails );
				}
			}
		} else if ( 'newline-separated-ips' === $type ) {
			$this->settings[$var] = $this->convert_string_to_array( $this->settings[$var] );

			if ( ! is_array( $this->settings[$var] ) ) {
				$error = sprintf( __( 'The %1$s value must be a string with each entry separated by a new line.', 'it-l10n-ithemes-security-pro' ), $name );
			} else {
				require_once( ITSEC_Core::get_core_dir() . 'lib/class-itsec-lib-ip-tools.php' );

				$invalid_ips = array();

				foreach ( $this->settings[$var] as $index => $ip ) {
					$ip = trim( $ip );

					if ( '' === $ip ) {
						unset( $this->settings[$var][$index] );
					} else {
						$validated_ip = ITSEC_Lib_IP_Tools::ip_wild_to_ip_cidr( $ip );

						if ( false === $validated_ip ) {
							$invalid_ips[] = $ip;
						} else {
							$this->settings[$var][$index] = $validated_ip;
						}
					}
				}

				$this->settings[$var] = array_unique( $this->settings[$var] );

				if ( ! empty( $invalid_ips ) ) {
					$error = wp_sprintf( _n( 'The following IP in %1$s is invalid: %2$l', 'The following IPs in %1$s are invalid: %2$l', count( $invalid_ips ), 'it-l10n-ithemes-security-pro' ), $name, $invalid_ips );
				}
			}
		} else if ( 'newline-separated-extensions' === $type ) {
			$this->settings[$var] = $this->convert_string_to_array( $this->settings[$var] );

			if ( ! is_array( $this->settings[$var] ) ) {
				$error = sprintf( __( 'The %1$s value must be a string with each entry separated by a new line.', 'it-l10n-ithemes-security-pro' ), $name );
			} else {
				$invalid_extensions = array();

				foreach ( $this->settings[$var] as $index => $extension ) {
					if ( ! preg_match( '/^(\.[^.]+)+$/', $extension ) ) {
						$invalid_extensions[] = $extension;
					}
				}

				$this->settings[$var] = array_unique( $this->settings[$var] );

				if ( ! empty( $invalid_extensions ) ) {
					$error = wp_sprintf( _n( 'The following extension in %1$s is invalid: %2$l', 'The following extensions in %1$s are invalid: %2$l', count( $invalid_extensions ), 'it-l10n-ithemes-security-pro' ), $name, $invalid_extensions );
				}
			}
		} else {
			/* translators: 1: sanitize type, 2: input name */
			$error = sprintf( __( 'An invalid sanitize type of "%1$s" was received for the %2$s input.', 'it-l10n-ithemes-security-pro' ), $type, $name );
		}

		if ( false !== $error ) {
			$this->add_error( new WP_Error( "itsec-validator-$id-invalid-type-$var-$type", $error ) );
			$this->vars_to_skip_validate_matching_types[] = $var;

			if ( $prevent_save_on_error && ITSEC_Core::is_interactive() ) {
				$this->set_can_save( false );
			}

			return false;
		}

		return true;
	}

	final protected function convert_string_to_array( $string ) {
		if ( is_string( $string ) ) {
			$array = preg_split( "/[\r\n]+/", $string );
		} else if ( is_array( $string ) ) {
			$array = $string;
		} else {
			return $string;
		}

		foreach ( $array as $key => $val ) {
			$val = trim( $val );

			if ( empty( $val ) ) {
				unset( $array[$key] );
			} else {
				$array[$key] = $val;
			}
		}

		return $array;
	}

	final protected function add_error( $error ) {
		$this->errors[] = $error;
	}

	final public function found_errors() {
		if ( empty( $this->errors ) ) {
			return false;
		} else {
			return true;
		}
	}

	final public function get_errors() {
		return $this->errors;
	}

	final protected function add_message( $message ) {
		$this->messages[] = $message;
	}

	final public function get_messages() {
		return $this->messages;
	}

	final protected function set_can_save( $can_save ) {
		$this->can_save = (bool) $can_save;
	}

	final public function can_save() {
		return $this->can_save;
	}

	final protected function set_needs_refresh( $needs_refresh ) {
		$this->needs_refresh = (bool) $needs_refresh;
	}

	final public function needs_refresh() {
		return $this->needs_refresh;
	}

	final public function get_settings() {
		return $this->settings;
	}
}
