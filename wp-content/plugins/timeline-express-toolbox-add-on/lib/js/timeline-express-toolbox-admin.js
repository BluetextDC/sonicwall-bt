/**
 * timeline-express-toolbox-admin.js
 *
 * Handles the admin side javascript functionality, mainly our options page.
 *
 * @since 1.0.0
 */

( function( $ ) {

		$( "input[name='timeline_express_storage[date_format]']" ).click( function() {

			if ( "date_format_custom_radio" != $(this).attr("id") ) {

				$( "input[name='timeline_express_storage[date_format_custom]']" ).val( $( this ).val() ).siblings( '.example' ).text( $( this ).parent( 'label' ).children( '.format-i18n' ).text() );

			}

		} );

		$( "input[name='timeline_express_storage[date_format_custom]']" ).focus( function() {

			$( '#date_format_custom_radio' ).prop( 'checked', true );

		} );

		$( "input[name='timeline_express_storage[date_format_custom]']" ).change( function() {

			var format = $( this );

			format.siblings( '.spinner' ).addClass( 'is-active' );

			$.post( ajaxurl, {
				action : 'date_format',
				date   : format.val()
			}, function( d ) {

				format.siblings( '.spinner' ).removeClass( 'is-active' );

				format.siblings( '.example' ).text( d );

			} );

		} );

})( jQuery );
