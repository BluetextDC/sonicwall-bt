<?php

namespace PixelCaffeine\Job;


use PixelCaffeine\Model\Job;

class RefreshAudiencesSize extends Job {

	public function tasks() {
		$tasks = array(
			'daily' => array(
				'hook' => 'aepc_refresh_audiences_size',
				'callback' => array( $this, 'task' ),
				'callback_args' => array()
			)
		);

		return $tasks;
	}

	/**
	 * The product catalog refresh task
	 */
	public function task() {
		\AEPC_Admin_CA_Manager::refresh_approximate_counts();
	}

}
