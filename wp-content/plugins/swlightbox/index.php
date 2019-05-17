<?php
/*
Plugin Name: SWLightbox
Plugin URI: http://sonicwall.com
Description: Easily add a lightbox to any link
Version: 1.0.0
Author: Brad Kendall
*/

function swlightbox( $atts, $content ){
	$a = new SimpleXMLElement($content);
	
	if (isset($a) && $a)
	{
		$url = $a['href'];
		if (isset($url) && $url)
		{
			$postid = url_to_postid( $url );
			
			if ($postid)
			{
				$is_video = get_post_meta($postid, "wpcf-content-type", true) == "Video";
				
				if ($is_video)
				{
					$content = str_replace("<a ", "<a data-fancybox data-src='#video-content".$postid."' ", $content);
					
					return '<div style="display: none; width: 100%; background-color: transparent;" id="video-content'.$postid.'">
		'.get_post_meta($postid, "wpcf-resource-content", true).'</div>'.$content;
				}
			}
		}
	}
	
    $content = str_replace("<a ", "<a data-fancybox data-type='iframe' ", $content);

	
    return $content;
}

function remove_http($url) {
   $disallowed = array('http://', 'https://');
   foreach($disallowed as $d) {
      if(strpos($url, $d) === 0) {
         return str_replace($d, '', $url);
      }
   }
   return $url;
}

add_shortcode( 'swlightbox', 'swlightbox' );