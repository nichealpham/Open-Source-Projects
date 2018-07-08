<?php

final class ITSEC_Form {
	private $options = array();
	private $tracked_booleans = array();
	private $tracked_strings = array();
	private $tracked_arrays = array();
	private $input_group = '';
	private $input_group_stack = array();


	public function __construct( $options = array() ) {
		$this->options =& $options;
	}

	public static function get_post_data() {
		$remove_vars = array( 'itsec-nonce', '_wp_http_referer' );
		$data = $_POST;

		foreach ( $remove_vars as $var ) {
			unset( $_POST[$var] );
		}

		$data = stripslashes_deep( $data );

		if ( isset( $data['data'] ) && isset( $data['data']['--itsec-form-serialized-data'] ) ) {
			parse_str( $data['data']['--itsec-form-serialized-data'], $data );
		}


		$defaults = array(
			'booleans' => false,
			'strings'  => '',
			'arrays'   => array(),
		);

		foreach ( $defaults as $name => $default ) {
			if ( ! isset( $data["--itsec-form-tracked-$name"] ) || ! is_array( $data["--itsec-form-tracked-$name"] ) ) {
				continue;
			}

			foreach ( $data["--itsec-form-tracked-$name"] as $index ) {
				$value = ITSEC_Form::get_array_value( $data, $index );

				if ( false === $default ) {
					$value = ( $value ) ? true : false;
					ITSEC_Form::add_array_value( $data, $index, $value );
				} else if ( '' === $default ) {
					if ( is_null( $value ) ) {
						ITSEC_Form::add_array_value( $data, $index, $default );
					}
				} else {
					if ( ! is_array( $value ) ) {
						ITSEC_Form::add_array_value( $data, $index, $default );
					}
				}
			}

			unset( $data["--itsec-form-tracked-$name"] );
		}

		if ( isset( $data['--itsec-form-convert-to-array'] ) && is_array( $data['--itsec-form-convert-to-array'] ) ) {
			foreach ( $data['--itsec-form-convert-to-array'] as $index ) {
				$value = ITSEC_Form::get_array_value( $data, $index );

				if ( is_array( $value ) ) {
					continue;
				} else if ( ! is_string( $value ) ) {
					ITSEC_Form::add_array_value( $data, $index, array() );
				}

				$lines = preg_split( "/[\r\n]+/", $value );

				foreach ( $lines as $key => $val ) {
					$val = trim( $val );

					if ( empty( $val ) ) {
						unset( $lines[$key] );
					} else {
						$lines[$key] = $val;
					}
				}

				ITSEC_Form::add_array_value( $data, $index, $lines );
			}

			unset( $data['--itsec-form-convert-to-array'] );
		}

		return $data;
	}

	public static function parse_values( $values = array(), $args = array() ) {
		$new_values = array();

		foreach ( (array) $values as $var => $val ) {
			$new_values[$var] = $val;
		}

		return $new_values;
	}

	public function start_form( $options = array() ) {
		if ( isset( $_REQUEST['page'] ) ) {
			list ( $location, $query ) = explode( '?', $_SERVER['REQUEST_URI'] );
			$file = basename( $location );

			if ( 'admin.php' == $file ) {
				$default_action = "$location?page={$_REQUEST['page']}";
			} else if ( ( 'edit.php' == $file ) && isset( $_REQUEST['post_type'] ) ) {
				$default_action = "$location?post_type={$_REQUEST['post_type']}&page={$_REQUEST['page']}";
			}
		}

		if ( ! isset( $default_action ) ) {
			$default_action = $_SERVER['REQUEST_URI'];
		}


		$defaults = array(
			'enctype' => 'multipart/form-data',
			'method'  => 'post',
			'action'  => $default_action,
		);

		if ( is_string( $options ) ) {
			$options = array(
				'id' => $options,
			);
		} else if ( ! is_array( $options ) ) {
			$options = array();
		}

		$options = array_merge( $defaults, $options );

		echo '<form';

		foreach ( (array) $options as $var => $val ) {
			if ( ! is_array( $val ) ) {
				$val = str_replace( '"', '&quot;', $val );
				echo " $var=\"$val\"";
			}
		}

		echo ">\n";
	}

	public function end_form() {
		echo "</form>\n";

		$this->new_form();
	}

	public function set_input_group() {
		$this->remove_all_input_groups();

		$args = func_get_args();

		call_user_func_array( array( &$this, 'add_input_group' ), $args );
	}

	public function add_input_group() {
		$args = func_get_args();

		$this->input_group = '';

		if ( is_array( $args[0] ) ) {
			$args = $args[0];
		}

		if ( ( 1 === count( $args ) ) && ! is_string( $args[0] ) && ! is_numeric( $args[0] ) ) {
			$args = array();
		}

		$this->input_group_stack = array_merge( $this->input_group_stack, $args );

		$this->generate_input_group();
	}

	public function remove_input_group() {
		array_pop( $this->input_group_stack );
		$this->generate_input_group();
	}

	public function remove_all_input_groups() {
		$this->input_group_stack = array();
		$this->generate_input_group();
	}

	public function push_input_groups() {
		if ( ! isset( $this->input_group_stack_cache ) || ! is_array( $this->input_group_stack_cache ) ) {
			$this->input_group_stack_cache = array();
		}

		array_push( $this->input_group_stack_cache, $this->input_group_stack );
	}

	public function pop_input_groups() {
		if ( ! is_array( $this->input_group_stack_cache ) || empty( $this->input_group_stack_cache ) ) {
			return;
		}

		$this->input_group_stack = array_pop( $this->input_group_stack_cache );

		$this->generate_input_group();
	}

	private function generate_input_group() {
		$args = $this->input_group_stack;

		$this->input_group = '';

		if ( ! empty( $args ) ) {
			$this->input_group = $args[0];

			for ( $x = 1; $x < count( $args ); $x++ ) {
				if ( ! is_array( $args[$x] ) && ! is_object( $args[$x] ) ) {
					$this->input_group .= '[' . ( (string) $args[$x] ) . ']';
				}
			}
		}
	}

	public function get_option( $name ) {
		if ( ! empty( $this->input_group ) ) {
			if ( false === strpos( $name, '[' ) ) {
				$name = "[$name]";
			} else {
				$name = preg_replace( '/^([^\[]+)\[/', '[$1][', $name );
			}

			$name = "{$this->input_group}$name";
		}

		return ITSEC_Form::get_array_value( $this->options, $name );
	}

	public function get_options() {
		if ( ! empty( $this->input_group ) ) {
			return ITSEC_Form::get_array_value( $this->options, $this->input_group );
		}

		return $this->options;
	}

	public function get_all_options() {
		return $this->options;
	}

	public function set_option( $name, $value ) {
		if ( ! empty( $this->input_group ) ) {
			if ( false === strpos( $name, '[' ) ) {
				$name = "[$name]";
			} else {
				$name = preg_replace( '/^([^\[]+)\[/', '[$1][', $name );
			}

			$name = "{$this->input_group}$name";
		}

		ITSEC_Form::add_array_value( $this->options, $name, $value );
	}

	public function set_options( $values ) {
		foreach ( $values as $name => $value ) {
			$this->set_option( $name, $value );
		}
	}

	public function set_default( $name, $value ) {
		if ( is_null( $this->get_option( $name ) ) ) {
			$this->set_option( $name, $value );
		}
	}

	public function set_defaults( $values ) {
		foreach ( $values as $name => $value ) {
			$this->set_default( $name, $value );
		}
	}

	public function clear_options() {
		$this->options = array();
	}

	public function push_options() {
		if ( ! isset( $this->options_cache ) || ! is_array( $this->options_cache ) ) {
			$this->options_cache = array();
		}

		array_push( $this->options_cache, $this->options );
	}

	public function pop_options() {
		if ( ! is_array( $this->options_cache ) || empty( $this->options_cache ) ) {
			return;
		}

		$this->options = array_pop( $this->options_cache );
	}

	public static function add_nonce( $name = null ) {
		wp_nonce_field( $name, 'itsec-nonce' );
	}

	public static function check_nonce( $name = null ) {
		check_admin_referer( $name, 'itsec-nonce' );
	}

	public function new_form() {
		$this->input_group = '';
		$this->input_group_stack = array();
	}

	public function add_submit( $var, $options = array() ) {
		if ( ! is_array( $options ) ) {
			$options = array( 'value' => $options );
		}

		$options['type'] = 'submit';
		$options['class'] = ( empty( $options['class'] ) ) ? 'button-primary' : $options['class'];

		$this->add_custom_input( $var, $options );
	}

	public function add_button( $var, $options = array() ) {
		if ( ! is_array( $options ) ) {
			$options = array( 'value' => $options );
		}

		$options['type'] = 'button';

		$this->add_custom_input( $var, $options );
	}

	public function add_text( $var, $options = array() ) {
		if ( ! is_array( $options ) ) {
			$options = array( 'value' => $options );
		}

		$options['type'] = 'text';

		$this->add_custom_input( $var, $options );
	}

	public function add_textarea( $var, $options = array() ) {
		if ( ! is_array( $options ) ) {
			$options = array( 'value' => $options );
		}

		$options['type'] = 'textarea';

		$this->add_custom_input( $var, $options );
	}

	public function add_password( $var, $options = array() ) {
		if ( ! is_array( $options ) ) {
			$options = array( 'value' => $options );
		}

		$options['type'] = 'password';
		$this->add_custom_input( $var, $options );
	}

	public function add_file( $var, $options = array() ) {
		if ( ! is_array( $options ) ) {
			$options = array( 'value' => $options );
		}

		$options['type'] = 'file';

		$this->add_custom_input( $var, $options );
	}

	public function add_checkbox( $var, $options = array() ) {
		if ( ! is_array( $options ) ) {
			$options = array( 'value' => $options );
		}

		$options['type'] = 'checkbox';

		if ( empty( $options['value'] ) ) {
			$options['value'] = '1';
		}

		$this->add_custom_input( $var, $options );
	}

	public function add_multi_checkbox( $var, $options = array() ) {
		if ( ! is_array( $options ) ) {
			$options = array( 'value' => $options );
		}

		$options['type'] = 'checkbox';
		$options['append_val_to_id'] = true;
		$var = $var . '[]';

		$this->add_custom_input( $var, $options );
	}

	public function add_radio( $var, $options = array() ) {
		if ( ! is_array( $options ) ) {
			$options = array( 'value' => $options );
		}

		$options['type'] = 'radio';
		$options['append_val_to_id'] = true;

		$this->add_custom_input( $var, $options );
	}

	public function add_select( $var, $options = array() ) {
		if ( ! is_array( $options ) ) {
			$options = array();
		} else if ( ! isset( $options['value'] ) || ! is_array( $options['value'] ) ) {
			$options = array( 'value' => $options );
		}

		$options['type'] = 'select';

		$this->add_custom_input( $var, $options );
	}

	public function add_multi_select( $var, $options = array() ) {
		if ( ! is_array( $options ) ) {
			$options = array();
		} else if ( ! isset( $options['value'] ) || ! is_array( $options['value'] ) ) {
			$options = array( 'value' => $options );
		}

		$options['type'] = 'select';
		$options['multiple'] = 'multiple';
		$var = $var . '[]';

		$this->add_custom_input( $var, $options );
	}

	public function add_hidden( $var, $options = array() ) {
		if ( ! is_array( $options ) ) {
			$options = array( 'value' => $options );
		}

		$options['type'] = 'hidden';

		$this->add_custom_input( $var, $options );
	}

	private function add_custom_input( $var, $options ) {
		if ( empty( $options['type'] ) ) {
			trigger_error( 'add_custom_input called without a type option set' );
		}

		$scrub_list['textarea']['value'] = true;
		$scrub_list['file']['value'] = true;
		$scrub_list['select']['value'] = true;
		$scrub_list['select']['type'] = true;

		$defaults = array(
			'name' => $var,
		);

		$input_group_name = $defaults['name'];

		$var = str_replace( '[]', '', $var );

		$clean_var = trim( preg_replace( '/[^a-z0-9_]+/i', '-', $var ), '-' );

		if ( ! empty( $this->input_group ) ) {
			if ( false === strpos( $defaults['name'], '[' ) ) {
				$defaults['name'] = "[{$defaults['name']}]";
			} else {
				$defaults['name'] = preg_replace( '/^([^\[]+)\[/', '[$1][', $defaults['name'] );
			}

			$input_group_name = $defaults['name'];

			$defaults['name'] = "{$this->input_group}{$defaults['name']}";

			$clean_var = trim( preg_replace( '/[^a-z0-9_]+/i', '-', $defaults['name'] ), '-' );
		}

		$val = $this->get_option( $var );

		$defaults['id'] = "itsec-$clean_var";

		if ( ! empty( $options['append_val_to_id'] ) && ( true === $options['append_val_to_id'] ) && ! empty( $options['value'] ) ) {
			unset( $options['append_val_to_id'] );
			$defaults['id'] .= '-' . trim( preg_replace( '/[^a-z0-9_]+/i', '-', $options['value'] ), '-' );
		}

		$options = ITSEC_Form::merge_defaults( $options, $defaults );

		if ( ! is_null( $val ) ) {
			if ( in_array( $options['type'], array( 'checkbox', 'radio' ) ) ) {
				if ( ( is_array( $val ) && in_array( $options['value'], $val ) ) || ( ! is_array( $val ) && ( (string) $val === (string) $options['value'] ) ) ) {
					$options['checked'] = 'checked';
				}
			} else if ( 'select' !== $options['type'] ) {
				$options['value'] = $val;
			}
		}


		$attributes = '';

		if ( false !== $options ) {
			foreach ( (array) $options as $name => $content ) {
				if ( ! is_array( $content ) && ( ! isset( $scrub_list[$options['type']][$name] ) || ( true !== $scrub_list[$options['type']][$name] ) ) ) {
					if ( 'value' == $name ) {
						$content = ITSEC_Form::esc_value_attr( $content );
					} else if ( ! in_array( $options['type'], array( 'submit', 'button' ) ) ) {
						$content = esc_attr( $content );
					}

					$attributes .= "$name=\"$content\" ";
				}
			}
		}


		if ( 'textarea' === $options['type'] ) {
			if ( ! isset( $options['value'] ) ) {
				$options['value'] = '';
			} else if ( is_array( $options['value'] ) ) {
				$options['value'] = implode( "\n", $options['value'] );
				echo '<input type="hidden" name="--itsec-form-convert-to-array[]" value="' . esc_attr( $options['name'] ) . '" />' . "\n";
			}

			echo "<textarea $attributes >" . ITSEC_Form::esc_value_attr( $options['value'] ) . '</textarea>';
		} else if ( 'select' === $options['type'] ) {
			echo "<select $attributes>\n";

			if ( isset( $options['value'] ) && is_array( $options['value'] ) ) {
				foreach ( (array) $options['value'] as $content => $name ) {
					if ( is_array( $name ) ) {
						if ( preg_match( '/^__optgroup_\d+$/', $content ) ) {
							echo "<optgroup class='it-classes-optgroup-separator'>\n";
						} else {
							echo "<optgroup label='" . esc_attr( $content ) . "'>\n";
						}

						foreach ( (array) $name as $content => $sub_name ) {
							if ( is_array( $val ) ) {
								$selected = '';

								foreach ( $val as $set_val ) {
									if ( (string) $set_val === (string) $content ) {
										$selected = ' selected="selected"';
									}
								}
							} else {
								$selected = ( ! is_null( $val ) && ( (string) $val === (string) $content ) ) ? ' selected="selected"' : '';
							}

							echo "<option value=\"" . ITSEC_Form::esc_value_attr( $content ) . "\"$selected>$sub_name</option>\n";
						}

						echo "</optgroup>\n";
					} else {
						if ( is_array( $val ) ) {
							$selected = '';

							foreach ( $val as $set_val ) {
								if ( (string) $set_val === (string) $content ) {
									$selected = ' selected="selected"';
								}
							}
						} else {
							$selected = ( ! is_null( $val ) && ( (string) $val === (string) $content ) ) ? ' selected="selected"' : '';
						}

						echo "<option value=\"" . ITSEC_Form::esc_value_attr( $content ) . "\"$selected>$name</option>\n";
					}
				}
			}

			echo "</select>\n";
		} else {
			echo '<input ' . $attributes . '/>' . "\n";
		}


		if ( '[]' === substr( $options['name'], -2 ) ) {
			if ( ! isset( $this->tracked_arrays[$options['name']] ) ) {
				echo '<input type="hidden" name="--itsec-form-tracked-arrays[]" value="' . esc_attr( substr( $options['name'], 0, -2 ) ) . '" />' . "\n";
				$this->tracked_arrays[$options['name']] = true;
			}
		} else if ( 'checkbox' === $options['type'] ) {
			if ( ! isset( $this->tracked_booleans[$options['name']] ) ) {
				echo '<input type="hidden" name="--itsec-form-tracked-booleans[]" value="' . esc_attr( $options['name'] ) . '" />' . "\n";
				$this->tracked_booleans[$options['name']] = true;
			}
		} else if ( 'radio' === $options['type'] ) {
			if ( ! isset( $this->tracked_strings[$options['name']] ) ) {
				echo '<input type="hidden" name="--itsec-form-tracked-empty-strings[]" value="' . esc_attr( $options['name'] ) . '" />' . "\n";
				$this->tracked_strings[$options['name']] = true;
			}
		}
	}

	private static function esc_value_attr( $text ) {
		$text = wp_check_invalid_utf8( $text );
		$text = htmlspecialchars( htmlspecialchars_decode( htmlspecialchars_decode( $text ) ), ENT_QUOTES );

		return $text;
	}

	private static function get_array_value( $array, $index ) {
		if ( is_string( $index ) ) {
			if ( false === strpos( $index, '[' ) ) {
				$index = array( $index );
			} else {
				$index = rtrim( $index, '[]' );
				$index = preg_split( '/[\[\]]+/', $index );
			}
		}

		while ( count( $index ) > 1 ) {
			if ( isset( $array[$index[0]] ) ) {
				$array = $array[$index[0]];
				array_shift( $index );
			} else {
				return null;
			}
		}

		if ( isset( $array[$index[0]] ) ) {
			return $array[$index[0]];
		}

		return null;
	}

	private static function add_array_value( &$array, $index, $val ) {
		if ( is_string( $index ) ) {
			if ( false === strpos( $index, '[' ) ) {
				$index = array( $index );
			} else {
				$index = rtrim( $index, '[]' );
				$index = preg_split( '/[\[\]]+/', $index );
			}
		}

		$cur_array =& $array;

		while ( count( $index ) > 1 ) {
			if ( ! isset( $cur_array[$index[0]] ) || ! is_array( $cur_array[$index[0]] ) ) {
				$cur_array[$index[0]] = array();
			}

			$cur_array =& $cur_array[$index[0]];
			array_shift( $index );
		}

		$cur_array[$index[0]] = $val;
	}

	private static function merge_defaults( $values, $defaults, $force = false ) {
		if ( ! ITSEC_Form::is_associative_array( $defaults ) ) {
			if ( ! isset( $values ) ) {
				return $defaults;
			}

			if ( false === $force ) {
				return $values;
			}

			if ( isset( $values ) || is_array( $values ) ) {
				return $values;
			}

			return $defaults;
		}

		foreach ( (array) $defaults as $key => $val ) {
			if ( ! isset( $values[$key] ) ) {
				$values[$key] = null;
			}

			$values[$key] = ITSEC_Form::merge_defaults( $values[$key], $val, $force );
		}

		return $values;
	}

	private static function is_associative_array( &$array ) {
		if ( ! is_array( $array ) || empty( $array ) ) {
			return false;
		}

		$next = 0;

		foreach ( $array as $k => $v ) {
			if ( $k !== $next++ ) {
				return true;
			}
		}

		return false;
	}
}
