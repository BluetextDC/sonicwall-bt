<?php
namespace ACP\Sorting\Admin\Section;

use AC\Admin\Section;
use AC\Capabilities;
use AC\Message;
use AC\Preferences;
use AC\Registrable;

class ResetSorting extends Section
	implements Registrable {

	public function __construct() {
		parent::__construct( 'reset-sorting', __( 'Sorting Preferences', 'codepress-admin-columns' ), __( 'This will reset the sorting preference for all users.', 'codepress-admin-columns' ) );
	}

	public function register() {
		$this->handle_request();
	}

	/**
	 * Reset all sorting preferences for all users
	 */
	private function handle_request() {
		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			return;
		}
		if ( ! wp_verify_nonce( filter_input( INPUT_POST, '_acnonce' ), 'reset-sorting-preference' ) ) {
			return;
		}

		$preference = new Preferences\Site( 'sorted_by' );
		$preference->reset_for_all_users();

		$notice = new Message\Notice( __( 'All sorting preferences have been reset.', 'codepress-admin-columns' ) );
		$notice->register();
	}

	public function display_fields() {
		?>
		<form action="" method="post">
			<?php wp_nonce_field( 'reset-sorting-preference', '_acnonce' ); ?>
			<input type="submit" class="button" value="<?php _e( 'Reset sorting preferences', 'codepress-admin-columns' ); ?>">
		</form>
		<?php
	}

}