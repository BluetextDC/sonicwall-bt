<?php
/**
 * Copyright (C) 2014-2019 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

class Ai1wmde_Settings {

	public function revoke() {
		// Set Dropbox client
		$dropbox = new Ai1wmde_Dropbox_Client(
			get_option( 'ai1wmde_dropbox_token', false ),
			get_option( 'ai1wmde_dropbox_ssl', true )
		);

		// Revoke token
		$dropbox->revoke();

		// Remove token option
		delete_option( 'ai1wmde_dropbox_token' );

		// Remove cron option
		delete_option( 'ai1wmde_dropbox_cron' );

		// Reset cron schedules
		Ai1wm_Cron::clear( 'ai1wmde_dropbox_hourly_export' );
		Ai1wm_Cron::clear( 'ai1wmde_dropbox_daily_export' );
		Ai1wm_Cron::clear( 'ai1wmde_dropbox_weekly_export' );
		Ai1wm_Cron::clear( 'ai1wmde_dropbox_monthly_export' );
	}

	public function get_last_backup_date( $last_backup_timestamp ) {
		if ( $last_backup_timestamp ) {
			$last_backup_date = get_date_from_gmt( date( 'Y-m-d H:i:s', $last_backup_timestamp ), 'F j, Y g:i a' );
		} else {
			$last_backup_date = __( 'None', AI1WMDE_PLUGIN_NAME );
		}

		return $last_backup_date;
	}

	public function get_next_backup_date( $schedules ) {
		$future_backup_timestamps = array();

		foreach ( $schedules as $schedule ) {
			$future_backup_timestamps[] = wp_next_scheduled( "ai1wmde_dropbox_{$schedule}_export", array(
				array(
					'secret_key' => get_option( AI1WM_SECRET_KEY ),
					'dropbox'    => 1,
				),
			) );
		}

		sort( $future_backup_timestamps );

		if ( isset( $future_backup_timestamps[0] ) ) {
			$next_backup_date = get_date_from_gmt( date( 'Y-m-d H:i:s', $future_backup_timestamps[0] ), 'F j, Y g:i a' );
		} else {
			$next_backup_date = __( 'None', AI1WMDE_PLUGIN_NAME );
		}

		return $next_backup_date;
	}

	public function get_account( Ai1wmde_Dropbox_Client $dropbox = null ) {
		// Set Dropbox client
		if ( is_null( $dropbox ) ) {
			$dropbox = new Ai1wmde_Dropbox_Client(
				get_option( 'ai1wmde_dropbox_token', false ),
				get_option( 'ai1wmde_dropbox_ssl', true )
			);
		}

		// Get account info
		$account = $dropbox->get_account_info();

		// Get space usage info
		$usage = $dropbox->get_usage_info();

		// Set account name
		$name = null;
		if ( isset( $account['name']['display_name'] ) ) {
			$name = $account['name']['display_name'];
		}

		// Set used quota
		$used = 1;
		if ( isset( $usage['used'] ) ) {
			$used = $usage['used'];
		}

		// Set total quota
		$total = 1;
		if ( isset( $usage['allocation']['allocated'] ) ) {
			$total = $usage['allocation']['allocated'];
		}

		// Set email
		$email = null;
		if ( isset( $account['email'] ) ) {
			$email = $account['email'];
		}

		return array(
			'name'     => $name,
			'email'    => $email,
			'used'     => size_format( $used ),
			'total'    => size_format( $total ),
			'progress' => ceil( ( $used / $total ) * 100 ),
		);
	}

	/**
	 * Set cron schedules
	 *
	 * @param  array   $schedules List of schedules
	 * @return boolean
	 */
	public function set_cron( $schedules ) {
		// Reset cron schedules
		Ai1wm_Cron::clear( 'ai1wmde_dropbox_hourly_export' );
		Ai1wm_Cron::clear( 'ai1wmde_dropbox_daily_export' );
		Ai1wm_Cron::clear( 'ai1wmde_dropbox_weekly_export' );
		Ai1wm_Cron::clear( 'ai1wmde_dropbox_monthly_export' );

		// Update cron schedules
		foreach ( $schedules as $schedule ) {
			Ai1wm_Cron::add( "ai1wmde_dropbox_{$schedule}_export", $schedule, array(
				'secret_key' => get_option( AI1WM_SECRET_KEY ),
				'dropbox'    => 1,
			) );
		}

		return update_option( 'ai1wmde_dropbox_cron', $schedules );
	}

	public function get_cron() {
		return get_option( 'ai1wmde_dropbox_cron', array() );
	}

	public function set_token( $token ) {
		return update_option( 'ai1wmde_dropbox_token', $token );
	}

	public function get_token() {
		return get_option( 'ai1wmde_dropbox_token', false );
	}

	public function set_ssl( $mode ) {
		return update_option( 'ai1wmde_dropbox_ssl', $mode );
	}

	public function get_ssl() {
		return get_option( 'ai1wmde_dropbox_ssl', false );
	}

	public function set_backups( $number ) {
		return update_option( 'ai1wmde_dropbox_backups', $number );
	}

	public function get_backups() {
		return get_option( 'ai1wmde_dropbox_backups', false );
	}

	public function set_total( $size ) {
		return update_option( 'ai1wmde_dropbox_total', $size );
	}

	public function get_total() {
		return get_option( 'ai1wmde_dropbox_total', false );
	}

	public function set_folder_path( $folder_path ) {
		return update_option( 'ai1wmde_dropbox_folder_path', $folder_path );
	}

	public function get_folder_path() {
		return get_option( 'ai1wmde_dropbox_folder_path', false );
	}

	public function set_notify_ok_toggle( $toggle ) {
		return update_option( 'ai1wmde_dropbox_notify_toggle', $toggle );
	}

	public function get_notify_ok_toggle() {
		return get_option( 'ai1wmde_dropbox_notify_toggle', false );
	}

	public function set_notify_error_toggle( $toggle ) {
		return update_option( 'ai1wmde_dropbox_notify_error_toggle', $toggle );
	}

	public function get_notify_error_toggle() {
		return get_option( 'ai1wmde_dropbox_notify_error_toggle', false );
	}

	public function set_notify_error_subject( $subject ) {
		return update_option( 'ai1wmde_dropbox_notify_error_subject', $subject );
	}

	public function get_notify_error_subject() {
		return get_option( 'ai1wmde_dropbox_notify_error_subject', sprintf( __( '❌ Backup to Dropbox has failed (%s)', AI1WMDE_PLUGIN_NAME ), parse_url( site_url(), PHP_URL_HOST ) . parse_url( site_url(), PHP_URL_PATH ) ) );
	}

	public function set_notify_email( $email ) {
		return update_option( 'ai1wmde_dropbox_notify_email', $email );
	}

	public function get_notify_email() {
		return get_option( 'ai1wmde_dropbox_notify_email', false );
	}
}
