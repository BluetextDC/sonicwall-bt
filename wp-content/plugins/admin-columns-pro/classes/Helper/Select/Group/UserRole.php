<?php

namespace ACP\Helper\Select\Group;

use AC;
use ACP\Helper\Select\Formatter;
use ACP\Helper\Select\Group;
use ACP\Helper\Select\Option;
use WP_User;

class UserRole extends Group {

	/**
	 * @var WP_User[]
	 */
	private $helper;

	/**
	 * @param Formatter $formatter
	 */
	public function __construct( Formatter $formatter ) {
		$this->helper = new AC\Helper\User();

		parent::__construct( $formatter );
	}

	/**
	 * @param WP_User $user
	 * @param Option  $option
	 *
	 * @return string
	 */
	public function get_label( $user, Option $option ) {
		return $this->helper->get_role_name( $user->roles[0] );
	}

}