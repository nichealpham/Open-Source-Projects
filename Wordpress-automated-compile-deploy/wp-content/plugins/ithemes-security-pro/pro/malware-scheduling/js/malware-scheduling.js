"use strict";

(function( $ ) {
	var itsecMalwareScheduling = {
		init: function() {
			this.bindEvents();
			this.toggleSettings();
			this.toggleEmailContacts();
		},
		
		bindEvents: function() {
			$('#itsec_malware_scheduling_enabled').on('change', this.toggleSettings);
			$('#itsec_malware_scheduling_email_notifications').on('change', this.toggleEmailContacts);
		},
		
		toggleSettings: function( event ) {
			if ( $('#itsec_malware_scheduling_enabled').is( ':checked' ) ) {
				$('#malware_scheduling-settings').show();
			} else {
				$('#malware_scheduling-settings').hide();
			}
		},
		
		toggleEmailContacts: function( event ) {
			if ( $('#itsec_malware_scheduling_email_notifications').is( ':checked' ) ) {
				$('#malware_scheduling-email_contacts').show();
			} else {
				$('#malware_scheduling-email_contacts').hide();
			}
		}
	};
	
	$(document).ready(function() {
		itsecMalwareScheduling.init();
	});
})( jQuery );
