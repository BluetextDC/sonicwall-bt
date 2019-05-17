<?php

namespace ACP\Sorting\Model;

use ACP\Sorting\Model;

class Disabled extends Model {

	public function is_active() {
		return false;
	}

	public function register_settings() {
	}

}