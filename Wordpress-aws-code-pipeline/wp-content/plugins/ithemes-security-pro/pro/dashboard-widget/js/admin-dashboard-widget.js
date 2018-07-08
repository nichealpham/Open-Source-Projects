jQuery( document ).ready( function () {

	var direction = null;
	var toggle_id = null;

	var toggle_dashboard_postbox = function ( toggle_id, direction ) {

		var data = {
			action   : 'itsec_dashboard_summary_postbox_toggle',
			id       : toggle_id,
			direction: direction,
			nonce    : itsec_dashboard_widget_js.postbox_nonce
		};

		//call the ajax
		jQuery.post( ajaxurl, data );

	}

	//make widget toggles persistent
	jQuery( '#itsec_lockout_host_postbox div.handlediv' ).bind( 'click', function ( event ) {

		toggle_id = 'itsec_lockout_host_postbox';
		direction = 'close';

		if ( jQuery( '#itsec_lockout_host_postbox' ).hasClass( 'closed' ) ) {
			direction = 'open';
		}

		toggle_dashboard_postbox( toggle_id, direction );

	} );

	jQuery( '#itsec_lockout_user_postbox div.handlediv' ).bind( 'click', function ( event ) {

		toggle_id = 'itsec_lockout_user_postbox';
		direction = 'close';

		if ( jQuery( '#itsec_lockout_user_postbox' ).hasClass( 'closed' ) ) {
			direction = 'open';
		}

		toggle_dashboard_postbox( toggle_id, direction );

	} );

	jQuery( '#itsec_lockout_summary_postbox div.handlediv' ).bind( 'click', function ( event ) {

		toggle_id = 'itsec_lockout_summary_postbox';
		direction = 'close';

		if ( jQuery( '#itsec_lockout_summary_postbox' ).hasClass( 'closed' ) ) {
			direction = 'open';
		}

		toggle_dashboard_postbox( toggle_id, direction );

	} );

	//Add full width class to malware or file-change for proper formatting (where needed)
	var has_malware = false;
	var has_file_scan = false;

	if ( jQuery( '.itsec_malware_widget' ).length ) {
		has_malware = true;
	}

	if ( jQuery( '.itsec_file-change_widget' ).length ) {
		has_file_scan = true;
	}

	if ( has_malware == true && has_file_scan != true ) {
		jQuery( '.itsec_malware_widget' ).addClass( 'full-width' );
	}

	if ( has_malware != true && has_file_scan == true ) {
		jQuery( '.itsec_file-change_widget' ).addClass( 'full-width' );
	}
	
	jQuery( '#itsec-dashboard-widget' ).on( 'click', '#itsec_dashboard_one_time_file_check', function( e ) {
		e.preventDefault();
		
		var $link = jQuery( this );
		var original_name = $link.attr( 'value' );
		
		$link
			.removeClass( 'button-primary' )
			.addClass( 'button-secondary' )
			.attr( 'value', itsec_dashboard_widget_js.scanning )
			.prop( 'disabled', true );
		
		jQuery( '#itsec_dashboard_one_time_file_check_results' ).html( '' );
		
		var data = {
			action: 'itsec_dashboard_file_check',
			nonce:  itsec_dashboard_widget_js.scan_nonce
		};
		
		jQuery.post( ajaxurl, data, function( response ) {
			jQuery( '#itsec_dashboard_one_time_file_check_results' ).html( response );
			
			$link
				.removeClass( 'button-secondary' )
				.addClass( 'button-primary' )
				.attr( 'value', original_name )
				.prop( 'disabled', false );
		} );
	} );

	//process clear lockouts
	jQuery( '.itsec_release_lockout' ).bind( 'click', function ( event ) {

		event.preventDefault();

		var caller = this;

		if ( jQuery( caller ).hasClass( 'locked_host' ) ) {
			var lock_type = 'host';
		}

		if ( jQuery( caller ).hasClass( 'locked_user' ) ) {
			var lock_type = 'user';
		}

		var data = {
			action  : 'itsec_release_dashboard_lockout',
			nonce   : jQuery( caller ).attr( 'href' ),
			type    : lock_type,
			resource: jQuery( caller ).attr( 'id' )
		};

		//call the ajax
		jQuery.post( ajaxurl, data, function ( response ) {

			if ( response == 1 ) {

				var item = jQuery( caller ).closest( 'li' );

				var list = jQuery( item ).closest( 'ul' )

				jQuery( item ).remove();

				var list_length = jQuery( list ).children( 'li' ).length

				if ( list_length == 0 ) {

					if ( lock_type == 'user' ) {

						jQuery( list ).replaceWith( itsec_dashboard_widget_js.user );

					} else {

						jQuery( list ).replaceWith( itsec_dashboard_widget_js.host );

					}

				}

				var current_total = parseInt( jQuery( '#current-itsec-lockout-summary-total' ).html() );

				jQuery( '#current-itsec-lockout-summary-total' ).html( current_total - 1 );

			}

		} );

	} );

} );
