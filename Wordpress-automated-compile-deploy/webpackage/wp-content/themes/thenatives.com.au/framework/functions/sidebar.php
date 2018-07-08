<?php
global $default_sidebars;
$default_sidebars = array(
	array(
		'name' => __('First Sidebar', 'nam_tech'),
		'id' => 'first-sidebar',
		'description' => __('First Sidebar', 'nam_tech'),
		'class' => 'sidebar first-sidebar',
		'before_widget' => '<div id="%1$s" class="widget sidebar-widget first-sidebar-widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h2 class="widget-title sidebar-title first-sidebar-title">',
		'after_title' => '</h2>'
	),
	array(
		'name' => __('Second Sidebar', 'nam_tech'),
		'id' => 'second-sidebar',
		'description' => __('Second Sidebar', 'nam_tech'),
		'class' => 'sidebar second-sidebar',
		'before_widget' => '<div id="%1$s" class="widget sidebar-widget second-sidebar-widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h2 class="widget-title sidebar-title second-sidebar-title">',
		'after_title' => '</h2>'
	),
	array(
		'name' => __('Third Sidebar', 'nam_tech'),
		'id' => 'third-sidebar',
		'description' => __('Third Sidebar', 'nam_tech'),
		'class' => 'sidebar third-sidebar',
		'before_widget' => '<div id="%1$s" class="widget sidebar-widget third-sidebar-widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h2 class="widget-title sidebar-title third-sidebar-title">',
		'after_title' => '</h2>'
	),
	array(
		'name' => __('Fourth Sidebar', 'nam_tech'),
		'id' => 'fourth-sidebar',
		'description' => __('Fourth Sidebar', 'nam_tech'),
		'class' => 'sidebar fourth-sidebar',
		'before_widget' => '<div id="%1$s" class="widget sidebar-widget fourth-sidebar-widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h2 class="widget-title sidebar-title fourth-sidebar-title">',
		'after_title' => '</h2>'
	),
	array(
		'name' => __('Fifth Sidebar', 'nam_tech'),
		'id' => 'fifth-sidebar',
		'description' => __('Fifth Sidebar', 'nam_tech'),
		'class' => 'sidebar fifth-sidebar',
		'before_widget' => '<div id="%1$s" class="widget sidebar-widget fifth-sidebar-widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h2 class="widget-title sidebar-title fifth-sidebar-title">',
		'after_title' => '</h2>'
	),
	array(
		'name' => __('Sixth Sidebar', 'nam_tech'),
		'id' => 'sixth-sidebar',
		'description' => __('Sixth Sidebar', 'nam_tech'),
		'class' => 'sidebar sixth-sidebar',
		'before_widget' => '<div id="%1$s" class="widget sidebar-widget sixth-sidebar-widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h2 class="widget-title sidebar-title sixth-sidebar-title">',
		'after_title' => '</h2>'
	),
);
if(!function_exists('addWidget')){
	function addWidget() {	
		global $default_sidebars;
		foreach( $default_sidebars as $sidebar ){
			register_sidebar($sidebar);
		}
	}
	add_action( 'widgets_init', 'addWidget' );
}
?>