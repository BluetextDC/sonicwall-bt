<?php

// Creating the widget
class Timeline_Express_Filter_Widget extends WP_Widget {

	function __construct() {

		parent::__construct(

			'timeline_express_filter_widget',
			__( 'Timeline Express Filters', 'timeline-express-pro' ),
			array(
				'description' => __( 'Display the Timeline Express filtering options in your sidebar.', 'timeline-express-pro' ),
			)
		);

	}

	/**
	 * Frontend Widget
	 * @param  array  $args     Array arguments
	 * @param  object $instance Object instance
	 *
	 * @return mixed Markup for the widget
	 */
	public function widget( $args, $instance ) {

		$shortcode_atts = extract_timeline_express_shortcode_params( get_the_content() );

		/* Abort, if filters have been disabled or are not present */
		if ( empty( $shortcode_atts['filter'] ) || 0 === $shortcode_atts['filter'] ) {

			return;

		}

		$title = apply_filters( 'widget_title', $instance['title'] );

		// before and after widget arguments are defined by themes
		echo wp_kses_post( $args['before_widget'] );

		if ( ! empty( $title ) ) {

			echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );

		}

		?>
		<style>
		.hentry .timeline-express-filter-group,
		.hentry .timeline-express-filter-group + input {
			display: none;
		}
		</style>
		<script type="text/javascript">
		jQuery( document ).ready( function() {
			jQuery( '#cd-timeline, .timeline-express-no-announcements-found' ).parent().find( '.timeline-express-filter-group, .timeline-express-filter-submit' ).remove();
		} );
		</script>
		<?php

		get_timeline_express_template( 'timeline-express-filters', $shortcode_atts );

		echo wp_kses_post( $args['after_widget'] );

	}

	// Widget Backend
	public function form( $instance ) {

		$title = ( isset( $instance['title'] ) ) ? $instance['title'] : '';

		// Widget admin form
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'timeline-express-pro' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {

		$instance = array();

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;

	}

}

// Register and load the widget
function timeline_express_load_filter_widget() {

	register_widget( 'timeline_express_filter_widget' );

}
add_action( 'widgets_init', 'timeline_express_load_filter_widget' );
