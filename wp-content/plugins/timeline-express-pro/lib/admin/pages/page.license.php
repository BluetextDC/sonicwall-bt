<?php
/**
 * License Page - Admin Page
 *
 * @package Timeline Express
 *
 * @since   1.2
 */

// If accessed directly, exit;
if ( ! defined( 'ABSPATH' ) ) :

	exit;

endif;

/* Premium Support Template */
$license = get_option( 'timeline_express_license_key' );
$status = get_option( 'timeline_express_license_status' );
$license_data = get_option( 'timeline_express_license_data' );
$license_valid = ( false !== $license && false !== $status && 'valid' === $status ) ? true : false;
// active tab
$active_tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'timeline-express-license';
?>

<div id="timeline-express-support-page-wrap">

	<h1>
		<?php esc_html_e( 'Timeline Express Pro &amp; Add-On Licenses', 'timeline-express-pro' ); ?>
	</h1>

	<div class="wrap">

		<!-- tabs -->
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab
			<?php
			if ( 'timeline-express-license' === $active_tab ) {
?>
	nav-tab-active<?php } ?>" href="<?php echo esc_url( admin_url( 'edit.php?post_type=te_announcements&page=timeline-express-license' ) ); ?>">
				<?php esc_html_e( 'Timeline Express Pro', 'timeline-express-pro' ); ?>
			</a>
			<?php
				// action hook for additional settings tabs
				do_action( 'timeline-express-support-tabs', $active_tab );
			?>
		</h2>
		<br />
		<?php
		if ( 'timeline-express-license' === $active_tab ) {
			?>
			<section id="timeline-express-support-page-header">

				<img src="<?php echo esc_url( TIMELINE_EXPRESS_URL ) . 'lib/admin/images/timeline-express-logo-128.png'; ?>" title="Timeline Express Logo" class="te-logo" >

				<section class="support-subhead">
					<h1 style="margin:0 0 .5em 0;font-size:25px;"><?php esc_html_e( 'Timeline Express Pro License' , 'timeline-express-pro' ); ?></h1>
					<?php if ( false !== $status && 'valid' === $status ) { ?>
						<p style="font-weight:200;"><?php esc_html_e( 'Thank you for purchasing the pro version!' , 'timeline-express-pro' ); ?></p>
						<p style="font-weight:200;"><?php esc_html_e( 'If you run into any issues, or need support, feel free to submit a support ticket via the contact form below.' , 'timeline-express-pro' ); ?></p>
					<?php } else { ?>
						<p style="font-weight:200;">
							<?php
							printf(
								/* translators: HTML anchor linking back to the Timeline Express documentation */
								esc_html__( "Please enter your license key in the field below. Your license key will grant you access to updates and priority support. Don't forget to check out our awesome %s.", 'timeline-express-pro' ),
								'<a href="https://www.wp-timelineexpress.com/documentation/" target="_blank" title="' . esc_attr__( 'Timeline Express Documentation', 'timeline-express-pro' ) . '">' . esc_html__( 'documentation', 'timeline-express-pro' ) . '</a>'
							);
							?>
						</p>
					<?php } ?>

				</section>

			</section>

			<hr />

			<form id="support-license-form" method="post" action="options.php">

				<?php settings_fields( 'timeline_express_license' ); ?>

				<label for="timeline_express_license_key">
					<strong>
						<?php esc_html_e( 'Support License Key', 'timeline-express-pro' ); ?>
					</strong>

					<p style="display:inline-block;width:100%;">

						<input id="timeline_express_license_key" placeholder="<?php esc_attr_e( 'Support license key' , 'timeline-express-pro' ); ?>" name="timeline_express_license_key" value="<?php echo esc_attr( $license ); ?>" <?php echo ( $license_valid ) ? 'disabled="disabled" type="password"' : 'type="text"'; ?> />

						<?php
						if ( ! $license_valid ) {

							if ( ! empty( $license_data->error ) ) {

								switch ( $license_data->error ) {

									default:
										$error_text = __( 'An unknown error occured.', 'timeline-express-ajax-limit-add-on' );

										break;

									case 'item_name_mismatch':
										$error_text = __( 'The license key you entered does not appear to be for this product. Please double check your license key.', 'timeline-express-ajax-limit-add-on' );

										break;

									case 'no_activations_left':
										$error_text = sprintf( /* translators: Anchor tag linking to user account, to upgrade the plugin license. */ __( 'You have reached the limit on the number of sites this license key can be active on. Please %s.', 'timeline-express-pro' ), '<a href="https://www.wp-timelineexpress.com/account/">' . esc_html__( 'upgrade your license', 'timeline-express-pro' ) . '</a>' );

										break;

									case 'missing':
										$error_text = __( 'Your license key appears to be invalid. Please double check that you have entered a valid license key.', 'timeline-express-ajax-limit-add-on' );

										break;

								}
								?>
								<p class="description license-error">
									<span class="dashicons dashicons-no-alt timeline-express-invalid-license-error"></span>
									<?php echo wp_kses_post( $error_text ); ?>
								</p>
								<?php
							}// End if().
						} else {
							?>
							<p class="description license-active">
								<span class="dashicons dashicons-yes timeline-express-valid-license" title="<?php esc_attr_e( 'Valid and Active License' , 'timeline-express-ajax-limit-add-on' ); ?>"></span>
								<?php esc_html_e( 'Active License', 'timeline-express-ajax-limit-add-on' ); ?>
							</p>
							<?php
						}// End if().

						// Purchase/Retrieve License Button
						if ( isset( $status ) && 'valid' !== $status ) {
							?>
							<section style="display:block;width:100%;margin:8px 0 0 6px;" class="license-links">
								<a href="https://www.wp-timelineexpress.com/products/timeline-express/" title="<?php esc_attr_e( 'Purchase a Support License Now', 'timeline-express-ajax-limit-add-on' ); ?>" target="_blank">
									<input type="button" class="button-secondary purchase-support-license" value="<?php esc_attr_e( 'Purchase a License' , 'timeline-express-ajax-limit-add-on' ); ?>">
								</a> &nbsp;
								<a href="https://www.wp-timelineexpress.com/account/" title="<?php esc_attr_e( 'Retrieve License', 'timeline-exoress' ); ?>" target="_blank">
									<input type="button" class="button-secondary purchase-support-license" value="<?php esc_attr_e( 'Retrieve License' , 'timeline-express-ajax-limit-add-on' ); ?>">
								</a>
							</section>
							<?php
						}
						?>
				</label>

				<section class="timeline-express-license-buttons">
					<!-- when active key, display a support ticketing form -->
					<?php if ( false !== $license ) { ?>
						<?php if ( false !== $status && 'valid' === $status ) { ?>
							<?php wp_nonce_field( 'timeline_express_nonce', 'timeline_express_nonce' ); ?>
							<input type="submit" class="button-secondary" name="timeline_express_license_deactivate" value="<?php esc_attr_e( 'Deactivate License', 'timeline-express-pro' ); ?>"/>
						<?php } else { ?>
							<?php wp_nonce_field( 'timeline_express_nonce', 'timeline_express_nonce' ); ?>
							<input type="submit" class="button-primary" name="timeline_express_license_activate" value="<?php esc_attr_e( 'Save and Activate License', 'timeline-express-pro' ); ?>"/>
					<?php
}
} else {
						wp_nonce_field( 'timeline_express_nonce', 'timeline_express_nonce' );
						?>
						<input type="submit" class="button-primary" name="timeline_express_license_activate" value="<?php esc_attr_e( 'Save and Activate License', 'timeline-express-pro' ); ?>"/>
					<?php } ?>
				</section>

			</form>

			<?php
			if ( false !== $license ) {

				if ( false !== $status && 'valid' === $status ) {

					$license_data = get_option( 'timeline_express_license_data' );
					?>

					<hr style="margin-bottom:2.5em;" />

					<div style="width:100%; display:inline-block;">

						<table class="widefat fixed" cellspacing="0" style="width:100%;max-width:500px; float:right;">
							<thead>
								<tr>
									<th id="columnname" class="manage-column column-columnname" scope="col">
										<?php esc_html_e( 'License Info.' , 'timeline-express-pro' ); ?>
									</th>
									<th id="columnname" class="manage-column column-columnname num" scope="col"></th>
								</tr>
							</thead>

							<tbody>

								<tr class="alternate">
									<td class="column-columnname"><b><?php esc_html_e( 'License Holder' , 'timeline-express-pro' ); ?></b></td>
									<td class="column-columnname" style="text-align:center;"><?php echo esc_html( $license_data->customer_name ); ?></td>
								</tr>

								<tr class="alternate">
									<td class="column-columnname"><b><?php esc_html_e( 'Sites Active/Limit' , 'timeline-express-pro' ); ?></b></td>
									<td class="column-columnname" style="text-align:center;"><?php echo esc_html( $license_data->site_count . '/' . $license_data->license_limit ); ?></td>
								</tr>

								<tr>
									<td class="column-columnname"><b><?php esc_html_e( 'License Expires' , 'timeline-express-pro' ); ?></b></td>
									<td class="column-columnname" style="text-align:center;">
										<?php
										echo esc_html( date_i18n( 'F jS, Y' , strtotime( $license_data->expires ) ) );
										$days_remaining = ( strtotime( $license_data->expires ) - strtotime( 'now' ) ) / ( 60 * 60 * 24 );
										if ( round( $days_remaining ) < 30 ) {
											echo '<span class="license-expiring-soon">expiring soon</span>';
										}
										?>
									</td>
								</tr>

							</tbody>
						</table>


						<section id="premium-support-contact-form">
							<h2 style="margin-bottom:.5em;margin-top:0;"><?php esc_html_e( 'Premium Support Ticketing' , 'timeline-express-pro' ); ?></h2>
							<?php
							// check if the user has sent a request in the past hour
							if ( false === get_transient( 'timeline_express_support_request_sent' ) ) {

								require_once TIMELINE_EXPRESS_PATH . 'lib/support-files/support-contact-form.php';

							} else {

								esc_attr__( 'It looks like you recently sent us a support request. We limit the number of support requests to 1 per hour, to prevent spam. Sorry for the inconvinience, and thank you for understanding.' , 'timeline-express-pro' );

							}
							?>
						</section>

					</div>

			<?php
				}// End if().
			}// End if().
		}// End if().

		// action hook for additional settings sections
		do_action( 'timeline-express-support-sections', $active_tab );

		?>
	</div>

	<section id="eh-logos" style="display:block;width:100%;text-align:right;">
		<a href="http://www.codeparrots.com" target="_blank" title="Code Parrots | Professional WordPress Plugins">
			<img src="<?php echo esc_url( TIMELINE_EXPRESS_URL ); ?>lib/admin/images/code-parrots-logo-dark.png" alt="Code Parrot Logo" style="width:300px;margin-right:2em;"><br />
		</a>
	</section>

</div>
