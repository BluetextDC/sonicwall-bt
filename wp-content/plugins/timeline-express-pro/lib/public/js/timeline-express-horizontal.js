/**
 * Horizontal Timeline Express Scripts
 *
 * @author Code Parrots <support@codeparrots.com>
 * @since 1.3.6
 */
var horizontal_timeline = {

	init: function() {

		if ( jQuery( '#cd-timeline.horizontal-timeline.scroll' ).length ) {

			jQuery( '#cd-timeline.horizontal-timeline.scroll' ).each( function() {

				horizontal_timeline.scroll( jQuery( this ) );

			} );

		}

		if ( jQuery( '#cd-timeline.horizontal-timeline.slider' ).length ) {

			var count = 1;

			jQuery( '#cd-timeline.horizontal-timeline.slider' ).each( function() {

				var iteration_class = 'horizontal-timeline-' + count,
				    visible_items   = jQuery( this ).data( 'visible' ),
				    slide_distance  = jQuery( this ).data( 'distance' );

				jQuery( this ).addClass( iteration_class );

				horizontal_timeline.slider( jQuery( this ), '.' + iteration_class, visible_items, slide_distance );

				count++;

			} );

		}

	},

	scroll: function( $scrolling_timeline ) {

		var $container = $scrolling_timeline.find( '.horizontal-timeline' ),
		    width      = 0;

		$container.find( '.cd-timeline-block' ).each( function() {
			width += jQuery( this ).outerWidth( true );
		} );

		$container.find( '.background-line' ).width( width );
		$container.width( width ).fadeTo( 900, 1 );

	},

	slider: function( $carousel, iteration_class, visible_items, slide_distance ) {

		var $background_line = $carousel.find( '.background-line' );

		$background_line.remove();

		$carousel.find( '.horizontal-timeline .cd-timeline-block' ).prepend( $background_line.clone() );

		$carousel.find( '.horizontal-timeline' ).carouFredSel( {
			auto: false,
			responsive: true,
			width: '100%',
			scroll: parseInt( slide_distance ),
			circular: false,
			items: {
				visible: parseInt( visible_items ),
			},
			prev: {
				button: iteration_class + ' .prev',
				key: 'left'
			},
			swipe: {
				onTouch: true
			},
			next: {
				button: iteration_class + ' .next',
				key: 'right'
			},
			onCreate: function() {

				var length = $carousel.find( '.cd-timeline-block' ).length;

				if ( length <= parseInt( visible_items ) ) {

					jQuery( '.navigation' ).remove();

				}

			},
		} );

		var $navigation = $carousel.find( '.navigation' ),
		    $carousel   = $carousel.find( '.horizontal-timeline' );

		setTimeout( function() {
			onResize();
			$carousel.closest( '#cd-timeline' ).fadeTo( 900, 1 );
			$carousel.fadeTo( 900, 1 );
			$navigation.fadeTo( 900, 1 );
		}, 800 );

	}

};

/**
 * Resize function - handles height and visible item adjustments
 *
 * @return {[type]} [description]
 */
function onResize() {

	jQuery( '#cd-timeline.horizontal-timeline.slider' ).each( function() {

		var carousel_height = 0;

		jQuery( this ).find( '.horizontal-timeline .cd-timeline-block' ).each( function() {
			carousel_height = ( carousel_height >= jQuery( this ).outerHeight() ) ? carousel_height : jQuery( this ).outerHeight();
		} );

		var window_width    = jQuery( window ).width(),
		    visible_items   = parseInt( jQuery( this ).data( 'visible' ) ),
		    scroll_distance = parseInt( jQuery( this ).data( 'distance' ) );

		if ( 1 !== visible_items ) {

			if ( window_width <= 845 ) {

				visible_items   = 2;
				scroll_distance = 2;

			}

			if ( window_width <= 600 ) {

				visible_items   = 1;
				scroll_distance = 1;

			}

		}

		jQuery( this ).find( '.horizontal-timeline' ).trigger( 'configuration', {
			items: {
				visible: visible_items
			},
			height: carousel_height,
			scroll: scroll_distance,
			reInit: true,
		}, true );

	} );

}

jQuery( window ).bind( 'load', horizontal_timeline.init );

jQuery( window ).on( 'resize', onResize );
