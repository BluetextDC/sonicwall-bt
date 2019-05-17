<?php
/**
 * Timeline Express Helper Functions
 * By Code Parrots
 *
 * @link http://www.codeparrots.com
 *
 * @package WordPress
 * @subpackage Component
 * @since 1.2
 */

/**
 * Custom CMB2 callback and sanitization functions
 *
 * @since 1.2
 */

/* Render content in the timeline express addon advertisments metabox */
add_action( 'cmb2_render_te_advert_metabox', 'cmb2_render_callback_te_advert_metabox', 10, 5 );

/* Render content in the help & doc metabox */
add_action( 'cmb2_render_te_help_docs_metabox', 'cmb2_render_callback_te_help_docs_metabox', 10, 5 );

/* Render custom bootstrap icons dropdown field */
add_action( 'cmb2_render_te_bootstrap_dropdown', 'cmb2_render_callback_te_bootstrap_dropdown', 10, 5 );

/* Sanitize custom bootstrap icons dropdown field */
add_filter( 'cmb2_sanitize_te_bootstrap_dropdown', 'cmb2_validate_te_bootstrap_dropdown_callback', 10, 2 );

/**
 * Output the Start and End content wrappers on the single timeline express template
 * @since 1.2.8.5
 */
add_action( 'timeline_express_before_main_content', 'timeline_express_generate_page_wrapper_start', 10 );
add_action( 'timeline_express_after_main_content', 'timeline_express_generate_page_wrapper_end', 10 );

/**
 * Display the comment field, if enabled
 * @since 2.0.0
 */
add_action( 'timeline_express_after_main_content', 'timeline_express_comments', 10 );

/**
 * Output the Timeline Express Sidebar on the single announcement template
 * @since 1.2.8.5
 */
add_action( 'timeline_express_sidebar', 'timeline_express_generate_sidebar', 10 );

/**
 * Retreive plugin settings from the database
 *
 * @since 1.2
 * @return plugin options or defaults if not set
 */
function timeline_express_get_options( $option = '' ) {

	$option_defaults = array(
		'announcement-time-frame'                     => '1',
		'announcement-display-order'                  => 'ASC',
		'excerpt-trim-length'                         => 250,
		'excerpt-random-length'                       => 0,
		'legacy_support'                              => false,
		'enable_comments'                             => false,
		'date-visibility'                             => '1',
		'read-more-visibility'                        => '1',
		'default-announcement-icon'                   => 'exclamation-triangle',
		'default-announcement-color'                  => '#75CE66',
		'announcement-box-shadow-color'               => '#B9C5CD',
		'announcement-background-line-color'          => '#D7E4ED',
		'announcement-bg-color'                       => '#EFEFEF',
		'pagination-bg-color'                         => '#555555',
		'pagination-text-color'                       => '#FFFFFF',
		'pagination-hover-bg-color'                   => '#F7A933',
		'pagination-hover-text-color'                 => '#FFFFFF',
		'no-events-message'                           => esc_html__( 'No announcements found', 'timeline-express-pro' ),
		'announcement-appear-in-searches'             => 'true',
		'disable-animation'                           => 0,
		'delete-announcement-posts-on-uninstallation' => 0,
		'version'                                     => TIMELINE_EXPRESS_VERSION_CURRENT,
		'timeline_sidebar'                            => false,
	);

	$options = apply_filters( 'timeline_express_options', get_option( TIMELINE_EXPRESS_OPTION, $option_defaults ) );

	return ! empty( $option ) ? ( isset( $options[ $option ] ) ? $options[ $option ] : false ) : $options;

}

/**
 * Generate our metaboxes to assign to our announcements
 *
 * @since 1.2
 */
function timeline_express_announcement_metaboxes() {

	require_once TIMELINE_EXPRESS_PATH . 'lib/admin/metaboxes/metaboxes.announcements.php';

}

/**
 * CMB2 Specific functions
 * Render and sanitize our metaboxes
 * (note: individual custom metaboxes are defined inside of /admin/metaboxes/partials/)
 *
 * @since 1.2
 */

/**
 * Function cmb_render_te_bootstrap_dropdown()
 * Render the custom bootstrap dropdown
 *
 * @param int        $field field to render.
 * @param int/string $escaped_value stored value for this field.
 * @since v1.1.5.7
 */
function cmb2_render_callback_te_bootstrap_dropdown( $field, $escaped_value ) {

	timeline_express_build_bootstrap_icon_dropdown( $field, $escaped_value );

}

/**
 * Render the custom 'Advertisment' metabox.
 *
 * @param int        $field field to render.
 * @param int/string $meta stored value for this field.
 * @param type       $object_id this specific fields id.
 * @param type       $object_type the type for this field.
 * @param type       $field_type_object the entire field object.
 *
 * @since v1.1.5
 */
function cmb2_render_callback_te_advert_metabox( $field, $meta, $object_id, $object_type, $field_type_object ) {

	include_once( TIMELINE_EXPRESS_PATH . 'lib/admin/metaboxes/partials/advertisment-metabox.php' );

}

/**
 * Render the custom 'Help & Documentation' metabox
 *
 * @param int        $field field to render.
 * @param int/string $meta stored value for this field.
 * @param type       $object_id this specific fields id.
 * @param type       $object_type the type for this field.
 * @param type       $field_type_object the entire field object.
 *
 * @since v1.1.5
 */
function cmb2_render_callback_te_help_docs_metabox( $field, $meta, $object_id, $object_type, $field_type_object ) {

	include_once( TIMELINE_EXPRESS_PATH . 'lib/admin/metaboxes/partials/help-docs-metabox.php' );

}

/**
 * Custom sanitization function for our custom time stamp field.
 *
 * @param string $override_value -.
 * @param string $value new icon value to store in the database.
 *
 * @since @v1.1.5
 */
function cmb2_validate_te_bootstrap_dropdown_callback( $override_value, $value ) {

	if ( isset( $value ) && ! empty( $value ) ) {

		return apply_filters( 'timeline_express_bootstrap_dropdown_icon_value', 'fa-' . $value );

	}

	return '';

}

/**
 * Enqueue Font Awesome from netdna CDN if accessible.
 * if not, load from a local copy.
 *
 * @since v1.1.5.7
 */
function timeline_express_enqueue_font_awesome() {

	$local_font_awesome = ( ! defined( 'TIMELINE_EXPRESS_FONT_AWESOME_LOCAL' ) || ( defined( 'TIMELINE_EXPRESS_FONT_AWESOME_LOCAL' ) && TIMELINE_EXPRESS_FONT_AWESOME_LOCAL ) ) ? true : false;

	if ( $local_font_awesome ) {

		/* If not, load the local version */
		wp_enqueue_style( 'font-awesome', TIMELINE_EXPRESS_URL . 'lib/icons/css/font-awesome.min.css', array(), '4.7.0' );

		return;

	}

	$font_awesome_version = apply_filters( 'timeline_express_font_awesome_version', '4.7.0' );

	$http = ( is_ssl() ) ? 'https:' : 'http:';

	/* Enqueue font awesome for use in column display */
	wp_enqueue_style( 'font-awesome', $http . '//netdna.bootstrapcdn.com/font-awesome/' . $font_awesome_version . '/css/font-awesome.min.css', array(), $font_awesome_version );

}

/**
 * Construct a dropdown for our bootstrap icons.
 *
 * @param string $field the field type being displayed.
 * @param string $meta the stored value in the database.
 *
 * @since v1.1.5.7
 */
function timeline_express_build_bootstrap_icon_dropdown( $field, $meta ) {

	$screen = get_current_screen();

	$screen_base = $screen->base;

	$http = ( is_ssl() ) ? 'https:' : 'http:';

	$font_awesome_version = apply_filters( 'timeline_express_font_awesome_version', '4.7.0' );

	delete_transient( 'te_font_awesome_transient' );

	// Store our response in a transient for faster page loading.
	if ( false === ( $response = get_transient( 'te_font_awesome_transient' ) ) ) { // @codingStandardsIgnoreLine

		// Retreive the icons out of the css file.
		$response = wp_remote_get( $http . '//netdna.bootstrapcdn.com/font-awesome/' . $font_awesome_version . '/css/font-awesome.css' );

		if ( is_wp_error( $response ) ) {

			// Load font awesome locally.
			$response = wp_remote_get( TIMELINE_EXPRESS_URL . 'lib/icons/css/font-awesome.css' );

		}

		// It wasn't there, so regenerate the data and save the transient.
		set_transient( 'te_font_awesome_transient', $response, 12 * HOUR_IN_SECONDS );

	}

	/* If the response body is empty, abort */
	if ( empty( $response['body'] ) || ! isset( $response['body'] ) ) {

		return printf( '<em>' . esc_html__( 'There was an error processing the bootstrap icons.', 'timeline-express-pro' ) . '</em>' );

	}

	// Extract the icons from the stylesheet
	$pattern = '/(\.(?:fa-(?:\w+(?:-)?)+):before(?:,\s*\.(?:fa-(?:\w+(?:-)?)+):before)*)\s*{\s*content:\s*"(.+)";\s*}/';

	$sub_pattern = '/(fa-.+?):before/';

	preg_match_all( $pattern, $response['body'], $matches, PREG_SET_ORDER );

	$icons = array();

	foreach ( $matches as $match ) {

		preg_match_all( $sub_pattern, $match[1], $sub_matches, PREG_SET_ORDER );

		foreach ( $sub_matches as $sub_match ) {

			$icons[ str_replace( 'fa-', '', $sub_match[1] ) ] = $match[2];

		} // @codingStandardsIgnoreLine

	}

	/**
	 * Filter the icons, allowing us to pass in custom icons
	 */
	$icons = apply_filters( 'timeline_express_announcement_icons', $icons );

	?>

	<script>
	jQuery( document ).ready( function() {

		jQuery('.selectpicker').selectpicker({
			style: 'btn-info',
			size: 6
		});

	});
	</script>

	<style>
		.dropdown-toggle { background: transparent !important; border: 1px solid rgb(201, 201, 201) !important; }
		.dropdown-toggle .caret { border-top-color: #333 !important; }
		.ui-datepicker-prev:hover, .ui-datepicker-next:hover { cursor: pointer; }
	</style>

	<?php
	// Check which page were on, set name appropriately.
	if ( isset( $field->args['id'] ) ) {

		$field_name = $field->args['id'];

	} else {

		$field_name = esc_attr( $field['id'] );

	}

	?>

	<!-- start the font awesome icon select -->
	<select class="selectpicker" name="<?php echo esc_attr( $field_name ); ?>" id="default-announcement-icon" name="<?php echo esc_attr( $field_name ); ?>">

		<?php

		// Sort icons alphabetically
		ksort( $icons );

		foreach ( $icons as $icon_name => $icon_content ) {

			$icon_font_base = apply_filters( 'timeline_express_icon_dropdown_font_base', 'fa', $icon_name );
			$icon_name      = apply_filters( 'timeline_express_icon_dropdown_font_name', $icon_name );

			?>

			<option class="<?php echo esc_attr( $icon_font_base ); ?>" data-icon="<?php echo esc_attr( $icon_font_base ); ?>-<?php echo esc_attr( $icon_name ); ?>" <?php selected( $icon_font_base . '-' . esc_attr( $icon_name ), $meta ); ?>>
				<?php echo esc_html( $icon_name ); ?>
			</option>

		<?php } ?>

	</select>
	<!-- end select -->

	<?php
	if ( 'te_announcements_page_timeline-express-settings' !== $screen_base ) {

		echo '<p class="cmb2-metabox-description">' . esc_html( $field->args['desc'] ) . '</p>';

	}

}

/**
 * Include a Timeline Express template
 * @param  string $template_name Template name to load
 *
 * @return null                Include the template needed
 *
 * @since 1.2
 */
function get_timeline_express_template( $template_name = 'timeline-container', $atts = false ) {

	/**
	 * Switch over the template name, return template
	 * - Check if a file exists locally (theme root), and load it.
	 * - Note: Users can create a directory (timeline-express), and copy over the announcement template into the theme root.
	 */
	switch ( $template_name ) {

		default:
		case 'timeline-container':
			$file_name = 'timeline-express-container';
			break;

		case 'single-announcement':
			$file_name = 'single-timeline-express-content';
			break;

		case 'timeline-express-filters':
			$file_name = 'timeline-express-filters';
			break;
		case 'page-wrappers-start':
			$file_name = 'timeline-express-page-wrappers-start';
			break;

		case 'page-wrappers-end':
			$file_name = 'timeline-express-page-wrappers-end';
			break;

		case 'timeline-express-sidebar':
			$file_name = 'timeline-express-sidebar';
			break;

	}

	// check for and load file
	if ( file_exists( get_stylesheet_directory() . '/timeline-express/' . $file_name . '.php' ) ) {

		include( get_stylesheet_directory() . '/timeline-express/' . $file_name . '.php' );

		return;

	}

	include( TIMELINE_EXPRESS_PATH . 'lib/public/partials/' . $file_name . '.php' );

}

/**
 * Helper function to retreive the timeline express single announcement templates
 * This is redundant, but will be easier for our users to integrate into their themes
 */
function timeline_express_content() {

	// check for and load file
	if ( file_exists( get_stylesheet_directory() . '/timeline-express/single-timeline-express-content.php' ) ) {

		include( get_stylesheet_directory() . '/timeline-express/single-timeline-express-content.php' );

		return;

	}

	include( TIMELINE_EXPRESS_PATH . 'lib/public/partials/single-timeline-express-content.php' );

}

/**
 * Helper function used to clear out the timeline express transients
 * This is fired when the settings are saved, and when an announcement is updated/published
 *
 * @param integer $page_id The page ID to delete transients for
 *
 * @since 1.2
 */
function delete_timeline_express_transients( $page_id = false ) {

	$query_transient = $page_id ? "timeline-express-query-{$page_id}" : 'timeline-express-query';

	global $wpdb;

	$query_transient_results = $wpdb->get_results( $wpdb->prepare( "SELECT * from `{$wpdb->prefix}options` WHERE option_name LIKE %s;", '%' . $wpdb->esc_like( $query_transient ) . '%' ) );

	if ( ! $query_transient_results && ! empty( $query_transient_results ) ) {

		return;

	}

	foreach ( $query_transient_results as $transient ) {

		delete_transient( str_replace( '_transient_', '', $transient->option_name ) );

	}

}

/**
 * Delete a style transients from the databse.
 *
 * @param  boolean $page_id (optional) The announcement ID to delete from.
 *
 * @since 2.0.0
 */
function delete_timeline_express_styles_transient( $page_id = false ) {

	$styles_query_transient = 'timeline_express_styles_announcement' . ( $page_id ? "_{$page_id}" : '' );

	global $wpdb;

	$styles_transient_results = $wpdb->get_results( $wpdb->prepare( "SELECT * from `{$wpdb->prefix}options` WHERE option_name LIKE %s;", '%' . $wpdb->esc_like( $styles_query_transient ) . '%' ) );

	if ( ! $styles_transient_results && ! empty( $styles_transient_results ) ) {

		return;

	}

	foreach ( $styles_transient_results as $transient ) {

		delete_site_transient( str_replace( '_site_transient_', '', $transient->option_name ) );

	}

}

/**
 * Check if our Timeline Express Init class exists
 * if it does not, include our class file.
 */
function does_timeline_express_init_class_exist() {

	if ( class_exists( 'Timeline_Express_Initialize' ) ) {

		return;

	}

	include TIMELINE_EXPRESS_PATH . 'lib/classes/class-timeline-express-initialize.php';

}

/**
 * Get the full icon HTML markup
 * @param  int $post_id The announcement ID to retreive the icon from
 * @return string       The HTML markup to return
 */
function timeline_express_get_announcement_icon_markup( $post_id, $link = true ) {

	$timeline_express_options = timeline_express_get_options();

	$custom_icon_html = apply_filters( 'timeline_express_custom_icon_html', apply_filters( 'timeline-express-custom-icon-html', false, $post_id, $timeline_express_options ), $post_id, $timeline_express_options );

	$icon_container_class = ' icon-no-readmore';

	$hide_read_more = get_post_meta( $post_id, '_timeline_styles_hide_read_more', true );

	/* Generate the Icon */
	if ( $custom_icon_html ) {

		return $custom_icon_html;

	}

	/* If read more visibility is set to true, wrap the icon in a link. */
	if ( te_is_readmore_visible( get_post( $post_id ) ) && ! $hide_read_more && '1' === $timeline_express_options['read-more-visibility'] && $link ) {

		$icon_container_class = '';

		?>

		<a class="cd-timeline-icon-link" href="<?php echo esc_attr( apply_filters( 'timeline_express_announcement_permalink', get_the_permalink( $post_id ), $post_id ) ); ?>">

		<?php

	}

	?>

		<div class="cd-timeline-img cd-picture<?php echo esc_attr( $icon_container_class ); ?>" style="background:<?php echo esc_attr( timeline_express_get_announcement_icon_color( $post_id ) ); ?>;">

			<!-- Custom Action Hook -->

			<?php if ( defined( 'TIMELINE_EXPRESS_YEAR_ICONS' ) && TIMELINE_EXPRESS_YEAR_ICONS ) { ?>

				<!-- Year Icons Markup -->

				<span class="year">

					<strong>

						<?php
						$date = date_i18n( 'Y', timeline_express_get_announcement_date_timestamp( $post_id ) );

						echo esc_html( apply_filters( 'timeline_express_frontend_year_icons', $date, timeline_express_get_announcement_date_timestamp( $post_id ) ) );
						?>

					</strong>

				</span>

			<?php } else { ?>

				<!-- Standard Font Awesome Icon -->

				<span class="fa <?php echo esc_attr( timeline_express_get_announcement_icon( $post_id ) ); ?>" title="<?php echo esc_attr( get_the_title( $post_id ) ); ?>"></span>

			<?php } ?>

		</div> <!-- cd-timeline-img -->
	<?php

	/* If read more visibility is set to true, wrap the icon in a link. */
	if ( '1' === $timeline_express_options['read-more-visibility'] && $link ) {

		?>

		</a>

		<?php

	}

}

/**
 * Get the announcement icon chosen in the dropdown
 *
 * @param  int      $post_id    The announcement ID to retreive the icon from
 *
 * @return string               The announcement icon to use
 */
function timeline_express_get_announcement_icon( $post_id ) {

	return apply_filters( 'timeline_express_icon', get_post_meta( $post_id, 'announcement_icon', true ), $post_id );

}

/**
 * Get the announcement color chosen on the announcement edit page
 *
 * @param  int     $post_id   The announcement ID to retreive the color from
 *
 * @return string             The announcement color to use behind the icon
 */
function timeline_express_get_announcement_icon_color( $post_id ) {

	return apply_filters( 'timeline_express_icon_color', get_post_meta( $post_id, 'announcement_color', true ), $post_id );

}

/**
 * Retreive the timeline express announcement image
 *
 * @param  int     $post_id     The announcement (post) ID whos image you want to retreive.
 * @param  string  $image_size  (optional) The image size to retreive.
 *
 * @return mixed                Announcement image markup.
 */
function timeline_express_get_announcement_image( $post_id, $image_size = 'timeline-express' ) {

	if ( ! get_post_meta( $post_id, 'announcement_image_id', true ) && ! get_post_meta( $post_id, 'announcement_image', true ) ) {

		return;

	}

	/**
	 * Filter the announcement image, allow users to set this to false to short circuit the announcement image.
	 *
	 * @var bool|string
	 */
	$image = apply_filters( 'timeline-express-announcement-img', ( get_post_meta( $post_id, 'announcement_image_id', true ) ) ? (int) get_post_meta( $post_id, 'announcement_image_id', true ) : get_post_meta( $post_id, 'announcement_image', true ), $post_id );

	if ( empty( $image ) ) {

		return;

	}

	/**
	 * Filter the announcement image size
	 *
	 * @var string
	 */
	$image_size = (string) apply_filters( 'timeline-express-announcement-img-size', $image_size, $post_id );

	if ( ! wp_attachment_is_image( get_post_meta( $post_id, 'announcement_image_id', true ) ) ) {

		/**
		 * Allow users to filter the video banner atts.
		 *
		 * This allows people to set things to autoload, specify a video banner etc.
		 *
		 * @param array            The original video options.
		 * @param integer $post_id The announcement ID.
		 */
		$src = esc_url( get_post_meta( $post_id, 'announcement_image', true ) );

		// @codingStandardsIgnoreStart
		echo '<div class="announcement-banner" data-src="' . $src . '">' . wp_video_shortcode(
			(array) apply_filters(
				'timeline_express_video_banner_atts', [
					'src' => $src,
				], $post_id
			)
		) . '</div>';
		// @codingStandardsIgnoreEnd

		return;

	}

	/**
	* If on a single page announcement, return the srcset image - for proper responsive images
	* @since 1.2.7
	*/
	if ( is_single() ) {

		$img_src = is_integer( $image ) ? wp_get_attachment_image_url( $image, $image_size ) : $image;

		$image_attributes = array(
			'class' => 'announcement-banner-image',
			'src'   => esc_url( $img_src ),
			'sizes' => '(max-width: 100%) 75vw, 680px',
			'alt'   => get_the_title(),
		);

		$img_srcset = wp_get_attachment_image_srcset( get_post_meta( $post_id, 'announcement_image_id', true ), $image_size );

		if ( $img_srcset ) {

			$image_attributes['srcset'] = $img_srcset;

		}

		?>

		<img <?php echo timeline_express_map_html_attributes( $image_attributes ); ?>>

		<?php

		return;

	}

	$attachment = wp_get_attachment_image(
		get_post_meta( $post_id, 'announcement_image_id', true ),
		$image_size,
		false,
		array(
			'alt'   => esc_attr( get_the_title() ),
			'class' => 'announcement-banner-image',
		)
	);

	$announcement_image = apply_filters( 'timeline_express_image', $attachment, $post_id );

	if ( empty( $announcement_image ) ) {

		$image_attributes = array(
			'class' => 'announcement-banner-image external-image',
			'alt'   => get_the_title(),
			'src'   => get_post_meta( $post_id, 'announcement_image', true ),
		);

		$announcement_image = '<img ' . timeline_express_map_html_attributes( $image_attributes ) . '>';

	}

	/* Escaped on output in the timeline/single page */
	return apply_filters( 'timeline_express_announcement_banner', $announcement_image );

}

/**
 * Map an array to HTML attributes
 *
 * @param  array $attribute_array Array of HTML attributes
 *
 * @return string                 String of attributes to be used in the final HTML element.
 */
function timeline_express_map_html_attributes( $attribute_array ) {

	if ( ! $attribute_array || empty( $attribute_array ) ) {

		return;

	}

	// @codingStandardsIgnoreStart
	return join(
		' ', array_map(
			function( $key ) use ( $attribute_array ) {

				if ( is_bool( $attribute_array[ $key ] ) ) {

					return $attribute_array[ $key ] ? $key : '';

				}

					return $key . '="' . $attribute_array[ $key ] . '"';

			}, array_keys( $attribute_array )
		)
	);
	// @codingStandardsIgnoreEnd

}

/**
 * Retreive the timeline express announcement date
 *
 * @param int       $post_id The announcement (post) ID whos image you want to retreive.
 *
 * @return string   Formatted
 */
function timeline_express_get_announcement_date( $post_id ) {

	$announcement_date = ( get_post_meta( $post_id, 'announcement_date', true ) ) ? get_post_meta( $post_id, 'announcement_date', true ) : strtotime( 'now' );

	return apply_filters( 'timeline_express_frontend_date_filter', date_i18n( apply_filters( 'timeline_express_custom_date_format', get_option( 'date_format' ) ), $announcement_date ), $post_id );

}

/**
 * Retreive the timeline express announcement date timestamp
 *
 * @param int       $post_id The announcement (post) ID whos image you want to retreive.
 *
 * @return string   The UNIX timestamp announcement_date value
 */
function timeline_express_get_announcement_date_timestamp( $post_id ) {

	return ( get_post_meta( $post_id, 'announcement_date', true ) ) ? get_post_meta( $post_id, 'announcement_date', true ) : strtotime( 'now' );

}

/**
 * Retreive the timeline express announcement content.
 * Note: Cannot be used on the single announcement template.
 *
 * @param  int $post_id The announcement (post) ID whos content you want to retreive.
 *
 * @return array The announcement content, passed through the_content() filter.
 */
function timeline_express_get_announcement_content( $post_id ) {

	$announcement_object = get_post( $post_id );

	return ( isset( $announcement_object->post_content ) ) ? apply_filters( 'the_content', $announcement_object->post_content ) : '';

}

/**
 * Get the announcement excerpt
 * @param  int $post_id The announcement (post) ID whos excerpt you want to retreive.
 * @return string       The announcement excerpt
 */
function timeline_express_get_announcement_excerpt( $post_id ) {

	/* Setup the excerpt */
	return apply_filters( 'the_content', apply_filters( 'timeline_express_frontend_excerpt', get_the_excerpt(), $post_id ) );

}


/**
 * Setup a custom or random excerpt length based on the options set in the settings
 *
 * @return string The announcement excerpt
 *
 * @since 1.2
 */
function timeline_express_custom_excerpt_length( $length ) {

	global $post;

	// if not an announcement post, abort
	if ( 'te_announcements' !== get_post_type( $post ) ) {

		return $length;

	}

	$timeline_express_options = timeline_express_get_options();

	if ( 1 === $timeline_express_options['excerpt-random-length'] ) {

		$random_length = (int) rand( apply_filters( 'timeline_express_random_excerpt_min', 50 ), apply_filters( 'timeline_express_random_excerpt_max', 200 ) );

		return (int) $random_length;

	}

	return (int) apply_filters( 'timeline_express_excerpt_length', $timeline_express_options['excerpt-trim-length'] );

}
add_filter( 'excerpt_length', 'timeline_express_custom_excerpt_length', PHP_INT_MAX );

/**
 * Filter the read more links to a custom state
 * @param string $more The default HTML markup for the read more link.
 * @since 1.2
 */
function timeline_express_custom_read_more( $more ) {

	global $post;

	$timeline_express_options = timeline_express_get_options();

	// if not timeline post
	if ( 'te_announcements' !== get_post_type( $post ) ) {

		return $more;

	}

	// if read more visibility is set to hidden
	if ( '1' !== $timeline_express_options['read-more-visibility'] ) {

		return '';

	}

	// return the default
	return apply_filters( 'timeline_express_read_more_ellipses', '...' );

}
add_filter( 'excerpt_more', 'timeline_express_custom_read_more', 999 );

/**
 * Hook in and generate a read more link below each announcement
 *
 * @return string HTML markup for the new read me link.
 */
function timeline_express_custom_read_more_link() {

	global $post;

	$timeline_express_options = timeline_express_get_options();

	$hide_read_more = get_post_meta( $post->ID, '_timeline_styles_hide_read_more', true );

	if ( ! te_is_readmore_visible( $post ) ) {

		return;

	}

	// if read more visibility is set to hidden
	if ( $hide_read_more || '1' !== $timeline_express_options['read-more-visibility'] ) {

		return;

	}

	echo wp_kses_post( apply_filters( 'timeline_express_read_more_link', '<a class="' . esc_attr( apply_filters( 'timeline_express_read_more_class', 'timeline-express-read-more-link', $post->ID ) ) . '" href="' . apply_filters( 'timeline_express_announcement_permalink', get_permalink( $post->ID ), $post->ID ) . '"> ' . esc_attr( apply_filters( 'timeline_express_read_more_text', esc_html__( 'Read more' ), $post->ID ) ) . '</a>', $post->ID ) );

}
add_action( 'timeline-express-after-excerpt', 'timeline_express_custom_read_more_link', 10 );

/**
 * Conditionally display read more links.
 *
 * If an excerpt does not meet the minimum set on the settings page,
 * do not render the readmore links.
 *
 * @since 2.0.4
 *
 * @param  object $post Post object, used for post ID and post content.
 *
 * @return boolean True if read more should be visible, else false.
 */
function te_is_readmore_visible( $post ) {

	if ( ! apply_filters( 'timeline_express_conditional_readmore', true ) ) {

		return true;

	}

	return ( strlen( $post->post_content ) > (int) timeline_express_get_options( 'excerpt-trim-length' ) );

}

/**
 * Generate an excerpt of random length
 *
 * @param  int     $post_id   The announcement ID to retreive the excerpt
 *
 * @return string             The announcement excerpt of random length
 */
function timeline_express_generate_random_announcement( $post_id ) {

	return apply_filters( 'the_content', apply_filters( 'timeline_express_random_excerpt', get_the_excerpt(), $post_id ) );

}

/**
 * Retreive a custom, user defined, field object.
 * This is used after you define custom fields using the timeline_express_custom_fields filter.
 *
 * @param int     $post_id    The announcement (post) ID whos content you want to retreive.
 * @param string  $meta_name  The name of the meta field (id), whos value you want to retrieve.
 * @param bool    $array      True/False to return an array. Optional. Default: true.
 *
 * @return array The announcement content, passed through the_content() filter.
 */
function timeline_express_get_custom_meta( $post_id, $meta_name, $array = true ) {

	/* If no post id was passed in, abort */
	if ( ! $post_id ) {

		return esc_html__( 'You forgot to include the announcement ID.', 'timeline-express-pro' );

	}

	/* If no meta name was passed in, abort */
	if ( ! $meta_name ) {

		return esc_html__( 'You forgot to include the meta key.', 'timeline-express-pro' );

	}

	/* Return the post meta, or false if nothing was found */
	return ( get_post_meta( $post_id, $meta_name, $array ) ) ? get_post_meta( $post_id, $meta_name, $array ) : false;

}

/**
 * Extract Timeline Express Shortcode Parmaeters
 * @param  string   $str  String of text to extract the shortcode from.
 *
 * @return string         Timeline Express Attributes
 */
function extract_timeline_express_shortcode_params( $str ) {

	$sub = substr( $str, strpos( $str, '[timeline-express' ) + strlen( '[timeline-express' ), strlen( $str ) );

	if ( ! $sub ) {

		return;
	}

	return shortcode_parse_atts( substr( $sub, 0, strpos( $sub, ']' ) ) );

}

/**
 * Load the beginning of the single announcement content wrapper
 *
 * @return mixed   HTML   Content to be used for the wrappers.
 *
 * @since 1.2.6.4
 */
function timeline_express_generate_beginning_content_wrapper() {

	ob_start();

	// check for beginning content wrappers template in local theme
	if ( file_exists( get_stylesheet_directory() . '/timeline-express/single-announcement-content-wrapper-start.php' ) ) {

		include( get_stylesheet_directory() . '/timeline-express/single-announcement-content-wrapper-start.php' );

	} else {

		include( TIMELINE_EXPRESS_PATH . 'lib/public/partials/single-announcement-content-wrapper-start.php' );

	}

	$content_wrapper_start = ob_get_contents();

	ob_get_clean();

	echo wp_kses_post( $content_wrapper_start );

}

/**
 * Load the end of the single announcement content wrapper
 *
 * @return mixed   HTML   content to be used for the wrappers.
 *
 * @since 1.2.6.4
 */
function timeline_express_generate_end_content_wrapper() {

	ob_start();

	// check for beginning content wrappers template in local theme
	if ( file_exists( get_stylesheet_directory() . '/timeline-express/single-announcement-content-wrapper-end.php' ) ) {

		include( get_stylesheet_directory() . '/timeline-express/single-announcement-content-wrapper-end.php' );

	} else {

		include( TIMELINE_EXPRESS_PATH . 'lib/public/partials/single-announcement-content-wrapper-end.php' );

	}

	$content_wrapper_end = ob_get_contents();

	ob_get_clean();

	echo wp_kses_post( $content_wrapper_end );

}

/**
 * Check if any add-ons are installed
 *
 * @since 1.3.0
 */
function get_timeline_express_add_ons() {

	$addons = get_option( 'timeline_express_installed_add_ons', array() );

	ksort( $addons );

	return $addons;

}

/**
 * Check if any add-ons are installed
 *
 * @since 1.3.0
 */
function add_timeline_express_add_on( $add_on_slug ) {

	$installed_add_ons = get_timeline_express_add_ons();

	if ( isset( $installed_add_ons[ $add_on_slug ] ) ) {

		return;

	}

	$installed_add_ons[ $add_on_slug ] = ucwords( str_replace( '-', ' ', $add_on_slug ) );

	update_option( 'timeline_express_installed_add_ons', $installed_add_ons );

	return;

}

/**
 * Check if any add-ons are installed
 *
 * @since 1.3.0
 */
function remove_timeline_express_add_on( $add_on_slug ) {

	$installed_add_ons = get_timeline_express_add_ons();

	if ( isset( $installed_add_ons[ $add_on_slug ] ) ) {

		unset( $installed_add_ons[ $add_on_slug ] );

	}

	update_option( 'timeline_express_installed_add_ons', $installed_add_ons );

	return;

}

if ( ! function_exists( 'timeline_express_generate_page_wrapper_start' ) ) {
	/**
	 * Generate the Timeline Express beginning page wrappers
	 *
	 * @return mixed
	 *
	 * @since 1.2.7
	 */
	function timeline_express_generate_page_wrapper_start() {

		get_timeline_express_template( 'page-wrappers-start' );

	}
}

if ( ! function_exists( 'timeline_express_generate_page_wrapper_end' ) ) {
	/**
	 * Generate the Timeline Express ending page wrappers
	 *
	 * @return mixed
	 *
	 * @since 1.2.7
	 */
	function timeline_express_generate_page_wrapper_end() {

		get_timeline_express_template( 'page-wrappers-end' );

	}
}

if ( ! function_exists( 'timeline_express_comments' ) ) {
	/**
	 * Generate the Timeline Express comment container.
	 *
	 * @return mixed
	 *
	 * @since 2.0.0
	 */
	function timeline_express_comments() {

		global $post;

		$options = timeline_express_get_options();

		if ( '1' !== $options['enable_comments'] || ! comments_open( $post->ID ) ) {

			return;

		}

		comments_template();

	}
}

if ( ! function_exists( 'timeline_express_generate_sidebar' ) ) {
	/**
	 * Generate the Timeline Express ending page wrappers
	 *
	 * @return mixed
	 *
	 * @since 1.2.7
	 */
	function timeline_express_generate_sidebar() {

		get_timeline_express_template( 'timeline-express-sidebar' );

	}
}

/**
 * Options helpers
 */

/**
 * Genereate the options title and description text
 *
 * @param $active_tab string The current active tab.
 *
 * @since 1.3.0
 */
function timeline_express_generate_options_header( $active_tab ) {

	if ( 'base' === $active_tab ) {

		?>

		<h1 id="timeline-express-page-header">
			<?php esc_html_e( 'Timeline Express Pro Settings', 'timeline-express-pro' ); ?>
		</h1>

		<p class="description">
			<?php esc_html_e( 'Alter your timeline settings here. You can adjust some of the visual settings as well as the display order, below.', 'timeline-express-pro' ); ?>
		</p>

		<?php

		return;

	}

	do_action( 'timeline_express_add_on_options_page_header', $active_tab );

}

/**
 * Generate the options tabs
 *
 * @param $active_tab string The current active tab.
 *
 * @since 1.3.0
 */
function timeline_express_generate_options_tabs( $active_tab ) {

	$active_add_ons = get_timeline_express_add_ons();

	if ( ! empty( $active_add_ons ) ) {

		?>

		<h2 class="nav-tab-wrapper te-options">

		<?php

		$active_add_ons = array(
			'base' => __( 'Timeline Express', 'timeline-express-pro' ),
		) + $active_add_ons;

		foreach ( $active_add_ons as $add_on_slug => $add_on_name ) {

			$active = ( $active_tab === $add_on_slug ) ? 'nav-tab-active' : '';

			printf(
				'<a href="%1$s" class="nav-tab %2$s">%3$s</a>',
				admin_url( 'edit.php?post_type=te_announcements&page=timeline-express-settings&tab=' . $add_on_slug ),
				esc_attr( $active ),
				esc_html( $add_on_name )
			);

		}

		?>

		</h2>

		<?php

	}

}

/*
 * Matches each symbol of PHP date format standard
 * with jQuery equivalent codeword
 * @author Tristan Jahier
 */
function te_dateformat_php_to_jqueryui( $php_format ) {

	$formats = [
		'm/d/Y',
		'd/m/Y',
		'Y-m-d',
		'Y-d-m',
		'd-m-Y',
		'm-d-Y',
	];

	$acceptable_formats = (array) apply_filters( 'timeline_express_jqueryui_acceptable_formats', $formats );

	/**
	 * Loop over each acceptable format and add an associated . date format
	 * eg: m/d/Y => m.d.Y, Y-d-m => Y.d.m etc.
	 *
	 * @var array
	 */
	foreach ( $acceptable_formats as $format ) {

		$acceptable_formats[] = str_replace( '-', '.', str_replace( '/', '.', $format ) );

	}

	$date_format = in_array( $php_format, array_unique( $acceptable_formats ), true ) ? $php_format : 'm/d/Y';

	// if all else fails, return m/d/Y format (eg: 07/17/2017)
	return (string) apply_filters( 'timeline_epxress_jqueryui_date_format', $date_format );

}

/**
 * Convert passed in shortcode attributes into an array for use in WP_Query.
 *
 * @param  string $atts The attributes to convert in an array.
 *
 * @return array        Array of taxonomy id's.
 *
 * @since  1.4.1
 */
function timeline_express_tax_array( $tax, $atts ) {

	$tax = 'timelines' === $tax ? 'timeline' : ( 'categories' === $tax ? 'timeline_express_categories' : false );

	if ( ! $atts || ! $tax ) {

		return false;

	}

	if ( 'all' === strtolower( $atts ) ) {

		$term_args = [
			'taxonomy'   => $tax,
			'hide_empty' => false,
		];

		$args = (array) apply_filters( 'timeline_express_all_tax_get_term_args', $term_args );

		$terms = get_terms( $args );

		return wp_list_pluck( $terms, 'term_id' );

	}

	// @codingStandardsIgnoreStart
	/**
	 * convert ranges into values
	 * eg: 1-3 === 1, 2, 3
	 */
	$atts = preg_replace_callback(
		'/(\d+)-(\d+)/', function( $m ) {

			return implode( ', ', range( $m[1], $m[2] ) );

		}, $atts
	);
	// @codingStandardsIgnoreEnd

	// Splig the string into an array
	$atts_array = explode( ',', $atts );

	// @codingStandardsIgnoreStart
	/**
	 * Convert string values into integers to use in the query
	 *
	 * @var array
	 */
	$atts_array = array_map(
		function( $value ) use ( $tax ) {

			if ( is_numeric( $value ) ) {

				return $value;

			}

				$term = get_term_by( 'name', (string) $value, $tax );

				// If no term was found, return an empty string to strip later
			if ( ! $term ) {

				return '';

			}

				return $term->term_id;

		}, $atts_array
	);
	// @codingStandardsIgnoreEnd

	return array_filter( $atts_array );

}

/**
 * Append new fields onto the Timeline Express TinyMCE shortcode generator.
 *
 * When creating a new field, specify an 'attribute' key with the element class
 * you want to target (eg: mce-name) and a value of the shortcode attribute.
 *
 * eg: mce-name => 'name', will look for the html element '.mce-name' in the popup,
 * and pull the value and use it in the shortcode in a name="value" parameter.
 *
 * @param  array $new_fields Field(s) to add.
 *
 * @uses timeline_express_tinymce_shortcode_params
 * @uses timeline_express_tinymce_fields
 */
function timeline_express_shortcode_generator_field( $new_fields ) {

	// @codingStandardsIgnoreStart
	add_filter(
		'timeline_express_tinymce_shortcode_params', function( $targets ) use ( $new_fields ) {

			foreach ( $new_fields as $field ) {

				$targets[ "mce-{$field['attribute']}" ] = $field['attribute'];

			}

			return $targets;

		}
	);

	add_filter(
		'timeline_express_tinymce_fields', function( $fields ) use ( $new_fields ) {

			$last = array_pop( $fields );

			foreach ( $new_fields as $field ) {

				$fields[] = $field;

			}

			$fields[] = $last;

			return $fields;

		}
	);
	// @codingStandardsIgnoreEnd

}

/**
 * Print a comma separated list of assigned taxonomies.
 *
 * @param  string $taxonomy The name of the taxonomy to retrieve.
 *
 * @return mixed            Comma separated list of assigned categories.
 */
function timeline_express_tax_links( $taxonomy = '', $pre_text = '' ) {

	if ( empty( $taxonomy ) ) {

		return;

	}

	$term_args = [
		'fields' => 'names',
	];

	$post_terms = wp_get_object_terms( get_the_ID(), $taxonomy, $term_args );

	if ( empty( $post_terms ) || is_wp_error( $post_terms ) ) {

		return;

	}

	printf(
		'<span class="timeline-express-tax-container %1$s">
			<span class="pre-text">%2$s </span>%3$s
		</span>',
		esc_attr( $taxonomy ),
		esc_html( $pre_text ),
		implode( ', ', $post_terms )
	);

}

/**
 * Retreive extra content for the timeline announcement.
 *
 * @param  string  $type    Type to retreive. Before|After
 * @param  integer $post_id The announcement ID.
 *
 * @return mixed            Markup for the extra content container.
 */
function timeline_express_extra_content( $type = 'before', $post_id ) {

	ob_start();

	do_action( "timeline-express-{$type}-announcement-block", $post_id );

	$contents = ob_get_contents();

	ob_get_clean();

	if ( empty( $contents ) ) {

		return;

	}

	printf(
		'<div class="extra-content %1$s te-animated">%2$s</div>',
		esc_attr( $type ),
		wp_kses_post( $contents )
	);

}

/**
 * Get the ID of the sidebar the widget is assigned to.
 *
 * @param  string $widget_id The ID of the widget to retreive the sidebar ID from.
 *
 * @return boolean|string    False if no parent sidebar ID is found, else the sidebar ID.
 *
 * @since 2.1.0
 */
function timeline_express_get_widget_sidebar( $widget_id ) {

	if ( ! $widget_id ) {

		return false;

	}

	$sidebar_widgets = get_option( 'sidebars_widgets', array() );

	if ( empty( $sidebar_widgets ) || ! $sidebar_widgets ) {

		return false;

	}

	foreach ( $sidebar_widgets as $sidebar_id => $widgets ) {

		if ( empty( $widgets ) ) {

			continue;

		}

		foreach ( $widgets as $key => $widget ) {

			if ( $widget !== $widget_id ) {
				continue;
			}

			return $sidebar_id;

		} // @codingStandardsIgnoreLine

	}

	return false;

}
