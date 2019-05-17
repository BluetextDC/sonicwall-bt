<?php

namespace ACP\Editing\Model\Post;

use AC;
use ACP\Editing\Model;

/**
 * @property AC\Column\Post\PageTemplate $column
 */
class PageTemplate extends Model\Post {

	public function __construct( AC\Column\Post\PageTemplate $column ) {
		parent::__construct( $column );
	}

	public function get_view_settings() {
		return array(
			'type'    => 'select',
			'options' => array_merge( array( '' => __( 'Default Template' ) ), array_flip( (array) $this->column->get_page_templates() ) ),
		);
	}

	public function save( $id, $value ) {
		$this->update_post( $id );

		return $this->has_error() || false !== update_post_meta( $id, '_wp_page_template', $value );
	}

}