<?php
/**
#_________________________________________________ PLUGIN
Module Name: Timeline Express - Side Nav
Module URI: https://www.wp-timelineexpress.com
Description: Display a navigation on the sidebar to nicely scroll to certain years on the timeline.
Version: 1.0.0
Author: Code Parrots
Author URI: http://www.codeparrots.com
License: GPL2

#_________________________________________________ LICENSE
Copyright 2012-16 Code Parrots (email : codeparrots@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

#_________________________________________________ CONSTANTS
*/

if ( ! defined( 'WPINC' ) ) {

	die;

}

include_once plugin_dir_path( __FILE__ ) . '/constants.php';
include_once plugin_dir_path( __FILE__ ) . 'lib/helpers.php';

function initialize_timeline_express_sidenav() {

	class Timeline_Express_Side_Nav extends TimelineExpressBase {

		/**
		 * Default sidenav shortcode attributes array.
		 *
		 * @var array
		 */
		private $shortcode_defaults;

		public function __construct() {

			include_once plugin_dir_path( __FILE__ ) . 'lib/class-tinymce.php';

			$shortcode_defaults = [
				'sidenav'                   => false,
				'sidenav-offscreen'         => true,
				'sidenav-full'              => true,
				'sidenav-theme'             => 'light',
				'sidenav-icon'              => 'bars',
				'sidenav-icon-open'         => 'times',
				'sidenav-icon-color'        => '#333333',
				'sidenav-location'          => 'left',
				'sidenav-title'             => false,
				'sidenav-description'       => false,
				'sidenav-label'             => false,
				'sidenav-speed'             => 700,
				'sidenav-easing'            => 'easeInOutCubic',
				'sidenav-background'        => '#ffffff',
				'sidenav-title-color'       => 'inherit',
				'sidenav-description-color' => 'inherit',
				'sidenav-link-color'        => 'inherit', // must be hex value for our lighten value hlper to work (:hover color)
				'sidenav-padding-element'   => 'header',
			];

			$this->shortcode_defaults = (array) apply_filters( 'timeline_express_sidenav_default_atts', $shortcode_defaults );

			add_filter( 'shortcode_atts_timeline-express', [ $this, 'side_nav_shortcode_param' ], 15, 4 );

		}

		/**
		 * Check if the sidebar nav is enabled.
		 *
		 * @param  array  $atts Shortcode attributes.
		 *
		 * @return boolean       True when enabled, else false.
		 */
		public function is_sidebar_nav_enabled( $atts ) {

			return ( $atts['sidenav'] && '1' === $atts['sidenav'] );

		}

		/**
		 * Enable the side nav shortcode parameters.
		 *
		 * @param mixed  $output
		 * @param array  $pairs
		 * @param array  $atts
		 * @param string $shortcode
		 *
		 * @since 1.0.0
		 */
		public function side_nav_shortcode_param( $output, $pairs, $atts, $shortcode ) {

			$atts = wp_parse_args( $atts, $this->shortcode_defaults );

			if ( ! $this->is_sidebar_nav_enabled( $atts ) ) {

				return $output;

			}

			// @codingStandardsIgnoreStart
			add_action( 'timeline-express-scripts', function() use ( $atts ) {

				$rtl          = ! is_rtl() ? '' : '-rtl';
				$suffix       = SCRIPT_DEBUG ? '' : '.min';
				$dependencies = [ 'jquery' ];

				wp_enqueue_style( 'timeline-express-sidenav', TIMELINE_EXPRESS_SIDENAV_URL . "/lib/css/timeline-express-sidenav{$rtl}{$suffix}.css", [], TIMELINE_EXPRESS_SIDENAV_VERSION, 'all' );

				wp_enqueue_script( 'timeline-express-sidenav', TIMELINE_EXPRESS_SIDENAV_URL . "/lib/js/timeline-express-sidenav{$suffix}.js", [ 'jquery' ], TIMELINE_EXPRESS_SIDENAV_VERSION, true );

				wp_localize_script(
					'timeline-express-sidenav', 'teSideNav', (array) apply_filters(
						'timeline_express_sidenav_script_data', [
							'speed'          => (int) $atts['sidenav-speed'], // integer value
							'easing'         => $atts['sidenav-easing'],    // liner|swing
							'offScreen'      => $atts['sidenav-offscreen'],
							'full'           => $atts['sidenav-full'],
							'direction'      => $atts['sidenav-location'],
							'paddingElement' => $atts['sidenav-padding-element'],
						]
					)
				);

				$inline_nav_styles = "
				.timeline-express-sidenav {
					background: {$atts['sidenav-background']};
				}
			";

				if ( 'inherit' !== $atts['sidenav-title-color'] ) {

					$inline_nav_styles .= "
					.timeline-express-sidenav .title {
						color: {$atts['sidenav-title-color']};
					}
				";

				}

				if ( 'inherit' !== $atts['sidenav-description-color'] ) {

					$inline_nav_styles .= "
					.timeline-express-sidenav .description {
						color: {$atts['sidenav-description-color']};
					}
				";

				}

				if ( 'inherit' !== $atts['sidenav-link-color'] ) {

					$inline_nav_styles .= "
					.timeline-express-sidenav li,
					.timeline-express-sidenav li a {
						color: {$atts['sidenav-link-color']};
					}
				";

					$lighter_link = te_adjust_brightness( $atts['sidenav-link-color'] );

					$inline_nav_styles .= "
					.timeline-express-sidenav li:hover,
					.timeline-express-sidenav li:hover a,
					.timeline-express-sidenav li.selected,
					.timeline-express-sidenav li.selected a {
						color: {$lighter_link};
						transition: color .15s ease-out;
				 -o-transition: color .15s ease-out;
			 -moz-transition: color .15s ease-out;
		-webkit-transition: color .15s ease-out;
					}
				";

				}

				if ( $atts['sidenav-offscreen'] ) {

					$lighter_icon   = te_adjust_brightness( $atts['sidenav-icon-color'] );
					$icon_direction = ( 'left' === $atts['sidenav-location'] ) ? 'right' : 'left';

					$inline_nav_styles .= "
					.timeline-express-sidenav.offscreen .sidenav-toggle {
						{$icon_direction}: -36px;
						color: {$atts['sidenav-icon-color']};
						transition: color .15s ease-out;
				 -o-transition: color .15s ease-out;
			 -moz-transition: color .15s ease-out;
		-webkit-transition: color .15s ease-out;
					}
					.timeline-express-sidenav .sidenav-toggle:hover {
						color: {$lighter_icon};
					}
				";

				}

				wp_add_inline_style( 'timeline-express-sidenav', $inline_nav_styles );

			} );

			add_action( 'timeline-express-after-timeline', function ( $shortcode_atts, $args, $query ) use ( $atts ) {

				$years = [];

				if ( $query->have_posts() ) {

					while ( $query->have_posts() ) {

						$query->the_post();

						$years[] = date_i18n( 'Y', timeline_express_get_announcement_date_timestamp( get_the_ID() ) );

					} // @codingStandardsIgnoreLine

				}

				if ( empty( $years ) ) {

					return;

				}

				$sidenav_full = $atts['sidenav-full'] ? 'full' : '';

				?>

				<div class="timeline-express-sidenav <?php echo esc_attr( $atts['sidenav-theme'] . ' ' . $atts['sidenav-location'] . ' ' . $sidenav_full ); ?>" style="display:none;">

					<?php

					if ( $atts['sidenav-offscreen'] ) {

						printf(
							'<span class="fa fa-%1$s sidenav-toggle" data-open-icon="%2$s" data-closed-icon="%1$s"></span>',
							esc_attr( $atts['sidenav-icon'] ),
							esc_attr( $atts['sidenav-icon-open'] )
						);

					}

					$header_class = ( $atts['sidenav-title'] || $atts['sidenav-label'] || $atts['sidenav-description'] ) ? ' styled' : '';

					?>

					<div class="timeline-express-sidenav-wrapper">

						<div class="header<?php echo esc_attr( $header_class ); ?>">

							<?php

							do_action( 'timeline_express_sidenav_top' );

							if ( $atts['sidenav-title'] ) {

								printf(
									'<h3 class="title">%s</h3>',
									esc_html( $atts['sidenav-title'] )
								);

							}

							if ( $atts['sidenav-label'] ) {

								printf(
									'<span class="label">%s</span>',
									esc_html( $atts['sidenav-label'] )
								);

							}

							if ( $atts['sidenav-description'] ) {

								printf(
									'<p class="description">%s</p>',
									esc_html( $atts['sidenav-description'] )
								);

							}

							do_action( 'timeline_express_sidenav_after_title' );

							?>

						</div>

						<ul>

						<?php

						foreach ( array_unique( $years ) as $year ) {

							$classes = (array) apply_filters(
								'timeline_express_sidenav_link_class', [
									$year,
								], $year
							);

							printf(
								'<li class="%1$s year-scroller">
									<a href="#year-%1$s" data-year="%1$s">%2$s</a>
								</li>',
								esc_attr( implode( ' ', $classes ) ),
								esc_html( (string) apply_filters( 'timeline_express_sidenav_link', $year ) )
							);

						}

						?>

						</ul>

						<?php do_action( 'timeline_express_sidenav_bottom' ); ?>

					</div>

				</div>

				<?php

			}, 10, 3 );
			// @codingStandardsIgnoreEnd

			return $output;

		}

	}

	new Timeline_Express_Side_Nav;

}
add_action( 'plugins_loaded', 'initialize_timeline_express_sidenav' );
