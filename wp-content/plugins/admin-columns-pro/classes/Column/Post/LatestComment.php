<?php

namespace ACP\Column\Post;

use AC;
use ACP\Export;
use ACP\Sorting;

/**
 * @since 4.2
 */
class LatestComment extends AC\Column
	implements Export\Exportable, Sorting\Sortable {

	public function __construct() {
		$this->set_type( 'column-latest_comment' );
		$this->set_label( __( 'Latest Comment', 'codepress-admin-columns' ) );
	}

	public function is_valid() {
		return post_type_supports( $this->get_post_type(), 'comments' );
	}

	public function get_raw_value( $post_id ) {
		$comments = get_comments( array(
			'number'  => 1,
			'fields'  => 'ids',
			'post_id' => $post_id,
		) );

		if ( empty( $comments ) ) {
			return false;
		}

		return $comments[0];
	}

	public function register_settings() {
		$this->add_setting( new AC\Settings\Column\Comment( $this ) );
	}

	public function sorting() {
		return new Sorting\Model\Value( $this );
	}

	public function export() {
		return new Export\Model\StrippedValue( $this );
	}

}