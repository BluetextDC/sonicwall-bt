<?php

/**
 * Represents a media extension (e.g. .jpg, .pdf, etc.) dynamic folder.
 */
class Wicked_Folders_Media_Extension_Dynamic_Folder extends Wicked_Folders_Dynamic_Folder {

    private $extension = false;

    public function __construct( $args ) {
        parent::__construct( $args );
    }

    public function pre_get_posts( $query ) {

        $this->parse_id();

        if ( $this->extension ) {

            $meta_query = $query->get( 'meta_query' );

            if ( ! $meta_query ) $meta_query = array();

            $meta_query[] = array(
                'key'       => '_wp_attached_file',
                'value'     => '.' . $this->extension,
                'compare'   => 'LIKE',
            );

            $query->set( 'meta_query', $meta_query );

        }

    }

    /**
     * Parses the folder's ID to determine the extension the folder should
     * filter by.
     */
    private function parse_id() {

        $this->extension = substr( $this->id, 24 );

    }

}
