<?php

namespace ACP\Check;

use AC\Ajax;
use AC\Capabilities;
use AC\Message;
use AC\Registrable;
use AC\Screen;
use AC\Storage;
use ACP\License;

class Activation
	implements Registrable {

	/**
	 * @var License
	 */
	protected $license;

	/**
	 * @param License $license
	 */
	public function __construct( License $license ) {
		$this->license = $license;
	}

	/**
	 * @throws \Exception
	 */
	public function register() {
		add_action( 'ac/screen', array( $this, 'register_notice' ) );

		$this->get_ajax_handler()->register();
	}

	/**
	 * @param Screen $screen
	 *
	 * @throws \Exception
	 */
	public function register_notice( Screen $screen ) {
		if ( ! $screen->has_screen()
		     || ! current_user_can( Capabilities::MANAGE )
		     || $this->license->is_active()
		) {
			return;
		}

		// Inline message on plugin page
		if ( $screen->is_plugin_screen() ) {
			$notice = new Message\Plugin( $this->get_message(), ACP()->get_basename() );
			$notice
				->set_type( Message::INFO )
				->register();
		}

		// Permanent message on admin page
		if ( $screen->is_admin_screen() ) {
			$notice = new Message\Notice( $this->get_message() );
			$notice
				->set_type( Message::INFO )
				->register();
		}

		// Dismissible on list tables
		if ( $screen->get_list_screen() && $this->get_dismiss_option()->is_expired() ) {
			$notice = new Message\Notice\Dismissible( $this->get_message(), $this->get_ajax_handler() );
			$notice
				->set_type( Message::INFO )
				->register();
		}
	}

	/**
	 * return string
	 */
	private function get_message() {
		$message = sprintf(
			__( "To enable automatic updates <a href='%s'>enter your license key</a>. If you don't have a licence key, please see <a href='%s' target='_blank'>details & pricing</a>.", 'codepress_admin_columns' ),
			ac_get_admin_url( 'settings' ),
			ac_get_site_utm_url( 'pricing-purchase', 'plugins' )
		);

		return $message;
	}

	/**
	 * @return Ajax\Handler
	 */
	private function get_ajax_handler() {
		$handler = new Ajax\Handler();
		$handler
			->set_action( 'ac_notice_dismiss_activation' )
			->set_callback( array( $this, 'ajax_dismiss_notice' ) );

		return $handler;
	}

	/**
	 * @return Storage\Timestamp
	 * @throws \Exception
	 */
	private function get_dismiss_option() {
		return new Storage\Timestamp(
			new Storage\UserMeta( 'ac_notice_dismiss_activation' )
		);
	}

	/**
	 * @throws \Exception
	 */
	public function ajax_dismiss_notice() {
		$this->get_ajax_handler()->verify_request();
		$this->get_dismiss_option()->save( time() + ( MONTH_IN_SECONDS * 4 ) );

		wp_die( 1 );
	}

}