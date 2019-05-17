<?php
/**
 * Timeline Express Pagination
 *
 * @since 1.3.2
 */

global $paged;

$range     = 4;
$showitems = ( $range * 2 ) + 1;
$paged     = empty( $paged ) ? 1 : $paged;
$pages     = $query_results->max_num_pages;
$page_id   = isset( $post->ID ) ? $post->ID : get_the_ID();

delete_timeline_express_transients( $page_id );

if ( ! $pages ) {

	$pages = 1;

}

if ( 1 !== $pages ) {

	printf( '<div class="te-pagination-container"><div class="te-pagination"><span>Page %1s of %2s</span>', $paged, $pages );

	// "First" & "Previous" links
	if ( $paged > 1 && $showitems > $pages ) {

		if ( ! apply_filters( 'timeline_express_pagination_disable_additional_links', false ) ) {

			printf(
				'<a href="%1s">&lsaquo; %2s</a>',
				get_pagenum_link( $paged - 1 ),
				__( 'Previous', 'timeline-express-pro' )
			);

		}
	}

	for ( $i = 1; $i <= $pages; $i++ ) {

		if ( 1 != $pages && ( ! ( $i >= $paged + $range + 1 || $i <= $paged - $range - 1 ) || $pages <= $showitems ) ) {

			if ( $paged === $i ) {

				printf(
					'<span class="current">%s</span>',
					absint( $paged )
				);

			} else {

				printf(
					'<a href="%1s" class="inactive">%2s</a>',
					esc_url( get_pagenum_link( $i ) ),
					absint( $i )
				);

			}
		}
	}

	// "Next" & "Last" links
	if ( $paged < $pages && $showitems > $pages ) {

		if ( ! apply_filters( 'timeline_express_pagination_disable_additional_links', false ) ) {

			printf(
				'<a href="%1s">%2s</a>',
				esc_url( get_pagenum_link( $paged + 1 ) ),
				__( 'Next &rsaquo;', 'timeline-express-pro' )
			);

		}
	}

	printf( '</div></div>%s', "\n" );

} // End if().
