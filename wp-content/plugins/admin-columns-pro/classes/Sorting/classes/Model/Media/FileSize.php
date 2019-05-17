<?php

namespace ACP\Sorting\Model\Media;

use ACP\Sorting\Model;

class FileSize extends Model {

	public function get_sorting_vars() {
		$ids = array();

		foreach ( $this->strategy->get_results() as $id ) {
			$value = false;

			if ( $file = get_attached_file( $id ) ) {
				$value = is_file( $file ) ? filesize( $file ) : false;
			}

			if ( $value || acp_sorting()->show_all_results() ) {
				$ids[ $id ] = $value;
			}
		}

		return array(
			'ids' => $this->sort( $ids ),
		);
	}
}