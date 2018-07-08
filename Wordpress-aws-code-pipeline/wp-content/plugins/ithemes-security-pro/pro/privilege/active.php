<?php

// Set up Privilege Scheduling
require_once( 'class-itsec-privilege.php' );
$itsec_privilege = new ITSEC_Privilege();
$itsec_privilege->run();
