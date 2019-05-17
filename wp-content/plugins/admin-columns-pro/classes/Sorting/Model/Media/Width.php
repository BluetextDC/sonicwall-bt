<?php

namespace ACP\Sorting\Model\Media;

class Width extends Dimensions {

	protected function get_aspect( $values ) {
		return $this->get_width( $values );
	}

}