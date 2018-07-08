(function( $ ) {
	$(document).ready(function() {
		$( '#itsec-ssl-admin' ).change(function( e ) {
			if ( this.checked && ! confirm( itsec_ssl.translations.ssl_warning ) ) {
				$(this).attr( 'checked', false );
			}
		} );
	});
})( jQuery );
