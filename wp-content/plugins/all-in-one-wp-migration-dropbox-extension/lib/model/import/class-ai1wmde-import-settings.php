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

class Ai1wmde_Import_Settings {

	public static function execute( $params ) {

		// Set progress
		Ai1wm_Status::info( __( 'Getting Dropbox settings...', AI1WMDE_PLUGIN_NAME ) );

		$settings = array(
			'ai1wmde_dropbox_cron'                 => get_option( 'ai1wmde_dropbox_cron', array() ),
			'ai1wmde_dropbox_token'                => get_option( 'ai1wmde_dropbox_token', false ),
			'ai1wmde_dropbox_ssl'                  => get_option( 'ai1wmde_dropbox_ssl', false ),
			'ai1wmde_dropbox_backups'              => get_option( 'ai1wmde_dropbox_backups', false ),
			'ai1wmde_dropbox_folder_path'          => get_option( 'ai1wmde_dropbox_folder_path', false ),
			'ai1wmde_dropbox_total'                => get_option( 'ai1wmde_dropbox_total', false ),
			'ai1wmde_dropbox_notify_toggle'        => get_option( 'ai1wmde_dropbox_notify_toggle', false ),
			'ai1wmde_dropbox_notify_error_toggle'  => get_option( 'ai1wmde_dropbox_notify_error_toggle', false ),
			'ai1wmde_dropbox_notify_error_subject' => get_option( 'ai1wmde_dropbox_notify_error_subject', false ),
			'ai1wmde_dropbox_notify_email'         => get_option( 'ai1wmde_dropbox_notify_email', false ),
		);

		// Save settings.json file
		$handle = ai1wm_open( ai1wm_settings_path( $params ), 'w' );
		ai1wm_write( $handle, json_encode( $settings ) );
		ai1wm_close( $handle );

		// Set progress
		Ai1wm_Status::info( __( 'Done getting Dropbox settings.', AI1WMDE_PLUGIN_NAME ) );

		return $params;
	}
}
