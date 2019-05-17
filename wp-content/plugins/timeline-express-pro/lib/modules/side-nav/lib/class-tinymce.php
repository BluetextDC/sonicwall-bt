<?php
/**
 * Timeline Express Side Nav TinyMCE shortcode generator fields
 *
 * @since 2.0.0
 */
final class Timeline_Express_Side_Nav_TinyMCE {

	public function __construct() {

		add_action( 'init', [ $this, 'generate_fields' ] );

	}

	/**
	 * Add new fields to the shortcode generator.
	 *
	 * @since 2.0.0
	 */
	public function generate_fields() {

		$sidenav_fields = [
			[
				'attribute' => 'sidenav', // used by our helper function only.
				'type'      => 'checkbox',
				'classes'   => 'sidenav',
				'label'     => esc_html__( 'Display Side Navigation', 'timeline-express-pro' ),
				'style'     => 'height: 40px;',
				'tooltip'   => esc_html__( 'Display the side navigation for this timeline.', 'timeline-express-pro' ),
			],
		];

		timeline_express_shortcode_generator_field( $sidenav_fields );

	}

}

new Timeline_Express_Side_Nav_TinyMCE;
