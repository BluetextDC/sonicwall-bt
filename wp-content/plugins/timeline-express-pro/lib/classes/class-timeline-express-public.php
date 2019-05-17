<?php
/**
 * Timeline Express :: Admin Class
 * By Code Parrots
 * @link http://www.codeparrots.com
 *
 * @package WordPress
 * @subpackage Component
 * @since 1.2
 **/
class Timeline_Express_Public {
	/**
	 * Main constructor
	 */
	public function __construct() {

		/* Include the Timeline Express Init class */
		include TIMELINE_EXPRESS_PATH . 'lib/classes/class-timeline-express-initialize.php';

		include_once TIMELINE_EXPRESS_PATH . 'lib/classes/class-timeline-express-hooks.php';

		/* Define our [timeline-express] shortcode, so it's usable on the frontend */
		add_shortcode( 'timeline-express', array( $this, 'process_timeline_express_shortcode' ) );

		/* load a custom page template (override the default) */
		add_filter( 'single_template', array( $this, 'timeline_express_announcement_single_page_template' ) );

		/* Enqueue single announcement template styles */
		add_action( 'wp_enqueue_scripts', array( $this, 'timeline_express_single_template_styles' ) );

	}

	/**
	 * Define and process [timeline-express] shortcode
	 *
	 * @param array $atts array of shortcode attributes.
	 * @since  1.0
	 */
	public function process_timeline_express_shortcode( $atts ) {

		$options = timeline_express_get_options();

		$shortcode_atts = array(
			'limit'          => -1,
			'order'          => $options['announcement-display-order'],
			'display'        => $options['announcement-time-frame'],
			'filter'         => false,
			'categories'     => false,
			'timeline'       => false,
			'pagination'     => false,
			'horizontal'     => false,
			'slider'         => true,
			'items'          => 2,
			'slide_distance' => 1,
			'ids'            => false,
		);

		/* Parse the shortcode attributes */
		$atts = shortcode_atts( $shortcode_atts, $atts, 'timeline-express' );

		$timeline_express_init = new Timeline_Express_Initialize();

		return $timeline_express_init->generate_timeline_express( $options, $atts );

	}

	/**
	 * Decide what template file to use to display single announcements.
	 *  1. First checks for a directory in the theme root /timeline-express/single-te_announcements.php,
	 *  2. Else it will use the default single post template.
	 *
	 * @param  string $page_template The page template name to be used for single announcements.
	 *
	 * @return string                The page template to be used for the single announcements.
	 *
	 * @since 1.2
	 */
	public function timeline_express_announcement_single_page_template( $single_template ) {

		global $post;

		if ( ! isset( $post->post_type ) || 'te_announcements' !== $post->post_type ) {

			return $single_template;

		}

		// Legacy support for old the old template.
		if ( defined( 'TIMELINE_EXPRESS_LEGACY_SINGLE_TEMPLATE' ) && TIMELINE_EXPRESS_LEGACY_SINGLE_TEMPLATE ) {

			$single_template = TIMELINE_EXPRESS_PATH . 'lib/public/partials/single-timeline-express.php';

		}

		/* If custom template file exists */
		if ( file_exists( get_stylesheet_directory() . '/timeline-express/single-timeline-express.php' ) ) {

			$single_template = get_stylesheet_directory() . '/timeline-express/single-timeline-express.php';

			if ( ! defined( 'TIMELINE_EXPRESS_LEGACY_SINGLE_TEMPLATE' ) ) {

				define( 'TIMELINE_EXPRESS_LEGACY_SINGLE_TEMPLATE', true );

			} // @codingStandardsIgnoreLine

		}

		if ( ! defined( 'TIMELINE_EXPRESS_LEGACY_SINGLE_TEMPLATE' ) || ( defined( 'TIMELINE_EXPRESS_LEGACY_SINGLE_TEMPLATE' ) && ! TIMELINE_EXPRESS_LEGACY_SINGLE_TEMPLATE ) ) {

			// Announcement Date.
			add_filter( 'the_date', array( $this, 'timeline_express_filter_single_post_date' ), 10, 4 );

			// Filter the modified dates - for themes.
			add_filter( 'get_the_modified_date', array( $this, 'timeline_express_filter_single_modified_date' ), 10, 3 );
			add_filter( 'get_the_date', array( $this, 'timeline_express_filter_single_modified_date' ), 10, 3 );

			// Filter the title, display the categories/timelines.
			add_filter( 'the_title', array( $this, 'timeline_express_taxonomies' ), 10, 2 );

			// Announcement Featured Image.
			add_filter( 'get_post_metadata', array( $this, 'timeline_express_filter_featured_image' ), 10, 4 );

			// Announcement Video Featured Image.
			add_filter( 'post_thumbnail_html', array( $this, 'timeline_express_filter_featured_image_video' ), 10, 5 );

			// Announcement Categories.
			add_filter( 'the_category_list', array( $this, 'timeline_express_filter_categories' ), 10, 2 );

			$options = timeline_express_get_options();

			if ( isset( $options['timeline_sidebar'] ) && $options['timeline_sidebar'] ) {

				global $wp_registered_sidebars;

				/**
				 * The sidebar ID to hijack and display the Timeline Express sidebar
				 * in it's place.
				 *
				 * @since 2.1.0
				 */
				$sidebar_target = (string) apply_filters( 'timeline_express_sidebar_id', key( $wp_registered_sidebars ) );

				// Single Timeline Express Announcement Sidebar Widgets.
				ob_start();
				dynamic_sidebar( 'timeline-express' );
				$sidebar = ob_get_contents();
				ob_get_clean();

				// @codingStandardsIgnoreStart
				add_action( 'dynamic_sidebar', function( $index ) use ( $sidebar, $sidebar_target ) {

					$parent_widget_id = timeline_express_get_widget_sidebar( $index['id'] );

					if ( ! $parent_widget_id || $parent_widget_id !== $sidebar_target ) {

						return;

					}

					echo $sidebar; // XSS ok.

				} );

				/**
				 * Note: We hijack the first widget that appears on the page.
				 * This could very well be a widget in the header, or a pre-content
				 * widget. If that is the case
				 */
				add_filter( 'widget_display_callback', function( $i, $t, $a ) use ( $sidebar_target ) {

					if ( $sidebar_target !== $a['id'] ) {

						return $i;

					}

					return false;

				}, 10, 3 );
				// @codingStandardsIgnoreEnd

			} // @codingStandardsIgnoreLine

		}

		/**
		 * Return our template, passed through filters
		 * Legacy Support, 2 filters
		 */
		return apply_filters( 'timeline_express_single_page_template', apply_filters( 'timeline-express-single-page-template', $single_template ) );

	}

	/**
	 * Filter the sigle timeline express post date.
	 *
	 * @since 2.1.0
	 *
	 * @param string $the_date The formatted date string.
	 * @param string $d        PHP date format. Defaults to 'date_format' option
	 *                         if not specified.
	 * @param string $before   HTML output before the date.
	 * @param string $after    HTML output after the date.
	 *
	 * @return string Timeline Express announcement date.
	 */
	public function timeline_express_filter_single_post_date( $the_date, $d, $before, $after ) {

		global $post;

		return timeline_express_get_announcement_date( $post->ID );

	}

	/**
	 * Filter the sigle timeline express modified date.
	 *
	 * @since 2.1.0
	 *
	 * @param string|bool  $the_time The formatted date or false if no post is found.
	 * @param string       $d        PHP date format. Defaults to value specified in
	 *                               'date_format' option.
	 * @param WP_Post|null $post     WP_Post object or null if no post is found.
	 *
	 * @return string Timeline Express announcement date.
	 */
	public function timeline_express_filter_single_modified_date( $the_time, $d, $post ) {

		return timeline_express_get_announcement_date( $post->ID );

	}

	public function timeline_express_taxonomies( $title, $post_id ) {

		if ( get_the_ID() !== $post_id || ! in_the_loop() || ! is_singular( 'te_announcements' ) ) {

			return $title;

		}

		// Categories
		ob_start();
		timeline_express_tax_links( 'timeline_express_categories', __( 'Categories:', 'timeline-express-pro' ) );
		$categories = ob_get_contents();
		ob_get_clean();

		// Timelines
		ob_start();
		timeline_express_tax_links( 'timeline', __( 'Timelines:', 'timeline-express-pro' ) );
		$timelines = ob_get_contents();
		ob_get_clean();

		return $title . $categories . $timelines;

	}

	/**
	 * Filters the post thumbnail HTML.
	 *
	 * @since 2.0.5
	 *
	 * @param null|array|string $value    The value get_metadata() should return a single metadata value, or an
	 *                                    array of values.
	 * @param int               $post_id  Post ID.
	 * @param string            $meta_key Meta key.
	 * @param string|array      $single   Meta value, or an array of values.
	 * @return array|null|string The attachment metadata value, array of values, or null.
	 */
	public function timeline_express_filter_featured_image( $value, $post_id, $meta_key, $single ) {

		// Only filter if we're not recursing and if it is a post thumbnail ID
		if ( '_thumbnail_id' !== $meta_key ) {

			return $value;

		}

		return (int) get_post_meta( $post_id, 'announcement_image_id', true );

	}

	/**
	 * Display the YouTube/Vimeo video in the featured image location.
	 *
	 * @param string       $html              The post thumbnail HTML.
	 * @param int          $post_id           The post ID.
	 * @param string       $post_thumbnail_id The post thumbnail ID.
	 * @param string|array $size              The post thumbnail size. Image size or array of width and height
	 *                                        values (in that order). Default 'post-thumbnail'.
	 * @param string       $attr              Query string of attributes.
	 *
	 * @return mixed Markup for the video featured image.
	 */
	function timeline_express_filter_featured_image_video( $html, $post_id, $post_thumbnail_id, $size, $attr ) {

		/**
		 * PHP_INT_MAX === non-image featured image
		 * eg: YouTube/Vimeo URL
		 */
		if ( PHP_INT_MAX !== $post_thumbnail_id ) {

			return $html;

		}

		ob_start();
		timeline_express_get_announcement_image( $post_id );
		$image = ob_get_contents();
		ob_get_clean();

		return $image;

	}


	/**
	 * Filter the Timeline Express announcement categories.
	 * Resource: https://github.com/WordPress/WordPress/blob/39c7daf7db151eee6375974aceb8520fa85a822d/wp-includes/category-template.php#L148
	 *
	 * @filter the_category_list
	 *
	 * @param array    $categories An array of the post's categories.
	 * @param int|bool $post_id    ID of the post we're retrieving categories for. When `false`, we assume the
	 *                             current post in the loop.
	 *
	 * @return array Array of announcement categories.
	 */
	public function timeline_express_filter_categories( $categories, $post_id ) {

		return timeline_express_tax_array( 'categories', 'all' );

	}

	/**
	 * Filter the content, and load our template in it's place.
	 * @param array  $the_content The page content to filter.
	 * @return array The single page content to display for this announcement.
	 * @since  1.0
	 */
	public function timeline_express_single_page_content( $the_content ) {

		global $post;

		$post_id = ( isset( $post->ID ) ) ? $post->ID : '';

		// When this is not a single post, or it is and it isn't an announcement, abort
		if ( ! is_single() || 'te_announcements' !== $post->post_type ) {

			return $the_content;

		}

		ob_start();

		/* Store the announcement content from the WYSIWYG editor */
		$announcement_content = $the_content;

		/* Include the single template */
		get_timeline_express_template( 'single-announcement' );

		/* Return the output buffering */
		$the_content = ob_get_clean();

		/* Return announcement meta & append the announcement content */
		return apply_filters( 'timeline_express_single_content', $the_content . $announcement_content, $post_id );

	}

	/**
	 * Enqueue styles on single announcement templates.
	 * @return null
	 * @since 1.2
	 */
	public function timeline_express_single_template_styles() {

		global $post;

		// When this is not a single post, or it is but is not an announcement
		if ( ! is_single() || 'te_announcements' !== $post->post_type ) {

			return;

		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'single-timeline-express-styles', TIMELINE_EXPRESS_URL . "lib/public/css/timeline-express-single-page{$suffix}.css", array(), 'all' );

	} /* Final Function */

}
$timeline_express_public = new Timeline_Express_Public();
