jQuery( document ).ready(function ( $ ) {
	var $container = jQuery( '#wpcontent' );

	$container.on( 'click', '#itsec-network-brute-force-reset_api_key', function( e ) {
		e.preventDefault();

		if ( ! itsec_network_brute_force.original_button_text ) {
			itsec_network_brute_force.original_button_text = $( '#itsec-network-brute-force-reset_api_key' ).prop( 'value' );
		}

		$( '#itsec-network-brute-force-reset_api_key' )
			.removeClass( 'button-primary' )
			.addClass( 'button-secondary' )
			.prop( 'value', itsec_network_brute_force.resetting_button_text )
			.prop( 'disabled', true );

		var data = {
			'method': 'reset-api-key'
		};

		$( '#itsec-network-brute-force-reset-status' ).html( '' );

		itsecSettingsPage.sendModuleAJAXRequest( 'network-brute-force', data, function( results ) {
			$( '#itsec-network-brute-force-reset-status' ).html( '' );

			if ( true !== results.response ) {
				if ( results.errors && results.errors.length > 0 ) {
					$.each( results.errors, function( index, error ) {
						$( '#itsec-network-brute-force-reset-status' ).append( '<div class="error inline"><p><strong>' + error + '</strong></p></div>' );
					} );
				} else if ( 0 == results.response ) {
					$( '#itsec-network-brute-force-reset-status' ).append( '<div class="updated fade inline"><p><strong>' + itsec_network_brute_force.no_changes + '</strong></p></div>' );
				}

				$( '#itsec-network-brute-force-reset_api_key' )
					.removeClass( 'button-secondary' )
					.addClass( 'button-primary' )
					.prop( 'value', itsec_network_brute_force.original_button_text )
					.prop( 'disabled', false );
			}
		} );
	} );
});
