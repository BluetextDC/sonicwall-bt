<?php
/*
Plugin Name: Resource Redirect
Plugin URI: http://sonicwall.com
Description: A WordPress plugin to redirect resources to external links on CPT
Version: 1.0.1
Author: Brad Kendall
*/

function resource_template_redirect()
{
  if( get_post_type() == 'resources-slug' )
  {
    $id = get_the_id();
    $link_type = get_post_meta( $id, 'wpcf-link-type', true );
    if( $link_type && ($link_type == "external" || $link_type == "invalid" || $link_type == "404") ) {
        $url = get_post_meta( $id, 'wpcf-resource-content', true);
        if ($url)
        {
            wp_redirect( $url );
            die;
        }
    }
  }
}
add_action( 'template_redirect', 'resource_template_redirect' );

function resource_exerpt( $atts ){
    $id = get_the_id();
    $link_type = get_post_meta( $id, 'wpcf-link-type', true );
    if( $link_type && ($link_type == "gated" || $link_type == "default") ) {
        $length = 30;
        $more = '...';
        $content = get_post_meta( $id, 'wpcf-resource-content', true );

        $excerpt = strip_tags( trim( $content ) );
        $words = str_word_count( $excerpt, 2 );
        if ( count( $words ) > $length ) {
            $words = array_slice( $words, 0, $length, true );
            end( $words );
            $position = key( $words ) + strlen( current( $words ) );
            $excerpt = substr( $excerpt, 0, $position ) . $more;
        }
        return $excerpt;
    }
 
    return "";
}

add_shortcode( 'resource_exerpt', 'resource_exerpt' );