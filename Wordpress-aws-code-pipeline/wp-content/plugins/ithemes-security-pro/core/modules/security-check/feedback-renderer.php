<?php

final class ITSEC_Security_Check_Feedback_Renderer {
	public static function render( $data ) {
		$section_groups = array();

		foreach ( $data['sections'] as $name => $args ) {
			$section_groups[$args['status']][$name] = $args;
		}

		if ( isset( $section_groups['call-to-action'] ) ) {
			self::render_sections( 'call-to-action', $section_groups['call-to-action'] );
		}
		if ( isset( $section_groups['action-taken'] ) ) {
			self::render_sections( 'action-taken', $section_groups['action-taken'] );
		}
		if ( isset( $section_groups['confirmation'] ) ) {
			self::render_sections( 'confirmation', $section_groups['confirmation'] );
		}
	}

	private static function render_sections( $status, $sections ) {
		foreach ( $sections as $name => $args ) {
			$classes = array( 'itsec-security-check-container', "itsec-security-check-container-$status" );

			if ( $args['interactive'] ) {
				$classes[] = 'itsec-security-check-container-is-interactive';
			}

			echo '<div class="' . self::esc_attr( implode( ' ', $classes ) ) . '"';

			if ( ! empty( $id ) ) {
				echo " id=\"$id\"";
			}

			echo ">\n";

			if ( $args['interactive'] ) {
				echo '<div class="itsec-security-check-feedback"></div>';
			}

			foreach ( $args['entries'] as $entry ) {
				self::render_entry( $entry );
			}

			echo "</div>\n";
		}
	}

	private static function render_entry( $entry ) {
		if ( empty( $entry['type'] ) ) {
			return;
		}

		if ( 'text' === $entry['type'] ) {
			if ( isset( $entry['value'] ) ) {
				echo "<p>{$entry['value']}</p>\n";
			}
		} else if ( 'input' === $entry['type'] ) {
			if ( empty( $entry['input'] ) ) {
				return;
			}

			$defaults = array(
				'format'      => '%1$s',
				'value'       => '',
				'style_class' => '',
				'data'        => array(),
			);
			$entry = array_merge( $defaults, $entry );

			if ( ! empty( $entry['value_alias'] ) ) {
				$entry['value'] = self::get_alias_value( $entry['value_alias'] );
			}

			$data_attrs = array();

			foreach ( (array) $entry['data'] as $key => $val ) {
				$key = preg_replace( '/[^a-zA-Z0-9\-_]+/', '', $key );
				$val = self::esc_attr( $val );

				$data_attrs[] = " data-$key=\"$val\"";
			}


			if ( 'select' === $entry['input'] ) {
				if ( empty( $entry['name'] ) || empty( $entry['options'] ) ) {
					return;
				}

				$options = "\n";

				foreach ( $entry['options'] as $value => $description ) {
					$option = '<option value="' . self::esc_attr( $value ) . '"';

					if ( $value === $entry['value'] ) {
						$option .= ' selected="selected"';
					}

					$option .= '>' . self::esc_html( $description ) . "</option>\n";

					$options .= $option;
				}

				$input_format = '<select name="%1$s" class="%2$s"%3$s>%4$s</select>';
				$input = sprintf( $input_format, self::esc_attr( $entry['name'] ), self::esc_attr( $entry['style_class'] ), implode( '', $data_attrs ), $options );
			} else if ( 'textarea' === $entry['input'] ) {
				if ( empty( $entry['name'] ) ) {
					return;
				}

				$input_format = '<textarea name="%1$s" class="%2$s"%3$s>%4$s</textarea>';
				$input = sprintf( $input_format, self::esc_attr( $entry['name'] ), self::esc_attr( $entry['style_class'] ), implode( '', $data_attrs ), self::esc_html( $entry['value'] ) );
			} else {
				if ( empty( $entry['name'] ) ) {
					return;
				}

				$input_format = '<input type="%1$s" name="%2$s" value="%3$s" class="%4$s"%5$s />';
				$input = sprintf( $input_format, self::esc_attr( $entry['input'] ), self::esc_attr( $entry['name'] ), self::esc_attr( $entry['value'] ), self::esc_attr( $entry['style_class'] ), implode( '', $data_attrs ) );
			}

			echo '<p><label>';
			printf( $entry['format'], $input );
			echo "</label></p>\n";
		}
	}

	private static function esc_attr( $attr ) {
		return esc_attr( $attr );
	}

	private static function esc_html( $html ) {
		return esc_html( $html );
	}

	private static function get_alias_value( $alias ) {
		if ( 'email' === $alias ) {
			return get_option( 'admin_email' );
		}
	}
}
