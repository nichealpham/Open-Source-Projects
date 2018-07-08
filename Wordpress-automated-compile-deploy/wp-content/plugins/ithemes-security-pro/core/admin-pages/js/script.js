"use strict";

var itsecSettingsPage = {
	init: function() {
		jQuery( '.itsec-module-settings-container' ).hide();

		this.bindEvents();

		jQuery( '.itsec-settings-view-toggle .itsec-selected' ).removeClass( 'itsec-selected' ).trigger( 'click' );
		jQuery( '.itsec-settings-toggle' ).trigger( 'change' );

		jQuery(window).on("popstate", function(e, data) {
			if ( null !== e.originalEvent.state && 'string' == typeof e.originalEvent.state.module && '' !== e.originalEvent.state.module.replace( /[^\w-]/g, '' ) ) {
				jQuery( '#itsec-module-card-' + e.originalEvent.state.module.replace( /[^\w-]/g, '' ) + ' button.itsec-toggle-settings' ).trigger( 'itsec-popstate' );
			} else {
				itsecSettingsPage.closeGridSettingsModal( e );
			}

			if ( null !== e.originalEvent.state && 'string' == typeof e.originalEvent.state.module_type && '' !== e.originalEvent.state.module_type.replace( /[^\w-]/g, '' ) ) {
				jQuery( '#itsec-module-filter-' + e.originalEvent.state.module_type.replace( /[^\w-]/g, '' ) + ' a' ).trigger( 'itsec-popstate' );
			}
		});

		var module_type = this.getUrlParameter( 'module_type' );
		if ( false === module_type || 0 === jQuery( '#itsec-module-filter-' + module_type.replace( /[^\w-]/g, '' ) ).length ) {
			module_type = 'recommended';
		}
		jQuery( '#itsec-module-filter-' + module_type.replace( /[^\w-]/g, '' ) + ' a' ).trigger( 'click' );

		var module = this.getUrlParameter( 'module' );
		if ( 'string' === typeof module ) {
			jQuery( '#itsec-module-card-' + module.replace( /[^\w-]/g, '' ) + ' button.itsec-toggle-settings' ).trigger( 'click' );
		}
	},

	bindEvents: function() {
		var $container = jQuery( '#wpcontent' );

		$container.on( 'click', '.itsec-module-filter a', this.filterView );
		$container.on( 'itsec-popstate', '.itsec-module-filter a', this.filterView );
		$container.on( 'click', '.itsec-settings-view-toggle a', this.toggleView );
//		$container.on( 'click', '.itsec-toggle-settings, .itsec-module-card-content h2', this.toggleSettings );
		$container.on( 'click', '.list .itsec-module-card:not(.itsec-module-pro-upsell) .itsec-module-card-content, .itsec-toggle-settings, .itsec-module-settings-cancel', this.toggleSettings );
		$container.on( 'itsec-popstate', '.list .itsec-module-card-content, .itsec-toggle-settings', this.toggleSettings );
		$container.on( 'click', '.itsec-close-modal, .itsec-modal-background', this.closeGridSettingsModal );
		$container.on( 'keyup', this.closeGridSettingsModal );
		$container.on( 'click', '.itsec-toggle-activation', this.toggleModuleActivation );
		$container.on( 'click', '.itsec-module-settings-save', this.saveSettings );
		$container.on( 'click', '.itsec-reload-module', this.reloadModule );

		$container.on( 'change', '#itsec-filter', this.logPageChangeFilter );

		// For use by module content to show/hide settings sections based upon an input.
		$container.on( 'change', '.itsec-settings-toggle', this.toggleModuleContent );
	},

	logPageChangeFilter: function( e ) {
		var filter = jQuery( this ).val();
		var url = itsec_page.logs_page_url + '&filter=' + filter;
		window.location.href = url;
	},

	toggleModuleContent: function( e ) {
		if ( 'checkbox' === jQuery(this).attr( 'type' ) ) {
			var show = jQuery(this).prop( 'checked' );
		} else {
			var show = ( jQuery(this).val() ) ? true : false;
		}

		var $content = jQuery( '.' + jQuery(this).attr( 'id' ) + '-content' );

		if ( show ) {
			$content.show();


			var $container = jQuery( '.itsec-module-cards-container' );

			if ( $container.hasClass( 'grid' ) ) {
				var $modal = jQuery(this).parents( '.itsec-module-settings-content-container' );
				var scrollOffset = $modal.scrollTop() + jQuery(this).parent().position().top;

				$modal.animate( {'scrollTop': scrollOffset}, 'slow' );
			}
		} else {
			$content.hide();
		}
	},

	saveSettings: function( e ) {
		e.preventDefault();

		var $button = jQuery(this);

		if ( $button.hasClass( 'itsec-module-settings-save' ) ) {
			var module = $button.parents( '.itsec-module-card' ).attr( 'id' ).replace( 'itsec-module-card-', '' );
		} else {
			var module = '';
		}

		$button.prop( 'disabled', true );

		var data = {
			'--itsec-form-serialized-data': jQuery( '#itsec-module-settings-form' ).serialize()
		};

		itsecSettingsPage.sendAJAXRequest( module, 'save', data, itsecSettingsPage.saveSettingsCallback );
	},

	saveSettingsCallback: function( results ) {
		if ( '' === results.module ) {
			jQuery( '#itsec-save' ).prop( 'disabled', false );
		} else {
			jQuery( '#itsec-module-card-' + results.module + ' button.itsec-module-settings-save' ).prop( 'disabled', false );
		}

		var $container = jQuery( '.itsec-module-cards-container' );

		if ( $container.hasClass( 'grid' ) ) {
			var view = 'grid';
		} else {
			var view = 'list';
		}

		itsecSettingsPage.clearMessages();

		if ( results.errors.length > 0 || ! results.closeModal ) {
			itsecSettingsPage.showErrors( results.errors, results.module, 'open' );
			itsecSettingsPage.showMessages( results.messages, results.module, 'open' );

			if ( 'grid' === view ) {
				$container.find( '.itsec-module-settings-content-container:visible' ).animate( {'scrollTop': 0}, 'fast' );
			}

			if ( 'list' === view ) {
				jQuery(document).scrollTop( 0 );
			}
		} else {
			itsecSettingsPage.showMessages( results.messages, results.module, 'closed' );

			if ( 'grid' === view ) {
				$container.find( '.itsec-module-settings-content-container:visible' ).scrollTop( 0 );
				itsecSettingsPage.closeGridSettingsModal();
			}
		}
	},

	clearMessages: function() {
		jQuery( '#itsec-settings-messages-container, .itsec-module-messages-container' ).empty();
	},

	showErrors: function( errors, module, containerStatus ) {
		jQuery.each( errors, function( index, error ) {
			itsecSettingsPage.showError( error, module, containerStatus );
		} );
	},

	showError: function( error, module, containerStatus ) {
		if ( jQuery( '.itsec-module-cards-container' ).hasClass( 'grid' ) ) {
			var view = 'grid';
		} else {
			var view = 'list';
		}

		if ( 'closed' !== containerStatus && 'open' !== containerStatus ) {
			containerStatus = 'closed';
		}

		if ( 'string' !== typeof module ) {
			module = '';
		}


		if ( 'closed' === containerStatus || '' === module ) {
			var container = jQuery( '#itsec-settings-messages-container' );

			if ( '' === module ) {
				container.addClass( 'no-module' );
			}
		} else {
			var container = jQuery( '#itsec-module-card-' + module + ' .itsec-module-messages-container' );
		}

		container.append( '<div class="error"><p><strong>' + error + '</strong></p></div>' ).addClass( 'visible' );
	},

	showMessages: function( messages, module, containerStatus ) {
		jQuery.each( messages, function( index, message ) {
			itsecSettingsPage.showMessage( message, module, containerStatus );
		} );
	},

	showMessage: function( message, module, containerStatus ) {
		if ( jQuery( '.itsec-module-cards-container' ).hasClass( 'grid' ) ) {
			var view = 'grid';
		} else {
			var view = 'list';
		}

		if ( 'closed' !== containerStatus && 'open' !== containerStatus ) {
			containerStatus = 'closed';
		}

		if ( 'string' !== typeof module ) {
			module = '';
		}


		if ( 'closed' === containerStatus || '' === module ) {
			var container = jQuery( '#itsec-settings-messages-container' );

			setTimeout( function() {
				container.removeClass( 'visible' );
				setTimeout( function() {
					container.find( 'div' ).remove();
				}, 500 );
			}, 4000 );
		} else {
			var container = jQuery( '#itsec-module-card-' + module + ' .itsec-module-messages-container' );
		}

		container.append( '<div class="updated fade"><p><strong>' + message + '</strong></p></div>' ).addClass( 'visible' );
	},

	filterView: function( e ) {
		e.preventDefault();

		var $activeLink = jQuery(this),
			$oldLink = $activeLink.parents( '.itsec-feature-tabs' ).find( '.current' ),
			type = $activeLink.parent().attr( 'id' ).substr( 20 );

		$oldLink.removeClass( 'current' );
		$activeLink.addClass( 'current' );

		if ( 'all' === type ) {
			jQuery( '.itsec-module-card' ).show();
		} else {
			jQuery( '.itsec-module-type-' + type ).show();
			jQuery( '.itsec-module-card' ).not( '.itsec-module-type-' + type ).hide();
		}

		// We use this to avoid pushing a new state when we're trying to handle a popstate
		if ( 'itsec-popstate' !== e.type ) {
			var url = '?page=itsec&module_type=' + type;
			var module = itsecSettingsPage.getUrlParameter( 'module' );
			if ( 'string' === typeof module ) {
				url += '&module=' + module;
			}

			window.history.pushState( {'module_type':type}, type, url );
		}
	},

	toggleView: function( e ) {
		e.preventDefault();

		var $self = jQuery(this);

		if ( $self.hasClass( 'itsec-selected' ) ) {
			// Do nothing if already selected.
			return;
		}

		var $view = $self.attr( 'class' ).replace( 'itsec-', '' );

		$self.addClass( 'itsec-selected' ).siblings().removeClass( 'itsec-selected' );
		jQuery( '.itsec-module-settings-container' ).hide();

		jQuery( '.itsec-toggle-settings' ).each(function( index ) {
			var $button = jQuery( this );

			if ( $button.parents( '.itsec-module-card' ).hasClass( 'itsec-module-type-enabled' ) ) {
				$button.html( itsec_page.translations.show_settings );
			} else if ( $button.hasClass( 'information-only' ) ) {
				$button.html( itsec_page.translations.information_only );
			} else {
				$button.html( itsec_page.translations.show_description );
			}
		});

		var $cardContainer = jQuery( '.itsec-module-cards-container' );
		jQuery.post( ajaxurl, {
			'action':                   'itsec-set-user-setting',
			'itsec-user-setting-nonce': $self.parent().data( 'nonce' ),
			'setting':                  'itsec-settings-view',
			'value':                    $view
		} );

		$cardContainer.fadeOut( 100, function() {
			$cardContainer.removeClass( 'grid list' ).addClass( $view );
		} );
		$cardContainer.fadeIn( 100 );
	},

	toggleSettings: function( e ) {
		e.stopPropagation();

		var $listClassElement = jQuery(e.currentTarget).parents( '.itsec-module-cards-container' );

		if ( $listClassElement.hasClass( 'list') ) {
			itsecSettingsPage.toggleListSettingsCard.call( this, e );
		} else if ( $listClassElement.hasClass( 'grid' ) ) {
			itsecSettingsPage.showGridSettingsModal.call( this, e );
		}

		// We use this to avoid pushing a new state when we're trying to handle a popstate
		if ( 'itsec-popstate' !== e.type ) {
			var module_id = jQuery(this).closest('.itsec-module-card').data( 'module-id' );

			var module_type = itsecSettingsPage.getUrlParameter( 'module_type' );
			if ( false === module_type || 0 === jQuery( '#itsec-module-filter-' + module_type.replace( /[^\w-]/g, '' ) ).length ) {
				module_type = 'recommended';
			}
			window.history.pushState( {'module':module_id}, module_id, '?page=itsec&module=' + module_id + '&module_type=' + module_type );
		}
	},

	toggleListSettingsCard: function( e ) {
		e.preventDefault();

		var $container = jQuery(this);

		if ( ! $container.hasClass( 'itsec-module-card-content' ) ) {
			$container = $container.parents( '.itsec-module-card' ).find( '.itsec-module-card-content' );
		}

		$container.siblings( '.itsec-module-settings-container' ).stop().slideToggle( 300 );

		var $button = $container.find( '.itsec-toggle-settings' );

		if ( $container.parent().hasClass( 'itsec-module-type-enabled' ) ) {
			if ( $button.html() == itsec_page.translations.show_settings ) {
				$button.html( itsec_page.translations.hide_settings );
			} else {
				$button.html( itsec_page.translations.show_settings );
			}
		} else {
			if ( $button.hasClass( 'information-only' ) ) {
				if ( $button.html() == itsec_page.translations.show_information ) {
					$button.html( itsec_page.translations.hide_description );
				} else {
					$button.html( itsec_page.translations.show_information );
				}
			} else {
				if ( $button.html() == itsec_page.translations.show_description ) {
					$button.html( itsec_page.translations.hide_description );
				} else {
					$button.html( itsec_page.translations.show_description );
				}
			}
		}
	},

	showGridSettingsModal: function( e ) {
		e.preventDefault();

		var $settingsContainer = jQuery(this).parents( '.itsec-module-card' ).find( '.itsec-module-settings-container' ),
			$modalBackground = jQuery( '.itsec-modal-background' );

		$modalBackground.show();
		$settingsContainer
			.show()
			.find( '.itsec-close' )
			.focus();

		jQuery( 'body' ).addClass( 'itsec-modal-open' );


/*		if ( jQuery(e.currentTarget).hasClass( 'page-title-action' ) ) {
			$modal.first().find( '.hidden' ).removeClass( 'hidden' );
		} else {
			$modal.first().find( '.itsec-right, .itsec-left' ).addClass( 'hidden' );
		}*/
	},

	closeGridSettingsModal: function( e ) {
		if ( 'undefined' !== typeof e ) {
			e.preventDefault();

			// For keyup events, only process esc
			if ( 'keyup' === e.type && 27 !== e.which ) {
				return;
			}
		}

		jQuery( '.itsec-modal-background' ).hide();
		jQuery( '.itsec-module-settings-container' ).hide();
		jQuery( 'body' ).removeClass( 'itsec-modal-open' );

		if ( 'undefined' === typeof e || 'popstate' !== e.type ) {
			var module_type = itsecSettingsPage.getUrlParameter( 'module_type' );
			if ( false === module_type || 0 === jQuery( '#itsec-module-filter-' + module_type.replace( /[^\w-]/g, '' ) ).length ) {
				module_type = 'recommended';
			}
			window.history.pushState( {'module':'', 'module_type':module_type}, module_type, '?page=itsec&module_type=' + module_type );
		}
	},

	toggleModuleActivation: function( e ) {
		e.preventDefault();
		e.stopPropagation();

		var $button = jQuery(this),
			$card = $button.parents( '.itsec-module-card' ),
			$buttons = $card.find( '.itsec-toggle-activation' ),
			module = $card.attr( 'id' ).replace( 'itsec-module-card-', '' );

		$buttons.prop( 'disabled', true );

		if ( $button.html() == itsec_page.translations.activate ) {
			var method = 'activate';
		} else {
			var method = 'deactivate';
		}

		itsecSettingsPage.sendAJAXRequest( module, method, {}, itsecSettingsPage.toggleModuleActivationCallback );
	},

	setModuleToActive: function( module ) {
		var args = {
			'module': module,
			'method': 'activate',
			'errors': []
		};

		itsecSettingsPage.toggleModuleActivationCallback( args );
	},

	setModuleToInactive: function( module ) {
		var args = {
			'module': module,
			'method': 'deactivate',
			'errors': []
		};

		itsecSettingsPage.toggleModuleActivationCallback( args );
	},

	toggleModuleActivationCallback: function( results ) {
		var module = results.module;
		var method = results.method;

		var $card = jQuery( '#itsec-module-card-' + module ),
			$buttons = $card.find( '.itsec-toggle-activation' )

		if ( results.errors.length > 0 ) {
			$buttons
				.html( itsec_page.translations.error )
				.addClass( 'button-secondary' )
				.removeClass( 'button-primary' );

			setTimeout( function() {
				itsecSettingsPage.isModuleActive( module );
			}, 1000 );

			return;
		}

		if ( 'activate' === method ) {
			$buttons
				.html( itsec_page.translations.deactivate )
				.addClass( 'button-secondary' )
				.removeClass( 'button-primary' )
				.prop( 'disabled', false );

			$card
				.addClass( 'itsec-module-type-enabled' )
				.removeClass( 'itsec-module-type-disabled' );

			var newToggleSettingsLabel = itsec_page.translations.show_settings;
		} else {
			$buttons
				.html( itsec_page.translations.activate )
				.addClass( 'button-primary' )
				.removeClass( 'button-secondary' )
				.prop( 'disabled', false );

			$card
				.addClass( 'itsec-module-type-disabled' )
				.removeClass( 'itsec-module-type-enabled' );

			var newToggleSettingsLabel = itsec_page.translations.show_description;
		}

		$card.find( '.itsec-toggle-settings' ).html( newToggleSettingsLabel );

		var enabledCount = jQuery( '.itsec-module-type-enabled' ).length,
			disabledCount = jQuery( '.itsec-module-type-disabled' ).length;

		jQuery( '#itsec-module-filter-enabled .count' ).html( '(' + enabledCount + ')' );
		jQuery( '#itsec-module-filter-disabled .count' ).html( '(' + disabledCount + ')' );
	},

	isModuleActive: function( module ) {
		var data = {
			'module': module,
			'method': 'is_active'
		};

		itsecSettingsPage.sendAJAXRequest( module, 'is_active', {}, itsecSettingsPage.isModuleActiveCallback );
	},

	isModuleActiveCallback: function( results ) {
		if ( true === results.response ) {
			results.method = 'activate';
		} else if ( false === results.response ) {
			results.method = 'deactivate';
		} else {
			return;
		}

		itsecSettingsPage.toggleModuleActivationCallback( results );
	},

	reloadModule: function( module ) {
		if ( module.preventDefault ) {
			module.preventDefault();

			module = jQuery(this).parents( '.itsec-module-card' ).attr( 'id' ).replace( 'itsec-module-card-', '' );
		}

		var method = 'get_refreshed_module_settings';
		var data = {};

		itsecSettingsPage.sendAJAXRequest( module, method, data, function( results ) {
			if ( results.success && results.response ) {
				jQuery( '#itsec-module-card-' + module + ' .itsec-module-settings-content-main' ).html( results.response );
				jQuery( '.itsec-settings-toggle' ).trigger( 'change' );
			} else if ( results.errors && results.errors.length > 0 ) {
				itsecSettingsPage.showErrors( results.errors, results.module, 'open' );
			}
		} );
	},

	reloadWidget: function( widget ) {
		var method = 'get_refreshed_widget_settings';
		var data = {};

		itsecSettingsPage.sendAJAXRequest( module, method, data, function( results ) {
			if ( results.success && results.response ) {
				jQuery( '#itsec-sidebar-widget-' + module + ' .inside' ).html( results.response );
			} else {
				itsecSettingsPage.showErrors( results.errors, results.module, 'closed' );
			}
		} );
	},

	sendAJAXRequest: function( module, method, data, callback ) {
		var postData = {
			'action': itsec_page.ajax_action,
			'nonce':  itsec_page.ajax_nonce,
			'module': module,
			'method': method,
			'data':   data,
		};

		jQuery.post( ajaxurl, postData )
			.always(function( a, status, b ) {
				itsecSettingsPage.processAjaxResponse( a, status, b, module, method, data, callback );
			});
	},

	processAjaxResponse: function( a, status, b, module, method, data, callback ) {
		var results = {
			'module':        module,
			'method':        method,
			'data':          data,
			'status':        status,
			'jqxhr':         null,
			'success':       false,
			'response':      null,
			'errors':        [],
			'messages':      [],
			'functionCalls': [],
			'redirect':      false,
			'closeModal':    true
		};


		if ( 'ITSEC_Response' === a.source && 'undefined' !== a.response ) {
			// Successful response with a valid format.
			results.jqxhr = b;
			results.success = a.success;
			results.response = a.response;
			results.errors = a.errors;
			results.messages = a.messages;
			results.functionCalls = a.functionCalls;
			results.redirect = a.redirect;
			results.closeModal = a.closeModal;
		} else if ( a.responseText ) {
			// Failed response.
			results.jqxhr = a;
			var errorThrown = b;

			if ( 'undefined' === typeof results.jqxhr.status ) {
				results.jqxhr.status = -1;
			}

			if ( 'timeout' === status ) {
				var error = itsec_page.translations.ajax_timeout;
			} else if ( 'parsererror' === status ) {
				var error = itsec_page.translations.ajax_parsererror;
			} else if ( 403 == results.jqxhr.status ) {
				var error = itsec_page.translations.ajax_forbidden;
			} else if ( 404 == results.jqxhr.status ) {
				var error = itsec_page.translations.ajax_not_found;
			} else if ( 500 == results.jqxhr.status ) {
				var error = itsec_page.translations.ajax_server_error;
			} else {
				var error = itsec_page.translations.ajax_unknown;
			}

			error = error.replace( '%1$s', status );
			error = error.replace( '%2$s', errorThrown );

			results.errors = [ error ];
		} else {
			// Successful response with an invalid format.
			results.jqxhr = b;

			results.response = a;
			results.errors = [ itsec_page.translations.ajax_invalid ];
		}


		if ( results.redirect ) {
			window.location = results.redirect;
		}


		if ( 'function' === typeof callback ) {
			callback( results );
		} else if ( 'function' === typeof console.log ) {
			console.log( 'ERROR: Unable to handle settings AJAX request due to an invalid callback:', callback, {'data': postData, 'results': results} );
		}


		if ( results.functionCalls ) {
			for ( var i = 0; i < results.functionCalls.length; i++ ) {
				if ( 'object' === typeof results.functionCalls[i] && 'string' === typeof results.functionCalls[i][0] && 'function' === typeof itsecSettingsPage[results.functionCalls[i][0]] ) {
					itsecSettingsPage[results.functionCalls[i][0]]( results.functionCalls[i][1] );
				} else if ( 'string' === typeof results.functionCalls[i] && 'function' === typeof window[results.functionCalls[i]] ) {
					window[results.functionCalls[i]]();
				} else if ( 'object' === typeof results.functionCalls[i] && 'string' === typeof results.functionCalls[i][0] && 'function' === typeof window[results.functionCalls[i][0]] ) {
					window[results.functionCalls[i][0]]( results.functionCalls[i][1] );
				} else if ( 'function' === typeof console.log ) {
					console.log( 'ERROR: Unable to call missing function:', results.functionCalls[i] );
				}
			}
		}
	},

	sendModuleAJAXRequest: function( module, data, callback ) {
		itsecSettingsPage.sendAJAXRequest( module, 'handle_module_request', data, callback );
	},

	sendWidgetAJAXRequest: function( widget, data, callback ) {
		itsecSettingsPage.sendAJAXRequest( widget, 'handle_widget_request', data, callback );
	},

	getUrlParameter: function( name ) {
		var pageURL = decodeURIComponent( window.location.search.substring( 1 ) ),
			URLParameters = pageURL.split( '&' ),
			parameterName,
			i;

		// Loop through all parameters
		for ( i = 0; i < URLParameters.length; i++ ) {
			parameterName = URLParameters[i].split( '=' );

			// If this is the parameter we're looking for
			if ( parameterName[0] === name ) {
				// Return the value or true if there is no value
				return parameterName[1] === undefined ? true : parameterName[1];
			}
		}
		// If the requested parameter doesn't exist, return false
		return false;
	}
};

jQuery(document).ready(function() {
	itsecSettingsPage.init();

	if ( itsec_page.show_security_check ) {
		jQuery( '.itsec-settings-view-toggle a.itsec-grid' ).trigger( 'click' );
		jQuery( '#itsec-module-card-security-check .itsec-toggle-settings' ).trigger( 'click' );
	}



	jQuery( '.dialog' ).click( function ( event ) {

		event.preventDefault();

		var target = jQuery( this ).attr( 'href' );
		var title = jQuery( this ).parents( '.inside' ).siblings( 'h3.hndle' ).children( 'span' ).text();

		jQuery( '#' + target ).dialog( {
			                               dialogClass  : 'wp-dialog itsec-dialog itsec-dialog-logs',
			                               modal        : true,
			                               closeOnEscape: true,
			                               title        : title,
			                               height       : ( jQuery( window ).height() * 0.8 ),
			                               width        : ( jQuery( window ).width() * 0.8 ),
			                               open         : function ( event, ui ) {

				                               jQuery( '.ui-widget-overlay' ).bind( 'click', function () {
					                               jQuery( this ).siblings( '.ui-dialog' ).find( '.ui-dialog-content' ).dialog( 'close' );
				                               } );

			                               }

		                               } );

		jQuery( '.ui-dialog :button' ).blur();

	} );
});
