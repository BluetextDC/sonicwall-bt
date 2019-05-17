<?php

namespace ACP\Check;

use AC\Ajax;
use AC\Capabilities;
use AC\Message;
use AC\Registrable;
use AC\Screen;
use AC\Storage;
use ACP\License;

class Expired
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
		add_action( 'ac/screen', array( $this, 'display' ) );

		$this->get_ajax_handler()->register();
	}

	/**
	 * @param Screen $screen
	 *
	 * @throws \Exception
	 */
	public function display( Screen $screen ) {
		if ( ! $screen->has_screen()
		     || ! current_user_can( Capabilities::MANAGE )
		     || ! $this->license->is_active()
		     || ! $this->license->is_expired()
		) {
			return;
		}

		if ( $screen->is_plugin_screen() ) {
			// Inline message on plugin page
			$notice = new Message\Plugin( $this->get_message(), ACP()->get_basename() );
		} else if ( $screen->is_admin_screen( 'settings' ) ) {
			// Permanent displayed on settings page
			$notice = new Message\Notice( $this->get_message() );
		} else if ( $screen->is_admin_screen( 'columns' ) && $this->get_dismiss_option()->is_expired() ) {
			// Dismissible on columns page
			$notice = new Message\Notice\Dismissible( $this->get_message(), $this->get_ajax_handler() );
		} else {
			$notice = false;
		}

		if ( $notice instanceof Message ) {
			$notice
				->set_type( Message::WARNING )
				->register();
		}
	}

	/**
	 * @return string
	 */
	private function get_message() {
		$expired_on = date_i18n( get_option( 'date_format' ), $this->license->get_expiry_date() );
		$my_account_link = ac_helper()->html->link( ac_get_site_utm_url( 'my-account', 'renewal' ), __( 'My Account Page', 'codepress-admin-columns' ) );

		return sprintf(
			__( 'Your Admin Columns Pro license has expired on %s. To receive updates, renew your license on the %s.', 'codepress-admin-columns' ),
			$expired_on,
			$my_account_link
		);
	}

	/**
	 * @return Ajax\Handler
	 */
	protected function get_ajax_handler() {
		$handler = new Ajax\Handler();
		$handler
			->set_action( 'ac_notice_dismiss_expired' )
			->set_callback( array( $this, 'ajax_dismiss_notice' ) );

		return $handler;
	}

	/**
	 * @return Storage\Timestamp
	 * @throws \Exception
	 */
	protected function get_dismiss_option() {
		return new Storage\Timestamp(
			new Storage\UserMeta( 'ac_notice_dismiss_expired' )
		);
	}

	/**
	 * @throws \Exception
	 */
	public function ajax_dismiss_notice() {
		$this->get_ajax_handler()->verify_request();
		$this->get_dismiss_option()->save( time() + ( MONTH_IN_SECONDS * 4 ) );
	}

}