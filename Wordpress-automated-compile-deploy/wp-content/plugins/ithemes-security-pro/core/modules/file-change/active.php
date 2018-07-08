<?php

require_once( dirname( __FILE__ ) . '/class-itsec-file-change.php' );
$itsec_file_change = new ITSEC_File_Change();
$itsec_file_change->run();

if ( is_admin() ) {
	require_once( dirname( __FILE__ ) . '/admin.php' );
}
