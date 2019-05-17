<?php

namespace ACP\Helper\Select\Entities;

use ACP\Helper\Select;
use ACP\Helper\Select\Value;
use WP_Query;

class Post extends Select\Entities
	implements Select\Paginated {

	/**
	 * @var WP_Query
	 */
	protected $query;

	/**
	 * @param array $args
	 * @param Value $value
	 */
	public function __construct( array $args = array(), Value $value = null ) {
		if ( null === $value ) {
			$value = new Value\Post();
		}

		$args = array_merge( array(
			'posts_per_page' => 30,
			'post_type'      => 'any',
			'orderby'        => 'title',
			'order'          => 'ASC',
			'paged'          => 1,
			's'              => null,
			'post_status'    => 'any',
		), $args );

		$this->query = new WP_Query( $args );

		parent::__construct( $this->query->get_posts(), $value );
	}

	/**
	 * @inheritDoc
	 */
	public function get_total_pages() {
		$per_page = $this->query->get( 'posts_per_page' );

		return ceil( $this->query->found_posts / $per_page );
	}

	/**
	 * @inheritDoc
	 */
	public function get_page() {
		return $this->query->get( 'paged' );
	}

	/**
	 * @inheritDoc
	 */
	public function is_last_page() {
		return $this->get_total_pages() <= $this->get_page();
	}

}