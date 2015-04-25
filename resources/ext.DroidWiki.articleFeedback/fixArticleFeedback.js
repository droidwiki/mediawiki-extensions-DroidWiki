$( function () {
	// This code must not be executed before the document is loaded.  mw-articlefeedback
	function getPath() {
		return window.location.hash.slice( 1 );
	}

	$( window ).on( 'hashchange', function() {
		if ( getPath() === "mw-articlefeedback" ) {
			$( '#mw-articlefeedback' ).css( 'display', 'block' );
			$( '#ratearticlelink' ).hide();
			$( 'html, body' ).animate( {
				scrollTop: $( '#mw-articlefeedback' ).offset().top - 60
			}, 0);
		}
	} );
} );
