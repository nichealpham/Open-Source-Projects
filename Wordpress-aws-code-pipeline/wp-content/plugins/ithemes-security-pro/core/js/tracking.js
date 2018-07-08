if ( typeof itsec_tracking_vars != 'undefined' ) {

	href = location.href;

	(function () {
		var ga = document.createElement( 'script' );
		ga.type = 'text/javascript';
		ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName( 'script' )[0];
		s.parentNode.insertBefore( ga, s );
	})();

	var _gaq = _gaq || [];
	_gaq.push( ['_setAccount', 'UA-47645120-1'] );

	function itsec_get_vars( type, values ) {

		var data = {
			action: 'itsec_tracking_ajax',
			type  : type,
			nonce : itsec_tracking_vars.nonce
		};

		if ( type != 'receive' ) {
			data.values = values;
		}

		jQuery.post( ajaxurl, data, function ( response ) {

			if ( response == 'false' ) {

				return false;

			} else {

				return true;

			}

		} );

	}

	jQuery( document ).ready( function () {

		var tracking_settings = itsec_tracking_vars.vars;
		var track_it = new Array();
		var timestamp = new Date().getTime();

		jQuery( '.itsec-settings-form' ).submit( function ( event ) {

			var values = jQuery( this ).serializeArray();

			jQuery.each( values, function ( name, value ) {

				var section = value.name.substring( 0, value.name.indexOf( '[' ) );
				var setting = value.name.substring( value.name.indexOf( '[' ) + 1, value.name.indexOf( ']' ) );

				if ( typeof tracking_settings[section] != 'undefined' && typeof  tracking_settings[section][setting] != 'undefined' ) {

					var setting_value = tracking_settings[section][setting];

					var value_array = setting_value.split( ':' );
					var default_type = value_array[1];

					if ( default_type == 'b' && value.value == 1 ) {

						var saved_value = 'true';

					}
					else {

						if ( default_type == 'b' ) {
							var saved_value = 'false';
						}
						else {
							var saved_value = value.value;
						}

					}

					delete tracking_settings[section][setting];

					var item = new Object();

					item.section = section;
					item.setting = setting;
					item.value = saved_value;

					track_it.push( item );

				}

			} );

			jQuery.each( tracking_settings, function ( section, settings ) {

				var section = section;

				jQuery.each( tracking_settings[section], function ( setting, value ) {

					var value_array = value.split( ':' );
					var default_type = value_array[1];
					var default_value = value_array[0];

					if ( default_type == 'b' && default_value == 0 ) {
						var saved_value = 'false';
					} else if ( default_type == 'b' ) {
						var saved_value = 'true';
					} else {
						var saved_value = default_value;
					}

					var item = new Object();

					item.section = section;
					item.setting = setting;
					item.value = saved_value;

					track_it.push( item );

				} );

			} );

			var group_size = Math.ceil( track_it.length / 9 );
			var group_count = 1;
			var count = 0;
			var saved_values = '';

			jQuery.each( track_it, function ( setting, value ) {

				saved_value = ( value.section + '[' + value.setting + '] = ' + value.value + '; ')
				saved_values = saved_values.concat( saved_value );

				if ( count === group_size - 1 ) {

					_gaq.push( ['_trackEvent', 'ITSEC', 'Group ' + ( group_count ), saved_values, timestamp, true] );
					saved_values = '';
					count = 0;
					group_count ++;

				} else {
					count ++;
				}

			} );

		} );

	} );

}
