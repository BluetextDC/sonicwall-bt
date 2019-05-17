( function( $ ) {

	var metaboxes = {

		init: function() {

		},

		hideStyleAttributes: function( value ) {

			$( '.te-attributes-container' ).fadeOut( 'fast', function() {

				setTimeout( function() {

					$( '.' + value + '-attributes' ).fadeIn();

				}, 350 );

			} );


		},

		hideInheritvalues: function( checkbox ) {

			checkbox.closest( '.cmb-td' ).children().not( checkbox.closest( 'label' ) ).fadeToggle();

		},

	};

	$( document ).ready( metaboxes.init );

	$( 'body' ).on( 'change', '#_timeline_styles_container_style', function() {

		metaboxes.hideStyleAttributes( $( this ).val() );

	} );

	$( 'body' ).on( 'change', '.inherit-toggle', function() {

		metaboxes.hideInheritvalues( $( this ) );

	} );

} )( jQuery );
