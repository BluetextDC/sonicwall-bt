/**
 * Timeline Express Scripts
 *
 * @author Code Parrots <support@codeparrots.com>
 */


/* Run on document load */
/* Script Used To Fadein Announcements */
jQuery( document ).ready(function() {

	// add the necessary classes on page load
	jQuery( 'html' ).addClass( 'cssanimations csscolumns cssgradients cssreflections csstransforms csstransforms3d csstransitions' );

	//hide timeline blocks which are outside the viewport
	jQuery( '.cd-timeline-block' ).each( function() {
		/* If the animation is set to disabled, do not hide the items */
		if ( timeline_express_data.animation_disabled ) {
			return;
		}
		if ( jQuery( this ).offset().top > jQuery( window ).scrollTop() + jQuery( window ).height() * 0.75 ) {
			/* add the animation class */
			jQuery( this ).find( '.cd-timeline-img, .cd-timeline-content' ).addClass( 'is-hidden' );
		}
	});

	/* If the animation is set to disabled, do not perform the scroll callback */
	if ( ! timeline_express_data.animation_disabled ) {
		/* on scolling, show/animate timeline blocks when enter the viewport */
		jQuery( window ).on( 'scroll', function() {
			jQuery( '.cd-timeline-block' ).each( function() {
				if ( jQuery( this ).offset().top <= jQuery( window ).scrollTop() + jQuery( window ).height() * 0.75 && jQuery( this ).find( '.cd-timeline-img' ).hasClass( 'is-hidden' ) ) {
					jQuery( this ).find( '.cd-timeline-img, .cd-timeline-content' ).removeClass( 'is-hidden' ).addClass( 'bounce-in' );
				}
			});
		});
	}

	var $masonryContainer = jQuery( '.timeline-express' );
	$masonryContainer.imagesLoaded( function() {
		$masonryContainer.masonry( { itemSelector : '.cd-timeline-block', } );
		jQuery( '.timeline-express' ).fadeTo( 'fast' , 1 );
	});

	/**
	*	Filter Submissions function
	*	@since 1.1.7.1
	*/
	jQuery( 'body' ).on( 'click', '.timeline-express-filter-submit', function() {
		var clicked_filter_button_prev = jQuery( this ).prev();
		var redirection_array = [];
		/* Redirection URL */
		var redirect_url = clicked_filter_button_prev.find( 'input[name="filter-redirect-url"]' ).val();
		redirection_array.push( redirect_url );
		/* Timelines */
		if ( clicked_filter_button_prev.find( 'select[name="timelines"]' ).length > 0 ) {
			var timeline = clicked_filter_button_prev.find( 'select[name="timelines"]' ).val();
			if ( timeline != '-1' ) {
				redirection_array.push( 'timeline=' + timeline );
			}
		}

		if ( jQuery( '.cat-checkbox-container' ).length > 0 ) {
			/* Category Checkboxes */
			var categories = [];
			jQuery( '.cat-checkbox-container' ).find( 'input[type="checkbox"]:checked' ).each( function() {
				categories.push( jQuery( this ).val() );
			});
			if ( categories.length > 0 ) {
				redirection_array.push( 'timeline-category=' + categories.join( ',' ) );
			}
		}

		// length 1 == only redirect URL no filtering
		if ( redirection_array.length > 0 ) {
			var new_redirection_url = null;
			var url_concat = ( redirection_array[0].indexOf( '?' ) > -1 ? '&' : '?' );
			if ( redirection_array.length == 1 ) {
				window.location.replace( redirection_array.join( '' ) );
			}
			// length 2 == one filter options, timeline or categories
			if ( redirection_array.length == 2 ) {
				new_redirection_url = redirection_array[0] + url_concat + redirection_array[1];
				window.location.replace( new_redirection_url );
			}
			// length 3 == all filtering, timlienes and categories
			if ( redirection_array.length == 3 ) {
				new_redirection_url = redirection_array[0] + url_concat + redirection_array[1] + '&' + redirection_array[2];
				window.location.replace( new_redirection_url );
			}
		}
	});
});
