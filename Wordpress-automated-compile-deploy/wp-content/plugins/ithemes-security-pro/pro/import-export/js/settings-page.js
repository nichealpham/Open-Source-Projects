"use strict";

(function( $ ) {
	var itsecImportExport = {
		init: function() {
			this.bindEvents();

			$( '#itsec-import-export-import-removed-selected-file' ).hide();

			itsecImportExport.filesList = new Array();

			var args = {
				url:        ajaxurl,
				autoUpload: false,
			};

			$('#itsec-import-export-settings_file').fileupload( args )
				.on( 'fileuploadadd', function( e, data ) {
					$.each( data.files, function( index, file ) {
						itsecImportExport.filesList[0] = data.files[index];
					} );

					$( '#itsec-import-export-import-select-file' ).hide();
					$( '#itsec-import-export-import-removed-selected-file' ).show();
				} );
		},

		bindEvents: function() {
			var $container = jQuery( '#wpcontent' );

			$container.on( 'click', '#itsec-import-export-export', this.doExport );
			$container.on( 'click', '#itsec-import-export-import', this.doImport );
			$container.on( 'click', '#itsec-import-export-import-removed-selected-file', this.removeSelectedFile );
		},

		doExport: function( e ) {
			e.preventDefault();

			itsecImportExport.originalExportButtonText = $(this).val();

			$(this)
				.prop( 'disabled', true )
				.val( itsecImportExportSettingsPage.text.exporting );

			$('.itsec-import-export-export-results-wrapper').html( '' );

			var data = {
				'method': 'export',
				'email':  $( '#itsec-import-export-email_address' ).val()
			};

			itsecSettingsPage.sendModuleAJAXRequest( 'import-export', data, itsecImportExport.handleExportResponse );
		},

		handleExportResponse: function( results ) {
			$('#itsec-import-export-export')
				.prop( 'disabled', false )
				.val( itsecImportExport.originalExportButtonText );

			if ( results.errors.length > 0 ) {
				var message;

				$('.itsec-import-export-export-results-wrapper').html( '' );

				$.each( results.errors, function( index, error ) {
					message = '<div class="error inline"><p><strong>' + error + '</strong></p></div>';
					$('.itsec-import-export-export-results-wrapper').append( message );
				} );
			} else {
				$('.itsec-import-export-export-results-wrapper').html( results.response );
			}
		},

		doImport: function( e ) {
			e.preventDefault();

			itsecImportExport.originalImportButtonText = $(this).val();

			$(this)
				.prop( 'disabled', true )
				.val( itsecImportExportSettingsPage.text.importing );

			$('.itsec-import-export-import-results-wrapper').html( '' );

			if ( itsecImportExport.filesList.length > 0 ) {
				var data = {
					files:     itsecImportExport.filesList,
					paramName: 'import_file',
					formData:  {
						action:         itsec_page.ajax_action,
						nonce:          itsec_page.ajax_nonce,
						module:         'import-export',
						method:         'handle_module_request',
						'data[method]': 'import'
					}
				};

				$('#itsec-import-export-settings_file').fileupload( 'send', data )
					.always(function( result, textStatus, jqXHR ) {
						itsecSettingsPage.processAjaxResponse( result, textStatus, jqXHR, 'import-export', 'handle_module_request', {method: 'import'}, itsecImportExport.handleImportResponse );
					});
			} else {
				var data = {
					'method': 'import'
				};

				itsecSettingsPage.sendModuleAJAXRequest( 'import-export', data, itsecImportExport.handleImportResponse );
			}
		},

		handleImportResponse: function( results ) {
			$('#itsec-import-export-import')
				.prop( 'disabled', false )
				.val( itsecImportExport.originalImportButtonText );

			if ( results.errors.length > 0 ) {
				var message;

				$.each( results.errors, function( index, error ) {
					message = '<div class="error inline"><p><strong>' + error + '</strong></p></div>';
					$('.itsec-import-export-import-results-wrapper').append( message );
				} );
			} else {
				$('.itsec-import-export-import-results-wrapper').html( results.response );
			}

			itsecImportExport.removeSelectedFile();
		},

		removeSelectedFile: function( e ) {
			if ( e && e.preventDefault ) {
				e.preventDefault();
			}

			itsecImportExport.filesList = new Array();

			$( '#itsec-import-export-import-select-file' ).show();
			$( '#itsec-import-export-import-removed-selected-file' ).hide();
		}
	};

	$(document).ready(function() {
		itsecImportExport.init();
	});
})( jQuery );
