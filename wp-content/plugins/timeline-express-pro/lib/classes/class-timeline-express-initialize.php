<?php
/**
 * Timeline Express Initialization class
 * @category Timeline_Express_Initialize
 * @package  TimelineExpressBase
 * @author    CodeParrots
 * @license  GPLv2
 * @link     http://www.codeparrots.com
 */

/**
 * Initialize Timeline Express.
 */
class Timeline_Express_Initialize {

	private $css_base;

	private $slider;

	/**
	 * Main class constructor
	 */
	public function __construct() {

		$this->css_base = 'timeline-express-base';

		// retreive & store our options
		$options = timeline_express_get_options();

		// check if the animation is disabled or not
		$animation_disabled = ( isset( $options['disable-animation'] ) && $options['disable-animation'] ) ? true : false;

		/**
		 * Enqueue our scripts & styles
		 * 1) jquery-masonry for laying out the announcements
		 * 2) Timeline Express Base Scripts to initialize the timeline.
		 * 3) do_action( 'timeline-express-scripts' ) for additional plugins to hook into.
		 */

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		/* Scripts */
		wp_enqueue_script( 'jquery-masonry' );
		wp_enqueue_script( 'timeline-express-js-base', TIMELINE_EXPRESS_URL . "lib/public/js/timeline-express{$suffix}.js", array( 'jquery-masonry' ) );

		$script_atts = array(
			// Filter animation_disabled option
			'animation_disabled' => (boolean) apply_filters( 'timeline_express_animation_disabled', $animation_disabled ),
			'isRTL'              => is_rtl(),
			// Filter the time it takes to fade in the timeline (ms)
			'fadeInTimeout'      => (int) apply_filters( 'timeline_express_fade_in_timeout', 800 ),
		);

		/* pass the disabled state to our script */
		wp_localize_script( 'timeline-express-js-base', 'timeline_express_data', $script_atts );

		do_action( 'timeline-express-scripts' );

		/**
		 * Styles
		 * 1) Font Awesome for timeline icons
		 * 2) Timeline Express Base Styles to layout the timeline properly
		 * 3) do_action( 'timeline-express-styles' ); for additional plugins to hook into.
		 */
		timeline_express_enqueue_font_awesome();

		$legacy = ( isset( $options['legacy_support'] ) && $options['legacy_support'] ) ? '-legacy' : '';
		$rtl    = is_rtl() ? '-rtl' : '';

		wp_enqueue_style( $this->css_base, TIMELINE_EXPRESS_URL . "lib/public/css/timeline-express{$legacy}{$rtl}{$suffix}.css", array( 'font-awesome' ), TIMELINE_EXPRESS_VERSION_CURRENT, 'all' );

		do_action( 'timeline-express-styles' );

		/** Print our Timeline Express styles */
		add_action( 'wp_enqueue_scripts', array( $this, self::timeline_express_print_inline_styles( $options ) ) );

		/* Add Custom Classes to the announcment container */
		add_filter( 'timeline-express-announcement-container-class', array( $this, 'timeline_express_announcement_container_classes' ), 10, 2 );

		/* Filter the front end queries each time the filter query args are found in the URL */
		add_filter( 'timeline_express_announcement_query_args', array( $this, 'timeline_express_process_filters_filter_queries' ), 10, 3 );

		/*
		 * Display timeline pagination
		 *
		 * @since 1.3.2
		 */
		add_action( 'timeline-express-after-timeline', array( $this, 'timeline_express_render_pagination' ), 10, 3 );

		add_filter( 'timeline_express_disable_cache', array( $this, 'disable_cache' ) );

	}

	/**
	 * Generate our timeline containers etc.
	 * @param array $timeline_express_options Array of timeline express settings, to be used in the timeline.
	 * @param array $atts Array of shortcode attributes, passed in above.
	 * @return string HTML content of our timeline used to render on the frontend.
	 */
	public function generate_timeline_express( $timeline_express_options, $atts ) {

		$horizontal           = (bool) $atts['horizontal'];
		$horizontal_html_atts = array();
		$additional_classes   = apply_filters( 'timeline_express_container_classes', array() );

		if ( $horizontal ) {

			$this->deregister_vertical_scripts();

			$this->slider = (bool) $atts['slider'];

			$horizontal_html_atts = array(
				'data-visible="' . (int) $atts['items'] . '"',
				'data-distance="' . (int) $atts['slide_distance'] . '"',
			);

			$additional_classes[] = 'horizontal-timeline';
			$additional_classes[] = ( $this->slider ) ? 'slider' : 'scroll';

			$this->register_horizontal_scripts();

		}

		ob_start();

		global $post;

		/**
		 * Used to count the number of times a shortcode is used on a page
		 * This helps us store transients appropriately
		 * @var integer
		 */
		static $shortcode_count = 1;

		/* Setup the 'shortcode_iteration' variable for our transient */
		$shortcode_iteration = ( $shortcode_count > 1 ) ? '-' . $shortcode_count : '';

		/* Setup the compare sign */
		$compare_sign = self::timeline_express_compare_sign( $atts['display'] );

		/**
		 * Check for our filtering query args
		 */
		$filter_by_timelines  = ( isset( $_GET['timeline'] ) ) ? explode( ',', $_GET['timeline'] ) : false;
		$filter_by_categories = ( isset( $_GET['timeline-category'] ) ) ? explode( ',', $_GET['timeline-category'] ) : false;

		$timeframe      = $timeline_express_options['announcement-time-frame'];
		$display_future = ( '0' === $timeline_express_options['announcement-time-frame'] );
		$display_past   = ( '2' === $timeline_express_options['announcement-time-frame'] );

		/**
		 * Allow users to bypass the Timeline caching
		 *
		 * @var boolean
		 */
		$disable_cache = (bool) apply_filters( 'timeline_express_disable_cache', false );

		$post_id = isset( $post->ID ) ? $post->ID : '';

		/**
		 * Filter the transient name for this page
		 *
		 * @param integer|string $post->ID            Post ID if found, else empy string
		 * @param integer        $shortcode_iteration The timeline number being displayed (1, 2, 3 etc.)
		 *
		 * @var string Filtered Timeline Express transient name
		 *
		 * @since 2.2.6
		 */
		$transient_name = (string) apply_filters( 'timeline_express_transient_name', $post_id . $shortcode_iteration, $post_id, $shortcode_iteration );

		/**
		 * Check if our transient is present, and use that
		 * if not, re-run our query and setup the transient
		 * @since 1.2
		 */
		if ( $disable_cache || $display_future || $display_past || WP_DEBUG || $filter_by_timelines || $filter_by_categories || false === ( $announcement_query = get_transient( 'timeline-express-query-' . $transient_name ) ) ) { // @codingStandardsIgnoreLine

			/* Setup the announcement args */
			$announcement_args = apply_filters( 'timeline_express_announcement_query_args', self::timeline_express_query_args( $compare_sign, $atts['order'], $atts ), $post, $atts );

			/* Run the query to retreive our announcements */
			$announcement_query = new WP_Query( $announcement_args );

			/* If either of our filtering options were found, do NOT set a new transient */
			if ( ! $filter_by_timelines && ! $filter_by_categories ) {

				/* Setup our transient, and store it for a full day - before running again */
				set_transient( 'timeline-express-query-' . $post_id . $shortcode_iteration, $announcement_query, 24 * HOUR_IN_SECONDS );

			} // @codingStandardsIgnoreLine

		}

		$announcement_query = apply_filters( 'timeline_express_announcement_query', $announcement_query );

		/**
		 * If the filters have been enabled, build and display the filters sections
		 * @since 1.2
		 */
		if ( ! empty( $atts['filter'] ) && 1 === (int) $atts['filter'] ) {

			self::generate_timeline_express_filters( $atts );

		}

		/* Loop over announcements, if found */
		if ( $announcement_query->have_posts() ) {

			?>

			<section id="cd-timeline" style="opacity: 0;" class="cd-container timeline-express <?php echo implode( ' ', $additional_classes ); ?>" <?php echo implode( ' ', $horizontal_html_atts ); ?>>

			<?php

			if ( $horizontal ) {

				?>

				<div class="navigation" style="opacity: 0;">

					<a id="prev" class="prev" href="#"></a>
					<a id="next" class="next" href="#"></a>

				</div>

				<div class="horizontal-timeline" style="opacity: 0;">

					<div class="background-line"></div>

				<?php

			}

			while ( $announcement_query->have_posts() ) {

				$announcement_query->the_post();

				get_timeline_express_template( 'timeline-container' );

			}

			if ( $horizontal ) {

				?>

				</div>

				<?php

			}

			?>

			</section>

			<?php

			// reset the post data
			wp_reset_postdata();

			// Action hook after timeline
			do_action( 'timeline-express-after-timeline', $atts, self::timeline_express_query_args( $compare_sign, $atts['order'], $atts ), $announcement_query );

		} else {

			/* Display the 'no events' message, setup in our options. */
			?>
				<h3 class="timeline-express-no-announcements-found">

					<?php echo apply_filters( 'timeline_express_no_announcements_found_message', esc_textarea( $timeline_express_options['no-events-message'] ) ); ?>

				</h3>
			<?php

		}// End if().

		/* Generate About Text */
		echo '<!-- ' . esc_html( self::timeline_express_about_comment() ) . ' -->';

		$shortcode = ob_get_contents();
		$shortcode_count++; // increment out shortcode counter

		ob_end_clean();
		return $shortcode;

	}

	/**
	 * Deregister the vertical timeline scripts and styles
	 *
	 * @since 1.3.6
	 */
	public function deregister_vertical_scripts() {

		wp_deregister_script( 'jquery-masonry' );

		wp_deregister_script( 'timeline-express-js-base' );

		wp_deregister_style( $this->css_base );

	}

	/**
	 * Register the horizontal timeline scripts and styles
	 *
	 * @since 1.3.6
	 */
	public function register_horizontal_scripts() {

		$script_dep = 'jquery';
		$mobile     = wp_is_mobile();

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( $this->css_base, TIMELINE_EXPRESS_URL . "lib/public/css/timeline-express-horizontal{$suffix}.css", array(), TIMELINE_EXPRESS_VERSION_CURRENT, 'all' );

		if ( $this->slider ) {

			$script_dep = 'carouFredSel';

			wp_enqueue_script( $script_dep, TIMELINE_EXPRESS_URL . "lib/public/js/carouFredSel{$suffix}.js", array( 'jquery' ), '6.2.1', true );

			if ( $mobile ) {

				wp_enqueue_script( 'touchSwipe', TIMELINE_EXPRESS_URL . 'lib/public/js/jquery.touchSwipe.min.js', array( $script_dep ), '1.3.3', true );

				$script_dep = 'touchSwipe';

			} // @codingStandardsIgnoreLine

		}

		wp_enqueue_script( $this->css_base, TIMELINE_EXPRESS_URL . "lib/public/js/timeline-express-horizontal{$suffix}.js", array( $script_dep ), TIMELINE_EXPRESS_VERSION_CURRENT, true );

		add_action( 'wp_enqueue_scripts', array( $this, self::timeline_express_print_inline_styles( timeline_express_get_options() ) ) );

	}

	/**
	 * Print the inlien styles for Timeline Express.
	 * These styles load the proper colors for Timeline Express containerss and background line.
	 * @param array $timeline_express_options Timeline Express options array.
	 * @since 1.2
	 */
	public function timeline_express_print_inline_styles( $timeline_express_options ) {

		$content_background    = ( '' === $timeline_express_options['announcement-bg-color'] ) ? 'transparent' : $timeline_express_options['announcement-bg-color'];
		$content_shadow        = ( '' === $timeline_express_options['announcement-box-shadow-color'] ) ? '0 3px 0 transparent' : '0 3px 0 ' . $timeline_express_options['announcement-box-shadow-color'];
		$background_line_color = ( '' === $timeline_express_options['announcement-background-line-color'] ) ? 'transparent' : $timeline_express_options['announcement-background-line-color'];

		/**
		 * Pagination container styles
		 * @since 1.3.2
		 */
		$pagination_bg         = isset( $timeline_express_options['pagination-bg-color'] ) ? ( '' === $timeline_express_options['pagination-bg-color'] ? 'transparent' : $timeline_express_options['pagination-bg-color'] ) : '#555555';
		$pagination_text_color = isset( $timeline_express_options['pagination-text-color'] ) ? ( '' === $timeline_express_options['pagination-text-color'] ? 'transparent' : $timeline_express_options['pagination-text-color'] ) : '#FFFFFF';
		$pagination_hover_bg   = isset( $timeline_express_options['pagination-hover-bg-color'] ) ? ( '' === $timeline_express_options['pagination-hover-bg-color'] ? 'transparent' : $timeline_express_options['pagination-hover-bg-color'] ) : '#F7A933';
		$pagination_hover_text = isset( $timeline_express_options['pagination-hover-text-color'] ) ? ( '' === $timeline_express_options['pagination-hover-text-color'] ? 'transparent' : $timeline_express_options['pagination-hover-text-color'] ) : '#FFFFFF';

		$timeline_express_styles = "
		.cd-timeline-block.timeline-announcement-left .cd-timeline-content::before {
			border-left-color: {$content_background};
		}
		.cd-timeline-block.timeline-announcement-right .cd-timeline-content::before {
			border-right-color: {$content_background};
		}
		#cd-timeline::before,
		#cd-timeline.horizontal-timeline .background-line {
			background: {$background_line_color};
		}
		#cd-timeline .cd-timeline-content {
			background: {$content_background};
			-webkit-box-shadow: {$content_shadow};
			-moz-box-shadow: {$content_shadow};
			box-shadow: {$content_shadow};
		}
		.te-pagination a,
		.te-pagination span.current {
			color: {$pagination_text_color};
		}
		.te-pagination span,
		.te-pagination a {
			background: {$pagination_bg};
		}
		.te-pagination a:hover,
		.te-pagination span.current {
			background: {$pagination_hover_bg};
		}
		#cd-timeline.horizontal-timeline .cd-timeline-content:after {
			border-bottom-color: {$content_background};
		}
		@media only screen and (max-width: 821px) {
				.cd-timeline-content::before {
					border-left-color: transparent;
					border-right-color: {$content_background};
				}
				.cd-timeline-block.timeline-announcement-left .cd-timeline-content::before {
					border-left-color: transparent;
				}
		}
		";

		wp_add_inline_style( $this->css_base, $timeline_express_styles );

	}

	/**
	 * Generate the timeline express filters
	 */
	public function generate_timeline_express_filters( $atts ) {

		get_timeline_express_template( 'timeline-express-filters', $atts );

	}

	/**
	 * Decide what compare sign should be used in the query arguments
	 * @param string $time_frame The time frame, defined on our settings page (possible values: 0, 1, 2).
	 * @return string $compare_sign Return the compare sign to be used.
	 */
	public function timeline_express_compare_sign( $time_frame ) {

		switch ( strtolower( $time_frame ) ) {

			default:
			case 'future':
			case '0':
				$compare_sign = '>=';

				break;

			case 'all':
			case '1':
				$compare_sign = '';

				break;

			case 'past':
			case '2':
				$compare_sign = '<';

				break;

		}

		return apply_filters( 'timeline_express_compare_sign', $compare_sign, get_the_ID() );

	}

	/**
	 * Setup the Timeline Express query
	 * @param string $compare_sign Compare sign to be used in the query arguments. Dictates the query to be used.
	 * @param string $display_order The display order set in the timeline express settings array.
	 * @return array $query_args Array of query arguments to be used.
	 */
	public function timeline_express_query_args( $compare_sign, $display_order, $shortcode_attributes ) {

		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

		/* Set up the announcement arguments */
		$announcement_args = array(
			'post_type'      => 'te_announcements',
			'meta_key'       => 'announcement_date',
			'orderby'        => array(
				'meta_value_num' => $display_order,
				'date'           => $display_order,
			),
			'order'          => $display_order,
			'posts_per_page' => $shortcode_attributes['limit'],
			'paged'          => $paged,
		);

		/**
		 * Allow users to specfiy certain events (or posts, pages etc. with the post types add-on)
		 * on the timeline by post ID.
		 *
		 * Since the shortcode atts can be filtered, we check if its set and not empty
		 * (ie: not false and not and empty string)
		 *
		 * @since next
		 */
		if ( isset( $shortcode_attributes['ids'] ) && ! empty( $shortcode_attributes['ids'] ) ) {

			$announcement_args['post__in'] = explode( ',', $shortcode_attributes['ids'] );

		}

		// If the compare sign equals ''
		if ( '' !== $compare_sign ) {

			$announcement_args['meta_query'] = array(
				array(
					'key'     => 'announcement_date',
					'value'   => strtotime( current_time( 'm/d/Y' ) ),
					'type'    => 'NUMERIC',
					'compare' => $compare_sign,
				),
			);

		}

		/**
		 * Fitlered on line 85
		 * filter name: timeline_express_announcement_query_args
		 */
		return $announcement_args;

	}

	/**
	 * Generate about text, to aid in debugging.
	 * @return string We're returning a comment block for the frontend.
	 */
	public function timeline_express_about_comment() {

		ob_start();

		echo 'Timeline Express Pro v' . esc_attr( TIMELINE_EXPRESS_VERSION_CURRENT );
		echo ' | Site: https://www.wp-timelineexpress.com';
		echo ' | Author: Code Parrots - http://www.codeparrots.com';

		return apply_filters( 'timeline_express_html_comment', ob_get_clean() );

	}

	/**
	 * Append additional classes to our container based on the announcement meta
	 * @param  string    $class             Initial class to add to our container (cd-container)
	 * @param  integer   $announcement_id   The announcement ID to retreive meta from
	 * @return string                       Imploded array of new classes to append to our container.
	 */
	public function timeline_express_announcement_container_classes( $class, $announcement_id ) {

		$options = timeline_express_get_options();

		$container_classes = array( $class );
		$announcement_obj  = get_post( $announcement_id );

		// Setup the date
		$announcement_date = ( get_post_meta( $announcement_id, 'announcement_date', true ) ) ? get_post_meta( $announcement_id, 'announcement_date', true ) : strtotime( $announcement_obj->post_date_gmt );

		// append the month
		$container_classes[] = strtolower( date_i18n( 'F', $announcement_date ) );

		// append the day
		$container_classes[] = date_i18n( 'd', $announcement_date );

		// append the year
		$container_classes[] = date_i18n( 'Y', $announcement_date );

		if ( isset( $options['legacy_support'] ) && $options['legacy_support'] ) {

			$container_classes[] = 'legacy-layout';

		}

		if ( defined( 'TIMELINE_EXPRESS_YEAR_ICONS' ) && TIMELINE_EXPRESS_YEAR_ICONS ) {

			$container_classes[] = 'year-icon';

		}

		// if the announcement has no announcement image
		if ( ! get_post_meta( $announcement_id, 'announcement_image_id', true ) ) {

			$container_classes[] = 'announcement-no-image';

		}

		// append the post ID
		$container_classes[] = 'announcement-' . $announcement_id;

		// append the custom classes if enabled
		if ( defined( 'TIMELINE_EXPRESS_CONTAINER_CLASSES' ) && TIMELINE_EXPRESS_CONTAINER_CLASSES ) {

			$container_classes[] = esc_textarea( get_post_meta( $announcement_id, 'announcement_container_classes', true ) );

		}

		// Append any taxonomy slugs (timeline/timeline_express_categories)
		// 'timeline_express_categories' taxonomy
		$timeline_express_taxonomy_array = array();

		$categories = wp_get_post_terms( $announcement_id, 'timeline_express_categories' );

		// if categories found, append them
		if ( ! empty( $categories ) ) {

			foreach ( $categories as $category ) {

				$container_classes[] = $category->slug;

			} // @codingStandardsIgnoreLine

		}

		// 'timeline' taxonomy

		$timelines = wp_get_post_terms( $announcement_id, 'timeline' );

		// if timelines found, append them
		if ( ! empty( $timelines ) ) {

			foreach ( $timelines as $timeline ) {

				$container_classes[] = $timeline->slug;

			} // @codingStandardsIgnoreLine

		}

		// return the array
		return implode( ' ', $container_classes );

	}

	/**
	 * Process the filters as they are passed in via the URL
	 * @param  array  $query_args     The default query arguments
	 * @param  object $post_obj       The post object for the current post
	 * @param  array  $shortcode_atts The shortcode attributes
	 * @return array                  New query arguments
	 */
	public function timeline_express_process_filters_filter_queries( $query_args, $post_obj, $shortcode_atts ) {

		$filter_by_timelines = ( isset( $_GET['timeline'] ) ) ? explode( ',', $_GET['timeline'] ) : false;

		$filter_by_categories = ( isset( $_GET['timeline-category'] ) ) ? explode( ',', $_GET['timeline-category'] ) : false;

		if ( ! $filter_by_timelines ) {

			if ( ! empty( $shortcode_atts['timeline'] ) ) {

				$filter_by_timelines = timeline_express_tax_array( 'timelines', $shortcode_atts['timeline'] );

			} // @codingStandardsIgnoreLine

		}

		if ( ! $filter_by_categories ) {

			if ( ! empty( $shortcode_atts['categories'] ) ) {

				$filter_by_categories = timeline_express_tax_array( 'categories', $shortcode_atts['categories'] );

			} // @codingStandardsIgnoreLine

		}

		/**
		 * If either are set, delete the transients so we can properly query
		 * and display the posts, as intended
		 * @since 1.2
		 */
		if ( $filter_by_timelines || $filter_by_categories ) {

			delete_timeline_express_transients( $post_obj->ID );

		}

		// Check for timeline filter
		if ( $filter_by_timelines ) {

			$query_args['tax_query'][] = array(
				array(
					'taxonomy' => 'timeline',
					'field'    => 'term_id',
					'terms'    => $filter_by_timelines,
				),
			);

		}

		// Check for category filter
		if ( $filter_by_categories ) {

			$query_args['tax_query'][] = array(
				array(
					'taxonomy' => 'timeline_express_categories',
					'field'    => 'term_id',
					'terms'    => $filter_by_categories,
				),
			);

		}

		return $query_args;

	}

	/**
	 * Generate the Timeline Express pagination links
	 *
	 * @param  array   $shortcode_atts The shortcode attributes used.
	 * @param  array   $query_args     WP Query arguments.
	 * @param  integer $found_posts    Number of posts found in the query.
	 *
	 * @return mixed                  HTML markup for the pagination.
	 *
	 * @since 1.3.2
	 */
	public function timeline_express_render_pagination( $shortcode_atts, $query_args, $query_results ) {

		if ( ! $shortcode_atts['pagination'] ) {

			return;

		}

		if ( file_exists( get_stylesheet_directory() . '/timeline-express/timeline-express-pagination.php' ) ) {

			include( get_stylesheet_directory() . '/timeline-express/timeline-express-pagination.php' );

			return;

		}

		include( TIMELINE_EXPRESS_PATH . 'lib/public/partials/timeline-express-pagination.php' );

	}

	/**
	 * Disable the cache when the option is set to 0
	 *
	 * @since 2.2.5
	 *
	 * @return boolean True when cache is disabled, else false.
	 */
	public function disable_cache() {

		return ( '0' === get_option( 'timeline_express_cache_enabled', 1 ) );

	}

}
