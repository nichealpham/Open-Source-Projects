"use strict";

(function( $ ) {
	var itsecMalware = {
		init: function() {
			this.bindEvents();
		},
		
		bindEvents: function() {
			$('#itsec-malware-scan').on('click', this.startScan);
			$('.itsec-malware-scan-results-wrapper').on('click', '.itsec-malware-scan-toggle-details', this.toggleDetails);
		},
		
		toggleDetails: function( event ) {
			event.preventDefault();
			
			var $container = $(this).parents('.itsec-malware-scan-results-section');
			var $details = $container.find('.itsec-malware-scan-details');
			
			if ( $details.is(':visible') ) {
				$(this).html('Show Details');
				$details.hide();
			} else {
				$(this).html('Hide Details');
				$details.show();
			}
		},
		
		startScan: function( event ) {
			event.preventDefault();
			
			itsecMalwareScanData.originalSubmitButtonText = $(this).val();
			
			$(this)
				.prop( 'disabled', true )
				.val( itsecMalwareScanData.clickedButtonText );
			
			var postData = {
				action: 'itsec_malware_scan',
				_wpnonce: itsecMalwareScanData.nonce
			};
			
			$.ajax( ajaxurl, {
				type: 'POST',
				data: postData,
				success: itsecMalware.handleSuccessResponse,
				error: itsecMalware.handleErrorResponse,
				timeout: 0
			});
		},
		
		handleSuccessResponse: function( data, status, jqXHR ) {
			$('#itsec-malware-scan').hide();
			
			if ( 'string' !== typeof data ) {
				itsecMalware.showError( itsecMalwareScanData.errorMessages.parseError );
			} else if ( '-1' === data ) {
				itsecMalware.showError( itsecMalwareScanData.errorMessages.nonceFailure );
			} else if ( '-2' === data ) {
				itsecMalware.showError( itsecMalwareScanData.errorMessages.invalidUser );
			} else {
				$('.itsec-malware-scan-results-wrapper').html( data );
			}
		},
		
		handleErrorResponse: function( jqXHR, status, exception ) {
			$('#itsec-malware-scan').hide();
			
			var message = itsecMalwareScanData.errorMessages.ajaxUnknown;
			
			if ( 'timeout' === status ) {
				message = itsecMalwareScanData.errorMessages.ajaxTimeout;
			} else if ( 'parsererror' === status ) {
				message = itsecMalwareScanData.errorMessages.parseError;
			}
			
			itsecMalware.showError( message, '(' + status + ') ' + exception )
		},
		
		showError: function( message, replacement ) {
			if ( 'string' === typeof replacement ) {
				message = message.replace( '%1$s', replacement );
			}
			
			message = '<div class="error inline"><p><strong>' + message + '</strong></p></div>';
			
			$('.itsec-malware-scan-results-wrapper').html( message );
		}
	};
	
	$(document).ready(function() {
		itsecMalware.init();
	});
})( jQuery );
