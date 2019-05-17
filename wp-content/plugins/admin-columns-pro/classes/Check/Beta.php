<?php

namespace ACP\Check;

use AC;
use AC\Message\Notice;
use ACP\Admin\Feedback;

class Beta
	implements AC\Registrable {

	/** @var Feedback */
	private $feedback;

	public function __construct( Feedback $feedback ) {
		$this->feedback = $feedback;
	}

	public function register() {
		$notice = new Notice( $this->get_message() );
		$notice->set_type( Notice::WARNING )
		       ->enqueue_scripts();

		$this->feedback->register();

		add_action( 'ac/admin/render', array( $notice, 'display' ) );
	}

	/**
	 * @return string
	 */
	protected function get_feedback_link() {
		return ac_get_site_utm_url( 'forums/forum/beta-feedback/', 'beta-notice' );
	}

	/**
	 * @return string
	 */
	protected function get_message() {
		return implode( ' ', array(
			sprintf( __( 'You are using a beta version of %s.', 'codepress-admin-columns' ), 'Admin Columns Pro' ),
			sprintf( __( 'If you have feedback or have found a bug, please %s.', 'codepress-admin-columns' ),
				sprintf( '<a href="#" data-ac-modal="feedback">%s</a>', __( 'leave us a message', 'codepress-admin-columns' ) )
			),
		) );
	}

}