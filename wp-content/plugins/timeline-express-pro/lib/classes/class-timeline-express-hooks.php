<?php

final class Timeline_Express_Hooks {

	public function __construct() {

		add_action( 'timeline-express-single-before-image', [ $this, 'timeline_express_timelines_tax_markup' ], 5 );
		add_action( 'timeline-express-single-before-image', [ $this, 'timeline_express_categories_tax_markup' ], 10 );

		// @codingStandardsIgnoreStart
		/**
		 * Pre content - single template.
		 * @since 2.0.0
		 */
		add_action( 'timeline-express-single-before-content', function() {

			print( '<div class="announcement-content">' );

		}, 2 );

		add_action( 'timeline-express-single-before-content', [ $this, 'timeline_express_date_markup' ], 5 );

		add_action( 'timeline-express-single-after-content', function() {

			print( '</div>' );

		}, 2 );
		// @codingStandardsIgnoreEnd

	}

	public function timeline_express_content_wrap_open() {

	}

	/**
	 * Render the date markup on the single template.
	 *
	 * @return mixed HTML markup for the date content.
	 */
	public function timeline_express_date_markup() {

		$options = timeline_express_get_options();

		if ( 1 !== (int) $options['date-visibility'] ) {

			return;

		}

		?>

			<!-- Render the announcement date -->
			<strong class="timeline-express-single-page-announcement-date">
				<?php
					/* Action hook to display content before the single announcement date */
					do_action( 'timeline-express-single-before-date' );

					printf(
						apply_filters( 'timeline_express_announcement_date_text', /* translators: The announcement date. */ __( 'Announcement Date: %s', 'timeline-express-pro' ) ),
						wp_kses_post( timeline_express_get_announcement_date( get_the_ID() ) )
					);

					/* Action hook to display content after the single announcement date */
					do_action( 'timeline-express-single-after-date' );
				?>
			</strong>

		<?php

	}

	/**
	 * Render the timelines markup on the single template.
	 *
	 * @return mixed HTML markup for the categories markup.
	 */
	public function timeline_express_timelines_tax_markup() {

		timeline_express_tax_links( 'timeline', __( 'Timelines:', 'timeline-express-pro' ) );

	}

	/**
	 * Render the categories markup on the single template.
	 *
	 * @return mixed HTML markup for the categories markup.
	 */
	public function timeline_express_categories_tax_markup() {

		timeline_express_tax_links( 'timeline_express_categories', __( 'Categories:', 'timeline-express-pro' ) );

	}

}

new Timeline_Express_Hooks;
