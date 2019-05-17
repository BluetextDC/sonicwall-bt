<?php

class Wicked_Folders_Pro_Media_List_Table extends Wicked_Folders_Pro_WP_Media_List_Table {

    protected function get_bulk_actions() {
        return array();
    }

    protected function extra_tablenav( $which ) {
        return '';
    }

    public function prepare_items() {

        // Trick Wicked_Folders_WP_Posts_List_Table into sorting by alphabetical
        $orderby    = isset( $_GET['orderby'] ) ? $_GET['orderby'] : false;
        $order      = isset( $_GET['order'] ) ? $_GET['order'] : false;

        if ( ! $orderby || 'menu_order title' == $orderby ) {
            $_GET['orderby'] = 'title';
            $_GET['order'] = 'asc';
        }

        parent::prepare_items();

        if ( $orderby ) {
            $_GET['orderby'] = $orderby;
        }

        if ( $order ) {
            $_GET['order'] = $order;
        }

    }

}
