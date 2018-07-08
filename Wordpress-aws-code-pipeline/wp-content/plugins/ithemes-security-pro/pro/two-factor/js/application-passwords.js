(function( $, data ) {
	"use strict";

	var itsec = {
		init: function() {
			this.bindEvents();
			this.refreshView();
		},

		bindEvents: function() {
			var $container = $('#itsec-application-passwords-section');

			$container.on( 'click', '#itsec-application-password-add', this.showSettings );
			$container.on( 'click', '#itsec-application-password-create', this.create );
			$container.on( 'click', '#itsec-application-password-cancel', this.cancelCreation );
			$container.on( 'click', '.itsec-application-password-revoke', this.revoke );
			$container.on( 'click', '#itsec-application-passwords-revoke-all', this.revokeAll );

			$container.on( 'change', '#itsec-application-password-enabled-for-rest-api', this.togglePermissionsVisibility );

			$('#itsec-application-password-name').on( 'keypress', this.handleKeyPress );
			$('#itsec-application-password-enabled-for-rest-api').on( 'keypress', this.handleKeyPress );
			$('#itsec-application-password-enabled-for-xml-rpc').on( 'keypress', this.handleKeyPress );
			$('input[name="itsec-application-password-rest-api-permissions"]').on( 'keypress', this.handleKeyPress );
		},

		handleKeyPress: function( event ) {
			if ( 13 === event.keyCode ) {
				itsec.create( event );
			}
		},

		togglePermissionsVisibility: function( event ) {
			if ( $('#itsec-application-password-enabled-for-rest-api').is( ':checked' ) ) {
				$('#itsec-application-password-rest-api-permissions-section').show();
			} else {
				$('#itsec-application-password-rest-api-permissions-section').hide();
			}
		},

		showSettings: function( event ) {
			event.preventDefault();

			$('#itsec-application-password-feedback').html( '' );

			$('#itsec-application-password-add').hide();
			$('#itsec-application-password-settings').show();

			$('#itsec-application-password-name').focus();
		},

		cancelCreation: function( event ) {
			event.preventDefault();

			itsec.refreshView();
		},

		disableButtons: function() {
			$('#itsec-application-password-create').prop( 'disabled', true );
			$('#itsec-application-password-cancel').prop( 'disabled', true );
			$('.itsec-application-password-revoke').prop( 'disabled', true );
			$('#itsec-application-passwords-revoke-all').prop( 'disabled', true );
		},

		enableButtons: function() {
			$('#itsec-application-password-create').prop( 'disabled', false );
			$('#itsec-application-password-cancel').prop( 'disabled', false );
			$('.itsec-application-password-revoke').prop( 'disabled', false );
			$('#itsec-application-passwords-revoke-all').prop( 'disabled', false );
		},

		refreshView: function() {
			$('#itsec-application-password-settings').hide();
			$('#itsec-application-password-add').show();

			$('#itsec-application-password-feedback').html( '' );
			$('#itsec-application-password-name').attr( 'value', '' );
			$('#itsec-application-password-enabled-for-rest-api').prop( 'checked', true );
			$('#itsec-application-password-enabled-for-xml-rpc').prop( 'checked', true );
			$('#itsec-application-password-rest-api-permissions-write').prop( 'checked', true );

			itsec.enableButtons();
			itsec.togglePermissionsVisibility();

			if ( 0 === $('#itsec-application-passwords-list-table-wrapper tbody tr').length ) {
				$('#itsec-application-passwords-list-table-wrapper tbody').append( data.emptyRow );
				$('#itsec-application-passwords-revoke-all').hide();
			} else if ( 1 === $('#itsec-application-passwords-list-table-wrapper tbody tr.' + data.emptyRowClass).length ) {
				$('#itsec-application-passwords-revoke-all').hide();
			} else {
				$('#itsec-application-passwords-revoke-all').show();
			}
		},

		create: function( event ) {
			event.preventDefault();

			if ( $('#itsec-application-password-create').prop( 'disabled' ) ) {
				return;
			}

			itsec.disableButtons();

			var enabled_for = [];

			if ( $('#itsec-application-password-enabled-for-rest-api').is( ':checked' ) ) {
				enabled_for.push( 'rest-api' );
			}
			if ( $('#itsec-application-password-enabled-for-xml-rpc').is( ':checked' ) ) {
				enabled_for.push( 'xml-rpc' );
			}

			var postData = {
				action:               'itsec_application_password_create',
				_wpnonce:             data.nonce,
				user_id:              data.user_id,
				name:                 $('#itsec-application-password-name').val(),
				enabled_for:          enabled_for,
				rest_api_permissions: $('#itsec-application-password-settings input[name="itsec-application-password-rest-api-permissions"]:checked').val()
			};

			$.ajax( ajaxurl, {
				type: 'POST',
				data: postData,
				success: itsec.handleSuccessResponse,
				error: itsec.handleErrorResponse,
				timeout: 0
			});
		},

		revoke: function( event ) {
			event.preventDefault();

			itsec.disableButtons();

			var postData = {
				action:   'itsec_application_password_revoke',
				_wpnonce: data.nonce,
				user_id:  data.user_id,
				slug:     $(this).parents( 'tr' ).data( 'slug' )
			};

			$.ajax( ajaxurl, {
				type: 'POST',
				data: postData,
				success: itsec.handleSuccessResponse,
				error: itsec.handleErrorResponse,
				timeout: 0
			});
		},

		revokeAll: function( event ) {
			event.preventDefault();

			itsec.disableButtons();

			var postData = {
				action:   'itsec_application_password_revoke_all',
				_wpnonce: data.nonce,
				user_id:  data.user_id
			};

			$.ajax( ajaxurl, {
				type: 'POST',
				data: postData,
				success: itsec.handleSuccessResponse,
				error: itsec.handleErrorResponse,
				timeout: 0
			});
		},

		handleSuccessResponse: function( response, status, jqXHR ) {
			if ( 'undefined' === typeof response.success ) {
				itsec.showError( response.errorMessages.unknownResponse );
				itsec.enableButtons();
			} else if ( ! response.success ) {
				itsec.showError( response.data[0].message, response.data[0].code );
				itsec.enableButtons();
			} else {
				if ( 'undefined' !== typeof response.data.add_row ) {
					$('#itsec-application-passwords-list-table-wrapper tbody tr:last').after( response.data.add_row );
					$('#itsec-application-passwords-list-table-wrapper tbody tr.' + data.emptyRowClass).remove();
					itsec.refreshView();
				}

				if ( 'undefined' !== typeof response.data.remove_row ) {
					$('#itsec-application-passwords-list-table-wrapper tbody tr[data-slug="' + response.data.remove_row + '"]').remove();
					itsec.refreshView();
				}

				if ( response.data.remove_all ) {
					$('#itsec-application-passwords-list-table-wrapper tbody tr').remove();
					itsec.refreshView();
				}

				if ( 'undefined' !== typeof response.data.message ) {
					var message = '<div class="updated fade inline"><p><strong>' + response.data.message + '</strong></p></div>';

					$('#itsec-application-password-feedback').html( message );
				}
			}
		},

		handleErrorResponse: function( jqXHR, status, exception ) {
			var message = data.errorMessages.ajaxUnknown;

			if ( 'timeout' === status ) {
				message = data.errorMessages.ajaxTimeout;
			} else if ( 'parsererror' === status ) {
				message = data.errorMessages.parseError;
			}

			itsec.showError( message, 'Status: ' + status + ', Exception: ' + exception );
		},

		showError: function( message, code ) {
			if ( 'undefined' !== typeof code ) {
				message = data.errorFormat.replace( '%1$s', message ).replace( '%2$s', code );
			}

			message = '<div class="error inline"><p><strong>' + message + '</strong></p></div>';

			$('#itsec-application-password-feedback').html( message );
		}
	};

	$(document).ready(function() {
		itsec.init();
	});
})( jQuery, itsecApplicationPasswordsData );

