jQuery( document ).ready( function () {

	jQuery( "#itsec_four_oh_four_enabled" ).change(function () {

		if ( jQuery( "#itsec_four_oh_four_enabled" ).is( ':checked' ) ) {

			jQuery( "#four_oh_four-settings" ).show();

		} else {

			jQuery( "#four_oh_four-settings" ).hide();

		}

	} ).change();

	if ( jQuery( 'p.noPermalinks' ).length ) {
		jQuery( "#four_oh_four-settings" ).hide();
	}

} );

