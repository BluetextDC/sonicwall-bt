<?php

class HandLUTM_MergeTags extends NF_Abstracts_MergeTags
{
  /*
   * The $id property should match the array key where the class is registered.
   */
  protected $id = 'handl_utm_merge_tags';
  
  public function __construct()
  {
    parent::__construct();
    
    /* Translatable display name for the group. */
    $this->title = __( 'HandL UTM Grabber', 'ninja-forms' );
    
    /* Individual tag registration. */
    
    $my_merge_tags = array();
    $fields = array('utm_source','utm_medium','utm_term', 'utm_content', 'utm_campaign', 'gclid', 'handl_original_ref', 'handl_landing_page', 'handl_ip', 'handl_ref', 'handl_url');
    foreach ($fields as $field){
        $cookie_field = isset($_COOKIE[$field]) ? $_COOKIE[$field] : '';
    	$my_merge_tags[$field] = array(
          'id' => $field,
          'tag' => '{handl:'.$field.'}', // The tag to be  used.
          'label' => __( $field, 'handl_utm_grabber' ), // Translatable label for tag selection.
          'callback' => function() use ($cookie_field) {return urldecode($cookie_field);} // Class method for processing the tag. See below.
        );
    }
    
    $this->merge_tags = $my_merge_tags;
    
    /*
     * Use the `init` and `admin_init` hooks for any necessary data setup that relies on WordPress.
     * See: https://codex.wordpress.org/Plugin_API/Action_Reference
     */
    add_action( 'init', array( $this, 'init' ) );
    add_action( 'admin_init', array( $this, 'admin_init' ) );
  }
  
  public function init(){ /* This section intentionally left blank. */ }
  public function admin_init(){ /* This section intentionally left blank. */ }
  
}
