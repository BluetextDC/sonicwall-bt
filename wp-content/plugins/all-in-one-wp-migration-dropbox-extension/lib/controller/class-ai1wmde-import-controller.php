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

class Ai1wmde_Import_Controller {

	public static function button() {
		return Ai1wm_Template::get_content(
			'import/button',
			array( 'token' => get_option( 'ai1wmde_dropbox_token', false ) ),
			AI1WMDE_TEMPLATES_PATH
		);
	}

	public static function picker() {
		Ai1wm_Template::render(
			'import/picker',
			array(),
			AI1WMDE_TEMPLATES_PATH
		);
	}

	public static function browser( $params = array() ) {
		ai1wm_setup_environment();

		// Set params
		if ( empty( $params ) ) {
			$params = stripslashes_deep( $_GET );
		}

		// Set folder path
		$folder_path = null;
		if ( isset( $params['folder_path'] ) ) {
			$folder_path = trim( $params['folder_path'] );
		}

		// Set Dropbox client
		$dropbox = new Ai1wmde_Dropbox_Client(
			get_option( 'ai1wmde_dropbox_token', false ),
			get_option( 'ai1wmde_dropbox_ssl', true )
		);

		// List folder
		$items = $dropbox->list_folder( $folder_path );

		// Set folder structure
		$response = array( 'items' => array(), 'num_hidden_files' => 0 );

		// Set folder items
		foreach ( $items as $item ) {
			if ( $item['type'] === 'folder' || pathinfo( $item['name'], PATHINFO_EXTENSION ) === 'wpress' ) {
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
		usort( $response['items'], 'Ai1wmde_Import_Controller::sort_by_type_desc_name_asc' );

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

	public static function pro() {
		return Ai1wm_Template::get_content( 'import/pro', array(), AI1WMDE_TEMPLATES_PATH );
	}
}
