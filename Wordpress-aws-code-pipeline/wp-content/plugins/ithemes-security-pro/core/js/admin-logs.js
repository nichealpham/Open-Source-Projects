jQuery( document ).ready( function () {

	var $_GET = {};

	document.location.search.replace( /\??(?:([^=]+)=([^&]*)&?)/g, function () {
		function decode( s ) {
			return decodeURIComponent( s.split( "+" ).join( " " ) );
		}

		$_GET[decode( arguments[1] )] = decode( arguments[2] );
	} );

	var uri = URI( window.location.href )

	jQuery( '#itsec_log_filter' ).on( 'change', function () {

		uri.removeSearch( 'itsec_log_filter' ).removeSearch( 'orderby' ).removeSearch( 'order' ).removeSearch( 'paged' ).addSearch( { itsec_log_filter: [this.value] } );
		window.location.replace( uri );

	} );

} );