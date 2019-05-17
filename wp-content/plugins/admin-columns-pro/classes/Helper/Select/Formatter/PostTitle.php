<?php

namespace ACP\Helper\Select\Formatter;

use ACP\Helper\Select\Entities;
use ACP\Helper\Select\Formatter;
use ACP\Helper\Select\Value;
use WP_Post;

class PostTitle extends Formatter {

	public function __construct( Entities $entities, Value $value = null ) {
		if ( null === $value ) {
			$value = new Value\Post();
		}

		parent::__construct( $entities, $value );
	}

	/**
	 * @param WP_Post $post
	 *
	 * @return string
	 */
	public function get_label( $post ) {
		$label = $post->post_title;

		if ( 'attachment' === $post->post_type ) {
			$label = ac_helper()->image->get_file_name( $post->ID );
		}

		if ( ! $label ) {
			$label = sprintf( __( '#%d (no title)' ), $post->ID );
		}

		return $label;
	}

}