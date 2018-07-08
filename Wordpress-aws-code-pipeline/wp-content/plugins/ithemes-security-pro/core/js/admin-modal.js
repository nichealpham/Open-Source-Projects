jQuery( document ).ready( function () {

	//setup the tooltip
	jQuery( '#itsec_intro_modal' ).dialog(
		{
			dialogClass  : 'wp-dialog itsec-setup-dialog',
			modal        : true,
			closeOnEscape: false,
			title        : itsec_tooltip_text.title,
			width        : '75%',
			resizable    : false,
			draggable    : false,
			close        : function ( event, ui ) {

				var data = {
					action: 'itsec_tooltip_ajax',
					module: 'close',
					nonce : itsec_tooltip_text.nonce
				};

				//call the ajax
				jQuery.post( ajaxurl, data, function () {

					var url = window.location.href;
					url = url.substring( 0, url.lastIndexOf( "&" ) );

					window.location.replace( url );

				} );

			}

		}
	);

	jQuery( '.ui-dialog a' ).blur();

	jQuery( '.itsec-intro-close' ).click( function ( event ) {
		jQuery( '#itsec_intro_modal' ).dialog( 'close' );
	} );

	//process tooltip actions
	jQuery( '.itsec_tooltip_ajax' ).click( function ( event ) {

		event.preventDefault();

		var module = jQuery( this ).attr( 'href' );
		var caller = this;

		var data = {
			action: 'itsec_tooltip_ajax',
			module: module,
			nonce : itsec_tooltip_text.nonce
		};

		//let user know we're working
		jQuery( caller ).removeClass( 'itsec_tooltip_ajax button-primary' ).addClass( 'button-secondary' ).html( 'Working...' );

		//call the ajax
		jQuery.post( ajaxurl, data, function ( response ) {

			if ( response == 'true' ) {

				jQuery( caller ).replaceWith( '<span class="itsec_tooltip_success">' + itsec_tooltip_text.messages[module].success + '</span>' );

			} else {

				jQuery( caller ).replaceWith( '<span class="itsec_tooltip_failure">' + itsec_tooltip_text.messages[module].failure + '</span>' );
			}

		} );

	} );

} );
