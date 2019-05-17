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

class Ai1wmde_Export_Upload {

	public static function execute( $params, Ai1wmde_Dropbox_Client $dropbox = null ) {

		$params['completed'] = false;

		// Set archive offset
		if ( ! isset( $params['archive_offset'] ) ) {
			$params['archive_offset'] = 0;
		}

		// Set archive size
		if ( ! isset( $params['archive_size'] ) ) {
			$params['archive_size'] = ai1wm_archive_bytes( $params );
		}

		// Set upload retries
		if ( ! isset( $params['upload_retries'] ) ) {
			$params['upload_retries'] = 0;
		}

		// Set Dropbox client
		if ( is_null( $dropbox ) ) {
			$dropbox = new Ai1wmde_Dropbox_Client(
				get_option( 'ai1wmde_dropbox_token', false ),
				get_option( 'ai1wmde_dropbox_ssl', true )
			);
		}

		// Open the archive file for reading
		$archive = fopen( ai1wm_archive_path( $params ), 'rb' );

		// Read file chunk data
		if ( ( fseek( $archive, $params['archive_offset'] ) !== -1 )
				&& ( $file_chunk_data = fread( $archive, AI1WMDE_FILE_CHUNK_SIZE ) ) ) {

			// Upload file in one chunk if <= 5MB
			if ( $params['archive_size'] <= AI1WMDE_FILE_CHUNK_SIZE ) {

				try {

					$params['upload_retries'] += 1;

					// Upload file chunk data
					$dropbox->upload_file( $file_chunk_data, sprintf( '%s/%s', $params['folder_path'], ai1wm_archive_name( $params ) ) );

					// Unset upload retries
					unset( $params['upload_retries'] );

				} catch ( Ai1wmde_Connect_Exception $e ) {
					if ( $params['upload_retries'] <= 3 ) {
						return $params;
					}

					throw $e;
				}
			} elseif ( $params['archive_offset'] === 0 ) {

				try {

					$params['upload_retries'] += 1;

					// Upload first file chunk data
					$params['session_id'] = $dropbox->upload_first_file_chunk( $file_chunk_data );

					// Unset upload retries
					unset( $params['upload_retries'] );

				} catch ( Ai1wmde_Connect_Exception $e ) {
					if ( $params['upload_retries'] <= 3 ) {
						return $params;
					}

					throw $e;
				}
			} elseif ( $params['archive_size'] > ( $params['archive_offset'] + AI1WMDE_FILE_CHUNK_SIZE ) ) {

				try {

					$params['upload_retries'] += 1;

					// Upload next file chunk data
					$dropbox->upload_next_file_chunk( $file_chunk_data, $params['session_id'], $params['archive_offset'] );

					// Unset upload retries
					unset( $params['upload_retries'] );

				} catch ( Ai1wmde_Connect_Exception $e ) {
					if ( $params['upload_retries'] <= 3 ) {
						return $params;
					}

					throw $e;
				}
			} else {

				try {

					$params['upload_retries'] += 1;

					// Upload file chunk data commit
					$dropbox->upload_file_chunk_commit( $file_chunk_data, sprintf( '%s/%s', $params['folder_path'], ai1wm_archive_name( $params ) ), $params['session_id'], $params['archive_offset'] );

					// Unset upload retries
					unset( $params['upload_retries'] );

				} catch ( Ai1wmde_Connect_Exception $e ) {
					if ( $params['upload_retries'] <= 3 ) {
						return $params;
					}

					throw $e;
				}
			}

			// Set archive offset
			$params['archive_offset'] = ftell( $archive );

			// Set archive details
			$name = ai1wm_archive_name( $params );
			$size = ai1wm_archive_size( $params );

			// Get progress
			$progress = (int) ( ( $params['archive_offset'] / $params['archive_size'] ) * 100 );

			// Set progress
			if ( defined( 'WP_CLI' ) ) {
				WP_CLI::log(
					sprintf(
						__( 'Uploading %s (%s) [%d%% complete]', AI1WMDE_PLUGIN_NAME ),
						$name,
						$size,
						$progress
					)
				);
			} else {
				Ai1wm_Status::info(
					sprintf(
						__(
							'<i class="ai1wmde-icon-dropbox"></i> ' .
							'Uploading <strong>%s</strong> (%s)<br />%d%% complete',
							AI1WMDE_PLUGIN_NAME
						),
						$name,
						$size,
						$progress
					)
				);
			}
		} else {

			// Set last backup date
			update_option( 'ai1wmde_dropbox_timestamp', time() );

			// Unset session ID
			unset( $params['session_id'] );

			// Unset archive offset
			unset( $params['archive_offset'] );

			// Unset archive size
			unset( $params['archive_size'] );

			// Unset completed
			unset( $params['completed'] );
		}

		// Close the archive file
		fclose( $archive );

		return $params;
	}
}
