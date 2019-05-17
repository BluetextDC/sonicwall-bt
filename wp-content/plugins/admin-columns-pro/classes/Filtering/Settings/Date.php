<?php

namespace ACP\Filtering\Settings;

class Date extends Ranged {

	protected function get_options() {
		return array(
			''            => __( 'Daily' ),
			'monthly'     => __( 'Monthly' ),
			'yearly'      => __( 'Yearly' ),
			'future_past' => __( 'Future / Past', 'codepress-admin-columns' ),
		);
	}

}