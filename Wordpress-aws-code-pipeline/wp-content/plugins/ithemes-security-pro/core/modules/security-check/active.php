<?php

function itsec_security_check_register_sync_verbs( $api ) {
	$api->register( 'itsec-do-security-check', 'Ithemes_Sync_Verb_ITSEC_Do_Security_Check', dirname( __FILE__ ) . '/sync-verbs/itsec-do-security-check.php' );
	$api->register( 'itsec-get-security-check-feedback-response', 'Ithemes_Sync_Verb_ITSEC_Get_Security_Check_Feedback_Response', dirname( __FILE__ ) . '/sync-verbs/itsec-get-security-check-feedback-response.php' );
	$api->register( 'itsec-get-security-check-modules', 'Ithemes_Sync_Verb_ITSEC_Get_Security_Check_Modules', dirname( __FILE__ ) . '/sync-verbs/itsec-get-security-check-modules.php' );
}
add_action( 'ithemes_sync_register_verbs', 'itsec_security_check_register_sync_verbs' );
