<?php
/**
 * Special thanks to Jay Wood
 *
 * @resource https://github.com/JayWood/CMB2_RGBa_Picker
 */
class Timeline_Express_RGBA_Colorpicker {

	const VERSION = '0.3.0';

	public function hooks() {

		add_action( 'cmb2_render_rgba_colorpicker', array( $this, 'render_color_picker' ), 10, 5 );

		add_action( 'admin_enqueue_scripts', array( $this, 'setup_admin_scripts' ) );

	}

	public function render_color_picker( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {

		echo $field_type_object->input(
			array(
				'name'               => $field_type_object->_name( '[color]' ),
				'id'                 => $field_type_object->_id( '[color]' ),
				'value'              => (string) $field_escaped_value['color'],
				'class'              => 'cmb2-colorpicker color-picker',
				'data-default-color' => (string) $field_type_object->field->args['default']['color'],
				'data-alpha'         => 'true',
			)
		);

	}

	public function setup_admin_scripts( $hook ) {

		$screen = get_current_screen();

		if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) || ( ! isset( $screen->post_type ) || 'te_announcements' !== $screen->post_type ) ) {

			return;

		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'wp-color-picker' );

		wp_enqueue_script( 'jw-cmb2-rgba-picker-js', TIMELINE_EXPRESS_STYLES_URL . "lib/js/timeline-express-styles-rgba-colorpicker{$suffix}.js", array( 'wp-color-picker' ), self::VERSION, true );

	}

}

$te_rgba_colorpicker = new Timeline_Express_RGBA_Colorpicker();

$te_rgba_colorpicker->hooks();
