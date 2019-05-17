<?php

namespace ACP\Filtering\Settings;

class DatePast extends Ranged {

	protected function get_options() {
		return array(
			''        => __( 'Daily' ),
			'monthly' => __( 'Monthly' ),
			'yearly'  => __( 'Yearly' ),
		);
	}

}