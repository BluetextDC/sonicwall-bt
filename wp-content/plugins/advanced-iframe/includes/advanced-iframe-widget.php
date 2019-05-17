<?php
/**
 * Advanced iFrame widget Class
 *
 * @since 4.0
 */
class AdvancedIframe_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'advanced_iframe_widget', 'description' => __('Shortcode of the Advanced iFrame Pro or HTML or Plain Text ', 'advanced-iframe'));
		$control_ops = array('width' => 500, 'height' => 300);
		parent::__construct('advanced-iframe-widget', __('Advanced iFrame Pro Widget', 'advanced-iframe'), $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$text = do_shortcode(apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance ));
		echo $before_widget;
    ?>
			<div class="textwidget"><?php echo !empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?></div>
		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		if ( current_user_can('unfiltered_html') ) {
			$instance['text'] =  $new_instance['text'];
		} else {
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); //wp_filter_post_kses() expects slashed
      }
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'text' => '' ) );
		$text = esc_textarea($instance['text']);
?>
		<p><label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Enter the Advanced iFrame Pro shortcode:', 'advanced-iframe'); ?></label></p>
		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>
<?php
	}
}

?>