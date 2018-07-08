jQuery(document).ready( function() {
	jQuery( '.itsec-notice' ).on( 'click', '.itsec-notice-hide', function( e ) {
		e.preventDefault();

		var $container = jQuery(this).parents( '.itsec-notice' );

		jQuery.post( ajaxurl, {
			'action':       'itsec-dismiss-notice-' + jQuery(this).data( 'source' ),
			'notice_nonce': jQuery(this).data( 'nonce' )
		}, function( response ) {
			if ( response.success ) {
				$container.hide();
			}
		} );
	} );
} );
