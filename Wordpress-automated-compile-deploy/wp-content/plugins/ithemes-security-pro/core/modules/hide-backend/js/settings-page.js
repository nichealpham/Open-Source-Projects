function itsec_hide_backend_update_logout_url( args ) {
	var url = jQuery( '#wp-admin-bar-logout a' ).attr( 'href' );
	url = url.replace( args[0], args[1] );
	jQuery( '#wp-admin-bar-logout a' ).attr( 'href', url );
}
