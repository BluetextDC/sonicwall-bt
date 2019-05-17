<?php

namespace ACP\Editing\Model\Post;

use AC\Storage\Transaction;
use ACP\Editing\Model;

class Attachment extends Model\Post {

	public function get_view_settings() {
		return array(
			'type'         => 'media',
			'attachment'   => array(
				'disable_select_current' => true,
			),
			'multiple'     => true,
			'store_values' => true,
		);
	}

	public function save( $id, $value ) {
		$attachment_ids = get_posts( array(
			'post_type'      => 'attachment',
			'post_parent'    => $id,
			'posts_per_page' => -1,
			'fields'         => 'ids',
		) );

		$transaction = new Transaction();

		$results = array();

		// Detach
		if ( $attachment_ids ) {
			foreach ( $attachment_ids as $attachment_id ) {
				$results[] = $this->update_post( $attachment_id, array(
					'post_parent' => '',
				) );
			}
		}

		// Attach
		if ( ! empty( $value ) ) {
			foreach ( $value as $attachment_id ) {
				$results[] = $this->update_post( $attachment_id, array(
					'post_parent' => $id,
				) );
			}
		}

		if ( in_array( false, $results, true ) ) {
			$transaction->rollback();

			return false;
		}

		$transaction->commit();

		return true;
	}

}