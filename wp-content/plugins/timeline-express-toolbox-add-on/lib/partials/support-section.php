<?php
// required to ensure compat. with CMB2
wp_enqueue_script( 'jquery-ui-sortable' );

/* Premium support template */
$license       = get_option( 'timeline_express_toolbox_license_key' );
$status        = get_option( 'timeline_express_toolbox_license_status' );
$license_data  = get_option( 'timeline_express_toolbox_license_data' );
$license_valid = ( false !== $license  && false !== $status && 'valid' === $status ) ? true : false;

// if this is the free version - we need to wrap it
if ( is_plugin_active( 'timeline-express/timeline-express.php' ) ) { ?>
	<div id="timeline-express-support-page-wrap">
<?php } ?>

<section id="timeline-express-support-page-header">

	<img src="<?php echo TIMELINE_EXPRESS_TOOLBOX_URL . 'lib/support-files/images/timeline-express-toolbox-logo-256.png'; ?>" title="Timeline Express Logo" class="te-logo" >

	<section class="support-subhead">
		<h1 style="margin:0 0 .5em 0;font-size:25px;">
			<?php esc_html_e( 'Timeline Express - Toolbox Add-On License' , 'timeline-express-toolbox-add-on' ); ?>
		</h1>
		<?php if ( false !== $status && 'valid' === $status ) { ?>
			<p style="font-weight:200;"><?php esc_html_e( 'Thank you for purchasing the Timeline Express Toolbox add-on!' , 'timeline-express-toolbox-add-on' ); ?></p>
			<p style="font-weight:200;"><?php esc_html_e( 'If you run into any issues, or need support, feel free to submit a support ticket via the contact form below.' , 'timeline-express-pro' ); ?></p>
		<?php } else { ?>
			<p style="font-weight:200;">
				<?php esc_html_e( 'Please enter your license key in below.' , 'timeline-express-pro' ); ?>
				<?php esc_html_e( 'Your license key will grant you access to updates and priority support.' , 'timeline-express-pro' ); ?>
			</p>
		<?php } ?>

	</section>

</section>

<hr />

<form id="support-license-form" method="post" action="options.php">

	<?php settings_fields( 'timeline_express_toolbox_license' ); ?>

	<label for="timeline_express_license_key">

		<strong style="display:block;width:100%;"><?php esc_html_e( 'License Key' , 'timeline-express-pro' ); ?></strong>

			<input id="timeline_express_toolbox_license_key" style="width:25%;min-width:350px;" placeholder="<?php esc_attr_e( 'License key' , 'timeline-express-pro' ); ?>" name="timeline_express_toolbox_license_key" value="<?php esc_attr_e( $license ); ?>" <?php echo ( $license_valid ) ? 'disabled="disabled" type="password"' : 'type="text"'; ?> />

			<?php

			if ( ! $license_valid ) {
				if ( ! empty( $license_data->error ) ) {
					switch ( $license_data->error ) {
						default:
							$error_text = __( 'An unknown error occured.', 'timeline-express-pro' );
							break;
						case 'item_name_mismatch':
							$error_text = __( 'The license key you entered does not appear to be for this product. Please double check your license key.', 'timeline-express-pro' );
							break;
						case 'no_activations_left':
							$error_text = sprintf( __( 'You have reached the limit on the number of sites this license key can be active on. Please %s.', 'timeline-express-pro' ), '<a href="https://www.wp-timelineexpress.com/account/">upgrade your license</a>' );
							break;
						case 'missing':
							$error_text = __( 'Your license key appears to be invalid. Please double check that you have entered a valid license key.', 'timeline-express-pro' );
							break;
					}
					?>
					<p class="description license-error">
						<span class="dashicons dashicons-no-alt timeline-express-invalid-license-error"></span>
						<?php echo wp_kses_post( $error_text ); ?>
					</p>
					<?php
				}
			} else {
				?>
				<p class="description license-active">
					<span class="dashicons dashicons-yes timeline-express-valid-license" title="<?php esc_attr_e( 'Valid and Active License' , 'timeline-express-pro' ); ?>"></span>
					<?php esc_html_e( 'Active License', 'timeline-express-pro' ); ?>
				</p>
				<?php
			}

			// Purchase/Retrieve License Button
			if ( isset( $status ) && 'valid' !== $status ) {
				?>
				<section style="display:block;width:100%;margin:8px 0 0 6px;" class="license-links">
					<a href="https://www.wp-timelineexpress.com/products/timeline-express-toolbox-add/" title="<?php esc_attr__( 'Purchase a License Now', 'timeline-express-pro' ); ?>" target="_blank">
						<input type="button" class="button-secondary purchase-support-license" value="<?php esc_attr_e( 'Purchase a License' , 'timeline-express-pro' ); ?>">
					</a> &nbsp;
					<a href="https://www.wp-timelineexpress.com/account/" title="<?php esc_attr__( 'Retrieve License', 'timeline-express-pro' ); ?>" target="_blank">
						<input type="button" class="button-secondary purchase-support-license" value="<?php esc_attr_e( 'Retrieve License' , 'timeline-express-pro' ); ?>">
					</a>
				</section>
				<?php
			}
			?>
	</label>

	<section class="timeline-express-license-buttons">

		<!-- when active key, display a support ticketing form -->
		<?php if ( false !== $license ) {
			if ( false !== $status && 'valid' === $status ) { ?>
			<?php wp_nonce_field( 'timeline_express_toolbox_nonce', 'timeline_express_toolbox_nonce' ); ?>
			<input type="submit" class="button-secondary" name="timeline_express_toolbox_license_deactivate" value="<?php esc_attr_e( 'Deactivate License' ); ?>" />
		<?php } else { ?>
			<?php wp_nonce_field( 'timeline_express_toolbox_nonce', 'timeline_express_toolbox_nonce' ); ?>
			<input type="submit" class="button-primary" value="Save Changes" name="timeline_express_toolbox_license_activate" style="float:left; margin-right: 1em;">
		<?php } } else { ?>
			<?php wp_nonce_field( 'timeline_express_toolbox_nonce', 'timeline_express_toolbox_nonce' ); ?>
			<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save and Activate License', 'timeline-express' ); ?>" name="timeline_express_toolbox_license_activate" style="float:left; margin-right: 1em;">
		<?php } ?>

	</section>

</form>

<?php if ( false !== $license ) {

	if ( $status !== false && $status == 'valid' ) {

		$license_data = get_option( 'timeline_express_toolbox_license_data' ); ?>

		<hr style="margin-bottom:2.5em;" />

		<div style="width:100%; display:inline-block;">

			<table class="widefat fixed" cellspacing="0" style="width:100%;max-width:500px; float:right;">
				<thead>
					<tr>
						<th id="columnname" class="manage-column column-columnname" scope="col"><?php _e( 'License Info.' , 'timeline-express-pro' ); ?></th>
						<th id="columnname" class="manage-column column-columnname num" scope="col"></th>
					</tr>
				</thead>

				<tbody>

					<tr class="alternate">
						<td class="column-columnname"><b><?php _e( 'License Holder' , 'timeline-express-pro' ); ?></b></td>
						<td class="column-columnname" style="text-align:center;"><?php echo $license_data->customer_name; ?></td>
					</tr>

					<tr class="alternate">
						<td class="column-columnname"><b><?php _e( 'Sites Active/Limit' , 'timeline-express-pro' ); ?></b></td>
						<td class="column-columnname" style="text-align:center;"><?php echo $license_data->site_count . '/' . $license_data->license_limit; ?></td>
					</tr>

					<tr>
						<td class="column-columnname"><b><?php _e( 'License Expires' , 'timeline-express-pro' ); ?></b></td>
						<td class="column-columnname" style="text-align:center;"><?php echo date( 'F jS, Y' , strtotime( $license_data->expires ) ); $days_remaining = (strtotime( $license_data->expires ) - strtotime('now'))  / (60 * 60 * 24); if ( round( $days_remaining ) < 30 ) { echo '<span class="license-expiring-soon">expiring soon</span>'; } ?></td>
					</tr>

				</tbody>
			</table>


			<section id="premium-support-contact-form">
				<h2 style="margin-bottom:.5em;margin-top:0;"><?php _e( 'Premium Support Ticketing' , 'timeline-express-pro' ); ?></h2>
				<?php
					// check if the user has sent a request in the past hour
					if ( false === get_transient( 'timeline_express_toolbox_support_request_sent' ) ) {
						require_once plugin_dir_path(__FILE__) . 'support-contact-form.php';
					} else {
						_e( "It looks like you have recently sent us a support request. We limit the number of support requests to 1 per hour, to avoid spam. Sorry for the inconvinience, and thank you for understanding." , "timeline-express" );
					}
				?>
			</section>

		</div>

<?php
	}
}

// if this is the free version - we need to close the wrap
if ( is_plugin_active( 'timeline-express/timeline-express.php' ) ) { ?>
	</div>
<?php }

// free version, display logos
if ( is_plugin_active( 'timeline-express/timeline-express.php' ) ) { ?>
	<section id="eh-logos" style="position:absolute;right:0;bottom:-2em;">
		<a href="http://www.codeparrots.com" target="_blank" title="Code Parrots | Professional WordPress Plugins">
			<img src="<?php echo TIMELINE_EXPRESS_TOOLBOX_URL . 'lib/support-files/images/code-parrots-logo-dark.png'; ?>" alt="Code Parrot Logo" style="width:300px;margin-right:2em;"><br />
		</a>
	</section>
<?php } ?>
