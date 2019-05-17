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

class Ai1wmde_Export_Dropbox {

	public static function execute( $params ) {
		// Set progress
		Ai1wm_Status::info( __( 'Connecting to Dropbox...', AI1WMDE_PLUGIN_NAME ) );

		// Open the archive file for writing
		$archive = new Ai1wm_Compressor( ai1wm_archive_path( $params ) );

		// Append EOF block
		$archive->close( true );

		$dropbox = new Ai1wmde_Dropbox_Client(
			get_option( 'ai1wmde_dropbox_token', false ),
			get_option( 'ai1wmde_dropbox_ssl', true )
		);

		// Get folder path
		$params['folder_path'] = get_option( 'ai1wmde_dropbox_folder_path', false );

		// Create folder
		if ( ! ( $params['folder_path'] = $dropbox->get_folder_path_by_path( $params['folder_path'] ) ) ) {
			if ( ! ( $params['folder_path'] = $dropbox->get_folder_path_by_path( sprintf( '/%s', ai1wm_archive_folder() ) ) ) ) {
				$params['folder_path'] = $dropbox->create_folder( sprintf( '/%s', ai1wm_archive_folder() ) );
			}
		}

		// Set progress
		Ai1wm_Status::info( __( 'Done connecting to Dropbox.', AI1WMDE_PLUGIN_NAME ) );

		return $params;
	}
}
