<?php

if (!defined('ABSPATH')) exit;


/*
  Elfsight Admin Widgets Helpers
*/

function elfsight_instashow_widgets_get_table_name() {
  global $wpdb;

  return $wpdb->prefix . str_replace('-', '_', ELFSIGHT_INSTASHOW_SLUG) . '_widgets';
}

function wp_elfsight_instashow_widgets_table_exists() {
  global $wpdb;

  $table_name = elfsight_instashow_widgets_get_table_name();

  return !!$wpdb->get_var('SHOW TABLES LIKE "' . esc_sql($table_name) . '"');
}



function wp_elfsight_instashow_widgets_upgrade() {
	if (wp_elfsight_instashow_widgets_table_exists()) {
		return;
	}

	elfsight_instashow_widgets_create_table();
}


/*
  Elfsight Admin Widgets DB Table Manipulations
*/

function elfsight_instashow_widgets_create_table() {
  $table_name = elfsight_instashow_widgets_get_table_name();

	if (!function_exists('dbDelta')) {
	  require (ABSPATH . 'wp-admin/includes/upgrade.php');
	}

	dbDelta(
	  'CREATE TABLE `' . esc_sql($table_name) . '` (
	    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	    `name` varchar(255) NOT NULL,
	    `time_created` varchar(10) NOT NULL,
	    `time_updated` varchar(10) NOT NULL,
	    `active` int(1) NOT NULL DEFAULT "1",
	    `options` text NOT NULL,
	    PRIMARY KEY (`id`)
	  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;'
	);
}
register_activation_hook(ELFSIGHT_INSTASHOW_FILE, 'wp_elfsight_instashow_widgets_upgrade');

/*
  Elfsight Admin Widgets API
  CRUD operations with widgets
*/

function elfsight_instashow_widgets_api_get_list(&$result) {
  global $wpdb;

  $table_name = elfsight_instashow_widgets_get_table_name();
  $id = !empty($_GET['id']) ? intval($_GET['id']) : null;

  $select_sql = 'SELECT * FROM `' . esc_sql($table_name) . '` WHERE `active` = "1"';

  if ($id) {
    $select_sql .= ' AND `id` = "' . esc_sql($id) . '"';
  }

  $select_sql .= ' ORDER BY `id` DESC';

  $list = $wpdb->get_results($select_sql, ARRAY_A);

  $result['status'] = is_null($list) ? false : true;

  foreach ($list as &$widget) {
    $options_raw_json = stripslashes($widget['options']);
    $widget['options'] = json_decode($options_raw_json);
  }

  $result['data'] = $list;
}

function elfsight_instashow_widgets_api_post_add(&$result) {
  global $wpdb;

  $table_name = elfsight_instashow_widgets_get_table_name();

  $options_json = null;
  $invalid_fields = array();

  if (empty($_POST['name']) || strlen($_POST['name']) > 255) {
    $invalid_fields[] = 'name';
  }

  if (empty($_POST['options'])) {
    $invalid_fields[] = 'options';

  } else {
    $options_json = rawurldecode($_POST['options']);

    if (!json_decode($options_json)) {
      $invalid_fields[] = 'options';
    }
  }

  if ($invalid_fields) {
    $result['status'] = false;
    $result['error'] = __('Incoming data is invalid.', ELFSIGHT_INSTASHOW_TEXTDOMAIN);
    $result['invalid_fields'] = $invalid_fields;

  } else {
    $status = !!$wpdb->insert($table_name, array(
      'name' => $_POST['name'],
      'time_created' => time(),
      'time_updated' => 0,
      'active' => 1,
      'options' => $options_json,
    ));

    $result['status'] = $status;

    if (!$status) {
      $result['error'] = __('An MySQL error occurred while adding new widget.', ELFSIGHT_INSTASHOW_TEXTDOMAIN);

    } else if (get_option('elfsight_instashow_widgets_clogged') !== 'true') {
      update_option('elfsight_instashow_widgets_clogged', 'true');
    }
  }
}

function elfsight_instashow_widgets_api_post_remove(&$result) {
  global $wpdb;

  $table_name = elfsight_instashow_widgets_get_table_name();
  $id = !empty($_POST['id']) ? intval($_POST['id']) : null;

  if (!$id) {
    $result['status'] = false;
    $result['error'] = __('Parameter "id" is required.', ELFSIGHT_INSTASHOW_TEXTDOMAIN);

    return;
  }

  $status = !!$wpdb->update($table_name, array('active' => 0, 'time_updated' => time()), array('id' => $id));
  $result['status'] = $status;

  if (!$status) {
    $result['error'] = __('Widget with specified id doesnt exist.', ELFSIGHT_INSTASHOW_TEXTDOMAIN);
  }
}

function elfsight_instashow_widgets_api_post_restore(&$result) {
  global $wpdb;

  $table_name = elfsight_instashow_widgets_get_table_name();
  $id = !empty($_POST['id']) ? intval($_POST['id']) : null;

  if (!$id) {
    $result['status'] = false;
    $result['error'] = __('Parameter "id" is required.', ELFSIGHT_INSTASHOW_TEXTDOMAIN);

    return;
  }

  $status = !!$wpdb->update($table_name, array('active' => 1, 'time_updated' => time()), array('id' => $id));
  $result['status'] = $status;

  if (!$status) {
    $result['error'] = __('Widget with specified id doesnt exist.', ELFSIGHT_INSTASHOW_TEXTDOMAIN);
  }
}

function elfsight_instashow_widgets_api_post_update(&$result) {
  global $wpdb;

  $table_name = elfsight_instashow_widgets_get_table_name();
  $id = !empty($_POST['id']) ? intval($_POST['id']) : null;
  $name = !empty($_POST['name']) ? $_POST['name'] : null;
  $options_json = !empty($_POST['options']) ? rawurldecode($_POST['options']) : null;

  if (!$id) {
    $result['status'] = false;
    $result['error'] = __('Parameter "id" is required.', ELFSIGHT_INSTASHOW_TEXTDOMAIN);

    return;
  }

  $invalid_fields = array();
  $fields = array('time_updated' => time());

  if ($name) {
    if (strlen($name) > 255) {
      $invalid_fields[] = 'name';

    } else {
      $fields['name'] = $name;
    }
  }

  if ($options_json) {
    if (!json_decode($options_json)) {
      $invalid_fields[] = 'options';

    } else {
      $fields['options'] = $options_json;
    }
  }

  if ($invalid_fields) {
    $result['status'] = false;
    $result['error'] = __('Incoming data is invalid.', ELFSIGHT_INSTASHOW_TEXTDOMAIN);
    $result['invalid_fields'] = $invalid_fields;

  } else {
    $status = !!$wpdb->update($table_name, $fields, array('id' => $id));
    $result['status'] = $status;

    if (!$status) {
      $result['error'] = __('Widget with specified id doesnt exist.', ELFSIGHT_INSTASHOW_TEXTDOMAIN);
    }
  }
}

function elfsight_instashow_widgets_api() {
  $result = array();

  $method = strtolower($_SERVER['REQUEST_METHOD']);
  $endpoint = !empty($_REQUEST['endpoint']) ? $_REQUEST['endpoint'] : '';
  $endpoint_handler_name = sprintf('elfsight_instashow_widgets_api_%s_%s', $method, $endpoint);

  if (function_exists($endpoint_handler_name)) {
    call_user_func_array($endpoint_handler_name, array(&$result));

  } else {
    $result['status'] = false;
    $result['error'] = sprintf('Unknown endpoint "%s/%s"', $method, $endpoint);
  }

  header('Content-type: application/json; charset=utf-8');
  echo json_encode($result);

  exit;
}

add_action('wp_ajax_elfsight_instashow_widgets_api', 'elfsight_instashow_widgets_api');
