<?php
/*
Plugin Name: SEO Import Tool
Plugin URI: http://sonicwall.com
Description: A WordPress plugin with helper functions for SEO Import
Version: 1.0.0
Author: Brad Kendall
*/


function import_titles()
{
    $csv = array_map('str_getcsv', file(plugin_dir_path(__FILE__).'titles.csv'));
    array_shift($csv);

    $site_url = get_site_url();

    foreach($csv as $row)
    {

        $url = $site_url.str_replace("~", "", $row[0]);

        echo "<hr />Starting to process: {$url} <br>";
        $title = $row[1];
        $desc = $row[2];
        $img_desc = $row[3];

        $post_id = url_to_postid( $url );

        if ($post_id)
        {

            //Update title
            if ($title && strlen($title) > 0)
            {
                update_post_meta( $post_id, "_yoast_wpseo_title", $title);
            }
            else
            {
                echo "No title for: {$url} <br>";
            }

            //Update meta description
            if ($desc && strlen($desc) > 0)
            {
                update_post_meta( $post_id, "_yoast_wpseo_metadesc", $desc );
            }
            else
            {
                echo "No description for: {$url} <br>";
            }

            if ($img_desc && strlen($img_desc) > 0)
            {
                $feature_image_id = get_post_thumbnail_id( $post_id );
                if ($feature_image_id)
                {
                    $attachment = get_post( $feature_image_id );

                    $attachment->post_content = $img_desc;
                    // Update the attachment into the database
                    wp_update_post( $attachment);
                }
                else
                {
                    echo "No feature image for: {$url} <br>";
                }
                
            }
            else
            {
                echo "No Image description for: {$url} <br>";
            }
        }
        else
        {
            echo "No Post found for: {$url} <br>";
        }

        echo "<hr><br><br>";
    }

    die();
}

add_action( "wp_ajax_import_titles", "import_titles" );


