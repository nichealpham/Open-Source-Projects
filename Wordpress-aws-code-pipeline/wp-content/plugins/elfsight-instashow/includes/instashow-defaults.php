<?php

if (!defined('ABSPATH')) exit;


$elfsight_instashow_defaults = array(
	'api' => '',

	// Source
	'source' => '',
	'filter_only' => '',
	'filter_except' => '',
	'filter' => '',
	'limit' => 0,
	'cache_media_time' => 0,

	// Sizes
	'width' => 'auto',
	'height' => 'auto',
	'columns' => 4,
	'rows' => 2,
	'gutter' => 0,
	'responsive' => '',

	// UI
	'arrows_control' => true,
	'scroll_control' => false,
	'drag_control' => true,
	'direction' => 'horizontal',
	'free_mode' => false,
	'scrollbar' => true,
	'effect' => 'slide',
	'speed' => 600,
	'easing' => 'ease',
	'loop' => true,
	'auto' => 0,
	'auto_hover_pause' => true,
	'popup_deep_linking' => false,
	'popup_speed' => 400,
	'popup_easing' => 'ease',
	'lang' => 'en',
	'mode' => 'popup',
	'popup_hr_images' => false,

	// Info
	'info' => 'likesCounter, commentsCounter, description',
	'popup_info' => 'username, instagramLink, likesCounter, commentsCounter, location, passedTime, description, comments',

	// Style
	'color_gallery_bg' => 'rgba(0, 0, 0, 0)',
	'color_gallery_counters' => 'rgb(255, 255, 255)',
	'color_gallery_description' => 'rgb(255, 255, 255)',
	'color_gallery_overlay' => 'rgba(33, 150, 243, 0.9)',
	'color_gallery_arrows' => 'rgb(0, 142, 255)',
	'color_gallery_arrows_hover' => 'rgb(37, 181, 255)',
	'color_gallery_arrows_bg' => 'rgba(255, 255, 255, 0.9)',
	'color_gallery_arrows_bg_hover' => 'rgb(255, 255, 255)',
	'color_gallery_scrollbar' => 'rgba(255, 255, 255, 0.5)',
	'color_gallery_scrollbar_slider' => 'rgb(68, 68, 68)',
	'color_popup_overlay' => 'rgba(43, 43, 43, 0.9)',
	'color_popup_bg' => 'rgb(255, 255, 255)',
	'color_popup_username' => 'rgb(0, 0, 0)',
	'color_popup_username_hover' => 'rgb(56, 151, 240)',
	'color_popup_instagram_link' => 'rgb(56, 151, 240)',
	'color_popup_instagram_link_hover' => 'rgb(38, 141, 234)',
	'color_popup_counters' => 'rgb(109, 109, 109)',
	'color_popup_passed_time' => 'rgb(152, 152, 152)',
	'color_popup_anchor' => 'rgb(17, 60, 110)',
	'color_popup_anchor_hover' => 'rgb(56, 151, 240)',
	'color_popup_text' => 'rgb(52, 52, 52)',
	'color_popup_controls' => 'rgb(103, 103, 103)',
	'color_popup_controls_hover' => 'rgb(255, 255, 255)',
	'color_popup_mobile_controls' => 'rgb(103, 103, 103)',
	'color_popup_mobile_controls_bg' => 'rgba(255, 255, 255, 0.8)',

	// Custom templates
	'tpl_gallery_wrapper' => '',
	'tpl_gallery_view' => '',
	'tpl_gallery_media' => '',
	'tpl_gallery_info' => '',
	'tpl_gallery_counter' => '',
	'tpl_gallery_cover' => '',
	'tpl_gallery_arrows' => '',
	'tpl_gallery_scroll' => '',
	'tpl_gallery_loader' => '',
	'tpl_popup_root' => '',
	'tpl_popup_twilight' => '',
	'tpl_popup_media' => '',

	// Other
	'debug' => false
);

$elfsight_instashow_add_scripts = false;

?>