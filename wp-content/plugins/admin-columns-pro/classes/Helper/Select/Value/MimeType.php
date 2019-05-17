<?php
namespace ACP\Helper\Select\Value;

use ACP\Helper\Select\Value;

final class MimeType
	implements Value {

	public function get_value( $mime_type ) {
		return $mime_type;
	}

}