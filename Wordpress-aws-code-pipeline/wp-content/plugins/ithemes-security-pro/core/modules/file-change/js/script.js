jQuery( document ).ready(function( $ ) {
	$( '#itsec-file-change-dismiss-warning' ).click(function( e ) {
		e.preventDefault();
		
		$( '#itsec-file-change-warning-dialog' ).hide();
		
		var data = {
			'action': itsec_file_change.ajax_action,
			'nonce':  itsec_file_change.ajax_nonce,
		};
		
		jQuery.post( ajaxurl, data );
	});
});
