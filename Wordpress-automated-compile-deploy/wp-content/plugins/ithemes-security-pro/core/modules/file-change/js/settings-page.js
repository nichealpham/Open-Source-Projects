jQuery( document ).ready( function ( $ ) {
	/**
	 * Show the file tree in the settings.
	 */
	$( '.jquery_file_tree' ).fileTree(
		{
			root         : itsec_file_change_settings.ABSPATH,
			script       : ajaxurl,
			expandSpeed  : -1,
			collapseSpeed: -1,
			multiFolder  : false

		}, function ( file ) {

			$( '#itsec-file-change-file_list' ).val( file.substring( itsec_file_change_settings.ABSPATH.length ) + "\n" + $( '#itsec-file-change-file_list' ).val() );

		}, function ( directory ) {

			$( '#itsec-file-change-file_list' ).val( directory.substring( itsec_file_change_settings.ABSPATH.length ) + "\n" + $( '#itsec-file-change-file_list' ).val() );

		}
	);

	/**
	 * Performs a one-time file scan
	 */
	$( '#itsec-file-change-one_time_check' ).click(function( e ) {
		e.preventDefault();
		
		//let user know we're working
		$( '#itsec-file-change-one_time_check' )
			.removeClass( 'button-primary' )
			.addClass( 'button-secondary' )
			.attr( 'value', itsec_file_change_settings.scanning_button_text )
			.prop( 'disabled', true );
		
		var data = {
			'method': 'one-time-scan'
		};
		
		$( '#itsec_file_change_status' ).html( '' );
		
		itsecSettingsPage.sendModuleAJAXRequest( 'file-change', data, function( results ) {
			$( '#itsec_file_change_status' ).html( '' );
			
			if ( false === results.response ) {
				$( '#itsec_file_change_status' ).append( '<div class="updated fade inline"><p><strong>' + itsec_file_change_settings.no_changes + '</strong></p></div>' );
			} else if ( true === results.response ) {
				$( '#itsec_file_change_status' ).append( '<div class="error inline"><p><strong>' + itsec_file_change_settings.found_changes + '</strong></p></div>' );
			} else if ( -1 === results.response ) {
				$( '#itsec_file_change_status' ).append( '<div class="error inline"><p><strong>' + itsec_file_change_settings.already_running + '</strong></p></div>' );
			} else if ( results.errors && results.errors.length > 0 ) {
				$.each( results.errors, function( index, error ) {
					$( '#itsec_file_change_status' ).append( '<div class="error inline"><p><strong>' + error + '</strong></p></div>' );
				} );
			} else {
				$( '#itsec_file_change_status' ).append( '<div class="error inline"><p><strong>' + itsec_file_change_settings.unknown_error + '</strong></p></div>' );
			}
			
			$( '#itsec-file-change-one_time_check' )
				.removeClass( 'button-secondary' )
				.addClass( 'button-primary' )
				.attr( 'value', itsec_file_change_settings.button_text )
				.prop( 'disabled', false );
		} );
	});

} );

jQuery( window ).load( function () {

	/**
	 * Shows and hides the red selector icon on the file tree allowing users to select an
	 * individual element.
	 */
	jQuery( document ).on( 'mouseover mouseout', '.jqueryFileTree > li a', function ( event ) {

		if ( event.type == 'mouseover' ) {

			jQuery( this ).children( '.itsec_treeselect_control' ).css( 'visibility', 'visible' );

		} else {

			jQuery( this ).children( '.itsec_treeselect_control' ).css( 'visibility', 'hidden' );

		}

	} );

} );
