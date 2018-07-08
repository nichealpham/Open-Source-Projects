<?php

if (!defined('ABSPATH')) exit;

function elfsight_instashow_shortcode_get_optons($id) {
	global $wpdb;

	$id = intval($id);
	$widgets_table_name = elfsight_instashow_widgets_get_table_name();
	$select_sql = '
		SELECT options FROM `' . esc_sql($widgets_table_name) . '`
		WHERE `id` = "' . esc_sql($id) . '" and `active` = "1"
	';

	$item = $wpdb->get_row($select_sql, ARRAY_A);
	$options = !empty($item['options']) ? json_decode($item['options'], true) : array();

	return $options;
}

// shortcode [instashow]
function elfsight_instashow_shortcode($atts) {
	global $elfsight_instashow_defaults, $elfsight_instashow_add_scripts;

	$elfsight_instashow_add_scripts = true;
	$api_url = get_option('elfsight_instashow_custom_api_url', ELFSIGHT_INSTASHOW_API_URL);

	if (!empty($atts['id'])) {
		$stored_options = elfsight_instashow_shortcode_get_optons($atts['id']);
		$stored_options_prepared = array();
		if (is_array($stored_options)) {
			foreach($stored_options as $name => $value) {
				$stored_options_prepared[ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $name)), '_')] = is_array($value) ? implode(', ', $value) : $value;
			}
		}

		$atts = array_combine(
			array_merge(array_keys($stored_options_prepared), array_keys($atts)),
			array_merge(array_values($stored_options_prepared), array_values($atts))
		);

		unset($atts['id']);
	}

	if (!empty($_GET['vc_editable'])) {
		$atts['debug'] = 'true';

		if (empty($atts['source'])) {
			$atts['source'] = '@muradosmann';
		}
	}

	foreach ($elfsight_instashow_defaults as $name => $value) {
		if (isset($atts[$name]) && is_bool($value)) {
			$atts[$name] = !empty($atts[$name]) && $atts[$name] !== 'false';
		}
	}

	$options = shortcode_atts($defaults = $elfsight_instashow_defaults, $atts, 'instashow');

	$result = '<div data-is';
	$result .= ' data-is-api="' . $api_url . '"';

	foreach ($options as $name => $value) {
		if ($value !== $elfsight_instashow_defaults[$name]) {

			// boolean
			if (is_bool($value)) {
				$value = $value ? 'true' : 'false';
			}

			// info
			if (($name == 'info' || $name == 'popup_info') && empty($value)) {
				$value = 'none';
			}

			// responsive
			if ($name == 'responsive') {
				$value = json_decode(rawurldecode($value));

				if (is_array($value)) {
					$new_value = array();
					foreach($value as $key => $responsive_item) {
						if (!empty($responsive_item->window_width) && (!empty($responsive_item->columns) || !empty($responsive_item->rows) || !empty($responsive_item->gutter))) {
							$new_value[intval($responsive_item->window_width)] = array(
								'columns' => !empty($responsive_item->columns) ? $responsive_item->columns : '',
								'rows' => !empty($responsive_item->rows) ? $responsive_item->rows : '',
								'gutter' => !empty($responsive_item->gutter) ? $responsive_item->gutter : ''
							);
						}
					}
					$value = $new_value;
				}

				$value = rawurlencode(json_encode($value));
			}

			$result .= sprintf(' data-is-%s="%s"', str_replace('_', '-', $name), esc_attr($value));
		}
	}
	$result .= '></div>';

	return $result;
}
add_shortcode('instashow', 'elfsight_instashow_shortcode');

?>
