( function( $ ) {
	$( '.collapsible' ).click( function() {
			$( this ).parent().siblings( '.module-body' ).children( '.module-lessons' ).slideToggle( 400, function() {
				$moduleHeader = $( this ).parent().siblings( '.module-header' );

				if ( $moduleHeader.length ) {
					// CSS "active" class triggers the animation.
					if ( $( this ).is( ':visible' ) ) {
						$moduleHeader.removeClass( 'active' );
					} else {
						$moduleHeader.addClass( 'active' );
					}
				}
			} );
	} );
} )( jQuery );
