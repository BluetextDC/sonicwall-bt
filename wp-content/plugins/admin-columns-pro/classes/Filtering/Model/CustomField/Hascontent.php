<?php

namespace ACP\Filtering\Model\CustomField;

use ACP\Filtering\Model;

class Hascontent extends Model\CustomField {

	public function get_filtering_data() {
		return array(
			'empty_option' => $this->get_empty_labels( __( 'Content', 'codepress-admin-columns' ) ),
		);
	}

}