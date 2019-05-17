<?php
/**
 * All filters, actions and tweaks that Timeline Express Toolbox Add-on
 * makes to the Timeline Express base plugin
 *
 * @author Code Parrots <support@codeparrots.com>
 *
 * @since 1.0.0
 */
final class Toolbox_Actions extends TimelineExpressBase {

	private $addon_options;

	public function __construct( $addon_options ) {

		$this->addon_options = $addon_options;

		add_filter( 'timeline-express-slug',                     array( $this, 'alter_announcement_slug' ) );

		add_filter( 'timeline_express_custom_date_format',       array( $this, 'alter_date_format' ) );

		add_filter( 'timeline_express_announcement_date_text',   array( $this, 'alter_date_string' ) );

		add_filter( 'timeline-express-announcement-img-size' ,   array( $this, 'alter_image_size' ), 10, 2 );

		add_filter( 'timeline_express_singular_name',            array( $this, 'alter_timeline_express_single_name' ), 20 );
		add_filter( 'timeline_express_plural_name',              array( $this, 'alter_timeline_express_plural_name' ), 20 );

		add_filter( 'timeline_express_menu_cap',                 array( $this, 'alter_timeline_express_menu_cap' ), 20 );

		add_action( 'wp_loaded', function() {

			return $this->addon_options['year_icons'] ? define( 'TIMELINE_EXPRESS_YEAR_ICONS', true ) : '';

		} );

	}

	/**
	 * Alter the Timeline Express announcement slug
	 *
	 * @param string $slug The original announcement slug (eg: announcement).
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function alter_announcement_slug( $slug ) {

		return ! $this->addon_options['announcement_slug'] ? $slug : sanitize_title( $this->addon_options['announcement_slug'] );

	}

	/**
	 * Alter the Timeline Express announcement date format
	 *
	 * @param string $format The original date format (eg: m/d/Y, or whatever is set in get_option( 'date_format' )).
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function alter_date_format( $format ) {

		if ( ! defined( 'TIMELINE_EXPRESS_ANNOUNCEMENT_DATE_FORMATS' ) || ! TIMELINE_EXPRESS_ANNOUNCEMENT_DATE_FORMATS ) {

			return $this->addon_options['date_format'];

		}

		$date_format = get_post_meta( get_the_ID(), 'announcement_date_format', true );

		return $date_format ? $date_format : $this->addon_options['date_format'];

	}

	/**
	 * Alter the date string that appears on the single announcement template
	 *
	 * @param string $date_string The original string (eg: Announcement Date: %s).
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function alter_date_string( $date_string ) {

		// Prevent multiple occurances of {date} in the string
		return ! $this->addon_options['date_string'] ? $date_string : str_replace( '{date}', '%s', implode( ' ', array_unique( explode( ' ', $this->addon_options['date_string'] ) ) ) );

	}

	/**
	 * Alter the Timeline Express announcement image size
	 *
	 * @param string  $image_size The original image size (Timeline: timeline-express-thumbnail; Single: timeline-express).
	 * @param integer $post_id    The ID of the current post.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function alter_image_size( $image_size, $post_id ) {

		return is_single() ? $this->addon_options['single_image_size'] : $this->addon_options['timeline_image_size'];

	}

	/**
	 * Alter the single Timeline Express announcement text
	 *
	 * @param string Original single text (eg: Announcement)
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function alter_timeline_express_single_name( $single_name ) {

		if ( empty( $this->addon_options['announcement_singular'] ) ) {

			return $single_name;

		}

		return $this->addon_options['announcement_singular'];

	}

	/**
	 * Alter the plural Timeline Express announcement text
	 *
	 * @param string Original plural text (eg: Announcements)
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function alter_timeline_express_plural_name( $plural_name ) {

		if ( empty( $this->addon_options['announcement_plural'] ) ) {

			return $plural_name;

		}

		return $this->addon_options['announcement_plural'];

	}

	/**
	 * Alter who can visit, view and edit Timeline Express post types
	 *
	 * @param  string $cap The original capability (eg: manage_options)
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function alter_timeline_express_menu_cap( $cap ) {

		if ( empty( $this->addon_options['edit_caps'] ) || current_user_can( 'manage_options' ) ) {

			return $cap;

		}

		return $this->addon_options['edit_caps'];

	}
}
