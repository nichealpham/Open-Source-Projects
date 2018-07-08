jQuery( document ).ready( function () {

	jQuery( '#itsec_reset_log_location' ).click( function ( event ) {

		event.preventDefault();

		jQuery( '#itsec_global_log_location' ).val( itsec_global_settings.location );

	} );

	jQuery( '.itsec_add_ip_to_whitelist' ).click( function ( event ) {

		event.preventDefault();

		jQuery( '#itsec_global_lockout_white_list' ).val( jQuery( '#itsec_global_lockout_white_list' ).val() + jQuery( '.itsec_add_ip_to_whitelist' ).attr( 'href' ) );

	} );

} );