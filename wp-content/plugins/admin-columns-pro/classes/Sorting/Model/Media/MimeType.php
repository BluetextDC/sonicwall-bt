<?php

namespace ACP\Sorting\Model\Media;

use ACP\Sorting\Model;

class MimeType extends Model {

	public function get_sorting_vars() {
		add_filter( 'posts_orderby', array( $this, 'posts_orderby_callback' ) );

		return array(
			'suppress_filters' => false,
		);
	}

	public function posts_orderby_callback() {
		global $wpdb;

		remove_filter( 'posts_orderby', array( $this, __FUNCTION__ ), 10 );

		return $wpdb->posts . '.post_mime_type ' . $this->get_order();
	}

}