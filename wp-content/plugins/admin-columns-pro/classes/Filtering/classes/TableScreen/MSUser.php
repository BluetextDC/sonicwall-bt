<?php

namespace ACP\Filtering\TableScreen;

class MSUser extends User {

	public function __construct( array $models ) {
		parent::__construct( $models );

		add_action( 'in_admin_footer', array( $this, 'render_markup' ) );
	}

}