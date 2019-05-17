<?php

namespace ACP\Settings\Column;

use AC;

class CustomField extends AC\Settings\Column\CustomField {

	public function get_dependent_settings() {
		return array( new CustomFieldType( $this->column ) );
	}

}