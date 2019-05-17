/**
 * Timeline Express Scripts
 *
 * @since 2.0.0
 * @package Timeline Express Pro
 * @author Code Parrots <support@codeparrots.com>
 */

/**
 * Enable timelineExpress to be accessed from the global scope
 *
 * @since 2.0.0
 * @type {Object}
 */
var timelineExpress = {};

( function( $ ) {

	var masonryAtts = {
		'columnWidth' : '.cd-timeline-block',
		'itemSelector': '.cd-timeline-block',
		'isRTL'       : timeline_express_data.isRTL,
	};

	if ( timeline_express_data.animation_disabled ) {

		masonryAtts.transitionDuration = 0;

	}

	timelineExpress = {

		/**
		 * Timeline Express initialization.
		 *
		 * Initialize the timelines on the page.
		 */
		init: function( hideElements ) {

			if ( typeof hideElements === 'undefined' ) {

				hideElements = true;

			}

			// Add the css animation class onto the body.
			$( 'html' ).addClass( 'cssanimations csscolumns cssgradients cssreflections csstransforms csstransforms3d csstransitions' );

			$( '.cd-timeline-block' ).each( function() {

				// If the animations are disabled, do not hide the items.
				if ( timeline_express_data.animation_disabled || ! hideElements ) {

					return;

				}

				$( this ).find( '.cd-timeline-img, .cd-timeline-content, .extra-content' ).addClass( 'is-hidden' );

			} );

			timelineExpress.scroll();

			$( '.timeline-express' ).each( function() {

				var $reinit = $( this ).imagesLoaded( function() {

					// Dispatch event timelineLayoutStart when timeline layout starts
					window.dispatchEvent( new CustomEvent( 'timelineLayoutStart' ) );

					// init Masonry after all images have loaded
					$reinit.masonry( masonryAtts );

				} );

				$( this ).on( 'layoutComplete', timelineExpress.display( $( this ) ) );

			} );

		},

		/**
		 * Display the timeline.
		 *
		 * After the timeline containers have been initialized, tweak the container
		 * classes, prevent container overlapping and re-initialize the timeline.
		 */
		display: function( timeline ) {

			timelineExpress.addLocationClasses( timeline );

			timelineExpress.preventOverlaps( timeline );

			timeline.imagesLoaded( function() {

				// init Masonry after all images have loaded
				timeline.masonry( masonryAtts );

			} );

			$( window ).trigger( 'resize' );

			setTimeout( function() {

				timeline.fadeTo( 700, 1 );

				// Dispatch event timelineLayoutComplete when timeline layout is completed
				window.dispatchEvent( new CustomEvent( 'timelineLayoutComplete' ) );

			}, timeline_express_data.fadeInTimeout );

		},

		/**
		 * Add location classes.
		 *
		 * Add the 'announcement-left'/'announcement-right' container classes
		 * to adjust the way the timeline is displayed.
		 *
		 * Note: Items with 0px left are considered 'timeline-announcement-left'.
		 *       This function runs on init and on masonry.layoutComplete.
		 */
		addLocationClasses: function( timeline ) {

			var length = timeline.find( '.cd-timeline-block' ).length;

			timeline.find( '.cd-timeline-block' ).each( function() {

				var addSide    = ( '0px' !== $( this ).css( 'left' ) ) ? 'right' : 'left',
				    removeSide = ( '0px' !== $( this ).css( 'left' ) ) ? 'left' : 'right';

				if ( timeline_express_data.isRTL ) {

					addSide    = ( 1 <= parseInt( $( this ).css( 'left' ).replace( 'px', '' ) ) ) ? 'left' : 'right',
					removeSide = ( 1 <= parseInt( $( this ).css( 'left' ).replace( 'px', '' ) ) ) ? 'right' : 'left';

				}

				$( this ).addClass( 'timeline-announcement-' + addSide ).removeClass( 'timeline-announcement-' + removeSide );

			} );

		},

		/**
		 * Add location classes.
		 *
		 * Prevent icon containers from overlapping on init. We tweak the margins
		 * based on the icon overlaps, then re-initialize the timelines.
		 *
		 * Note: This function runs on init and on masonry.layoutComplete.
		 */
		preventOverlaps: function( timeline ) {

			var overlaps = timeline.find( '.cd-timeline-img' ).overlaps();

			if ( overlaps.length < 1 ) {

				return;

			}

			overlaps.each( function( e ) {

				if ( e % 2 ) {

					var prevPreTextHeight = $( overlaps[ e ] ).parents( '.cd-timeline-block' ).prev().find( '.extra-content.before' ).height();
					var previousPreText = ( null === prevPreTextHeight ) ? 0 : prevPreTextHeight;

					var marginTop = 'calc( 2em + 65px + ' + previousPreText + 'px )';
					$( overlaps[ e ] ).parents( '.cd-timeline-block' ).css( 'margin-top', marginTop );

				}

			} );

		},

		/**
		 * Scroll method.
		 *
		 * Adds the animation classes to the icons/containers when the container
		 * is within the page view. If the animations are disabled, return early,
		 * and prevent animations from occuring.
		 */
		scroll: function() {

			// Animations are disabled, return.
			if ( timeline_express_data.animation_disabled ) {

				return;

			}

			$( '.cd-timeline-block' ).each( function() {

				if ( ! timelineExpress.isElementVisible( $( this ) ) ) {

					return;

				}

				timelineExpress.animateContainer( $( this ) );

			} );

		},

		/**
		 * Timeline Filters.
		 *
		 * Sets up the URL which users will be redirected to after they have
		 * filtered the timeline.
		 */
		filterTimeline: function() {

			var clicked_filter_button_prev = $( this ).prev(),
			    redirection_array          = [],
			    redirect_url               = clicked_filter_button_prev.find( 'input[name="filter-redirect-url"]' ).val();

			redirection_array.push( redirect_url );

			// Timelines
			if ( clicked_filter_button_prev.find( 'select[name="timelines"]' ).length > 0 ) {

				var timeline = clicked_filter_button_prev.find( 'select[name="timelines"]' ).val();

				if ( timeline != '-1' ) {

					redirection_array.push( 'timeline=' + timeline );

				}

			}

			if ( $( '.cat-checkbox-container' ).length > 0 ) {

				// Categories
				var categories = [];

				$( '.cat-checkbox-container' ).find( 'input[type="checkbox"]:checked' ).each( function() {

					categories.push( $( this ).val() );

				} );

				if ( categories.length > 0 ) {

					redirection_array.push( 'timeline-category=' + categories.join( ',' ) );

				}

			}

			// length 1 == only redirect URL no filtering.
			if ( redirection_array.length > 0 ) {

				var new_redirection_url = null,
				    url_concat          = ( redirection_array[0].indexOf( '?' ) > -1 ? '&' : '?' );

				if ( 1 === redirection_array.length ) {

					window.location.replace( redirection_array.join( '' ) );

				}

				// length 2 == one filter options, timeline or categories.
				if ( 2 === redirection_array.length ) {

					new_redirection_url = redirection_array[0] + url_concat + redirection_array[1];

					window.location.replace( new_redirection_url );

				}

				// length 3 == all filtering, timlienes and categories.
				if ( 3 === redirection_array.length ) {

					new_redirection_url = redirection_array[0] + url_concat + redirection_array[1] + '&' + redirection_array[2];

					window.location.replace( new_redirection_url );

				}

			}

		},

		/**
		 * Animate a container.
		 *
		 * Using the jQuery object passed in, find the .cd-timeline-content,
		 * .cd-timeline-img and .extra-content elements and animate them in.
		 *
		 * @param {object} element jQuery object to animate.
		 */
		animateContainer: function( element ) {

			var container_animation = element.data( 'container-animation' ) ? element.data( 'container-animation' ) : 'bounce-in',
					icon_animation      = element.data( 'icon-animation' ) ? element.data( 'icon-animation' ) : 'bounce-in';

			element.find( '.cd-timeline-content, .cd-timeline-img, .extra-content' ).removeClass( 'is-hidden' );

			element.find( '.cd-timeline-content, .extra-content' ).addClass( container_animation );
			element.find( '.cd-timeline-img' ).addClass( icon_animation );

			// Dispatch event announcementAnimateIn
			window.dispatchEvent( new CustomEvent( 'announcementAnimateIn', { 'detail': element } ) );

		},

		/**
		 * Checks if an element is visible in the browser window.
		 *
		 * @param  string element The class or ID name to check. eg: '.element' || '#element'
		 *
		 * @return boolean True when the element is in the browser window, else false.
		 */
		isElementVisible: function( element ) {

			/**
			 * Test if the element is a jQuery object
			 * If it is not, convert it into one.
			 * eg: it is either using class or ID '.element'/'#element'.
			 */
			if ( ! element instanceof jQuery ) { // confusion ok.

				element = $( element );

			}

			var offsetTop    = element.offset().top,
					scrollTop    = $( window ).scrollTop(),
					windowHeight = $( window ).height(),
					isHidden     = element.find( '.cd-timeline-img' ).hasClass( 'is-hidden' );

			if ( offsetTop > scrollTop + ( windowHeight * 0.75 ) || ! isHidden ) {

				return false;

			}

			return true;

		},

	};

	// Document ready, timeline initialization.
	$( document ).ready( timelineExpress.init );

	// User scrolling down the timeline.
	$( window ).on( 'scroll', timelineExpress.scroll );

	// Filter the timeline.
	$( 'body' ).on( 'click', '.timeline-express-filter-submit', timelineExpress.filterTimeline );

	// Resize the browser window.
	$( window ).on( 'resize', function( e ) {

		// Mobile devices, remove overlap margins
		if ( e.target.outerWidth <= 738 ) {

			$( '.cd-timeline-block' ).css( 'margin-top', '' );

		}

		$( '.timeline-express' ).on( 'layoutComplete', function() {

			timelineExpress.addLocationClasses( $( this ) );

			timelineExpress.preventOverlaps( $( this ) );

		} );

	} );

} )( jQuery );

/**
 * Helper function to maintain backwards compat.
 *
 * Note: This function maintains backwards compatability, and allows users who
 * have implemented initialize_timeline_express_container() to continue using
 * the function with our new initialization method. This function is also
 * utilized in our add-ons.
 *
 * @since 2.0.0
 */
function initialize_timeline_express_container( hideElements ) {

	if ( typeof hideElements === 'undefined' ) {

		hideElements = true;

	}

	timelineExpress.init( hideElements );

}
