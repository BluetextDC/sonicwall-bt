<?php
/**
 * Timeline Express :: TinyMCE Button and Shortocode Generator
 * By Code Parrots
 *
 * @link http://www.codeparrots.com
 *
 * @package WordPress
 * @subpackage Component
 * @since 2.0.0
 **/

class Timeline_Express_TinyMCE {

	public function __construct() {

		if ( ! is_admin() ) {

			return;

		}

		add_action( 'admin_head', [ $this, 'setup_tinymce_button' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'tinymce_data' ] );

	}

	/**
	 * Setup the tinymce button.
	 *
	 * @since 1.0.0
	 */
	public function setup_tinymce_button() {

		if (
			( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) ||
			get_user_option( 'rich_editing' ) !== 'true'
		) {

			return;

		}

		add_filter( 'mce_external_plugins', [ $this, 'add_tinymce_plugin' ] );
		add_filter( 'mce_buttons', [ $this, 'add_tinymce_toolbar_button' ] );

	}

	/**
	 * Localize the data for our tinymce plugin.
	 *
	 * @since 1.0.0
	 */
	public function tinymce_data() {

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'select2', TIMELINE_EXPRESS_URL . "/lib/admin/js/select2.full{$suffix}.js", [ 'editor' ], '4.0.3', true );

		wp_enqueue_style( 'select2', TIMELINE_EXPRESS_URL . '/lib/admin/css/select2.min.css', [], '4.0.3', 'all' );

		$editor_atts = [
			'i10n'             => [
				'popupTitle'  => __( 'Timeline Express Pro', 'timeline-express-pro' ),
				'supportText' => sprintf(
					/* translators: 1. string 'documentation' wrapped in an anchor tag. */
					esc_html__( 'If you need help please see our %s.', 'timeline-express-pro' ),
					sprintf(
						'<a href="%1$s" target="_blank">%2$s</a>',
						esc_url( 'https://www.wp-timelineexpress.com/documentation/display-the-timeline/' ),
						esc_html__( 'documentation', 'timeline-express-pro' )
					)
				),
			],
			'icon'             => TIMELINE_EXPRESS_URL . '/lib/admin/images/timeline-express-menu-icon.png',
			'data'             => [
				'fields'  => $this->get_tinymce_fields(),
				'buttons' => $this->get_tinymce_buttons(),
			],
			'shortcode_params' => $this->get_shortcode_parameters(),
		];

		wp_localize_script( 'editor', 'timeline_express_tinymce', $editor_atts );

		// @codingStandardsIgnoreStart
		add_action(
			'admin_head', function() {

				$admin_colors = $this->get_admin_color();

				?>
				<style type="text/css">
				<?php
				if ( $admin_colors ) {

					$select_hover = $admin_colors['select-hover'];
					$button_color = $admin_colors['button-color'];

					?>

					.select2-container--default .select2-results__option--highlighted[aria-selected] {
						background-color: <?php echo $select_hover; ?>;
					}

					#timeline_express_tinymce .mce-btn.mce-primary {
						background: <?php echo $button_color; ?> !important;
						border-color: <?php echo $button_color; ?> !important;
						box-shadow: 0 1px 0 <?php echo $button_color; ?> !important;
						text-shadow: 0 -1px 1px <?php echo $button_color; ?>,
													1px 0 1px <?php echo $button_color; ?>,
													0 1px 1px <?php echo $button_color; ?>,
													-1px 0 1px <?php echo $button_color; ?> !important;
					}

					<?php

				}

				?>
				#timeline_express_tinymce {
					width: 40%;
					min-width: 250px;
					max-width: 750px;
				}
				#timeline_express_tinymce .mce-timeline-express {
					left: 10px !important;
					padding: 0 5px;
				}
				#timeline_express_tinymce .mce-checkbox .mce-i-checkbox {
					display: block;
					margin: 0 auto;
				}
				#timeline_express_tinymce label.mce-label {
					font-weight: 600;
				}
				#timeline_express_tinymce .mce-description {
					font-style: italic;
				}
				#timeline_express_tinymce .mce-container {
					z-index: 1;
				}
				#timeline_express_tinymce #timeline_express_tinymce-body:after {
					content: ' ';
					display: block;
					position: absolute;
					width: 100%;
					height: 100%;
					opacity: 0.35;
					z-index: 0;
					background-image: url( "<?php echo TIMELINE_EXPRESS_URL; ?>/lib/admin/images/code-parrots-mascot.png" );
					background-size: 130px;
					background-repeat: no-repeat;
					background-position: bottom right;
				}
				#timeline_express_tinymce .mce-support-help a {
					line-height: 60px;
				}
				#timeline_express_tinymce .select2 {
					float: right;
				}
				#timeline_express_tinymce span.select2,
				#timeline_express_tinymce span.select2 span.select2-selection {
					height: 70px;
					overflow-y: auto;
				}
				.timeline-express-select2-dropdown {
					z-index: 100102;
				}
				</style>

				<?php
			}
		);
		// @codingStandardsIgnoreEnd

	}

	/**
	* Adds a TinyMCE plugin compatible JS file to the TinyMCE / Visual Editor instance
	*
	* @param array $plugin_array Array of registered TinyMCE Plugins
	*
	* @return array Modified array of registered TinyMCE Plugins
	*/
	public function add_tinymce_plugin( $plugin_array ) {

		$custom = [
			'timeline_express_tinymce' => TIMELINE_EXPRESS_URL . 'lib/admin/js/timeline-express-tinymce.js',
		];

		return array_merge( $plugin_array, $custom );

	}

	/**
	* Adds a button to the TinyMCE / Visual Editor which the user can click
	* to insert a link with a custom CSS class.
	*
	* @param array $buttons Array of registered TinyMCE Buttons
	*
	* @return array Modified array of registered TinyMCE Buttons
	*/
	public function add_tinymce_toolbar_button( $buttons ) {

		array_push( $buttons, 'timeline_express_tinymce' );

		return $buttons;

	}

	/**
	 * Get the TinyMCE fields
	 *
	 * @uses timeline_express_tinymce_fields
	 *
	 * @since 2.0.0
	 *
	 * @return string JSON encoded fields array for use in the JS.
	 */
	public function get_tinymce_fields() {

		// @codingStandardsIgnoreStart
		return json_encode(
			(array) apply_filters(
				'timeline_express_tinymce_fields', [
					[
						'type'    => 'label',
						'text'    => esc_html__( 'Use the fields below to generate your timeline shortcode.', 'timeline-express-pro' ),
						'classes' => 'description intro-text',
						'style'   => 'height: 40px',
					],
					[
						'type'    => 'selectbox',
						'label'   => esc_html__( 'Timelines', 'timeline-express-pro' ),
						'classes' => 'select2 multiple timelines',
						'style'   => 'height: 70px;',
						'options' => $this->get_timeline_terms( 'timeline' ),
						'tooltip' => esc_html__( 'Select the timelines to display.', 'timeline-express-pro' ),
					],
					[
						'type'    => 'selectbox',
						'label'   => esc_html__( 'Timeline Categories', 'timeline-express-pro' ),
						'classes' => 'select2 multiple categories',
						'style'   => 'height: 70px;',
						'options' => $this->get_timeline_terms( 'timeline_express_categories' ),
						'tooltip' => esc_html__( 'Select the timeline categories to display.', 'timeline-express-pro' ),
					],
					[
						'type'    => 'checkbox',
						'classes' => 'filters',
						'label'   => esc_html__( 'Display Filters', 'timeline-express-pro' ),
						'style'   => 'height: 40px;',
						'tooltip' => esc_html__( 'Display the timeline filters.', 'timeline-express-pro' ),
					],
					[
						'type'    => 'checkbox',
						'classes' => 'horizontal',
						'label'   => esc_html__( 'Horizontal Timeline', 'timeline-express-pro' ),
						'style'   => 'height: 40px;',
						'tooltip' => esc_html__( 'Display the timeline in a horizontal layout.', 'timeline-express-pro' ),
					],
					[
						'type'    => 'label',
						// This text is appended via JS to prevent width issues on thicbox render
						'text'    => '',
						'classes' => 'description support-help',
						'style'   => 'height: 40px',
					],
				]
			)
		);
		// @codingStandardsIgnoreEnd

	}

	/**
	 * Get the specified te_announcement terms.
	 *
	 * @param  string $term  The term to retrieve. timeline|timeline_express_categories
	 *
	 * @since 2.0.0
	 *
	 * @return array         Array used in the select2 field.
	 */
	public function get_timeline_terms( $term ) {

		$atts = [
			'taxonomy'   => (string) $term,
			'hide_empty' => false,
		];

		return wp_list_pluck( get_terms( $atts ), 'name' );

	}

	/**
	 * Get the TinyMCE buttons
	 *
	 * @uses timeline_express_tinymce_buttons
	 *
	 * @since 2.0.0
	 *
	 * @return string JSON encoded buttons array for use in the JS.
	 */
	public function get_tinymce_buttons() {

		// @codingStandardsIgnoreStart
		return json_encode(
			(array) apply_filters(
				'timeline_express_tinymce_buttons', [
					[
						'text'    => esc_html__( 'Insert Timeline', 'timeline-express-pro' ),
						'onclick' => 'submit',
						'classes' => 'primary timeline-express',
					],
					[
						'text'    => 'Cancel',
						'id'      => 'canc',
						'onclick' => 'close',
					],
				]
			)
		);
		// @codingStandardsIgnoreEnd

	}

	/**
	 * Get the Shortcode paremters to use.
	 *
	 * @uses timeline_express_tinymce_shortcode_params
	 *
	 * @since 2.0.0
	 *
	 * @return array key => value pair, where key is the class to grab the value
	 *               from and the value is the shortcode parameter to add.
	 *               eg: .mce-timeline = timeline="1,2,3"
	 */
	public function get_shortcode_parameters() {

		$shortcode_params = [
			'mce-timelines'  => 'timeline',
			'mce-categories' => 'categories',
			'mce-filters'    => 'filter',
			'mce-horizontal' => 'horizontal',
		];

		/**
		 * Filter the shortcode parameters.
		 *
		 * See note above about key => value pairs.
		 *
		 * @since 2.0.0
		 *
		 * @var array
		 */
		return json_encode( (array) apply_filters( 'timeline_express_tinymce_shortcode_params', $shortcode_params ) );

	}

	/**
	 * Get the select hover color from the admin color scheme.
	 *
	 * @param integer $key The color key to return.
	 *
	 * @since 2.0.0
	 *
	 * @return string The color to use as the hover.
	 */
	public function get_admin_color( $key = 2 ) {

		global $_wp_admin_css_colors;

		$admin_color = get_user_option( 'admin_color', get_current_user_ID() );

		$colors = isset( $_wp_admin_css_colors[ $admin_color ]->colors ) ? $_wp_admin_css_colors[ $admin_color ]->colors : false;

		$admin_colors = [];

		switch ( $admin_color ) {

			default:
			case 'default':
				return false;

			case 'light':
				return [
					'select-hover' => $colors[1],
					'button-color' => $colors[3],
				];

			case 'blue':
				return [
					'select-hover' => $colors[0],
					'button-color' => '#e1a948',
				];

			case 'coffee':
				return [
					'select-hover' => $colors[2],
					'button-color' => $colors[2],
				];

			case 'ectoplasm':
			case 'sunrise':
			case 'ocean':
				return [
					'select-hover' => $colors[2],
					'button-color' => $colors[2],
				];

			case 'midnight':
				return [
					'select-hover' => $colors[3],
					'button-color' => $colors[3],
				];

		} // End switch().

	}

}

new Timeline_Express_TinyMCE;
