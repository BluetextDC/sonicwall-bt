<?php

namespace ACP\Sorting\Model\Media;

class Height extends Dimensions {

	protected function get_aspect( $values ) {
		return $this->get_height( $values );
	}

}