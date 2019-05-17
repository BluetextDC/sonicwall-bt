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

class Ai1wmde_Settings_Controller {

	public static function index() {
		$model = new Ai1wmde_Settings;

		$dropbox_backup_schedules = get_option( 'ai1wmde_dropbox_cron', array() );
		$last_backup_timestamp    = get_option( 'ai1wmde_dropbox_timestamp', false );

		$last_backup_date = $model->get_last_backup_date( $last_backup_timestamp );
		$next_backup_date = $model->get_next_backup_date( $dropbox_backup_schedules );

		Ai1wm_Template::render(
			'settings/index',
			array(
				'backups'                  => get_option( 'ai1wmde_dropbox_backups', false ),
				'dropbox_backup_schedules' => $dropbox_backup_schedules,
				'notify_ok_toggle'         => get_option( 'ai1wmde_dropbox_notify_toggle', false ),
				'notify_error_toggle'      => get_option( 'ai1wmde_dropbox_notify_error_toggle', false ),
				'notify_email'             => get_option( 'ai1wmde_dropbox_notify_email', get_option( 'admin_email', false ) ),
				'last_backup_date'         => $last_backup_date,
				'next_backup_date'         => $next_backup_date,
				'folder_path'              => get_option( 'ai1wmde_dropbox_folder_path', false ),
				'ssl'                      => get_option( 'ai1wmde_dropbox_ssl', true ),
				'timestamp'                => get_option( 'ai1wmde_dropbox_timestamp', false ),
				'token'                    => get_option( 'ai1wmde_dropbox_token', false ),
				'total'                    => get_option( 'ai1wmde_dropbox_total', false ),
			),
			AI1WMDE_TEMPLATES_PATH
		);
	}

	public static function picker() {
		Ai1wm_Template::render(
			'settings/picker',
			array(),
			AI1WMDE_TEMPLATES_PATH
		);
	}

	public static function settings( $params = array() ) {
		ai1wm_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = stripslashes_deep( $_POST );
		}

		// Dropbox update
		if ( isset( $params['ai1wmde_dropbox_update'] ) ) {
			$model = new Ai1wmde_Settings;

			// Cron update
			if ( ! empty( $params['ai1wmde_dropbox_cron'] ) ) {
				$model->set_cron( (array) $params['ai1wmde_dropbox_cron'] );
			} else {
				$model->set_cron( array() );
			}

			// Set SSL mode
			if ( ! empty( $params['ai1wmde_dropbox_ssl'] ) ) {
				$model->set_ssl( 0 );
			} else {
				$model->set_ssl( 1 );
			}

			// Set number of backups
			if ( ! empty( $params['ai1wmde_dropbox_backups'] ) ) {
				$model->set_backups( (int) $params['ai1wmde_dropbox_backups'] );
			} else {
				$model->set_backups( 0 );
			}

			// Set size of backups
			if ( ! empty( $params['ai1wmde_dropbox_total'] ) && ! empty( $params['ai1wmde_dropbox_total_unit'] ) ) {
				$model->set_total( (int) $params['ai1wmde_dropbox_total'] . trim( $params['ai1wmde_dropbox_total_unit'] ) );
			} else {
				$model->set_total( 0 );
			}

			// Set folder path
			$model->set_folder_path( trim( $params['ai1wmde_dropbox_folder_path'] ) );

			// Set notify ok toggle
			$model->set_notify_ok_toggle( isset( $params['ai1wmde_dropbox_notify_toggle'] ) );

			// Set notify error toggle
			$model->set_notify_error_toggle( isset( $params['ai1wmde_dropbox_notify_error_toggle'] ) );

			// Set notify email
			$model->set_notify_email( trim( $params['ai1wmde_dropbox_notify_email'] ) );

			// Set message
			Ai1wm_Message::flash( 'settings', __( 'Your changes have been saved.', AI1WMDE_PLUGIN_NAME ) );
		}

		// Redirect to settings page
		wp_redirect( network_admin_url( 'admin.php?page=ai1wmde_settings' ) );
		exit;
	}

	public static function revoke( $params = array() ) {
		ai1wm_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = stripslashes_deep( $_POST );
		}

		// Dropbox logout
		if ( isset( $params['ai1wmde_dropbox_logout'] ) ) {
			$model = new Ai1wmde_Settings;
			$model->revoke();
		}

		// Redirect to settings page
		wp_redirect( network_admin_url( 'admin.php?page=ai1wmde_settings' ) );
		exit;
	}

	public static function account() {
		ai1wm_setup_environment();

		try {
			$model = new Ai1wmde_Settings;
			if ( ( $account = $model->get_account() ) ) {
				echo json_encode( $account );
				exit;
			}
		} catch ( Ai1wmde_Error_Exception $e ) {
			status_header( 400 );
			echo json_encode( array( 'message' => $e->getMessage() ) );
			exit;
		}

	}

	public static function selector( $params = array() ) {
		ai1wm_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = stripslashes_deep( $_GET );
		}

		// Set folder path
		$folder_path = null;
		if ( isset( $params['folder_path'] ) ) {
			$folder_path = $params['folder_path'];
		}

		// Set Dropbox client
		$dropbox = new Ai1wmde_Dropbox_Client(
			get_option( 'ai1wmde_dropbox_token' ),
			get_option( 'ai1wmde_dropbox_ssl', true )
		);

		// List folder
		$items = $dropbox->list_folder( $folder_path );

		// Set folder structure
		$response = array( 'items' => array(), 'num_hidden_files' => 0 );

		// Set folder items
		foreach ( $items as $item ) {
			if ( $item['type'] === 'folder' ) {
				$response['items'][] = array(
					'name'  => isset( $item['name'] ) ? $item['name'] : null,
					'path'  => isset( $item['path'] ) ? $item['path'] : null,
					'date'  => isset( $item['date'] ) ? human_time_diff( $item['date'] ) : null,
					'size'  => isset( $item['bytes'] ) ? size_format( $item['bytes'] ) : null,
					'bytes' => isset( $item['bytes'] ) ? $item['bytes'] : null,
					'type'  => isset( $item['type'] ) ? $item['type'] : null,
				);
			} else {
				$response['num_hidden_files']++;
			}
		}

		// Sort items by type desc and name asc
		usort( $response['items'], 'Ai1wmde_Settings_Controller::sort_by_type_desc_name_asc' );

		echo json_encode( $response );
		exit;
	}

	public static function sort_by_type_desc_name_asc( $first_item, $second_item ) {
		$sorted_items = strcasecmp( $second_item['type'], $first_item['type'] );
		if ( $sorted_items !== 0 ) {
			return $sorted_items;
		}

		return strcasecmp( $first_item['name'], $second_item['name'] );
	}

	public static function folder() {
		ai1wm_setup_environment();

		// Set Dropbox client
		$dropbox = new Ai1wmde_Dropbox_Client(
			get_option( 'ai1wmde_dropbox_token' ),
			get_option( 'ai1wmde_dropbox_ssl', true )
		);

		// Get folder path
		$folder_path = get_option( 'ai1wmde_dropbox_folder_path', false );

		// Create folder
		if ( ! ( $folder_path = $dropbox->get_folder_path_by_path( $folder_path ) ) ) {
			if ( ! ( $folder_path = $dropbox->get_folder_path_by_path( sprintf( '/%s', ai1wm_archive_folder() ) ) ) ) {
				$folder_path = $dropbox->create_folder( sprintf( '/%s', ai1wm_archive_folder() ) );
			}
		}

		// Set folder path
		update_option( 'ai1wmde_dropbox_folder_path', $folder_path );

		// Get folder name
		if ( ! ( $folder_name = $dropbox->get_folder_name_by_path( $folder_path ) ) ) {
			status_header( 400 );
			echo json_encode(
				array(
					'message' => __(
						'We were unable to retrieve your backup folder details. ' .
						'Dropbox servers are overloaded at the moment. ' .
						'Please wait for a few minutes and try again by refreshing the page.',
						AI1WMDE_PLUGIN_NAME
					),
				)
			);
			exit;
		}

		echo json_encode( array( 'path' => $folder_path, 'name' => $folder_name ) );
		exit;
	}

	public static function notify_ok_toggle() {
		$model = new Ai1wmde_Settings;
		if ( ( $notify_ok_toggle = $model->get_notify_ok_toggle() ) ) {
			return $notify_ok_toggle;
		}
	}

	public static function notify_error_toggle() {
		$model = new Ai1wmde_Settings;
		if ( ( $notify_error_toggle = $model->get_notify_error_toggle() ) ) {
			return $notify_error_toggle;
		}
	}

	public static function notify_error_subject() {
		$model = new Ai1wmde_Settings;
		if ( ( $notify_error_subject = $model->get_notify_error_subject() ) ) {
			return $notify_error_subject;
		}
	}

	public static function notify_email() {
		$model = new Ai1wmde_Settings;
		if ( ( $notify_email = $model->get_notify_email() ) ) {
			return $notify_email;
		}
	}
}
