<?php
/*
Plugin Name: SW Contact Form Lightbox
Plugin URI: https://www.sonicwall.com
Description: Deploy Contact form inside a lightbox
Version: 0.1.0
Author: Brad Kendall*/

add_action('wp_footer', 'add_lightbox_contact_form');
function add_lightbox_contact_form() {
    
    $id=1574; 
    $post = get_post($id); 
    $contact_form_content = do_shortcode(apply_filters('the_content', $post->post_content)); 
    $contact_form_content = apply_filters(‘avf_template_builder_content’, $contact_form_content);
    

    $first = strpos($contact_form_content, '[avia_codeblock_placeholder uid="');
    
    $middle = substr($contact_form_content, $first);
    
    $last = strpos($middle, ']');
    
    
    $fb = substr($contact_form_content, 0, $first);
    
    $lb = substr($middle, $last + 1);
    
    $contact_form_content = $fb.do_shortcode('[gravityform id="60" title="true" description="true" ajax="false"]').$lb;
    
    
    
    $output = "<style>
    #contact_form_popup .main_color {
        background-color: white !important;
    }
    #contact_form_popup .template-page {
        padding-top: 0;
    }
    
    #contact_form_popup .av-special-heading {
        margin-top: 10px;
    }
    
    #contact_form_popup #input_60_9 {
        height: 70px;
    }
    </style>";
    
    $output .= '<div id="contact_form_popup" style="display:none;">';
    $output .= "<div><div><div><div><div>{$contact_form_content}";
    $output .= '</div>';  
    $output .= '<script>';
    $output .= 'jQuery(document).ready(function(){
        jQuery(".contact-sales-popover a").attr("data-hide-copy-button", true);
        jQuery(".contact-sales-popover a").attr("data-src", "#contact_form_popup");
        jQuery(".contact-sales-popover a").attr("data-fancybox", "contact_form");
        setTimeout(function(){
            jQuery(\'[data-fancybox="contact_form"]\').fancybox({
                afterShow: function( instance, slide ) {
                    if (ga && "function" == typeof ga && ga.create && "function" === typeof ga.create) {
                           ga("send", "pageview", "/customers/contact-sales/");
                    }
                }
            });
        });
        
    })';
    $output .= '</script>';
    
    echo $output;
}
