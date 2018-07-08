jQuery( document ).ready( function () {
	var $container = jQuery( '#wpcontent' );
	
	
	$container.on( 'click', '#itsec-backup-reset_backup_location', function( e ) {
		e.preventDefault();
		
		jQuery( '#itsec-backup-location' ).val( itsec_backup.default_backup_location );
	} );
	
	$container.on( 'change', '#itsec-backup-method', function( e ) {
		var method = jQuery(this).val();
		
		if ( 1 == method ) {
			jQuery( '.itsec-backup-method-file-content' ).hide();
		} else {
			jQuery( '.itsec-backup-method-file-content' ).show();
		}
	} );
	
	jQuery( '#itsec-backup-method' ).trigger( 'change' );
	
	
	jQuery( '#itsec-backup-exclude' ).multiSelect( {
		selectableHeader: '<div class="custom-header">' + itsec_backup.available_tables_label + '</div>',
		selectionHeader:  '<div class="custom-header">' + itsec_backup.excluded_tables_label + '</div>',
		keepOrder:        true
	} );
	
	
	jQuery( '#itsec-backup-create_backup' ).click(function( e ) {
		e.preventDefault();
		
		var originalButtonLabel = jQuery( '#itsec-backup-create_backup' ).attr( 'value' );
		
		jQuery( '#itsec-backup-create_backup' )
			.removeClass( 'button-primary' )
			.addClass( 'button-secondary' )
			.attr( 'value', itsec_backup.creating_backup_text )
			.prop( 'disabled', true );
		
		jQuery( '#itsec_backup_status' ).html( '' );
		
		var data = {
			'method': 'create-backup'
		};
		
		itsecSettingsPage.sendModuleAJAXRequest( 'backup', data, function( results ) {
			jQuery( '#itsec_backup_status' ).html( results.response );
			
			jQuery( '#itsec-backup-create_backup' )
				.removeClass( 'button-secondary' )
				.addClass( 'button-primary' )
				.attr( 'value', originalButtonLabel )
				.prop( 'disabled', false );
		} );
	});
} );
