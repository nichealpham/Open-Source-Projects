jQuery( document ).ready( function ( $ ) {
	var $container = $( '#itsec-module-card-security-check' );

	$container.on( 'click', '#itsec-security-check-secure_site', function( e ) {
		e.preventDefault();

		$( '#itsec-security-check-secure_site' )
			.removeClass( 'button-primary' )
			.addClass( 'button-secondary' )
			.attr( 'value', itsec_security_check_settings.securing_site )
			.prop( 'disabled', true );

		$( '#itsec-security-check-details-container' ).html( '' );

		var data = {
			'method': 'secure-site'
		};

		itsecSettingsPage.sendModuleAJAXRequest( 'security-check', data, function( results ) {
			$( '#itsec-security-check-secure_site' )
				.addClass( 'button-primary' )
				.removeClass( 'button-secondary' )
				.attr( 'value', itsec_security_check_settings.rerun_secure_site )
				.prop( 'disabled', false );

			$( '#itsec-security-check-details-container' ).html( results.response );
		} );
	} );

	$container.on( 'click', '.itsec-security-check-container-is-interactive :submit', function( e ) {
		e.preventDefault();

		var $button = $( this );
		var $container = $( this ).parents( '.itsec-security-check-container-is-interactive' );
		var inputs = $container.find( ':input' ).serializeArray();
		var data = {};

		for ( var i = 0; i < inputs.length; i++ ) {
			var input = inputs[i];

			if ( '[]' === input.name.substr( -2 ) ) {
				var name = input.name.substr( 0, input.name.length - 2 );

				if ( data[name] ) {
					data[name].push( input.value );
				} else {
					data[name] = [input.value];
				}
			} else {
				data[input.name] = input.value;
			}
		};


		$button
			.removeClass( 'button-primary' )
			.addClass( 'button-secondary' )
			.prop( 'disabled', true );

		if ( $button.data( 'clicked-value' ) ) {
			$button
				.data( 'original-value', $( this ).val() )
				.attr( 'value', $( this ).data( 'clicked-value' ) )
		}

		var ajaxFunction = itsecSettingsPage.sendModuleAJAXRequest;

		if ( 'undefined' !== typeof itsecSecurityCheckAJAXRequest ) {
			ajaxFunction = itsecSecurityCheckAJAXRequest;
		}

		ajaxFunction( 'security-check', data, function( results ) {
			$button
				.removeClass( 'button-secondary' )
				.addClass( 'button-primary' )
				.prop( 'disabled', false );

			if ( $button.data( 'original-value' ) ) {
				$button
					.attr( 'value', $( this ).data( 'original-value' ) )
			}


			var $feedback = $container.find( '.itsec-security-check-feedback' );
			$feedback.html( '' );

			if ( results.errors && results.errors.length > 0 ) {
				$container
					.removeClass( 'itsec-security-check-container-call-to-action' )
					.removeClass( 'itsec-security-check-container-confirmation' )
					.addClass( 'itsec-security-check-container-error' );

				$.each( results.errors, function( index, error ) {
					$feedback.append( '<div class="error inline"><p><strong>' + error + '</strong></p></div>' );
				} );
			} else {
				$container
					.removeClass( 'itsec-security-check-container-call-to-action' )
					.removeClass( 'itsec-security-check-container-error' )
					.addClass( 'itsec-security-check-container-confirmation' );

				$container.html( results.response );
				$( '#itsec-notice-network-brute-force' ).hide();
			}
		} );
	} );
} );

/*
function itsecSecurityCheckAJAXRequest( type, data, callback ) {
	console.log( 'Override called' );
	itsecSettingsPage.sendModuleAJAXRequest( type, data, callback );
}
*/
