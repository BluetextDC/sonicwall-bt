<?php

namespace ACP\Export;

use AC;
use ACP\Asset\Enqueueable;

class TableScreen implements AC\Registrable {

	/**
	 * @var Enqueueable[]
	 */
	protected $assets;

	/**
	 * @param array $assets
	 */
	public function __construct( array $assets ) {
		$this->assets = $assets;
	}

	public function register() {
		add_action( 'ac/table_scripts', array( $this, 'scripts' ) );
		add_action( 'admin_footer', array( $this, 'export_form' ) );
	}

	public function scripts() {
		foreach ( $this->assets as $asset ) {
			$asset->enqueue();
		}
	}

	/**
	 * Output the form that holds the export query arguments
	 * @since 1.0
	 */
	public function export_form() {
		?>
		<form action="" method="post" id="acp-export">
			<?php wp_nonce_field( 'acp_export_listscreen_export', '_wpnonce', false ); ?>
			<input type="submit" class="button button-secondary"/>
		</form>
		<?php
	}

}