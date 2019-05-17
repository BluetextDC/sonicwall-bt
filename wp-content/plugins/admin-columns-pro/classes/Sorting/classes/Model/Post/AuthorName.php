<?php

namespace ACP\Sorting\Model\Post;

use ACP;
use ACP\Sorting\Model;

/**
 * @property ACP\Column\Post\AuthorName $column
 */
class AuthorName extends Model\Post\Field {

	public function __construct( $column ) {
		parent::__construct( $column );

		$this->set_field( 'post_author' );
	}

	protected function format( $value ) {
		/** @var ACP\Settings\Column\User $setting */
		$setting = $this->column->get_setting( 'user' );

		return $setting->get_user_name( $value );
	}

}