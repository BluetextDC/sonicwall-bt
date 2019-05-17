<?php
/**
 * Timeline Express :: Compatibility Class
 * By Code Parrots
 *
 * @link http://www.codeparrots.com
 *
 * @package WordPress
 * @subpackage Component
 * @since 1.2
 **/

/**
 * Define our main class
 */
class TimelineExpressCompat {

	public function __construct() {

		if ( is_plugin_active( 'qtranslate-x/qtranslate.php' ) ) {

			$this->qtranslate();

		}

	}

	/**
	 * qtranslate compatibility actions & filters.
	 *
	 * @see https://wordpress.org/plugins/qtranslate-x/
	 * @see https://www.wp-timelineexpress.com/documentation/qtranslate-languages-not-loading-properly/
	 *
	 * @since 2.2.1
	 */
	public function qtranslate() {

		// @codingStandardsIgnoreStart
		/**
		 * Bust Timeline Express cache when the qtranslate language is changed.
		 *
		 * @since 2.2.1
		 */
		add_action( 'qtranslate_head_add_css', function() {

			global $post;

			if ( ! isset( $post->ID ) || false === get_transient( "timeline-express-query-{$post->ID}" ) ) {

				return;

			}

			delete_transient( "timeline-express-query-{$post->ID}" );

		} );
		// @codingStandardsIgnoreEnd

	}

}

new TimelineExpressCompat();
