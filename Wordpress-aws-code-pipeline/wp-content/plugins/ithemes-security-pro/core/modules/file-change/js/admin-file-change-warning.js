jQuery( document ).ready( function ( $ ) {

	/**
	 * process the action button on the warning that a file change has been detected
	 */
	$( '#itsec_go_to_logs, #itsec_dismiss_file_change_warning' ).click( function ( event ) {

		event.preventDefault();

		var button = this.value;

		var data = {
			action: 'itsec_file_change_warning_ajax',
			nonce : itsec_file_change_warning.nonce
		};

		//call the ajax
		$.post( ajaxurl, data, function () {

			$( '#itsec_file_change_warning_dialog' ).remove();

			if ( button == 'View Logs' ) {
				window.location.replace( itsec_file_change_warning.url )
			}

		} );

	} );

} );



