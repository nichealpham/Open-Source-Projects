<?php

if ( is_admin() ) {
	require_once( 'class-itsec-dashboard-widget-admin.php' );
	$itsec_dashboard_widget_admin = new ITSEC_Dashboard_Widget_Admin();
	$itsec_dashboard_widget_admin->run();
}
