<?php

// Disable direct load
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

final class Wicked_Folders_Pro {

    public static $plugin_file;

	private static $instance;

	private function __construct() {

		spl_autoload_register( array( 'Wicked_Folders_Pro', 'autoload' ) );

		// Class may exist if core plugin is active
		if ( ! class_exists( 'Wicked_Folders' ) ) {
			require_once( dirname( dirname( __FILE__ ) ) . '/plugins/wicked-folders/wicked-folders.php' );
		}

		$core_admin = Wicked_Folders_Admin::get_instance();

		add_filter( 'wicked_folders_taxonomies', 			array( 'Wicked_Folders_Pro', 'wicked_folders_taxonomies' ) );
		add_filter( 'wicked_folders_get_dynamic_folders', 	array( $this, 'wicked_folders_get_dynamic_folders' ), 10, 2 );
        add_filter( 'plugin_action_links_wicked-folders-pro/wicked-folders-pro.php', array( 'Wicked_Folders_Pro_Admin', 'plugin_action_links' ) );
		add_filter( 'ajax_query_attachments_args', 			array( 'Wicked_Folders_Pro_Admin', 'ajax_query_attachments_args' ), 10, 1 );
		add_filter( 'wp_prepare_attachment_for_js', 		array( 'Wicked_Folders_Pro_Admin', 'wp_prepare_attachment_for_js' ), 10, 3 );
		add_filter( 'manage_admin_page_wf_attachment_folders_columns', 			array( 'Wicked_Folders_Pro_Admin', 'manage_media_columns' ) );
		add_filter( 'manage_admin_page_wf_attachment_folders_sortable_columns', array( 'Wicked_Folders_Pro_Admin', 'manage_upload_sortable_columns' ) );

		// WooCommerce filters
		add_filter( 'manage_product_posts_columns', 		array( $core_admin, 'manage_posts_columns' ), 20 );
		add_filter( 'manage_shop_coupon_posts_columns', 	array( $core_admin, 'manage_posts_columns' ), 20 );
		add_filter( 'manage_shop_order_posts_columns', 		array( $core_admin, 'manage_posts_columns' ), 20 );
		add_filter( 'manage_edit-wishlist_columns', 		array( $core_admin, 'manage_posts_columns' ), 20 );

		// Easy Digital Downloads
		add_filter( 'manage_edit-download_columns', 		array( $core_admin, 'manage_posts_columns' ), 20 );

		// ACF field groups
		add_filter( 'manage_edit-acf_columns', 				array( $core_admin, 'manage_posts_columns' ), 20 );

		add_filter( 'manage_media_columns', 				array( 'Wicked_Folders_Pro_Admin', 'manage_media_columns' ) );
        //add_filter( 'manage_media_page_wicked_attachment_folders_columns', array( 'Wicked_Folders_Pro_Admin', 'manage_media_columns' ) );

		add_action( 'admin_init',							array( 'Wicked_Folders_Pro_Admin', 'admin_init' ) );
        add_action( 'manage_media_custom_column', 			array( 'Wicked_Folders_Pro_Admin', 'manage_media_custom_column' ), 10, 2);
        add_action( 'admin_menu',							array( 'Wicked_Folders_Pro_Admin', 'admin_menu' ), 20000 );
		add_action( 'admin_enqueue_scripts',				array( 'Wicked_Folders_Pro_Admin', 'admin_enqueue_scripts' ) );
        add_action( 'wp_enqueue_media', 					array( 'Wicked_Folders_Pro_Admin', 'wp_enqueue_media' ) );
		add_action( 'restrict_manage_posts', 				array( 'Wicked_Folders_Pro_Admin', 'restrict_manage_posts' ), 10 );
		add_action( 'add_attachment',						array( 'Wicked_Folders_Pro_Admin', 'save_attachment' ) );
		add_action( 'edit_attachment',						array( 'Wicked_Folders_Pro_Admin', 'save_attachment' ) );
		add_action( 'post-plupload-upload-ui', 				array( 'Wicked_Folders_Pro_Admin', 'post_plupload_upload_ui' ) );
		add_action( 'network_admin_menu',					array( 'Wicked_Folders_Pro_Admin', 'network_admin_menu' ), 20000 );

        // Work-around to get folders page to work for attachments
        if ( Wicked_Folders_Admin::is_folders_page() && 'attachment' == Wicked_Folders_Admin::folder_page_post_type() ) {
            $_GET['post_type'] = 'attachment';
        }

	}

	/**
	 * Plugin activation hook.
	 */
	public static function activate() {

		// Check for multisite
		if ( is_multisite() && is_plugin_active_for_network( dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'wicked-folders-pro.php' ) ) {
			$sites = get_sites( array( 'fields' => 'ids' ) );
			foreach ( $sites as $id ) {
				switch_to_blog( $id );
				Wicked_Folders_Pro::activate_site();
				restore_current_blog();
			}
		} else {
			Wicked_Folders_Pro::activate_site();
		}

	}

	/**
	 * Activates/initalizes plugin for a single site.
	 */
	public static function activate_site() {

		$sync_upload_folder_dropdown = get_option( 'wicked_folders_sync_upload_folder_dropdown', null );

		// Wicked Folders is bundled with the pro version so deactivate
		// the core plugin
		deactivate_plugins( 'wicked-folders/wicked-folders.php' );

		// Activate the bundled core plugin
		Wicked_Folders::activate();

		$post_types 		= get_option( 'wicked_folders_post_types', array() );
        $dynamic_post_types = get_option( 'wicked_folders_dynamic_folder_post_types', array() );

        // Enable Media post type by default
        if ( ! in_array( 'attachment', $post_types ) ) {
            $post_types[] = 'attachment';
            update_option( 'wicked_folders_post_types', $post_types );
        }

		// Enable dynamic folders for media by default
		if ( ! in_array( 'attachment', $dynamic_post_types ) ) {
            $dynamic_post_types[] = 'attachment';
            update_option( 'wicked_folders_dynamic_folder_post_types', $dynamic_post_types );
        }

		if ( null === $sync_upload_folder_dropdown ) {
			update_option( 'wicked_folders_sync_upload_folder_dropdown', true );
		}

	}

	public static function activate_license() {

		$api_url 		= trailingslashit( Wicked_Common::wicked_plugins_url() ) . 'index.php';
		$plugin_data 	= get_plugin_data( Wicked_Folders_Pro::$plugin_file );
		$api_params 	= array(
			'edd_action' => 'activate_license',
			'license'    => get_site_option( 'wicked_folders_pro_license_key', false ),
			'item_name'  => urlencode( $plugin_data['Name'] ),
			'url'        => home_url()
		);

		$response = wp_remote_post( $api_url, array( 'body' => $api_params, 'timeout' => 10, 'sslverify' => false ) );

		// Make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( is_wp_error( $response ) ) {
				throw new Exception( $response->get_error_message() );
			} else {
				throw new Excpetion( __( 'An error occurred while attempting to activate your license. Please try again.', 'wicked-folders' ) );
			}
		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {

				switch( $license_data->error ) {
					case 'expired' :
						$message = sprintf(
							__( 'License key expired on %s.' , 'wicked-folders' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;
					case 'revoked' :
						$message = __( 'License key has been disabled.', 'wicked-folders' );
						break;
					case 'missing' :
						$message = __( 'The license key entered is invalid.', 'wicked-folders' );
						break;
					case 'invalid' :
					case 'site_inactive' :
						$message = __( 'License key is not active for this URL.', 'wicked-folders' );
						break;
					case 'item_name_mismatch' :
						$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'wicked-folders' ), $plugin_data['Name'] );
						break;
					case 'no_activations_left':
						$message = __( 'Your license key has reached its activation limit.', 'wicked-folders' );
						break;
					default :
						$message = __( 'An error occurred while attempting to activate your license. Please try again.', 'wicked-folders' );
						break;
				}

				throw new Exception( $message );

			}
		}

		return true;

	}

	public static function fetch_license_data() {

		$api_url 		= trailingslashit( Wicked_Common::wicked_plugins_url() ) . 'index.php';
		$plugin_data 	= get_plugin_data( Wicked_Folders_Pro::$plugin_file );
		$api_params 	= array(
			'edd_action' 	=> 'check_license',
			'license' 		=> get_site_option( 'wicked_folders_pro_license_key', false ),
			'item_name' 	=> $plugin_data['Name'],
			'url' 			=> home_url()
		);

		$response = wp_remote_post( $api_url, array( 'body' => $api_params, 'timeout' => 10, 'sslverify' => false ) );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		return json_decode( wp_remote_retrieve_body( $response ) );

	}

	/**
	 * Returns an HTML string with a friendly description of the plugin's
	 * license status. Used by the plugin's site and network settings pages.
	 */
	public static function get_license_status_text() {

		$license_data 		= get_site_option( 'wicked_folders_pro_license_data' );
		$license_status 	= '';

		if ( $license_data ) {
			if ( 'valid' == $license_data->license ) {
				$expiration = strtotime( $license_data->expires );
				if ( 'lifetime' == $license_data->expires ) {
					$license_status = '<em style="color: green;">' . __( 'Valid', 'wicked-folders' ) . '</em>';
				} else if ( time() > $expiration ) {
					$license_status = '<em style="color: red;">' . __( 'Expired', 'wicked-folders' ) . '</em>';
				} else {
					$license_status = '<em style="color: green;">' . sprintf( __( 'Valid. Expires %1$s.', 'wicked-folders' ), date( 'F j, Y', $expiration ) ). '</em>';
				}
			} else if ( 'expired' == $license_data->license ) {
				$license_status = '<em style="color: red;">' . __( 'Expired', 'wicked-folders' ) . '</em>';
			} else {
				$license_status = '<em style="color: red;">' . __( 'Invalid', 'wicked-folders' ) . '</em>';
			}
		}

		return $license_status;

	}

	public static function is_license_expired() {
		$expired 		= false;
		$license_data 	= get_site_option( 'wicked_folders_pro_license_data' );

		if ( $license_data ) {
			// We need to check even when the license is valid because it may
			// have expired since the license data was last updated
			if ( 'valid' == $license_data->license ) {
				$expiration = strtotime( $license_data->expires );

				if ( 'lifetime' != $license_data->expires && time() > $expiration ) {
					$expired = true;
				}
			} else if ( 'expired' == $license_data->license ) {
				$expired = true;
			}
		}

		return $expired;
	}

    public static function autoload( $class ) {

        $file 	= false;
        $files  = array(
            'Wicked_Folders_Pro_Admin'       				=> 'lib/class-wicked-folders-pro-admin.php',
            'Wicked_Folders_Pro_WP_Media_List_Table'   		=> 'lib/class-wicked-folders-pro-wp-media-list-table.php',
			'Wicked_Folders_Pro_Media_List_Table'   		=> 'lib/class-wicked-folders-pro-media-list-table.php',
			'Wicked_Folders_Media_Extension_Dynamic_Folder' => 'lib/class-wicked-folders-media-extension-dynamic-folder.php',
			'EDD_SL_Plugin_Updater' 						=> 'vendor/edd/EDD_SL_Plugin_Updater.php',
        );

        if ( array_key_exists( $class, $files ) ) {
            $file = dirname( dirname( __FILE__ ) ) . '/' . $files[ $class ];
        }

        if ( $file ) {
            $file = str_replace( '/', DIRECTORY_SEPARATOR, $file );
            include_once( $file );
        }

	}

    public static function get_instance( $plugin_file = false ) {

		if ( empty( self::$instance ) ) {
			self::$instance = new Wicked_Folders_Pro();
		}

		if ( $plugin_file ) {
			self::$plugin_file = $plugin_file;
		}

		return self::$instance;

	}

	/**
	 * Hooks into core plugin's taxonomy filter to include attachments.
	 */
	public static function wicked_folders_taxonomies( $taxonomies ) {
		$post_types = Wicked_Folders::post_types();
		if ( in_array( 'attachment', $post_types ) ) {
			$taxonomies[] = 'wf_attachment_folders';
		}
		return $taxonomies;
	}

	/**
	 * wicked_folders_get_dynamic_folders filter.
	 */
	public function wicked_folders_get_dynamic_folders( $dynamic_folders, $args ) {

		$post_type 	= $args['post_type'];
		$taxonomy 	= $args['taxonomy'];

		if ( 'attachment' == $post_type ) {

			$extension_folders = $this->get_media_extension_dynamic_folders( $post_type, $taxonomy );

			$dynamic_folders = array_merge( $dynamic_folders, $extension_folders );

		}

		return $dynamic_folders;

	}

	/**
	 * Returns a dynamically generated collection of folders for all media file
	 * extensions (e.g. .jpg, .pdf, etc.).
	 *
	 * @param string $post_type
	 *  The post type to generate folders for.
	 *
	 * @return array
	 *  Array of Wicked_Folders_Media_Extension_Dynamic_Folder objects.
	 */
	public function get_media_extension_dynamic_folders( $post_type, $taxonomy ) {

		global $wpdb;

		$extensions = array();
		$folders 	= array();

		// Fetch post dates
		$results = $wpdb->get_results( "SELECT pm.meta_value FROM {$wpdb->prefix}posts p INNER JOIN {$wpdb->prefix}postmeta pm ON p.ID = pm.post_id WHERE p.post_type = 'attachment' AND pm.meta_key = '_wp_attached_file' ORDER BY pm.meta_value ASC" );

		foreach ( $results as $row ) {
			$a = explode( '.', $row->meta_value );
			$extension = end( $a );
			$extensions[] = strtolower( $extension );
		}

		$extensions = array_unique( $extensions );

		asort( $extensions );

		if ( ! empty( $extensions ) ) {

			$folders[] = new Wicked_Folders_Media_Extension_Dynamic_Folder( array(
					'id' 		=> 'dynamic_media_extension',
					'name' 		=> __( 'All Extensions', 'wicked-folders' ),
					'parent' 	=> 'dynamic_root',
					'post_type' => $post_type,
					'taxonomy' 	=> $taxonomy,
				)
			);

			foreach ( $extensions as $extension ) {

				$folders[] = new Wicked_Folders_Media_Extension_Dynamic_Folder( array(
						'id' 		=> 'dynamic_media_extension_' . $extension,
						'name' 		=> '.' . $extension,
						'parent' 	=> 'dynamic_media_extension',
						'post_type' => $post_type,
						'taxonomy' 	=> $taxonomy,
					)
				);

			}

		}

		return $folders;

	}

	/**
	 * Returns the plugin's version.
	 */
	public static function plugin_version() {

		static $version = false;

		if ( ! $version ) {
			$plugin_data 	= get_plugin_data( dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'wicked-folders-pro.php' );
			$version 		= $plugin_data['Version'];
		}

		return $version;

	}

	/**
	 * Prints Javascript templates for media.
	 */
	public static function print_media_templates() {
		?>
			<script type="text/html" id="tmpl-wicked-attachment-browser-drag-details">
				<div class="title">
					<?php _e( 'Move', 'wicked-folders' ); ?> <%= count %>
					<% if ( 1 == count ) { %>
						<?php _e( 'File', 'wicked-folders' ); ?>
					<% } else { %>
						<?php _e( 'Files', 'wicked-folders' ); ?>
					<% } %>
				</div>
				<?php _e( 'Hold SHIFT key to copy file(s)', 'wicked-folders' ); ?>
			</script>
			<script type="text/html" id="tmpl-wicked-attachment-browser-folder-details">
				<header>
					<h2><%= title %></h2>
					<span class="wicked-spinner"></span>
					<a class="wicked-close" href="#" title="<?php _e( 'Close', 'wicked-folders' ); ?>"><span class="screen-reader-text"><?php _e( 'Close', 'wicked-folders' ); ?></span></a>
				</header>
				<div>
	                <div class="wicked-messages wicked-errors"></div>
	                <% if ( 'delete' == mode ) { %>
	                    <p><%= deleteFolderConfirmation %></p>
	                <% } else { %>
	                    <div class="wicked-folder-name"><input type="text" name="wicked_folder_name" placeholder="<?php _e( 'Folder name', 'wicked-folders' ); ?>" value="<%= folderName %>" /></div>
	                    <div class="wicked-folder-parent"></div>
	                <% } %>
	            </div>
	            <footer>
	                <a class="button wicked-cancel" href="#"><?php _e( 'Cancel', 'wicked-folders' ); ?></a>
	                <button class="button-primary wicked-save" type="submit"><%= saveButtonLabel %></button>
	            </footer>
		    </script>
		<?php
	}

}
