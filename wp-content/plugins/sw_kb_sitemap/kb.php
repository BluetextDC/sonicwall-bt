<?php

class BWP_GXS_MODULE_KB extends BWP_GXS_MODULE {
    function __construct() {
        $this->set_current_time();
        $this->build_data();
    }
    function build_data() {
        global $wpdb, $bwp_gxs;

        $kbs = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}ra_slugs GROUP BY sol_id ORDER BY created_at DESC;" );

        if ( !isset( $kbs) || 0 == sizeof( $kbs ))
        {
            return false;
        }
            
        $data = array();
        foreach($kbs as $kb) {
            
            switch ($kb->type) {
                case 'solution':
                    $data['location'] = get_site_url().'/support/knowledge-base/' . $kb->slug . '/' . $kb->sol_id . '/';
                    $data['lastmod'] = $this->format_lastmod( strtotime ( $kb->created_at ) );
                    $data['freq'] = "monthly";
                    $data['priority'] = 1;
                    $this->data[] = $data;
                    break;
                case 'alert':
                    $data['location'] = get_site_url().'/support/product-notification/' . $kb->slug . '/' . $kb->sol_id . '/';
                    $data['lastmod'] = $this->format_lastmod( strtotime ( $kb->created_at ) );
                    $data['freq'] = "monthly";
                    $data['priority'] = 1;
                    $this->data[] = $data;
                    break;
            }
        }
        return true;
    }
}
?>