<?php

namespace ACP\Filtering\Model\User;

use ACP\Column;
use ACP\Filtering\Model;

/**
 * @property Column\User\Language $column
 */
class Language extends Model\Meta {

	public function __construct( Column\User\Language $column ) {
		parent::__construct( $column );
	}

	public function get_filtering_data() {
		$options = $this->column->get_language_options( false );

		$options = array( 'cpac_empty' => _x( 'Site Default', 'default site language' ) ) + $options;

		return array(
			'options' => $options,
			'order'   => false,
		);
	}

}