var itsecUserSecurityCheck = {
	updateUserTable: function ( custom_data ) {
		var data = this.getUrlParameters(),
			s = jQuery('#user-search-input').data( 'last-search' );

		data.paged = 1;

		data._nonce = jQuery('#_nonce-itsec-user-security-check').val();
		if ( s ) {
			data.s = s;
		}

		jQuery.each( custom_data, function( key, value ) {
			data[key] = value;
		});

		var $this = jQuery(this);

		wp.ajax.post( 'itsec-user-security-check-user-search', data ).done( function( response ) {
			// Update our user table
			jQuery( '#itsec-user-table' ).html( response.users_table );

			// Update views
			jQuery( '#itsec-module-card-user-security-check .subsubsub' ).replaceWith( response.views );

			// Update the nonce value
			jQuery( '#_nonce-itsec-user-security-check' ).val( response.search_nonce );
		}).fail( function( response ) {
			itsecSettingsPage.showError( response.message, 'user-security-check', 'open' );
		});
	},

	search: function() {
		// Store search query
		jQuery('#user-search-input').data( 'last-search', jQuery('#user-search-input').val() );
		// Go to page 1 for a new search
		this.updateUserTable( { 'paged':1, 's':jQuery('#user-search-input').val() } );
	},

	getUrlParameters: function () {
		var data = {},
			pageURL = decodeURIComponent( window.location.search.substring( 1 ) ),
			URLParameters = pageURL.split( '&' ),
			parameterName,
			i;

		// Loop through all parameters
		for ( i = 0; i < URLParameters.length; i++ ) {
			parameterName = URLParameters[i].split( '=' );
			data[parameterName[0]] = parameterName[1];
		}

		return data;
	}

}

jQuery(document).ready(function() {
	var $container = jQuery( '#wpcontent' );

	$container.on( 'click', '#itsec-user-table .destroy-sessions', function( e ) {
		var $this = jQuery(this);

		wp.ajax.post('itsec-destroy-sessions', {
			nonce  : $this.data('nonce'),
			user_id: $this.data('user_id')
		}).done(function (response) {
			// Get the table cell we're in
			$cell = $this.closest( 'td' );
			// Update the table cell contents
			$cell.html( response.session_cell_contents );
			// Add the notice to the end of the table cell
			jQuery('<div class="notice notice-success inline"><p>' + response.message + '</p></div>').appendTo( $cell ).fadeOut( 3000 );
		}).fail(function (response) {
			$this.siblings('.notice').remove();
			$this.before('<div class="notice notice-error inline"><p>' + response.message + '</p></div>');
		});

		e.preventDefault();
	});

	$container.on( 'click', '#itsec-user-table .send-email a', function( e ) {
		var $this = jQuery(this);

		wp.ajax.post('itsec-send-2fa-email-reminder', {
			nonce  : $this.data('nonce'),
			user_id: $this.data('user_id')
		}).done(function (response) {
			// Get the table cell we're in
			$cell = $this.closest( 'td' );
			// Add the notice to the end of the table cell
			jQuery('<div class="notice notice-success inline"><p>' + response.message + '</p></div>').appendTo( $cell ).fadeOut( 3000 );
		}).fail(function (response) {
			$this.siblings('.notice').remove();
			$this.before('<div class="notice notice-error inline"><p>' + response.message + '</p></div>');
		});

		e.preventDefault();
	});

	$container.on( 'change', '#itsec-user-table .itsec-user-role select', function( e ) {
		e.preventDefault();

		var $this = jQuery(this);

		wp.ajax.post('itsec-set-user-role', {
			new_role: $this.val(),
			_nonce  : $this.data('nonce'),
			user_id : $this.data('user_id')
		}).done(function (response) {
			$this.siblings('.notice').remove();
			jQuery('<div class="notice notice-success inline"><p>' + response.message + '</p></div>').insertBefore( $this ).fadeOut( 3000 );

			// Update views
			jQuery( '#itsec-module-card-user-security-check .subsubsub' ).replaceWith( response.views );
		}).fail(function (response) {
			$this.siblings('.notice').remove();
			$this.before('<div class="notice notice-error inline"><p>' + response.message + '</p></div>');
		});

		e.preventDefault();
	});

	jQuery('#itsec-module-card-user-security-check .wp-list-table th.manage-column a').each(function () {
		jQuery(this).attr('href', jQuery(this).attr('href') + '#itsec-module-card-user-security-check');
	});

	$container.on( 'keypress', '#user-search-input', function(e) {
		if( 13 === e.which ) {
			e.preventDefault();
			itsecUserSecurityCheck.search();
		}
	});
	$container.on( 'click', '#itsec-module-card-user-security-check #search-submit', function (e) {
		e.preventDefault();
		itsecUserSecurityCheck.search();
	});

	$container.on( 'click', '#itsec-user-table .pagination-links a', function( e ) {
		e.preventDefault();
		itsecUserSecurityCheck.updateUserTable( { 'paged': jQuery( this ).data( 'paged' ) } );
	});

	$container.on( 'click', '#itsec-module-card-user-security-check .subsubsub a', function( e ) {
		e.preventDefault();
		itsecUserSecurityCheck.updateUserTable( { 'paged':1, 'role': jQuery( this ).data( 'role' ) } );
	});

	$container.on( 'click', '#itsec-user-table th.sorted a, th.sortable a', function( e ) {
		e.preventDefault();
		itsecUserSecurityCheck.updateUserTable( { 'order': jQuery( this ).data( 'order' ), 'orderby': jQuery( this ).data( 'orderby' ) } );
	});
});
