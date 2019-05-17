<?php
namespace ACP\Admin;

use AC\Ajax;
use AC\Registrable;
use AC\View;

class Feedback implements Registrable {

	public function register() {
		add_action( 'ac/admin/render', array( $this, 'feedback_render' ) );

		$this->get_ajax_handler()->register();
	}

	public function feedback_render() {
		wp_enqueue_style( 'acp-feedback', ACP()->get_url() . 'assets/core/css/feedback.css', array(), ACP()->get_version() );
		wp_enqueue_script( 'acp-feedback', ACP()->get_url() . 'assets/core/js/feedback.js', array(), ACP()->get_version() );

		$feedback = new View( array(
			'nonce' => $this->get_ajax_handler()->get_param( '_ajax_nonce' ),
			'email' => wp_get_current_user()->user_email,
		) );

		$feedback->set_template( 'admin/modal-feedback' );

		echo $feedback;
	}

	/**
	 * @return Ajax\Handler
	 */
	protected function get_ajax_handler() {
		$handler = new Ajax\Handler();
		$handler->set_action( 'acp-send-feedback' )
		        ->set_callback( array( $this, 'ajax_send_feedback' ) );

		return $handler;
	}

	public function ajax_send_feedback() {
		$this->get_ajax_handler()->verify_request();

		$email = filter_input( INPUT_POST, 'email', FILTER_SANITIZE_EMAIL );

		if ( ! is_email( $email ) ) {
			wp_send_json_error( __( 'Please insert a valid email so we can reply to your feedback.', 'codepress-admin-columns' ) );
		}

		$feedback = filter_input( INPUT_POST, 'feedback', FILTER_SANITIZE_STRING );

		if ( empty( $feedback ) ) {
			wp_send_json_error( __( 'Your feedback form is empty.', 'codepress-admin-columns' ) );
		}

		$headers = array(
			sprintf( 'From: <%s>', trim( $email ) ),
			'Content-Type: text/html',
		);

		wp_mail(
			acp_support_email(),
			sprintf( 'Beta Feedback on Admin Columns Pro %s', ACP()->get_version() ),
			nl2br( $feedback ),
			$headers
		);

		wp_send_json_success( __( 'Thank you very much for your feedback!' ) );
	}

}