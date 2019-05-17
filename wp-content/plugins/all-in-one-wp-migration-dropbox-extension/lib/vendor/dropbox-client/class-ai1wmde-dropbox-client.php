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

class Ai1wmde_Dropbox_Client {

	const API_URL         = 'https://api.dropboxapi.com/2';
	const API_CONTENT_URL = 'https://content.dropboxapi.com/2';

	/**
	 * OAuth access token
	 *
	 * @var string
	 */
	protected $access_token = null;

	/**
	 * SSL mode
	 *
	 * @var boolean
	 */
	protected $ssl = null;

	public function __construct( $access_token, $ssl = true ) {
		$this->access_token = $access_token;
		$this->ssl          = $ssl;
	}

	/**
	 * Upload file
	 *
	 * @param  string $file_data File data
	 * @param  string $file_path File path
	 * @return array
	 */
	public function upload_file( $file_data, $file_path ) {
		$api = new Ai1wmde_Dropbox_Curl;
		$api->set_access_token( $this->access_token );
		$api->set_ssl( $this->ssl );
		$api->set_base_url( self::API_CONTENT_URL );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option( CURLOPT_POSTFIELDS, $file_data );
		$api->set_path( '/files/upload' );
		$api->set_header( 'Content-Type', 'application/octet-stream' );
		$api->set_header( 'Dropbox-API-Arg', json_encode( array(
			'path'       => $file_path,
			'mode'       => 'add',
			'autorename' => false,
			'mute'       => false,
		) ) );

		try {
			$response = $api->make_request( true );
		} catch ( Ai1wmde_Error_Exception $e ) {
			throw $e;
		}

		return $response;
	}

	/**
	 * Upload first file chunk
	 *
	 * @param  string $file_chunk_data File chunk data
	 * @return string
	 */
	public function upload_first_file_chunk( $file_chunk_data ) {
		$api = new Ai1wmde_Dropbox_Curl;
		$api->set_access_token( $this->access_token );
		$api->set_ssl( $this->ssl );
		$api->set_base_url( self::API_CONTENT_URL );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option( CURLOPT_POSTFIELDS, $file_chunk_data );
		$api->set_path( '/files/upload_session/start' );
		$api->set_header( 'Content-Type', 'application/octet-stream' );
		$api->set_header( 'Dropbox-API-Arg', json_encode( array(
			'close' => false,
		) ) );

		try {
			$response = $api->make_request( true );
		} catch ( Ai1wmde_Error_Exception $e ) {
			throw $e;
		}

		if ( isset( $response['session_id'] ) ) {
			return $response['session_id'];
		}
	}

	/**
	 * Upload next file chunk
	 *
	 * @param  string  $file_chunk_data  File chunk data
	 * @param  string  $session_id       Session ID
	 * @param  integer $file_range_start File range start
	 * @return boolean
	 */
	public function upload_next_file_chunk( $file_chunk_data, $session_id, $file_range_start = 0 ) {
		$api = new Ai1wmde_Dropbox_Curl;
		$api->set_access_token( $this->access_token );
		$api->set_ssl( $this->ssl );
		$api->set_base_url( self::API_CONTENT_URL );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option( CURLOPT_POSTFIELDS, $file_chunk_data );
		$api->set_path( '/files/upload_session/append_v2' );
		$api->set_header( 'Content-Type', 'application/octet-stream' );
		$api->set_header( 'Dropbox-API-Arg', json_encode( array(
			'cursor' => array(
				'session_id' => $session_id,
				'offset'     => (int) $file_range_start,
			),
			'close'  => false,
		) ) );

		try {
			$api->make_request();
		} catch ( Ai1wmde_Error_Exception $e ) {
			throw $e;
		}

		return true;
	}

	/**
	 * Commit upload file chunk
	 *
	 * @param  string  $file_chunk_data  File chunk data
	 * @param  string  $file_path        File path
	 * @param  string  $session_id       Session ID
	 * @param  integer $file_range_start File range start
	 * @return array
	 */
	public function upload_file_chunk_commit( $file_chunk_data, $file_path, $session_id, $file_range_start = 0 ) {
		$api = new Ai1wmde_Dropbox_Curl;
		$api->set_access_token( $this->access_token );
		$api->set_ssl( $this->ssl );
		$api->set_base_url( self::API_CONTENT_URL );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option( CURLOPT_POSTFIELDS, $file_chunk_data );
		$api->set_path( '/files/upload_session/finish' );
		$api->set_header( 'Content-Type', 'application/octet-stream' );
		$api->set_header( 'Dropbox-API-Arg', json_encode( array(
			'cursor' => array(
				'session_id' => $session_id,
				'offset'     => (int) $file_range_start,
			),
			'commit' => array(
				'path'       => $file_path,
				'mode'       => 'add',
				'autorename' => false,
				'mute'       => false,
			),
		) ) );

		try {
			$response = $api->make_request( true );
		} catch ( Ai1wmde_Error_Exception $e ) {
			throw $e;
		}

		return $response;
	}

	/**
	 * Download file from Dropbox
	 *
	 * @param  resource $file_stream      File stream
	 * @param  string   $file_path        File path
	 * @param  integer  $file_range_start File range start
	 * @param  integer  $file_range_end   File range end
	 * @return boolean
	 */
	public function get_file( $file_stream, $file_path, $file_range_start = 0, $file_range_end = 0 ) {
		$api = new Ai1wmde_Dropbox_Curl;
		$api->set_access_token( $this->access_token );
		$api->set_ssl( $this->ssl );
		$api->set_base_url( self::API_CONTENT_URL );
		$api->set_path( '/files/download' );
		$api->set_header( 'Dropbox-API-Arg', json_encode( array( 'path' => $file_path ) ) );
		$api->set_header( 'Range', sprintf( 'bytes=%d-%d', $file_range_start, $file_range_end ) );

		try {
			$file_chunk_data = $api->make_request();
		} catch ( Ai1wmde_Error_Exception $e ) {
			throw $e;
		}

		// Copy file chunk data into file stream
		if ( fwrite( $file_stream, $file_chunk_data ) === false ) {
			throw new Ai1wmde_Error_Exception( __( 'Unable to save the file from Dropbox', AI1WMDE_PLUGIN_NAME ) );
		}

		return true;
	}

	/**
	 * Creates a folder
	 *
	 * @param  string $folder_path Folder path
	 * @return array
	 */
	public function create_folder( $folder_path ) {
		$api = new Ai1wmde_Dropbox_Curl;
		$api->set_access_token( $this->access_token );
		$api->set_ssl( $this->ssl );
		$api->set_base_url( self::API_URL );
		$api->set_path( '/files/create_folder_v2' );
		$api->set_header( 'Content-Type', 'application/json; charset=utf-8' );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option( CURLOPT_POSTFIELDS, json_encode( array(
			'path'       => $folder_path,
			'autorename' => false,
		) ) );

		try {
			$response = $api->make_request( true );
		} catch ( Ai1wmde_Error_Exception $e ) {
			throw $e;
		}

		if ( ! empty( $response['metadata']['path_lower'] ) ) {
			return $response['metadata']['path_lower'];
		}
	}

	/**
	 * Retrive the content of a folder
	 *
	 * @param  string $folder_path Folder path
	 * @return array
	 */
	public function list_folder( $folder_path ) {
		$api = new Ai1wmde_Dropbox_Curl;
		$api->set_access_token( $this->access_token );
		$api->set_ssl( $this->ssl );
		$api->set_base_url( self::API_URL );
		$api->set_path( '/files/list_folder' );
		$api->set_header( 'Content-Type', 'application/json; charset=utf-8' );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option( CURLOPT_POSTFIELDS, json_encode( array(
			'path'                                => $folder_path,
			'include_media_info'                  => false,
			'include_deleted'                     => false,
			'include_has_explicit_shared_members' => false,
			'include_mounted_folders'             => false,
		) ) );

		try {
			$response = $api->make_request( true );
		} catch ( Ai1wmde_Error_Exception $e ) {
			throw $e;
		}

		$items = array();
		if ( isset( $response['entries'] ) ) {
			foreach ( $response['entries'] as $entry ) {
				$items[] = array(
					'name'  => isset( $entry['name'] ) ? $entry['name'] : null,
					'path'  => isset( $entry['path_lower'] ) ? $entry['path_lower'] : null,
					'date'  => isset( $entry['server_modified'] ) ? strtotime( $entry['server_modified'] ) : null,
					'bytes' => isset( $entry['size'] ) ? $entry['size'] : null,
					'type'  => isset( $entry['.tag'] ) ? $entry['.tag'] : null,
				);
			}
		}

		return $items;
	}

	/**
	 * Get folder path by path
	 *
	 * @param  string $folder_path Folder path
	 * @return string
	 */
	public function get_folder_path_by_path( $folder_path ) {
		try {
			$response = $this->get_metadata( $folder_path );
		} catch ( Ai1wmde_Error_Exception $e ) {
			$response = array();
		}

		if ( isset( $response['path_lower'] ) ) {
			return $response['path_lower'];
		}
	}

	/**
	 * Get folder name by path
	 *
	 * @param  string $folder_path Folder path
	 * @return string
	 */
	public function get_folder_name_by_path( $folder_path ) {
		try {
			$response = $this->get_metadata( $folder_path );
		} catch ( Ai1wmde_Error_Exception $e ) {
			$response = array();
		}

		if ( isset( $response['name'] ) ) {
			return $response['name'];
		}
	}

	/**
	 * Get file/folder metadata
	 *
	 * @param  string $path File/folder path
	 * @return string
	 */
	private function get_metadata( $path ) {
		$api = new Ai1wmde_Dropbox_Curl;
		$api->set_access_token( $this->access_token );
		$api->set_ssl( $this->ssl );
		$api->set_base_url( self::API_URL );
		$api->set_path( '/files/get_metadata' );
		$api->set_header( 'Content-Type', 'application/json; charset=utf-8' );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option( CURLOPT_POSTFIELDS, json_encode( array(
			'path'                                => $path,
			'include_media_info'                  => false,
			'include_deleted'                     => false,
			'include_has_explicit_shared_members' => false,
		) ) );

		$response = $api->make_request( true );

		return $response;
	}


	/**
	 * Delete a file or folder
	 *
	 * @param  string  $file_path File path
	 * @return boolean
	 */
	public function delete( $file_path ) {
		$api = new Ai1wmde_Dropbox_Curl;
		$api->set_access_token( $this->access_token );
		$api->set_ssl( $this->ssl );
		$api->set_base_url( self::API_URL );
		$api->set_path( '/files/delete_v2' );
		$api->set_header( 'Content-Type', 'application/json; charset=utf-8' );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option( CURLOPT_POSTFIELDS, json_encode( array(
			'path' => $file_path,
		) ) );

		try {
			$response = $api->make_request( true );
		} catch ( Ai1wmde_Error_Exception $e ) {
			return false;
		}

		return true;
	}

	/**
	 * Get account info
	 *
	 * @return array
	 */
	public function get_account_info() {
		$api = new Ai1wmde_Dropbox_Curl;
		$api->set_access_token( $this->access_token );
		$api->set_ssl( $this->ssl );
		$api->set_base_url( self::API_URL );
		$api->set_path( '/users/get_current_account' );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option( CURLOPT_POSTFIELDS, json_encode( null ) );
		$api->set_header( 'Content-Type', 'application/json; charset=utf-8' );

		try {
			$response = $api->make_request( true );
		} catch ( Ai1wmde_Error_Exception $e ) {
			throw $e;
		}

		return $response;
	}

	/**
	 * Get space usage info
	 *
	 * @return array
	 */
	public function get_usage_info() {
		$api = new Ai1wmde_Dropbox_Curl;
		$api->set_access_token( $this->access_token );
		$api->set_ssl( $this->ssl );
		$api->set_base_url( self::API_URL );
		$api->set_path( '/users/get_space_usage' );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option( CURLOPT_POSTFIELDS, json_encode( null ) );
		$api->set_header( 'Content-Type', 'application/json; charset=utf-8' );

		try {
			$response = $api->make_request( true );
		} catch ( Ai1wmde_Error_Exception $e ) {
			throw $e;
		}

		return $response;
	}

	/**
	 * Revoke token
	 *
	 * @return boolean
	 */
	public function revoke() {
		$api = new Ai1wmde_Dropbox_Curl;
		$api->set_access_token( $this->access_token );
		$api->set_ssl( $this->ssl );
		$api->set_base_url( self::API_URL );
		$api->set_path( '/auth/token/revoke' );
		$api->set_option( CURLOPT_POST, true );
		$api->set_option( CURLOPT_POSTFIELDS, json_encode( null ) );
		$api->set_header( 'Content-Type', 'application/json; charset=utf-8' );

		try {
			$api->make_request();
		} catch ( Ai1wmde_Error_Exception $e ) {
			return false;
		}

		return true;
	}

	/**
	 * Encode URL query
	 *
	 * @param  array  $query Base query
	 * @return string
	 */
	public function rawurlencode_query( $query ) {
		return str_replace( '%7E', '~', array_map( 'rawurlencode', array_filter( $query, 'is_scalar' ) ) );
	}
}
