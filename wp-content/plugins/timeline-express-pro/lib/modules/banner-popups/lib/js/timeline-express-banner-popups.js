/**
 * Timeline Express - Banner Popups Module Scripts
 *
 * @param   {object} $ jQuery instance.
 * @package Timeline Express Pro
 * @since   2.0.0
 */
( function( $ ) {

	var bannerPopups = {

		init: function() {

			if ( ! $( '.banner-popup' ).length ) {

				return;

			}

			$( '.banner-popup' ).each( function() {

				// Videos
				var $wpVideo = $( this ).find( '.wp-video' );

				if ( $wpVideo.length ) {

					var src = $wpVideo.closest( '.announcement-banner' ).data( 'src' );

					$wpVideo.wrap( '<a data-fancybox href="' + src + '"></a>' ).append( '<div class="overlay"><span class="fa fa-' + bannerPopup.icon + ' preview-icon"></span></div>' );

				}

				// Images
				var $wpImage = $( this ).find( 'a.banner-preview' );

				if ( $wpImage.length ) {

					var src = $wpImage.attr( 'href' );

					$wpImage.attr( {
						'data-src': src,
						'timeline-popup': '',
						'href': 'javascript:;',
						'data-type': 'image',
					} );

				}

			} );

			setTimeout( function() {
				$( '[timeline-popup]' ).fancybox( {
					animationEffect : bannerPopup.animation,
					thumbs          : false,
					hash            : false,
					loop            : false,
					keyboard        : false,
					toolbar         : false,
					arrows          : false,
					clickContent    : false
				} );
			}, 2500 );

		}

	};

	$( document ).ready( bannerPopups.init );

} )( jQuery );
