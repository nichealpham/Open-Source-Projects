<?php

// Set up Password Scheduling
require_once( 'class-itsec-password-expiration.php' );
$itsec_password_expiration = new ITSEC_Password_Expiration();
$itsec_password_expiration->run();
