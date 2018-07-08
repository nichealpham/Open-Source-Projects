jQuery( document ).ready( function () {

	jQuery( "#itsec_enable_admin_user" ).change(function () {

		if ( jQuery( "#itsec_enable_admin_user" ).is( ':checked' ) ) {
			jQuery( "#admin_user_username_field, #admin_user_id_field" ).show();

		} else {
			jQuery( "#admin_user_username_field, #admin_user_id_field" ).hide();

		}

	} ).change();

} );