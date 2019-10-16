<?php
/*
Plugin Name: Resources Helper
Plugin URI: http://sonicwall.com
Description: A WordPress plugin with helper functions for resources
Version: 1.0.5
Author: Brad Kendall
*/

// Get the queried object and sanitize it

require 'vendor/autoload.php';

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use TheIconic\Tracking\GoogleAnalytics\Analytics;

add_filter( 'wpv_filter_query', 'wpv_combine', 99, 3  );
//Combine Whitepapers and Ebooks if White Paper is selected.
function wpv_combine( $query_args, $view_settings, $view_id ) {
     
    if (isset($view_settings['view_id']) && $view_settings['view_id'] == 17686) {
        
        foreach ($query_args['meta_query'] as &$q)
        {
            if (isset($q["key"]) && $q["key"] === "wpcf-content-type" && $q["value"] === "White Paper")
            {
                $q["value"] = array("White Paper", "Ebook");
                $q["compare"] = "IN";
            }
        }
        
    }
    //Ignore page ID
    $query_args['post__not_in'][] = 50455;
    
    return $query_args;
}

//Remove the Resources asset left/right navigation
add_filter('avia_post_nav_entries','avia_remove_post_nav', 10, 1);
function avia_remove_post_nav() {   
    return false; 
}

add_action( 'wp', 'lang_redirect' );
function lang_redirect()
{
    $current_page = sanitize_post( $GLOBALS['wp_the_query']->get_queried_object() );
    
    if ($current_page && isset($current_page->post_name))
    {
        // Get the page slug
        $slug = $current_page->post_name;
    
        if ($slug && $slug === "resources" && !isset($_GET['wpv-wpcf-resource-language']))
        {
            if (wp_redirect(add_query_arg('wpv-wpcf-resource-language','en')))
            {
                exit();
            }
        }
   
    }
}

add_filter("gform_confirmation", "custom_confirmation", 10, 4);
function custom_confirmation($confirmation, $form, $lead, $ajax){

    

    if ($form["id"] == 52 || $form["id"] == 61 || $form["id"] == 63 || $form["id"] == 68)
    {
        $id = get_the_id();

        $gated_content = get_post_meta( $id, 'wpcf-gated-content', true );

        if ($gated_content)
        {
           if (ignore_lightbox())
            {
                $gated_content = "<script>setTimeout(function(){window.location.href='{$gated_content}'}, 1500);</script>";
            }
            else
            {
                 $gated_content = "<a data-fancybox data-type='iframe' href='{$gated_content}' id='access_gated_content' class='gform_button button' style='left: 25%; top: 100px;'>Access Content</a><script>jQuery(document).ready(function(){setTimeout(function(){jQuery('#access_gated_content').click();}, 1500);});</script>";
            }
        }
        else
        {
            $gated_content = get_post_meta( $id, 'wpcf-postgate-content', true );
        }
        
        if (strlen($gated_content) <= 0)
        {
            $gated_content = $confirmation;
        }
    
        $floodlight_tag = "";
        
        if ($form["id"] == 52 || $form["id"] == 63 || $form["id"] == 68)
        {
            //Resource tag
            $floodlight_tag = '<script> window.dataLayer = window.dataLayer || []; window.dataLayer.push({"event": "gated-asset"})</script>';
        }
        
        if ($form["id"] == 61)
        {
            $floodlight_tag = '<script> window.dataLayer = window.dataLayer || []; window.dataLayer.push({"event": "trial-submit"})</script>';
        }
        
        $gated_content = $gated_content.$floodlight_tag;
        
        //sendAnalyticsPageView($id);
        
   
        return $gated_content;
    }
    
    return $confirmation;
}

function sendAnalyticsPageView($post_id)
{
    
    //Check to see if the data is sent:
    
    if (isset($_REQUEST['gaClientId']) && isset($_REQUEST['gaTrackingId']))
    {
        
        $post = get_post( $id );
        $content_type = get_post_meta( $id, 'wpcf-content_type', true );
        
        if ($post && $content_type)
        {
            $slug = $post->post_name;
        
            $submission_type = "gatedasset";
            
            if ($content_type == "Trial")
            {
                $submission_type = "trial";
            }
            
            
            $virtual_page = "/resources/submission/{$submission_type}/{$slug}";
                
            $gaClientId = $_REQUEST['gaClientId'];
            $gaTrackingId = $_REQUEST['gaTrackingId'];

            $ip = false;

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];        
            }
            else
            {
                $ip = $_SERVER['REMOTE_ADDR'];        
            }

            $analytics = new Analytics(true);

            // Build the GA hit using the Analytics class methods
            $analytics
                ->setProtocolVersion('1')
                ->setTrackingId($gaTrackingId)
                ->setClientId($gaClientId)
                ->setDocumentPath($virtual_page)
                ->setIpOverride($ip);

            // When you finish bulding the payload send a hit (such as an pageview or event)
            $analytics->sendPageview();      
        }
    }
}

function export_resources()
{
    global $wpdb;

   $sql = "SELECT p.post_title as title, 
       link_type.meta_value    AS link_type, 
       content_type.meta_value AS content_type,
       tags.meta_value AS tags,
       product_series.meta_value AS product_series,
       image.meta_value AS image,
       content.meta_value as content,
       pregate_image.meta_value as pregate_image,
       pregate_content.meta_value as pregate_content,
       postgate_content.meta_value as postgate_content,
       gated_content.meta_value as gated_content,
       language.meta_value as `language`,
       guid.meta_value as guid,
       p.ID as post_id,
       teaser.meta_value as teaser,
       CONCAT('/?page_id=', p.ID) as link,
       p.post_date as created_at,
       p.post_modified as updated_at,
       asset_name.meta_value as asset_name,
       asset_type.meta_value as asset_type,
       gated_form.meta_value as gated_form
FROM   wp_posts p 
       LEFT JOIN wp_postmeta link_type 
              ON link_type.post_id = p.id 
                 AND link_type.meta_key = 'wpcf-link-type' 
       LEFT JOIN wp_postmeta content_type 
              ON content_type.post_id = p.id 
                 AND content_type.meta_key = 'wpcf-content-type' 
       LEFT JOIN wp_postmeta tags 
              ON tags.post_id = p.id 
                 AND tags.meta_key = 'wpcf-resource-tags' 
       LEFT JOIN wp_postmeta product_series 
              ON product_series.post_id = p.id 
                 AND product_series.meta_key = 'wpcf-resource-product-series' 
       LEFT JOIN wp_postmeta image 
              ON image.post_id = p.id 
                 AND image.meta_key = 'wpcf-resource-image'
       LEFT JOIN wp_postmeta content 
              ON content.post_id = p.id 
                 AND content.meta_key = 'wpcf-resource-content' 
       LEFT JOIN wp_postmeta pregate_image 
              ON pregate_image.post_id = p.id 
                 AND pregate_image.meta_key = 'wpcf-pregate-image'
       LEFT JOIN wp_postmeta pregate_content 
              ON pregate_content.post_id = p.id 
                 AND pregate_content.meta_key = 'wpcf-pregate-content' 
       LEFT JOIN wp_postmeta postgate_content 
              ON postgate_content.post_id = p.id 
                 AND postgate_content.meta_key = 'wpcf-postgate-content'
       LEFT JOIN wp_postmeta gated_content 
              ON gated_content.post_id = p.id 
                 AND gated_content.meta_key = 'wpcf-gated-content' 
       LEFT JOIN wp_postmeta `language`
              ON language.post_id = p.id 
                 AND language.meta_key = 'wpcf-resource-language' 
       LEFT JOIN wp_postmeta guid
              ON guid.post_id = p.id 
                 AND guid.meta_key = 'wpcf-guid' 
       LEFT JOIN wp_postmeta teaser
              ON teaser.post_id = p.id 
                 AND teaser.meta_key = 'wpcf-resource-teaser'
       LEFT JOIN wp_postmeta asset_name
              ON asset_name.post_id = p.id 
                 AND asset_name.meta_key = 'wpcf-resource-asset-name'
       LEFT JOIN wp_postmeta asset_type
              ON asset_type.post_id = p.id 
                 AND asset_type.meta_key = 'wpcf-resource-asset-type' 
       LEFT JOIN wp_postmeta gated_form
              ON gated_form.post_id = p.id
                 AND gated_form.meta_key = 'wpcf-resource-gated-form'
WHERE  p.post_type = 'resources-slug' 
       AND p.post_status = 'publish'
       GROUP BY p.ID;";


       $resources = $wpdb->get_results( $sql, OBJECT );

       createSpreadsheet($resources);
}

function createSpreadsheet($resources)
{

    $spreadsheet = new Spreadsheet();

    $sheet = $spreadsheet->getActiveSheet();
        // $sheet->setTitle($lang);

        //Title Width
        $sheet->getColumnDimension('A')->setWidth(75);

        //Image Width
        $sheet->getColumnDimension('E')->setWidth(100);

        //Conent
        $sheet->getColumnDimension('F')->setWidth(100);

        //Gate
        $sheet->getColumnDimension('G')->setWidth(100);
        $sheet->getColumnDimension('H')->setWidth(100);
        $sheet->getColumnDimension('I')->setWidth(100);
        $sheet->getColumnDimension('J')->setWidth(100);

        $count = count($resources);
        $sheet->getStyle('A1:H'.$count)->getAlignment()->setWrapText(true);
        //Build the header
        $sheet->setCellValue('A1', 'Title');
        $sheet->setCellValue('B1', 'Link Type');
        $sheet->setCellValue('C1', 'Content Type');
        $sheet->setCellValue('D1', 'Tags');
        $sheet->setCellValue('E1', 'Product Series');
        $sheet->setCellValue('F1', 'Image');
        $sheet->setCellValue('G1', 'Content');
        $sheet->setCellValue('H1', 'PreGate Image');
        $sheet->setCellValue('I1', 'PreGate Content');
        $sheet->setCellValue('J1', 'PostGate Content');
        $sheet->setCellValue('K1', 'Gated Content');
        $sheet->setCellValue('L1', 'Language');
        $sheet->setCellValue('M1', 'GUID');
        $sheet->setCellValue('N1', 'Teaser');
        $sheet->setCellValue('O1', 'Link');
        $sheet->setCellValue('P1', 'Created At');
        $sheet->setCellValue('Q1', 'Updated At');
        $sheet->setCellValue('R1', 'Asset Name');
        $sheet->setCellValue('S1', 'Asset Type');
        $sheet->setCellValue('T1', 'Gated Form Type');


        $i = 1;

    foreach($resources as $resource)
    {

        

        // foreach ($resources as $resource)
        // {
            $gated_content = $resource->link_type == "gated" ? $resource->content : "";
        
            $ps = unserialize($resource->product_series);
            $product_series = array();

            foreach ($ps as $p)
            {
                foreach ($p as $gp)
                {
                    $product_series[] = $gp;
                }
            }
  
            $product_series = implode("\n", $product_series);
            $tags = implode("\n", $resource->tags);
            $i++;
            $sheet->setCellValue("A".$i, $resource->title);
            $sheet->setCellValue("B".$i, $resource->link_type);
            $sheet->setCellValue("C".$i, $resource->content_type);
            $sheet->setCellValue("D".$i, $tags);
            $sheet->setCellValue("E".$i, $product_series);
            $sheet->setCellValue("F".$i, $resource->image);
            $sheet->setCellValue("G".$i, $resource->content);
            $sheet->setCellValue("H".$i, $resource->pregate_image);
            $sheet->setCellValue("I".$i, $resource->pregate_content);
            $sheet->setCellValue("J".$i, $resource->postgate_content);
            $sheet->setCellValue("K".$i, $resource->gated_content);
            $sheet->setCellValue("L".$i, $resource->language);
            $validation = $sheet->getCell('L'.$i)->getDataValidation();
            $validation->setType( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST );
            $validation->setErrorStyle( \PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION );
            $validation->setAllowBlank(false);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setErrorTitle('Input error');
            $validation->setError('Language is not in list.');
            $validation->setPromptTitle('Pick from list');
            $validation->setPrompt('Please pick a language from the drop-down list.');
            $validation->setFormula1('"en,cn,fr,de,jp,br,mx,kr,it"');
            $sheet->setCellValue("M".$i, $resource->guid);
            $sheet->setCellValue("N".$i, $resource->teaser);
            $sheet->setCellValue("O".$i, $resource->link);
            $sheet->setCellValue("P".$i, $resource->created_at);
            $sheet->setCellValue("Q".$i, $resource->updated_at);
            $sheet->setCellValue("R".$i, $resource->asset_name);
            $sheet->setCellValue("S".$i, $resource->asset_type);
            $sheet->setCellValue("T".$i, $resource->gated_form);
    //  }
    
        
    }
    
    
    $writer = new Xlsx($spreadsheet);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="resources.xlsx"');
    $writer->save("php://output");
    die();
    // $writer->save('hello world.xlsx');
}

add_action( "wp_ajax_export_resources", "export_resources" );

add_action( "wp_ajax_import_resources", "import_resources" );

function import_resources()
{
  $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
  $reader->setReadDataOnly(false);
  $spreadsheet = $reader->load(__DIR__."/input.xlsx");
  $highestRow = $spreadsheet->getActiveSheet()->getHighestRow();

  $sheet = $spreadsheet->getActiveSheet();
  for ($i = 2; $i <= $highestRow; $i++)
  {

    $resource = new stdClass();
    $resource->title = $sheet->getCell('A'.$i)->getValue();
    $resource->link_type = $sheet->getCell('B'.$i)->getValue();
    $resource->content_type = $sheet->getCell('C'.$i)->getValue();
    $resource->tags = $sheet->getCell('D'.$i)->getValue();
    $resource->product_series = $sheet->getCell('E'.$i)->getValue();
    $resource->image = $sheet->getCell('F'.$i)->getValue();
    $resource->content = $sheet->getCell('G'.$i)->getValue();
    $resource->pregate_image = $sheet->getCell('H'.$i)->getValue();
    $resource->pregate_content = $sheet->getCell('I'.$i)->getValue();
    $resource->postgate_content = $sheet->getCell('J'.$i)->getValue();
    $resource->gated_content = $sheet->getCell('K'.$i)->getValue();
    $resource->language = $sheet->getCell('L'.$i)->getValue();
    $resource->guid = $sheet->getCell('M'.$i)->getValue();
    $resource->teaser = $sheet->getCell('N'.$i)->getValue();
    $resource->link = $sheet->getCell('O'.$i)->getValue();
    $resource->created_at = $sheet->getCell('P'.$i)->getValue();
    $resource->updated_at = $sheet->getCell('Q'.$i)->getValue();
    $resource->asset_name = $sheet->getCell('R'.$i)->getValue();
    $resource->asset_type = $sheet->getCell('S'.$i)->getValue();
    $resource->gated_form = $sheet->getCell('T'.$i)->getValue();

    echo "Processing: ".$resource->title."<br>";
 
    $link = str_replace("/?", "", $resource->link);
    parse_str($link, $parsed);
    
    $post_id = false;

    if ($parsed && $parsed["page_id"])
    {
      $post_id = intval($parsed["page_id"]);
    }
      

    if ($post_id)
    {

      //Update an existing post
      $post = get_post($post_id);

      if ($post)
      {
        $post->post_title = $resource->title;
                  
        wp_update_post( $post );
        
      }
      else
      {
        echo("Invalid post id<br>");
      }
      
    }
    else
    {
        $new_post = array(
            "post_title" => $resource->title,
            "post_type" => "resources-slug",
            "post_content" => '[wpv-post-body view_template="resources"]',
            "post_status" => 'publish',
        );
        
        $post_id = wp_insert_post($new_post);
      
    }
      
        update_post_meta( $post_id, "wpcf-link-type", $resource->link_type );
        update_post_meta( $post_id, "wpcf-content-type", $resource->content_type );

        //TODO: tags
        //TODO: product series

        update_post_meta( $post_id, "wpcf-resource-image", $resource->image );
        update_post_meta( $post_id, "wpcf-resource-content", $resource->content );

        update_post_meta( $post_id, "wpcf-pregate-image", $resource->pregate_image );
        update_post_meta( $post_id, "wpcf-pregate-content", $resource->pregate_content );
        update_post_meta( $post_id, "wpcf-postgate-content", $resource->postgate_content );
        update_post_meta( $post_id, "wpcf-gated-content", $resource->gated_content );
        update_post_meta( $post_id, "wpcf-resource-language", $resource->language );
        update_post_meta( $post_id, "wpcf-resource-teaser", $resource->teaser );
        update_post_meta( $post_id, "wpcf-resource-asset-name", $resource->asset_name );
        update_post_meta( $post_id, "wpcf-resource-asset-type", $resource->asset_type );
        update_post_meta( $post_id, "wpcf-resource-gated-form", $resource->gated_form );
          
        //Update the dates
        $post_modified = dateify($resource->updated_at);
        $post_modified_gmt = dateify($resource->updated_at);
        
        global $wpdb;
        $wpdb->query("UPDATE $wpdb->posts SET post_modified = '{$post_modified}', post_modified_gmt = '{$post_modified_gmt}'  WHERE ID = {$post_id}" );
  }

        set_taxonomy();
  die("Done!");  
    
}

function dateify($date)
{
  //should look like: "2018-12-28 03:38:25"
  // $date = str_replace("Updated: ", "", $date);
  $time = strtotime($date);

  $formatted = date('Y-m-d H:i:s', $time);

  return $formatted;
}

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


function gated_content_form( $atts ){
    $id = get_the_id();
    $form_type = get_post_meta( $id, 'wpcf-resource-gated-form', true );
    $asset_name = get_post_meta( $id, 'wpcf-resource-asset-name', true);
    $asset_type = get_post_meta( $id, 'wpcf-resource-asset-type', true);
    
    $field_values = "asset_name={$asset_name}&asset_type={$asset_type}";
    
    
    if ($form_type == "SWAssetGate.SWGatedContent_ShortForm")
    {
        
        $ga_js = '<script>jQuery("form[id^=gform]").submit(function(t){t.preventDefault();var a=this;ga&&"function"==typeof ga&&ga.create&&"function"===typeof ga.create?ga(function(t){var e=t.get("clientId"),n=ga.getAll()[0].get("trackingId");var slug = window.location.href.split("/");slug = slug[slug.length - 2];jQuery("<input />").attr("type","hidden").attr("name","gaClientId").attr("value",e).appendTo(a),jQuery("<input />").attr("type","hidden").attr("name","gaTrackingId").attr("value",n).appendTo(a),ga("send", "pageview", "/resources/submission/gatedasset/" + slug),a.submit()}):a.submit()});</script>';
        
       return do_shortcode('[gravityform id="52" field_values="'.$field_values.'"/]').$ga_js;
    }
    else
    {
        
        $ga_js = '<script>jQuery("form[id^=gform]").submit(function(t){t.preventDefault();var a=this;ga&&"function"==typeof ga&&ga.create&&"function"===typeof ga.create?ga(function(t){var e=t.get("clientId"),n=ga.getAll()[0].get("trackingId");var slug = window.location.href.split("/");slug = slug[slug.length - 2];jQuery("<input />").attr("type","hidden").attr("name","gaClientId").attr("value",e).appendTo(a),jQuery("<input />").attr("type","hidden").attr("name","gaTrackingId").attr("value",n).appendTo(a),ga("send", "pageview", "/resources/submission/trial/" + slug),a.submit()}):a.submit()});</script>';
        
        return do_shortcode('[gravityform id="61" field_values="'.$field_values.'"/]').$ga_js;
    }
 
    return "";
}

add_shortcode( 'gated_content_form', 'gated_content_form' );

function resource_nav_item( $atts ){
    
    $key = $atts['key'];
    $class = $atts['class'];
    $label = $atts['label'];
   
    $params = $_GET;
    $params['wpv-wpcf-content-type'] = $key;
    $href = "/resources/?".http_build_query($params);
    
    $active = $_GET['wpv-wpcf-content-type'] == $key ? "active" : "";
    return "
    <a href='{$href}' class='resources-toggle {$class} {$active}'>
        {$label}
    </a>
    ";
}

add_shortcode( 'resource_nav_item', 'resource_nav_item' );


function resource_link( $atts, $content ){
    $id = get_the_id();

    $title = do_shortcode($content);
    $url = get_post_permalink($id);
    
    $link_type = get_post_meta( $id, 'wpcf-link-type', true );
    $content_type = get_post_meta( $id, 'wpcf-content-type', true );
    
    if ($link_type == "external" && $content_type != "Blog")
    {
        $content = get_post_meta( $id, 'wpcf-resource-content', true);
        
        if(substr($content, -4) == '.pdf'){
            if (ignore_lightbox())
            {
                return "<a href='{$url}'>{$title}</a>";
            }
            else
            {
              return "<a data-fancybox data-type='iframe' href='{$url}'>{$title}</a>";  
            }
        }
    }
    
    $new_window = "";
    
    if ($content_type == "Blog")
    {
        $new_window = ' target="_blank" ';
    }
    return "<a href='{$url}' {$new_window}>{$title}</a>";
}

function ignore_lightbox()
{
    if (wp_is_mobile())
    {
        return true;
    }
    
    if (wp_is_ie())
    {
        return true;
    }
    
    return false;
}

function wp_is_ie()
{
    $ua = htmlentities($_SERVER['HTTP_USER_AGENT'], ENT_QUOTES, 'UTF-8');
    
    if (preg_match('~MSIE|Internet Explorer~i', $ua) || (strpos($ua, 'Trident/7.0') !== false && strpos($ua, 'rv:11.0') !== false)) {
        return true;
    }      
    else
    {
        return false;
    }
}
add_shortcode( 'resource-link', 'resource_link' );


function is_gated_resource( $atts, $content ){
   
    $id = get_the_id();
    $link_type = get_post_meta( $id, 'wpcf-link-type', true );

    if ($link_type == "gated")
    {
        $content = do_shortcode($content);
        return $content;
    }
    else
    {
        return "";
    }
}

add_shortcode( 'is_gated_resource', 'is_gated_resource' );



add_action('admin_menu', 'resource_setup_menu');
 
function resource_setup_menu(){
        add_menu_page( 'Resource Import / Export', 'Resource Import / Export', 'manage_options', 'resource-helper', 'resource_init' );
}
 
function resource_init(){
        ?>
        <h1>Import / Export Resources</h1>
        <hr />
        <h3>Export:</h3>
        <a href="/wp-admin/admin-ajax.php?action=export_resources">Download Resource XLSX file</a>
        <hr />
        <h3>Import: <span style="color: red;"> **Do not use yet!**</span></h3>
        <input type="file" disabled="disabled">
        <button disabled="disabled">Import (do not use yet!!!)</button>
        <hr />
        <?php
}

add_filter('avf_builder_boxes', 'avia_register_meta_boxes', 10, 1); //Add meta boxes to custom post types
function avia_register_meta_boxes($boxes)
{
    if(!empty($boxes))
    {
        foreach($boxes as $key => $box)
        {
            $boxes[$key]['page'][] = 'resources-slug';
        }
    }
    
    return $boxes;
}


add_action( "wp_ajax_set_taxonomy", "set_taxonomy" );

function set_taxonomy()
{
  $posts = get_posts([
      'post_type' => 'resources-slug',
      'post_status' => 'publish',
      'numberposts' => -1
    ]);
    
    foreach($posts as $post)
    {
        $post_id = $post->ID;
        $content_type = get_post_meta( $post_id, 'wpcf-content-type', true );
        
      //Lookup the proper content type and return it to the old format
      switch ($content_type) {
      case 'Brief':
          $content_type = 'Brief';
          break;
      case 'Ebook':
          $content_type = 'Ebook';
          break;
      case 'Trial':
          $content_type = 'Trials-Landing';
          break;
      case 'Analyst Report':
          $content_type = 'Analyst-Report';
          break;
      case 'Datasheet':
          $content_type = 'Datasheet';
          break;
      case 'Infographic':
          $content_type = 'Infographics';
          break;
      case 'On Demand Webcast':
          $content_type = 'On-Demand-Webcasts';
          break;
      case 'Blog':
          $content_type = 'Blog';
          break;
      case 'Case Study':
          $content_type = 'Case-Studies';
          break;
      case 'White Paper':
          $content_type = 'White-Papers';
          break;
      case 'Video':
          $content_type = 'Videos';
          break;
      case 'Webinar':
          $content_type = 'Webinars';
          break;
     }
      
      //Lowercase it
      $content_type = strtolower($content_type);
        
        wp_set_post_terms( $post_id, $content_type, "resource-content-type", false );
    }
    
    echo "Updated Custom Taxonomies";
    die();
}
