<?php

namespace ACP\Export\Asset\Script;

use ACP\Asset\Location;
use ACP\Asset\Script;

final class Table extends Script {

	/**
	 * @param string   $handle
	 * @param Location $location
	 */
	public function __construct( $handle, Location $location ) {
		parent::__construct( $handle, $location, array( 'jquery' ) );
	}

	public function register() {
		global $wp_list_table;

		if ( ! $wp_list_table ) {
			return;
		}

		parent::register();

		wp_localize_script( $this->get_handle(), 'ACP_Export', array(
			'total_num_items' => $wp_list_table->get_pagination_arg( 'total_items' ),
			'i18n'            => array(
				'Export'                                                                                                                           => __( 'Export', 'codepress-admin-columns' ),
				'Export to CSV'                                                                                                                    => __( 'Export to CSV', 'codepress-admin-columns' ),
				'Exporting current list of items.'                                                                                                 => __( 'Exporting current list of items.', 'codepress-admin-columns' ),
				'Processed {0} of {1} items ({2}%).'                                                                                               => __( 'Processed {0} of {1} items ({2}%).', 'codepress-admin-columns' ),
				'Export completed ({0} items). Your download will start automatically. If this does not happen, you can download the file again: ' => __( 'Export completed ({0} items). Your download will start automatically. If this does not happen, you can download the file again: ', 'codepress-admin-columns' ),
				'Download File'                                                                                                                    => __( 'Download File', 'codepress-admin-columns' ),
			),
		) );
	}

}