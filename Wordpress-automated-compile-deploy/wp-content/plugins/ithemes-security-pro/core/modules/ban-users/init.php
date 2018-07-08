<?php

function itsec_ban_users_handle_new_blacklisted_ip( $ip ) {
	$host_list = ITSEC_Modules::get_setting( 'ban-users', 'host_list', array() );

	if ( ! is_array( $host_list ) ) {
		$host_list = array();
	}

	$host_list[] = $ip;

	ITSEC_Modules::set_setting( 'ban-users', 'host_list', $host_list );
}
add_action( 'itsec-new-blacklisted-ip', 'itsec_ban_users_handle_new_blacklisted_ip' );
