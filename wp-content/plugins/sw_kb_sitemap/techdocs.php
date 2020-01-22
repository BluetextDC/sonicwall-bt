<?php

class BWP_GXS_MODULE_TECHDOCS extends BWP_GXS_MODULE {
    function __construct() {
        $this->set_current_time();
        $this->build_data();
    }
    function build_data() {
                
        $class_path = ABSPATH . 'wp-content/plugins/swtechdocs/sw_td_data.php';

        if (file_exists($class_path))
        {
           global $bwp_gxs;
            
           require_once($class_path);
        
           $sw_td_data = new SW_TD_Data();
            
           $techdocs = $sw_td_data->getTDData();
            
            if ( !isset( $techdocs) || 0 == sizeof( $techdocs ))
            {
                return false;
            }

            $data = array();
            foreach($techdocs as $techdoc) {
                $data['location'] = get_site_url().$techdoc->url;
                $data['lastmod'] = $this->format_lastmod($techdoc->modified);
                $data['freq'] = "monthly";
                $data['priority'] = 1;
                $this->data[] = $data;
            }
            return true;
        }
        else
        {
            return false;
        }
    
    }
}
?>