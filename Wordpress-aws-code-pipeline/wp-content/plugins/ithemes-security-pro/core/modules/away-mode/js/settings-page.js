(function( $ ) {
	var ithemesSecurityAwayModeSettingsPage = {
		init: function() {
			$( '#itsec-away-mode-start_date, #itsec-away-mode-end_date' ).datepicker({
				dateFormat: 'yy-mm-dd'
			});
			
			$( '#itsec-away-mode-type' ).change( ithemesSecurityAwayModeSettingsPage.typeChanged );
		},
		typeChanged: function() {
			if ( 'daily' === $( '#itsec-away-mode-type' ).val() ) {
				$( '#itsec-away-mode-start_date, #itsec-away-mode-end_date' ).closest( 'tr' ).hide();
			} else {
				$( '#itsec-away-mode-start_date, #itsec-away-mode-end_date' ).closest( 'tr' ).show();
			}
		}
	};
	
	$(document).ready(function() {
		ithemesSecurityAwayModeSettingsPage.init();
		ithemesSecurityAwayModeSettingsPage.typeChanged();
	});
})( jQuery );
