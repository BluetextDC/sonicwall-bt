<?php

final class Timeline_Express_Date_Time_Activation {

	public function __construct() {

		$this->check_for_announcements();

	}

	/**
	 * Check for any announcements that don't contain an announcement date/time
	 *
	 * @return [type] [description]
	 */
	private function check_for_announcements() {

		$query = new WP_Query( [
			'post_type'      => 'te_announcements',
			'posts_per_page' => -1,
			'meta_query'     => [
				[
					'key'     => 'announcement-date-time',
					'compare' => 'NOT EXISTS',
				],
			],
		] );

		if ( ! $query->have_posts() ) {

			return;

		}

		update_option( 'timeline_express_date_time_migration_notice', true );
		update_option( 'timeline_express_date_time_migration_ids', wp_list_pluck( $query->posts, 'ID' ) );

	}

}
new Timeline_Express_Date_Time_Activation();
