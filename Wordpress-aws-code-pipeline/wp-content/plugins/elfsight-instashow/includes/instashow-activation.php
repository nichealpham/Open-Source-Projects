<?php

if (!defined('ABSPATH')) exit;


function elfsight_instashow_activation_activate() {
	if (!function_exists('wp_remote_get')) {
		return;
	}

	$params = array(
		'product' => ELFSIGHT_INSTASHOW_SLUG,
		'version' => ELFSIGHT_INSTASHOW_VERSION,
		'name' => 'activation',
		'extra' => '-',
		'href' => $_SERVER['HTTP_REFERER'],
		'userAgent' => '-',
		'screen' => '-'
	);

	$url = 'https://a.elfsight.com/collect?' . http_build_query($params);

	wp_remote_get($url);
}
register_activation_hook(ELFSIGHT_INSTASHOW_FILE, 'elfsight_instashow_activation_activate');


function elfsight_instashow_activation_deactivate() {
	if (!function_exists('wp_remote_get')) {
		return;
	}

	$params = array(
		'product' => ELFSIGHT_INSTASHOW_SLUG,
		'version' => ELFSIGHT_INSTASHOW_VERSION,
		'name' => 'deactivation',
		'extra' => '-',
		'href' => $_SERVER['HTTP_REFERER'],
		'userAgent' => '-',
		'screen' => '-'
	);

	$url = 'https://a.elfsight.com/collect?' . http_build_query($params);

	wp_remote_get($url);
}
register_deactivation_hook(ELFSIGHT_INSTASHOW_FILE, 'elfsight_instashow_activation_deactivate');
