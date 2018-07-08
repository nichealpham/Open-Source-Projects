<?php

if ( is_admin() ) {
	require_once( 'class-itsec-ssl-admin.php' );
	$itsec_ssl_admin = new ITSEC_SSL_Admin();
	$itsec_ssl_admin->run( ITSEC_Core::get_instance() );
}

require_once( 'class-itsec-ssl.php' );
