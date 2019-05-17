<?php

// Disable direct load
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

final class Wicked_Folders_Pro_Admin {

	private function __construct() {
	}

	public static function admin_init() {

		if ( is_plugin_active( 'wicked-folders/wicked-folders.php' ) ) {
			deactivate_plugins( 'wicked-folders/wicked-folders.php' );
		}

		Wicked_Folders_Pro_Admin::save_settings();

		$plugin_data = get_plugin_data( Wicked_Folders_Pro::$plugin_file );

		$edd_updater = new EDD_SL_Plugin_Updater( Wicked_Common::wicked_plugins_url(), Wicked_Folders_Pro::$plugin_file, array(
				'version'   => $plugin_data['Version'],
				'license'   => get_site_option( 'wicked_folders_pro_license_key', false ),
				'item_name' => $plugin_data['Name'],
				'author'    => $plugin_data['Author'],
			)
		);

		// Set option if it hasn't been set so that it can be filtered
		if ( null === get_option( 'wicked_folders_persist_media_modal_folder_state', null ) ) {
			add_option( 'wicked_folders_persist_media_modal_folder_state', true );
		}

	}

	public static function admin_enqueue_scripts() {

		$screen = get_current_screen();

		if ( 'media' == $screen->base && 'add' == $screen->action ) {
			wp_enqueue_script( 'wicked-folders-media-new', plugin_dir_url( dirname( __FILE__ ) ) . 'js/media-new.js', array( 'jquery' ), Wicked_Folders::plugin_version()  );
		}

	}

    public static function plugin_action_links( $links ) {

        $settings_link = '<a href="' . esc_url( menu_page_url( 'wicked_folders_settings', 0 ) ) . '">' . __( 'Settings', 'wicked-folders' ) . '</a>';

        array_unshift( $links, $settings_link );

        return $links;

    }

    public static function wp_enqueue_media() {
		// No need to do anything if folders aren't enabled for media
		if ( ! Wicked_Folders::enabled_for( 'attachment' ) ) return false;

		$folders_array = array();
		$folders = Wicked_Folders::get_folders( 'attachment' );

		$tree_view = new Wicked_Folders_Tree_View( 'attachment' );
		$tree_view->add_folders( $folders );

		$folders = $tree_view->build_flat_tree_array( 'root' );

		foreach ( $folders as $key => $folder ) {
			$depth = $tree_view->get_ancestor_count( $folder->id );// - 1;
			$folders_array[] = array(
				'id' 		=> $folder->id,
				'name' 		=> $folder->name,
				'depth' 	=> $depth,
				'parent' 	=> $folder->parent,
				'type' 		=> get_class( $folder ),
			);
		}

		wp_enqueue_script( 'wicked-folders-media', plugin_dir_url( dirname( __FILE__ ) ) . 'js/media.js', array( 'media-editor', 'media-views' ), Wicked_Folders_Pro::plugin_version() );
		wp_localize_script( 'wicked-folders-media', 'WickedFoldersProData', array(
			'folders' 					=> $folders_array,
			'allFoldersText' 			=> __( 'All folders', 'wicked-folders' ),
			'syncUploadFolderDropdown' 	=> get_option( 'wicked_folders_sync_upload_folder_dropdown', false ),
			'persistFolderState' 		=> get_option( 'wicked_folders_persist_media_modal_folder_state', true ),
			'includeChildren' 			=> Wicked_Folders::include_children( 'attachment' ),
		) );

		// Add these actions here so that they're only triggered when
		// wp_enqueue_media is called
		add_action( 'admin_footer', array( 'Wicked_Folders_Pro', 'print_media_templates' ) );
		add_action( 'wp_footer', 	array( 'Wicked_Folders_Pro', 'print_media_templates' ) );
		add_action( 'customize_controls_print_footer_scripts', array( 'Wicked_Folders_Pro', 'print_media_templates' ) );

		// Overrides code styling to accommodate for a third dropdown filter
		/*
		add_action( 'admin_footer', function(){
			?>
				<style>
					.media-modal-content .media-frame select.attachment-filters {
						max-width: -webkit-calc(33% - 12px);
						max-width: calc(33% - 12px);
					}
				</style>
			<?php
		});
		*/

	}

    public static function admin_menu() {

		$enable_folder_pages = get_option( 'wicked_folders_enable_folder_pages', false );

		if ( $enable_folder_pages && Wicked_Folders::enabled_for( 'attachment' ) ) {

			$page_title 	= __( 'Folders', 'wicked-folders' );
			$menu_title 	= __( 'Folders', 'wicked-folders' );
			$capability 	= 'edit_posts';
			$menu_slug 		= 'wf_attachment_folders';
			$callback 		= array( 'Wicked_Folders_Admin', 'folders_page' );

			add_media_page( $page_title, $menu_title, $capability, $menu_slug, $callback );

		}

    }

	/**
	 * network_admin_menu action. Adds a network settings page for the plugin.
	 */
	public static function network_admin_menu() {

		// Add menu item for plugin network settings page
		$parent_slug 	= 'settings.php';
		$page_title 	= __( 'Wicked Folders Settings', 'wicked-folders' );
		$menu_title 	= __( 'Wicked Folders', 'wicked-folders' );
		$capability 	= 'manage_options';
		$menu_slug 		= 'wicked_folders_settings';
		$callback 		= array( 'Wicked_Folders_Pro_Admin', 'network_settings_page' );

		add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback );

	}

    public static function manage_media_columns( $columns ) {

		if ( Wicked_Folders_Admin::is_folders_page() ) {
	        $columns = array(
				'wicked_move' 	=> '<div class="wicked-move-multiple" title="' . __( 'Move selected items', 'wicked-folders' ) . '"><span class="wicked-move-file dashicons dashicons-move"></span><div class="wicked-items"></div></div>',
				'cb' 			=> '<input type="checkbox" />',
	            'title' 		=> 'File',
	            'author' 		=> 'Author',
	            'parent' 		=> 'Uploaded to',
	            'date' 			=> 'Date',
				'wicked_sort' 	=> __( 'Sort', 'wicked-folders' ),
	        );
		} else {
			$a = array( 'wicked_move' => '<div class="wicked-move-multiple" title="' . __( 'Move selected items', 'wicked-folders' ) . '"><span class="wicked-move-file dashicons dashicons-move"></span><div class="wicked-items"></div><span class="screen-reader-text">' . __( 'Move to Folder', 'wicked-folders' ) . '</span></div>' );
			$columns = $a + $columns;
		}

		return $columns;

    }

    public static function manage_media_custom_column( $column_name, $post_id ) {
		if ( 'wicked_move' == $column_name ) {
			echo '<div class="wicked-move-multiple" data-object-id="' . $post_id . '"><span class="wicked-move-file dashicons dashicons-move"></span><div class="wicked-items"><div data-object-id="' . $post_id . '">' . get_the_title() . '</div></div>';
		}
		if ( 'wicked_sort' == $column_name ) {
			echo '<a class="wicked-sort" href="#"><span class="dashicons dashicons-menu"></span></a>';
		}
    }

	public static function manage_upload_sortable_columns( $columns ) {
		$columns['wicked_sort'] = 'wicked_folder_order';
		return $columns;
	}

	public static function restrict_manage_posts( $post_type ) {

		// Post type isn't set on the media list screen
		if ( ! $post_type ) {
			$screen = get_current_screen();
			if ( 'upload' == $screen->base ) {
				$post_type = 'attachment';
			}
		}

		if ( 'attachment' == $post_type && Wicked_Folders::enabled_for( $post_type ) ) {

			// It appears that as of 4.8, WordPress automatically adds a taxonomy
			// filter to attachments so this is no longer necessary and will
			// cause a duplicate dropdown
			if ( version_compare( get_bloginfo( 'version' ), '4.8', '<' ) ) {
				$folder = 0;

				if ( isset( $_GET['wicked_attachment_folder_filter'] ) ) {
					$folder = ( int ) $_GET['wicked_attachment_folder_filter'];
				}

				wp_dropdown_categories( array(
					'orderby'           => 'name',
					'order'             => 'ASC',
					'show_option_none'  => __( 'All folders', 'wicked-folders' ),
					'taxonomy'          => 'wf_attachment_folders',
					'depth'             => 0,
					'hierarchical'      => true,
					'hide_empty'        => false,
					'option_none_value' => 0,
					'name' 				=> 'wicked_attachment_folder_filter',
					'id' 				=> 'wicked-attachment-folder-filter',
					'selected' 			=> $folder,
				) );
			}

		}

	}

	public static function wp_prepare_attachment_for_js( $response, $attachment, $meta ) {

		$folders = wp_get_object_terms( $attachment->ID, 'wf_attachment_folders', array(
			'fields' => 'ids',
		) );

		if ( is_wp_error( $folders ) ) {
			$folders = array();
		}

		$folders = array_map( 'strval', $folders );

		$response['wickedFolders'] = $folders;

		return $response;

	}

	public static function ajax_query_attachments_args( $query ) {

		// Change attachment browser query to not include children folders
		if ( isset( $query['wf_attachment_folders'] ) ) {
			if ( ! empty( $query['wf_attachment_folders'] ) ) {
				// Check if folder is in type.id format
				if ( false !== $index = strpos( $query['wf_attachment_folders'], '.' ) ) {
					$query['wf_attachment_folders'] = substr( $query['wf_attachment_folders'], $index + 1 );
				}
				$tax_query = array(
					array(
						'taxonomy' 			=> 'wf_attachment_folders',
						'field' 			=> 'term_id',
						'terms' 			=> $query['wf_attachment_folders'],
						'include_children' 	=> Wicked_Folders::include_children( 'attachment', $query['wf_attachment_folders'] ),
					),
				);
				$query['tax_query'] = $tax_query;
			}
			unset( $query['wf_attachment_folders'] );
		}

		return $query;

	}

	public static function save_settings() {

		// WARNING: this function is used in both multisite and non-multisite
		// instances. Be careful when adding new pro options.

		$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : false;

		// Save settings
		if ( 'wicked_folders_save_settings' == $action && wp_verify_nonce( $_REQUEST['nonce'], 'wicked_folders_save_settings' ) ) {

			// Save license key setting
			// Check for wicked_folders_pro_license_key (this field doesn't always
			// exist such as on the setting pages of individual sites on a
			// multisite network)
			if ( isset( $_POST['wicked_folders_pro_license_key'] ) ) {

				$existing_license_key 	= get_site_option( 'wicked_folders_pro_license_key', false );
				$new_license_key 		= trim( $_POST['wicked_folders_pro_license_key'] );
				$license_data 			= get_site_option( 'wicked_folders_pro_license_data', false );
				$expired 				= Wicked_Folders_Pro::is_license_expired();

				// Save the license key
				update_site_option( 'wicked_folders_pro_license_key', $new_license_key );

				// Process license key if we don't have any info about the license,
				// if the key has changed or, if the license has expired
				if ( ! $license_data || $existing_license_key != $new_license_key ) {
					// Make sure a non-empty license key was entered
					if ( $new_license_key ) {
						try {
							// Attemp to activate the plugin
							Wicked_Folders_Pro::activate_license();
							// No errors, refresh license data
							$license_data = Wicked_Folders_Pro::fetch_license_data();
							if ( $license_data ) {
								update_site_option( 'wicked_folders_pro_license_data', $license_data );
							} else {
								delete_site_option( 'wicked_folders_pro_license_data' );
							}
						} catch ( Exception $e ) {
							// License activation failed, display an error
							Wicked_Folders_Admin::add_admin_notice( $e->getMessage(), 'notice notice-error' );
							// Clear the license data cache
							delete_site_option( 'wicked_folders_pro_license_data' );
						}
					} else {
						// License key has been removed, delete license meta
						delete_site_option( 'wicked_folders_pro_license_key' );
						delete_site_option( 'wicked_folders_pro_license_data' );
					}
				} else if ( $expired ) {
					try {
						$license_data = Wicked_Folders_Pro::fetch_license_data();

						if ( $license_data ) {
							update_site_option( 'wicked_folders_pro_license_data', $license_data );
						}
					} catch ( Exception $e ) {
						// Failed to fetch license data
						Wicked_Folders_Admin::add_admin_notice( $e->getMessage(), 'notice notice-error' );
					}
				}
			}
		}

	}

	public static function save_attachment( $post_id ) {

		// Attachments saved via media modal
		if ( isset( $_REQUEST['action'] ) && 'save-attachment' == $_REQUEST['action'] && isset( $_REQUEST['changes']['wickedFolders'] ) ) {

			$terms = '';

			if ( is_array( $_REQUEST['changes']['wickedFolders'] ) ) {
				$terms = $_REQUEST['changes']['wickedFolders'];
				$terms = array_map( 'intval', $terms );
			}

			wp_set_object_terms( $post_id, $terms, 'wf_attachment_folders' );

		}

		// Attachments uploaded via media frame and upload new media page
		//if ( isset( $_REQUEST['action'] ) && 'upload-attachment' == $_REQUEST['action'] && ! empty( $_REQUEST['wicked_folder_id'] ) ) {
		if ( ! empty( $_REQUEST['wicked_folder_id'] ) ) {
			if ( is_array( $_REQUEST['wicked_folder_id'] ) ) {
				$terms = $_REQUEST['wicked_folder_id'];
			} else {
				$terms = array( $_REQUEST['wicked_folder_id'] );
			}
			$terms = array_map( 'intval', $terms );
			wp_set_object_terms( $post_id, $terms, 'wf_attachment_folders' );
		}
	}

	/**
	 * WordPress post-plupload-upload-ui action.
	 */
	public static function post_plupload_upload_ui() {
		echo '<div id="wicked-upload-folder-ui">';
		wp_dropdown_categories( array(
			'orderby'           => 'name',
			'order'             => 'ASC',
			'show_option_none'  => __( 'Assign to folder...', 'wicked-folders' ),
			'taxonomy'          => 'wf_attachment_folders',
			'depth'             => 0,
			'hierarchical'      => true,
			'hide_empty'        => false,
			'option_none_value' => 0,
			'name' 				=> 'wicked_upload_folder',
			'id' 				=> 'wicked-upload-folder',
		) );
		echo '</div>';
	}

	public static function network_settings_page() {

		$license_key 	= get_site_option( 'wicked_folders_pro_license_key', false );
		$license_status = Wicked_Folders_Pro::get_license_status_text();

		include( dirname( dirname( __FILE__ ) ) . '/admin-templates/network-settings-page.php' );

	}

}
