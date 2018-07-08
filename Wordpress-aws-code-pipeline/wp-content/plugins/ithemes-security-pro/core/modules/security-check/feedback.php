<?php

final class ITSEC_Security_Check_Feedback {
	private $sections = array();
	private $current_section = '';

	public function __construct( $raw_data = false ) {
		if ( is_array( $raw_data ) && isset( $raw_data['sections'] ) && is_array( $raw_data['sections'] ) ) {
			$this->sections = $raw_data['sections'];
		}
	}

	public function add_section( $name, $args = array() ) {
		if ( ! isset( $this->sections[$name] ) ) {
			$default_args = array(
				'interactive' => false,
				'status'      => 'confirmation',
				'entries'     => array(),
			);

			$args = array_merge( $default_args, $args );

			$this->sections[$name] = $args;
		}

		$this->current_section = $name;
	}

	public function switch_section( $name ) {
		if ( isset( $this->sections[$name] ) ) {
			$this->current_section = $name;
			return true;
		} else {
			$this->current_section = '';
			return false;
		}
	}

	public function set_section_arg( $arg, $value, $name = false ) {
		if ( false === $name ) {
			$name = $this->current_section;
		}

		if ( ! isset( $this->sections[$name] ) ) {
			return false;
		}

		$this->sections[$name][$arg] = $value;
		return true;
	}

	public function add_entry( $entry ) {
		if ( empty( $this->current_section ) ) {
			return false;
		}

		$this->sections[$this->current_section]['entries'][] = $entry;
		return true;
	}

	public function add_text( $text ) {
		$entry = array(
			'type'  => 'text',
			'value' => $text,
		);

		$this->add_entry( $entry );
	}

	public function add_input( $input, $name, $args = array() ) {
		$entry = array(
			'type'        => 'input',
			'input'       => $input,
			'name'        => $name,
			'format'      => '%1$s',
			'value'       => '',
			'style_class' => '',
		);

		if ( 'select' === $input ) {
			$entry['options'] = array();
		}

		$entry = array_merge( $entry, $args );

		$this->add_entry( $entry );
	}

	public function get_raw_data() {
		return array(
			'sections' => $this->sections,
		);
	}
}
