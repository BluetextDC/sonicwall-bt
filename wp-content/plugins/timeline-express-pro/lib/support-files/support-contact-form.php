<?php
/**
 * Support Contact Form
 * Sends support requests directly to the support staff
 * @since 1.0
 */
if ( isset( $_REQUEST['action'] ) ) {

	$action = $_REQUEST['action'];

}

/**
 * Display the form
 */
if ( ! isset( $action ) ) { ?>
	<p>
		<?php esc_html_e( 'If you need support, please fill out the following form. We will get back to you as soon as possible.', 'timeline-express-pro' ); ?>
	</p>
	<p>
		<em>
			<?php esc_html_e( 'Note: support requests are limited to one per hour, to help reduce spam.', 'timeline-express-pro' ); ?>
		</em>
	</p>
	<form  action="" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="action" value="submit" />
		<label for="name">
			<?php esc_html_e( 'Your name', 'timeline-express-pro' ); ?>: <br />
			<input name="name" type="text" value="<?php echo esc_attr( $license_data->customer_name ); ?>" size="30"/>
		</label>
		<label for="message"><?php esc_html_e( 'Your message', 'timeline-express-pro' ); ?>:<br>
			<textarea name="message" rows="7" cols="30" placeholder="<?php esc_attr_e( 'Please describe your issue in as much detail as possible', 'timeline-express-pro' ); ?>"></textarea>
		</label>
		<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Get Support', 'timeline-express-pro' ); ?>"/>
	</form>
	<?php
} else {

	$name = trim( $_REQUEST['name'] );

	$email = $license_data->customer_email;

	// append the users license key, and other data
	$message = '<strong>License Key :</strong> ' . $license . '<br /><strong>Expires :</strong> ' . date_i18n( 'F jS, Y', strtotime( $license_data->expires ) ) . ' <br /><br /> <strong>Support Issue :</strong><br />' . $_REQUEST['message'];

	// Check submitted data - if empty, abort
	if ( empty( $name ) || empty( $email ) || empty( $_REQUEST['message'] ) ) {

		echo esc_attr__( 'All fields are required, please fill in the form again.', 'timeline-express-pro' );

		return;

	}

	// Submit the form
	$from         = "From: $name <$email>";
	$content_type = 'Content-type: text/html';
	$subject      = esc_attr( 'Premium Support Request: Timeline Express' );
	$headers      = array( $from, $content_type );

	$email = wp_mail( 'codeparrots@gmail.com', $subject, stripslashes( $message ), $headers );

	if ( is_wp_error( $email ) ) {

		echo '<p>' . esc_html__( 'There was an error sending your request', 'timeline-express-pro' ) . ': ' . esc_attr( $email->get_error_message() ) . '</p>';
		echo '<p>' . sprintf( /* translators: Anchor tag linking to the Timeline Express support email address. */ esc_html__( 'If the error persists, please contact our support team directly for support at %s.', 'timeline-express-pro' ), '<a href="mailto:codeparrots@gmail.com" title="' . esc_attr__( 'Email Support', 'timeline-express-pro' ) . '">codeparrots@gmail.com</a></p>' );

	} else {

		if ( $email ) {

			set_transient( 'timeline_express_support_request_sent', '1', 1 * HOUR_IN_SECONDS );

			echo '<p>' . esc_html__( 'Support request successfully sent. I will be in touch regarding your issue shortly.', 'timeline-express-pro' ) . '</p>';

		} // @codingStandardsIgnoreLine

	} // @codingStandardsIgnoreLine

} // End if().
