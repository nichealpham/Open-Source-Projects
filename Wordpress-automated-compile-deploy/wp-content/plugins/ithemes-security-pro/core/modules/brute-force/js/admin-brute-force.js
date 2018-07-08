jQuery( document ).ready( function () {

	jQuery( "#itsec_brute_force_enabled" ).change(function () {

		if ( jQuery( "#itsec_brute_force_enabled" ).is( ':checked' ) ) {

			jQuery( "#brute_force-settings, .itsec_brute_force_lockout_information" ).show();

		} else {

			jQuery( "#brute_force-settings, .itsec_brute_force_lockout_information" ).hide();

		}

	} ).change();

} );