<?php

namespace ACP\ThirdParty\YoastSeo\Editing;

use ACP;
use ACP\Editing;
use ACP\Helper\Select;

/**
 * @property ACP\ThirdParty\YoastSeo\Column\PrimaryTaxonomy $column
 */
class PrimaryTaxonomy extends Editing\Model\Meta
	implements Editing\PaginatedOptions {

	/**
	 * @param int $id
	 *
	 * @return array|false
	 */
	public function get_edit_value( $id ) {
		$term = $this->column->get_raw_value( $id );

		if ( ! $term ) {
			$terms = wp_get_post_terms( $id, $this->column->get_taxonomy() );

			if ( empty( $terms ) || is_wp_error( $terms ) ) {
				return null;
			}

			return false;
		}

		$term = get_term( $term, $this->column->get_taxonomy() );

		return array(
			$term->term_id => $term->name,
		);
	}

	public function get_view_settings() {
		return array(
			'type'                   => 'select2_dropdown',
			'multiple'               => false,
			'ajax_populate'          => true,
			self::VIEW_BULK_EDITABLE => false,
		);
	}

	public function get_paginated_options( $search, $page, $id = null ) {

		$entities = new Select\Entities\Taxonomy( array(
			'search'     => $search,
			'page'       => $page,
			'taxonomy'   => $this->column->get_taxonomy(),
			'object_ids' => array( $id ),
		) );

		return new Select\Options\Paginated(
			$entities,
			new Select\Formatter\TermName( $entities )
		);
	}

}