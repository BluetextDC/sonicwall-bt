/**
 * Timeline Express Styles Scripts
 *
 * @since 2.0.0
 * @package Timeline Express Pro
 * @author Code Parrots <support@codeparrots.com>
 */
( function( $ ) {

	var $timelineBlock = $( '.cd-timeline-block' );

	var timelineExpressStyles = {

		/**
		 * Timeline Express Styles initialization.
		 *
		 * Carries out any necessary functionality on Timeline initialization.
		 */
		init: function() {

			// Shift the date down below the images on style-one containers
			$( '.cd-timeline-block.container-style-one' ).each( function() {

				var $date = $( this ).find( '.timeline-date' );

				$date.clone().insertBefore( $( this ).find( '.the-excerpt' ) );

				$date.remove();

			} );

		},

		/**
		 * Scroll.
		 *
		 * Adds the animation classes to the icons/containers when the container
		 * is within the page view. If the animations are disabled, return early,
		 * and prevent animations from occuring.
		 */
		scroll: function() {

			// Animations are disabled, return.
			if ( timeline_base_data.animation_disabled ) {

				return;

			}

			$timelineBlock.each( function() {

				if ( timelineExpress.isElementVisible( $( this ) ) ) {

					return;

				}

				$( this ).find( '.cd-timeline-content, .cd-timeline-img' ).addClass( 'te-animated' );

			} );

		},

	};

	$( document ).ready( timelineExpressStyles.init );

	$( window ).on( 'scroll', timelineExpressStyles.scroll );

})( jQuery );
