<?php

require_once( 'class-itsec-ipcheck.php' );
$itsec_ip_check = new ITSEC_IPCheck( ITSEC_Core::get_instance() );
$itsec_ip_check->run();
