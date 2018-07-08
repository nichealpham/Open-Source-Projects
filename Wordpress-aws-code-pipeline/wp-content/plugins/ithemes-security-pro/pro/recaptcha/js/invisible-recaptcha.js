var $itsecRecaptchaForm;

function itsecRecaptchaCallback( token ) {
	jQuery(document).off( 'submit', 'form', itsecRecaptchaHandleSubmit );
	jQuery(document).off( 'click', ':submit', itsecRecaptchaHandleSubmit );

	jQuery('#g-recaptcha-response').val( token );

	$itsecRecaptchaForm.submit();
};

var itsecRecaptchaHandleSubmit = function( e ) {
	if ( 0 !== jQuery( '.grecaptcha-user-facing-error' ).length && '' !== jQuery( '.grecaptcha-user-facing-error' ).first().html() ) {
		return;
	}

	e.preventDefault();

	if ( 'form' === jQuery(this).prop( 'tagName' ).toLowerCase() ) {
		$itsecRecaptchaForm = jQuery(this);
	} else {
		$itsecRecaptchaForm = jQuery(this).parents( 'form' );

		jQuery('<input type="hidden">').attr( {
			name:  jQuery(this).attr( 'name' ),
			value: jQuery(this).val()
		} ).appendTo( $itsecRecaptchaForm );
	}

	if ( 0 === jQuery( '#g-recaptcha-response', $itsecRecaptchaForm ).length ) {
		// Only handle forms that have the reCAPTCHA modifications.
		return true;
	}

	grecaptcha.execute();
}

jQuery(document).on( 'submit', 'form', itsecRecaptchaHandleSubmit );
jQuery(document).on( 'click', ':submit', itsecRecaptchaHandleSubmit );
