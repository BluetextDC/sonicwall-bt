<?php
/**
 * General admin settings page
 *
 * This is the template with the HTML code for the General Settings admin page
 *
 * @var AEPC_Admin_View $page
 *
 * @package Pixel Caffeine
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="panel panel-advanced-settings">
	<div class="panel-heading">
		<a class="tit" role="button" data-toggle="collapse" href="#collapseAdvancedSettings" aria-expanded="false" aria-controls="collapseAdvancedSettings"><?php esc_html_e( 'Advanced settings', 'pixel-caffeine' ) ?></a>
	</div>

	<div id="collapseAdvancedSettings" class="panel-collapse collapse">
		<div class="panel-body">

			<article class="sub-panel sub-panel-adv-opt form-horizontal-inline">
				<h4 class="tit"><?php _e( 'Tracking tools', 'pixel-caffeine' ) ?></h4>

				<div class="form-group">
					<div class="control-wrap">
						<div class="checkbox">
							<label for="<?php $page->field_id( 'aepc_force_ids' ) ?>">
								<?php printf( esc_html_x( 'Force to use product IDs even if there is a SKU defined', 'pixel-caffeine' ),
									'<strong>',
									'</strong>'
								) ?>
								<input
									type="checkbox"
									name="<?php $page->field_name( 'aepc_force_ids' ) ?>"
									id="<?php $page->field_id( 'aepc_force_ids' ) ?>"
									<?php checked( $page->get_value( 'aepc_force_ids' ), 'yes' ) ?>>
							</label>
						</div>
					</div><!-- ./control-wrap -->
				</div><!-- ./form-group -->

				<div class="form-group">
					<div class="control-wrap">
						<div class="checkbox with-form-control">
							<label for="<?php $page->field_id( 'aepc_enable_pixel_delay' ) ?>">
								<?php printf( esc_html_x( 'Delay %2$sPageView%3$s pixel firing of %1$s seconds', '%1$s is an input text, the other strong tags', 'pixel-caffeine' ),
									'<input 
										type="text"
										class="form-control inline-text"
										placeholder="' . esc_attr__( 'num', 'pixel-caffeine' ) . '"
										id="' . $page->get_field_id( 'aepc_general_delay_firing' ) . '"
										name="' . $page->get_field_name( 'aepc_general_delay_firing' ) . '"
										value="' . $page->get_value( 'aepc_general_delay_firing' ) . '">',
									'<strong>',
									'</strong>'
								) ?>
								<input
									type="checkbox"
									name="<?php $page->field_name( 'aepc_enable_pixel_delay' ) ?>"
									id="<?php $page->field_id( 'aepc_enable_pixel_delay' ) ?>"
									<?php checked( $page->get_value( 'aepc_enable_pixel_delay' ), 'yes' ) ?>>
							</label>
							<small class="text"><?php esc_html_e( 'Postpone the events fired on page load. It\'s useful to avoid to track bouncing users that spends less time on pages.', 'pixel-caffeine' ) ?></small>
						</div>
					</div><!-- ./control-wrap -->
				</div><!-- ./form-group -->

				<div class="form-group">
					<div class="control-wrap">
						<div class="checkbox with-form-control">
							<label for="<?php $page->field_id( 'aepc_enable_advanced_pixel_delay' ) ?>">
								<?php printf( esc_html_x( 'Delay %2$sAdvancedEvents%3$s and %2$sCustom Conversions%3$s pixels firing of %1$s seconds', '%1$s is an input text, the other strong tags', 'pixel-caffeine' ),
									'<input 
										type="text"
										class="form-control inline-text"
										placeholder="' . esc_attr__( 'num', 'pixel-caffeine' ) . '"
										id="' . $page->get_field_id( 'aepc_advanced_pixel_delay_firing' ) . '"
										name="' . $page->get_field_name( 'aepc_advanced_pixel_delay_firing' ) . '"
										value="' . $page->get_value( 'aepc_advanced_pixel_delay_firing' ) . '">',
									'<strong>',
									'</strong>'
								) ?>
								<input
									type="checkbox"
									name="<?php $page->field_name( 'aepc_enable_advanced_pixel_delay' ) ?>"
									id="<?php $page->field_id( 'aepc_enable_advanced_pixel_delay' ) ?>"
									<?php checked( $page->get_value( 'aepc_enable_advanced_pixel_delay' ), 'yes' ) ?>>
							</label>
							<small class="text"><?php _e( 'Postpone the AdvancedEvents pixel that contains data for post ID, post type, taxonomy, custom fields, so on.', 'pixel-caffeine' ) ?></small>
						</div>
					</div><!-- ./control-wrap -->
				</div><!-- ./form-group -->

				<div class="form-group">
					<div class="control-wrap">
						<div class="checkbox">
							<label for="<?php $page->field_id( 'aepc_conversions_no_product_group' ) ?>">
								<?php printf( esc_html_x( 'Do not track variable products as %1$sproduct_group%2$s in the conversion events', '%1$s and %2$s are for strong tag', 'pixel-caffeine' ),
									'<strong>',
									'</strong>'
								) ?>
								<input
									type="checkbox"
									name="<?php $page->field_name( 'aepc_conversions_no_product_group' ) ?>"
									id="<?php $page->field_id( 'aepc_conversions_no_product_group' ) ?>"
									<?php checked( $page->get_value( 'aepc_conversions_no_product_group' ), 'yes' ) ?>>
							</label>
						</div>
					</div><!-- ./control-wrap -->
				</div><!-- ./form-group -->

				<div class="form-group">
					<div class="control-wrap">
						<div class="checkbox">
							<label for="<?php $page->field_id( 'aepc_track_shipping_costs' ) ?>">
								<?php printf( esc_html_x( 'Track %1$sshipping costs%2$s into %1$sPurchase%2$s and %1$sInitiateCheckout%2$s events', '%1$s and %2$s are for strong tag', 'pixel-caffeine' ),
									'<strong>',
									'</strong>'
								) ?>
								<input
									type="checkbox"
									name="<?php $page->field_name( 'aepc_track_shipping_costs' ) ?>"
									id="<?php $page->field_id( 'aepc_track_shipping_costs' ) ?>"
									<?php checked( $page->get_value( 'aepc_track_shipping_costs' ), 'yes' ) ?>>
							</label>
						</div>
					</div><!-- ./control-wrap -->
				</div><!-- ./form-group -->

				<div class="form-group">
					<div class="control-wrap">
						<div class="checkbox with-form-control">
							<label for="<?php $page->field_id( 'aepc_no_pixel_when_logged_in' ) ?>">
								<?php printf( esc_html_x( 'Don\'t fire the pixels if the user is logged in as %1$s', '%1$s is an input text, the other strong tags', 'pixel-caffeine' ),
									'<input 
										type="text"
										class="form-control inline-text multi-tags user-roles"
										placeholder="' . esc_attr__( 'role', 'pixel-caffeine' ) . '"
										id="' . $page->get_field_id( 'aepc_no_pixel_if_user_is' ) . '"
										name="' . $page->get_field_name( 'aepc_no_pixel_if_user_is' ) . '"
										value="' . $page->get_value( 'aepc_no_pixel_if_user_is' ) . '">',
									'<strong>',
									'</strong>'
								) ?>
								<input
									type="checkbox"
									name="<?php $page->field_name( 'aepc_no_pixel_when_logged_in' ) ?>"
									id="<?php $page->field_id( 'aepc_no_pixel_when_logged_in' ) ?>"
									<?php checked( $page->get_value( 'aepc_no_pixel_when_logged_in' ), 'yes' ) ?>>
							</label>
							<small class="text"><?php _e( 'Useful for those roles (such as Administrators) that don\'t want to track pixels for themselves.', 'pixel-caffeine' ) ?></small>
						</div>
					</div><!-- ./control-wrap -->
				</div><!-- ./form-group -->

				<div class="form-group">
					<div class="control-wrap">
						<div class="checkbox with-form-control">
							<label for="<?php $page->field_id( 'aepc_enable_no_value_parameter' ) ?>">
								<?php printf( esc_html_x( 'Don\'t track "value" when following events are fired: %1$s', '%1$s is an input text, the other strong tags', 'pixel-caffeine' ),
									'<input 
										type="text"
										class="form-control inline-text multi-tags standard-events"
										placeholder="' . esc_attr__( 'event', 'pixel-caffeine' ) . '"
										id="' . $page->get_field_id( 'aepc_no_value_parameter' ) . '"
										name="' . $page->get_field_name( 'aepc_no_value_parameter' ) . '"
										value="' . $page->get_value( 'aepc_no_value_parameter' ) . '">',
									'<strong>',
									'</strong>'
								) ?>
								<input
									type="checkbox"
									name="<?php $page->field_name( 'aepc_enable_no_value_parameter' ) ?>"
									id="<?php $page->field_id( 'aepc_enable_no_value_parameter' ) ?>"
									<?php checked( $page->get_value( 'aepc_enable_no_value_parameter' ), 'yes' ) ?>>
							</label>
							<small class="text"><?php _e( 'Exclude "value" and "currency" parameters from the specified DPA standard pixels.', 'pixel-caffeine' ) ?></small>
						</div>
					</div><!-- ./control-wrap -->
				</div><!-- ./form-group -->

				<div class="form-group">
					<div class="control-wrap">
						<div class="checkbox with-form-control">
							<label for="<?php $page->field_id( 'aepc_enable_no_content_parameters' ) ?>">
								<?php printf( esc_html_x( 'Don\'t track "content_ids", "content_type" and "content_name" when following events are fired: %1$s', '%1$s is an input text, the other strong tags', 'pixel-caffeine' ),
									'<input 
										type="text"
										class="form-control inline-text multi-tags standard-events"
										placeholder="' . esc_attr__( 'event', 'pixel-caffeine' ) . '"
										id="' . $page->get_field_id( 'aepc_no_content_parameters' ) . '"
										name="' . $page->get_field_name( 'aepc_no_content_parameters' ) . '"
										value="' . $page->get_value( 'aepc_no_content_parameters' ) . '">',
									'<strong>',
									'</strong>'
								) ?>
								<input
									type="checkbox"
									name="<?php $page->field_name( 'aepc_enable_no_content_parameters' ) ?>"
									id="<?php $page->field_id( 'aepc_enable_no_content_parameters' ) ?>"
									<?php checked( $page->get_value( 'aepc_enable_no_content_parameters' ), 'yes' ) ?>>
							</label>
							<small class="text"><?php _e( 'Exclude "content_ids", "content_type" and "content_name" parameters from the specified DPA standard pixels.', 'pixel-caffeine' ) ?></small>
						</div>
					</div><!-- ./control-wrap -->
				</div><!-- ./form-group -->

			</article><!-- ./sub-panel -->

			<article class="sub-panel sub-panel-adv-opt">
				<h4 class="tit"><?php _e( 'Developers tools', 'pixel-caffeine' ) ?></h4>

				<div class="form-group form-group-btn-single">
					<div class="control-wrap">
						<a
							href="<?php echo wp_nonce_url( $page->get_view_url( array( 'action' => 'aepc_clear_transients' ) ), 'clear_transients' ) ?>"
							class="btn btn-settings"
							id="aepc-clear-transients"
						><?php esc_html_e( 'Clear transients', 'pixel-caffeine' ) ?></a>
						<small class="text"><?php printf( esc_html__( 'Reset all Facebook API cached to better performance. Rarely used, it is useful to fix some data don\'t fetched from facebook.', 'pixel-caffeine' ), '<br /><strong>', '</strong>' ) ?></small>
					</div><!-- ./control-wrap -->
				</div><!-- ./form-group -->

				<div class="form-group full-width">
					<div class="control-wrap">
						<div class="checkbox">
							<label>
								<?php esc_html_e( 'Enable debug mode', 'pixel-caffeine' ) ?>
								<input
									type="checkbox"
									name="<?php $page->field_name( 'aepc_enable_debug_mode' ) ?>"
									id="<?php $page->field_id( 'aepc_enable_debug_mode' ) ?>"
									<?php checked( $page->get_value( 'aepc_enable_debug_mode' ), 'yes' ) ?>>
							</label>
							<small class="text"><?php esc_html_e( 'You will be able to have a details dump of pixels events fired, on javascript console of browser inspector.', 'pixel-caffeine' ) ?></small>
							<small class="text"><strong><?php esc_html_e( 'Note:', 'pixel-caffeine' ) ?></strong> <?php esc_html_e( 'by activating this mode, the pixels won\'t be sent to facebook, so a warning is shown on Facebook Pixel Helper chrome extension.', 'pixel-caffeine' ) ?></small>
						</div>
					</div><!-- ./control-wrap -->
				</div><!-- ./form-group -->

				<div class="form-group form-group-btn-single">
					<div class="control-wrap">
						<a
							href="<?php echo wp_nonce_url( $page->get_view_url( array( 'action' => 'aepc_reset_fb_connection' ) ), 'reset_fb_connection' ) ?>"
							class="btn btn-settings"
							id="aepc-reset-fb-connection"
						><?php esc_html_e( 'Reset Facebook Connection', 'pixel-caffeine' ) ?></a>
						<small class="text"><?php printf( esc_html__( 'Reset facebook connection status when it is blocked by an error (you won\'t lose any data).', 'pixel-caffeine' ), '<br /><strong>', '</strong>' ) ?></small>
					</div><!-- ./control-wrap -->
				</div><!-- ./form-group -->

			</article>
		</div><!-- ./panel-body -->
	</div><!-- ./panel-collapse -->
</div><!-- ./panel-advanced-settings -->
