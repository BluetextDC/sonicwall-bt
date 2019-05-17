<?php

namespace ACP\Search\Comparison\User\Date;

use ACP\Search\Comparison;

class Registered extends Comparison\User\Date {

	protected function get_field() {
		return 'user_registered';
	}

}