<?php

/**
 * Represents a folder object that is represented as a taxonomy term.
 */
class Wicked_Folders_Term_Folder extends Wicked_Folders_Folder {

    public function __construct( $args ) {
        parent::__construct( $args );
    }

    public function ancestors() {
        return get_ancestors( $this->id, $this->taxonomy, 'taxonomy' );
    }

    public function fetch_posts() {

        return get_posts( array(
            'post_type'         => $this->post_type,
            'orderby'           => 'title',
            'order'             => 'ASC',
            'posts_per_page'    => -1,
            'tax_query' => array(
                array(
                    'taxonomy'          => $this->taxonomy,
                    'field'             => 'term_id',
                    'terms'             => ( int )$this->id,
                    'include_children'  => false,
                ),
            ),
        ) );

    }

    /**
     * Returns a new folder instance containing the same objects as the current
     * instance.
     *
     * @return Wicked_Folders_Term_Folder
     */
    public function clone_folder() {
        global $wpdb;

        $folder                 = clone $this;
        $name_index             = 1;
        $unique_name_generated  = false;
        $sort_key               = '_wicked_folder_order__' . $this->taxonomy . '__' . $this->id;

        // Get folder siblings so we can generate a unique name
        if ( version_compare( get_bloginfo( 'version' ), '4.5.0', '<' ) ) {
            $siblings = get_terms( $this->taxonomy, array(
                'hide_empty' 	=> false,
                'parent'        => $this->parent,
                'fields'        => 'names',
            ) );
        } else {
            $siblings = get_terms( array(
                'taxonomy' 		=> $this->taxonomy,
                'hide_empty' 	=> false,
                'parent'        => $this->parent,
                'fields'        => 'names',
            ) );
        }

        // Generate a unique name
        while ( ! $unique_name_generated ) {
            $name = $this->name . ' ' . sprintf( __( '(Copy %1$d)', 'wicked-folders' ), $name_index );
            if ( ! in_array( $name, $siblings ) ) {
                $unique_name_generated  = true;
                $folder->name           = $name;
            }
            $name_index++;
        }

        // Create a new folder term
        $term = wp_insert_term( $folder->name, $folder->taxonomy, array(
            'parent' => $folder->parent,
        ) );

        if ( is_wp_error( $term ) ) {
            throw new Exception( __( 'Error cloning folder.', 'wicked-folders' ) );
        }

        // Update the new folder's ID
        $folder->id = ( string ) $term['term_id'];

        $cloned_folder_sort_key = '_wicked_folder_order__' . $folder->taxonomy . '__' . $folder->id;

        // Get the IDs of objects assigned to the current folder
        $posts_ids = get_posts( array(
            'post_type'         => $this->post_type,
            'posts_per_page'    => -1,
            'fields'            => 'ids',
            'tax_query' => array(
                array(
                    'taxonomy'          => $this->taxonomy,
                    'field'             => 'term_id',
                    'terms'             => $this->id,
                    'include_children'  => false,
                ),
            ),
        ) );

        // Assign the posts in the current folder to the new folder
        foreach ( $posts_ids as $id ) {
            $result = wp_set_object_terms( $id, ( int ) $folder->id, $folder->taxonomy, true );
        }

        // Copy the existing folder's sort order
        $wpdb->query( "
            INSERT INTO
                {$wpdb->prefix}postmeta (post_id, meta_key, meta_value)
            SELECT
                pm.post_id, '{$cloned_folder_sort_key}', pm.meta_value FROM {$wpdb->prefix}postmeta pm WHERE pm.meta_key = '{$sort_key}'
        " );

        return $folder;
    }

}
