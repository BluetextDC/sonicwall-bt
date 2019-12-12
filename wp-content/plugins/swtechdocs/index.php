<?php
/*
Plugin Name: SW Techdocs
Plugin URI: https://www.sonicwall.com
Description: Advanced Techdocs Override

*/
require 'vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

add_action( 'wp_loaded', function() {

if (class_exists('TablePress_Table_Model')) {
    
 
    //Create a new tablepress table modal class
   class TablePress_Table_Model_2 extends TablePress_Table_Model{
       
       //overload the load function to load custom data for the techdocs table
      public function load( $table_id, $load_data = true, $load_options_visibility = true ) {
      
        if ($table_id == "84")
        {
            if ( empty( $table_id ) ) {
			     return new WP_Error( 'table_load_empty_table_id' );
            }
            $post_id = $this->_get_post_id( $table_id );
            if ( false === $post_id ) {
                return new WP_Error( 'table_load_no_post_id_for_table_id', '', $table_id );
            }
            $post = $this->model_post->get( $post_id );
            if ( false === $post ) {
                return new WP_Error( 'table_load_no_post_for_post_id', '', $post_id );
            }
            $table = self::TD_Data_Override($this->_post_to_table( $post, $table_id, $load_data ));
            if ( $load_options_visibility ) {
                $table['options'] = $this->_get_table_options( $post_id );
                $table['visibility'] = $this->_get_table_visibility( $post_id );
            }

            return $table;
        }
        else
        {
            //Use the default TablePress classes if it's not the table we are interested in
            return parent::load($table_id, $load_data, $load_options_visibility);
        }
      }
       
      private function TD_Data_Override($table)
      {
          $header = array("Document Title", "Document Title", "Product", "Category", "Resources", "Language");
          
          //Create a global techdocs array that will be overwritten with the most desired doc type
          $techdocs = array();
                  
          //Glob through the PDFs for the base
          $pdf_glob = "techdocs/pdf/";
          $pdf_dirs = array_filter(glob($pdf_glob.'*'), 'is_dir');
          
          foreach ($pdf_dirs as $pdf)
          {
              $meta_file = $pdf."/meta.txt";
              
              if (file_exists($meta_file))
              {
                  $meta = $this->process_td_meta($meta_file);
                
                  $slug = explode($pdf_glob, $pdf)[1];
                  $title = $meta->title;
                  $product = $meta->product;
                  $category = $meta->category;
                  $resources = $meta->resources;
                  $language = $meta->language;
                  $beta = strtolower($meta->beta) == "yes";

                  if ($this->beta_check($beta))
                  {
                      $techdocs[$slug] = array(
                        '<a href ="https://techdocs.sonicwall.com/wp-content/uploads/pdf/'.$slug.'/">'.$title.'</a>',
                        '[swlightbox]<a href ="https://techdocs.sonicwall.com/wp-content/uploads/pdf/'.$slug.'.pdf">'.$title.'</a>[/swlightbox]',
                        $product,
                        $category,
                        $resources,
                        $language  
                      );      
                  }
              }              
          }
          
          //Now glob through the generated html docs
              
          $html_glob = "techdocs/html/";
          $html_dirs = array_filter(glob($html_glob.'*'), 'is_dir');
          
          foreach ($html_dirs as $html)
          {
              $meta_file = $html."/meta.txt";
              
              if (file_exists($meta_file))
              {
                  $meta = $this->process_td_meta($meta_file);
                
                  $slug = explode($html_glob, $html)[1];
                  $title = $meta->title;
                  $product = $meta->product;
                  $category = $meta->category;
                  $resources = $meta->resources;
                  $language = $meta->language;
                  $beta = strtolower($meta->beta) == "yes";
                  if ($this->beta_check($beta))
                  {
                      $techdocs[$slug] = array(
                        '<a href ="/techdocs/html/'.$slug.'/">'.$title.'</a>',
                        '[swlightbox]<a href="/techdocs/html/'.$slug.'">'.$title.'</a>[/swlightbox]',
                        $product,
                        $category,
                        $resources,
                        $language  
                      );   
                  }
              }              
          }
          
          //Now glob throught the flare docs
          
          $flare_glob = "techdocs/flare/";
  
          $flare_dirs = array_filter(glob($flare_glob.'*'), 'is_dir');

          foreach ($flare_dirs as $flare)
          {
              $meta_file = $flare."/Content/Resources/meta.txt";
              
              if (file_exists($meta_file))
              {
                  $meta = $this->process_td_meta($meta_file);
                                  
                  $slug = explode($flare_glob, $flare)[1];
                  $title = $meta->title;
                  $product = $meta->product;
                  $category = $meta->category;
                  $resources = $meta->resources;
                  $language = $meta->language;
                  $beta = strtolower($meta->beta) == "yes";
                  
                  if ($beta)
                  {
                    $title = $title." ".$beta;   
                  }
                  

                  if ($this->beta_check($beta))
                  {
                      $techdocs[$slug] = array(
                        '<a href ="/techdocs/flare/'.$slug.'/">'.$title.'</a>',
                        '[swlightbox]<a href="/techdocs/flare/'.$slug.'">'.$title.'</a>[/swlightbox]',
                        $product,
                        $category,
                        $resources,
                        $language  
                      );   
                  }
              }              
          }
          
          
          
          $table_data = array($header);
          
          foreach($techdocs as $techdoc)
          {
              $table_data[] = $techdoc;
          }
          
          
          $table["data"] = $table_data;
          
          return $table;
      }
       
       private function process_td_meta($meta_file)
       {
           $meta = new stdClass();
           
           if (file_exists($meta_file))
           {
               
              $contents = file_get_contents($meta_file);
               
              $lines = explode("\n", $contents);
               
              foreach($lines as $line)
              {
                  $parts = explode("=",$line);
                  
                  if (count($parts) == 2)
                  {
                      $key = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', strval(trim(strtolower($parts[0]))));
                      $meta->{$key} = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', strval(trim($parts[1])));
                  }
              }               
           }           
           return $meta;
       }
       
       private function beta_check($is_beta)
       {
           if ($is_beta)
           {
               //The document is a beta document, so make sure the session is set to beta login
               session_start();
               
               if ($_SESSION['beta_techdocs_login'])
               {
                   return true;
               }
               else
               {
                   return false;
               }
           }
           else
           {
               //Return true because it isn't a beta so it should always show
               return true;
           }
           
           return false;
       }

   }

    
   $techdocs_override = new TablePress_Table_Model_2();
   //Set the modal table variable to the override class 
   TablePress::$model_table = $techdocs_override;
}

});

//The WPML String translation package
function getWPMLPackage()
{
    return array(
        "kind" => "Techdocs",
        "name" => "techdocs",
        "title" => "Techdocs",
        "edit_link" => "",
        "view_link" => ""
    );
}

//The filter field map for friendly names to meta.txt categories
function getFilterFieldMap()
{
    return array(
        "category" => "Product Category",
        "product" => "Model",
        "resources" => "Document Type",
        "language" => "Language"
    );

}

//Techdocs 2.0 shortcode
add_shortcode('techdocs_2', 'do_techdocs');

function do_techdocs( $atts, $content, $tag)
{
    wp_register_script('sw-techdocs_js', plugins_url('js/sw-techdocs.js', __FILE__));
    wp_enqueue_script('sw-techdocs_js');
    wp_register_style('sw-techdocs_css', plugins_url('css/sw-techdocs.css',__FILE__ ));
    wp_enqueue_style('sw-techdocs_css');
    
  //Get the content
  $td_data = getTDData();
    
  $filter_lang_map = new stdClass();
    
  $filter_lang_map->category = td_translate_str('Product Category', 'UI-Product-Category');
  $filter_lang_map->product = td_translate_str('Model', 'UI-Model');
  $filter_lang_map->resources = td_translate_str('Document Type', 'UI-Document-Type');
  $filter_lang_map->language = td_translate_str('Language', 'UI-Language');    
    

    

  ob_start();
  //include the specified file
  include(plugin_dir_path(__FILE__).'views/techdocs-main.php');
  //assign the file output to $content variable and clean buffer
  $content = ob_get_clean();
  //return the $content
  //return is important for the output to appear at the correct position
  //in the content
  return $content;
}


function TD_get_wpml_lang()
{
    $lang = explode("-", ICL_LANGUAGE_NAME_EN);
    
    if (count($lang) > 1)
    {
        $lang = $lang[1];
    }
    else
    {
        $lang = ICL_LANGUAGE_NAME_EN;
    } 
    
    $lang2 = explode(" ", trim($lang));
    
    if (count($lang2) > 0)
    {
        $lang2 = trim($lang2[0]);
    }
    
    if (trim($lang2) == "Spanis")
    {
        $lang2 = "Spanish";
    }
    
    
    $lang2 = preg_replace("/[^A-Za-z0-9 ]/", '', $lang2);
    
    
    if ($lang2 == "Chinese")
    {
        $lang2 = "Chinese (Simplified)";
    }
    
    return $lang2;
}

function td_build_filter($type, $data, $display_value)
{
    $lang = TD_get_wpml_lang();
    //Build the categories
    $datab = array();
    
    foreach ($data as $td)
    {
        $splitter = explode(",", $td->{$type});
        
        foreach ($splitter as $t)
        {
            $t = trim($t);
            
            if ($t && strlen($t) > 0)
            {
                if (!$datab[$t])
                {
                    $datab[$t] = new stdClass();
                    $datab[$t]->title = $t;
                    $datab[$t]->total = 0;
                    $datab[$t]->type = $type;
                    $datab[$t]->site_language = trim($lang);
                }

                $datab[$t]->total = $datab[$t]->total + 1; 
            }   
        }
    }    
    
    usort($datab, function($a, $b) {

        if ($a->type == "language")
        {
           if ($a->title != $a->site_language && $b->title == $b->site_language) {
               return 1;
            } elseif ($a->title == $a->site_language && $b->title != $b->site_language) {
                return -1;
            } else {
                return strcmp($a->title, $b->title);
            }
        }
        else
        {
            return strcmp($a->title, $b->title);
        }
    });
    
    //Now build the markup
    $output = "";
    
    $i = 0;
    foreach($datab as $d)
    {
        $hide_class = '';
        
        $content = '';
        
        if ($i == 10)
        {
            $content = '<div class="td_filter_expand"></div>';
        }
        
        if ($i >= 10 && $type != 'language')
        {
            $hide_class = 'td_filter_hidden';
        }
        
        $hide_total = "";
        
        if ($type == "language")
        {
            $hide_total = "hide-total";
        }
                
        $content = $content.'
            <p class="sw-filter-value '.$hide_class.'" data-filter-value="'.$d->title.'" data-filter-type="'.$type.'">
              <label id="checkbox" class="sw-checkbox" style="width: 100%;">
                <input class="sw-checkbox-input td-filter-checkbox" type="checkbox" value="'.$d->title.'" data-value="'.$d->title.'" data-type="'.$type.'" data-display-value="'.$display_value.'">
                  <a class="sw-checkbox-value">'.swTDItalics($d->title).'</a>
                  <span class="sw-filter-count pull-right '.$hide_total.'">'.$d->total.'</span>
              </label>
            </p>
        ';
        
        $output = $output.$content;
        $i++;
    }
    
    return $output;
}

function getTDData()
{
    //TODO - Add cacheing
    $techdocs = array();
    $pdf_links = array();
                  
      //Glob through the PDFs for the base
      $pdf_glob = "techdocs/pdf/";
      $pdf_dirs = array_filter(glob($pdf_glob.'*'), 'is_dir');

      foreach ($pdf_dirs as $pdf)
      {
          $meta_file = $pdf."/meta.txt";

          if (file_exists($meta_file))
          {
              $meta = process_td_meta($meta_file);

              $d = new stdClass();
              
              $d->slug = explode($pdf_glob, $pdf)[1];
              $d->title = swTDItalics($meta->title);
              $d->product = $meta->product;
              $d->category = $meta->category;
              $d->resources = $meta->resources;
              $d->language = $meta->language;
              $d->beta = strtolower($meta->beta) == "yes";
              $d->file_type = "pdf";
              $d->url = "https://techdocs.sonicwall.com/wp-content/uploads/pdf/" . $d->slug . ".pdf";

              $pdf_links[$d->slug] = $d->url;
              
              if (beta_check($d->beta))
              {
                  $techdocs[$d->slug] = $d;
              }
          }              
      }

      //Now glob through the generated html docs

      $html_glob = "techdocs/html/";
      $html_dirs = array_filter(glob($html_glob.'*'), 'is_dir');

      foreach ($html_dirs as $html)
      {
          $meta_file = $html."/meta.txt";

          if (file_exists($meta_file))
          {
              $meta = process_td_meta($meta_file);

              $d = new stdClass();
              
              $d->slug = explode($html_glob, $html)[1];
              $d->title = swTDItalics($meta->title);
              $d->product = $meta->product;
              $d->category = $meta->category;
              $d->resources = $meta->resources;
              $d->language = $meta->language;
              $d->beta = strtolower($meta->beta) == "yes";
              $d->file_type = "html";
              $d->url = "/techdocs/" . $d->file_type . "/" . $d->slug;
              $d->pdf = $pdf_links[$d->slug];
              
              if (beta_check($d->beta))
              {
                  $techdocs[$d->slug] = $d;
              }
          }              
      }

      //Now glob throught the flare docs

      $flare_glob = "techdocs/flare/";

      $flare_dirs = array_filter(glob($flare_glob.'*'), 'is_dir');

      foreach ($flare_dirs as $flare)
      {
          $meta_file = $flare."/Content/Resources/meta.txt";

          if (file_exists($meta_file))
          {
              $meta = process_td_meta($meta_file);

              $d = new stdClass();
              
              $d->slug = explode($flare_glob, $flare)[1];
              $d->title = swTDItalics($meta->title);
              $d->product = $meta->product;
              $d->category = $meta->category;
              $d->resources = $meta->resources;
              $d->language = $meta->language;
              $d->beta = strtolower($meta->beta) == "yes";
              $d->file_type = "flare";
              $d->url = "/techdocs/" . $d->file_type . "/" . $d->slug;
              $d->pdf = $pdf_links[$d->slug];
              
              if (beta_check($d->beta))
              {
                  $techdocs[$d->slug] = $d;
              }
          }              
      }

    return bumpOrder(td_localize($techdocs));
}

function td_localize($techdocs) {

    foreach($techdocs as &$techdoc)
    {
        $filter_field_map = getFilterFieldMap();
          
          foreach ($filter_field_map as $field => $friendly_name)
          {
             //Register the field string in WPML
              $pieces = explode(",", $techdoc->{$field});
              $p_holder = array();
              foreach ($pieces as $piece)
              {
                  $piece = trim($piece);
                  $string_value = $piece;
                  $string_name = $friendly_name."-".$piece;
                  $p_holder[] = td_translate_str($string_value, $string_name);
              }
              
              $techdoc->{$field} = implode(",", $p_holder);
          }        
    }
    
    return $techdocs;
}

function td_translate_str($string_value, $string_name)
{
    $str = apply_filters( 'wpml_translate_string', $string_value, $string_name, getWPMLPackage() );
    
    return $str;
}

function swTDItalics($str)
{
    //NSa
    $str = str_replace("NSa", "NS<i class='td-italics'>a</i>", $str);
    
    //NSa
    $str = str_replace("NSsp", "NS<i class='td-italics'>sp</i>", $str);
    
    //NSa
    $str = str_replace("NSv", "NS<i class='td-italics'>v</i>", $str);
    
    return $str;
}

function bumpOrder($techdocs)
{
    $bumpfile = plugin_dir_path(__FILE__).'bump_list.txt';
    
    if (file_exists($bumpfile))
    {
        $bump = file_get_contents($bumpfile);
        
        if ($bump)
        {
            $bump = explode("\n", $bump);
            
            if ($bump && count($bump) > 0)
            {
                $ordered_techdocs = array();
                $techdocs_copy = $techdocs;
                
                foreach ($bump as $key)
                {
                    if (array_key_exists($key,$techdocs))
                    {
                        $ordered_techdocs[$key] = $techdocs[$key];
                        unset($techdocs_copy[$key]);
                    }
                }
                
                $sorted = array_merge($ordered_techdocs, $techdocs_copy);
                return $sorted;
            }
        }
    }
    return $techdocs;
}

function process_td_meta($meta_file)
{
   $meta = new stdClass();

   if (file_exists($meta_file))
   {

      $contents = file_get_contents($meta_file);

      $lines = explode("\n", $contents);

      foreach($lines as $line)
      {
          $parts = explode("=",$line);

          if (count($parts) == 2)
          {
              $key = strval(trim(strtolower($parts[0])));//preg_replace('/[\x00-\x1F\x80-\xFF]/', '', strval(trim(strtolower($parts[0]))));
              $meta->{$key} = strval(trim($parts[1]));// preg_replace('/[\x00-\x1F\x80-\xFF]/', '', strval(trim($parts[1])));
          }
      }               
   }           
   return $meta;
}

function beta_check($is_beta)
{
   if ($is_beta)
   {
       //The document is a beta document, so make sure the session is set to beta login
       session_start();

       if ($_SESSION['beta_techdocs_login'])
       {
           return true;
       }
       else
       {
           return false;
       }
   }
   else
   {
       //Return true because it isn't a beta so it should always show
       return true;
   }

   return false;
}

//Beta login shortcode
add_shortcode( 'techdocs_beta_login', 'techdocs_beta_login' );
add_action("wp_ajax_techdocs_beta_login", "techdocs_beta_login_ajax");
add_action("wp_ajax_nopriv_techdocs_beta_login", "techdocs_beta_login_ajax");

function techdocs_beta_login( $atts, $content, $tag ){
    
    session_start();
    
    if ($_SESSION['beta_techdocs_login'])
    {
      $html = file_get_contents(plugin_dir_path(__FILE__).'views/beta-logout.html');
    }
    else
    {
      $html = file_get_contents(plugin_dir_path(__FILE__).'views/beta-login.html');  
    }
        
    return $html;
}

function techdocs_beta_login_ajax() {
    
    session_start();
    
    if ($_SESSION['beta_techdocs_login'])
    {
        $_SESSION['beta_techdocs_login'] = false;   
    }
    else
    {
        $_SESSION['beta_techdocs_login'] = true;
    }
    return true;
}



if ( class_exists( 'WP_CLI' ) ) {
    
class cli_techdocs extends WP_CLI_Command {
    
   /**
   * Import PDFs from CSV
   */
  function register_wpml_strings($args) {
      
      $techdocs = getTDData();
        
      foreach ($techdocs as $techdoc)
      {
          
          $filter_field_map = getFilterFieldMap();
          
          foreach ($filter_field_map as $field => $friendly_name)
          {
             //Register the field string in WPML
              $pieces = explode(",", $techdoc->{$field});

              foreach ($pieces as $piece)
              {
                  $piece = trim($piece);
                  $string_value = $piece;
                  $string_name = $friendly_name."-".$piece;
                  $string_title = $friendly_name."-".$piece;
                  do_action( 'wpml_register_string', $string_value, $string_name, getWPMLPackage(), $string_title, "LINE" );
              }
          }
      }
      
      
      //Register the UI Elements
      $ui_elements = array(
        "Filter Results" => "UI",
        "Product Category" => "UI",
        "Model" => "UI",
        "Document Type" => "UI",
        "Language" => "UI",
        "Search Results" => "UI",
        "Results" => "UI",
        "of" => "UI",
        "Previous" => "UI",
        "Next" => "UI",
        "Search documents" => "UI",
        "No results for" => "UI",
        "Check the spelling of your keywords" => "UI",
        "Try using fewer, different or more general keywords" => "UI"
      );
      
      foreach ($ui_elements as $str => $context)
      {
        $string_value = $str;
        $string_name = $context."-".$str;
        $string_title = $context."-".$str;
        do_action( 'wpml_register_string', $string_value, $string_name, getWPMLPackage(), $string_title, "LINE" );   
      }
      
      
      
      WP_CLI::line( 'WPML string registration complete.' ); 
  }
      
  /**
   * Import PDFs from CSV
   */
  function import_pdf($args) {
    $csv_file = plugin_dir_path(__FILE__).'output.csv';
    $data = array_map('str_getcsv', file($csv_file));
      
    $docs = new stdClass();
      
      
    //Ray Map
//    $map = array(
//        "title" => 0,
//        "link" => 1,
//        "category" => 4,
//        "product" => 9,
//        "resources" => 15,
//        "language" => 23,
//        "description" => 7
//    );
      
    //Terri Map
    $map = array(
        "title" => 0,
        "link" => 1,
        "category" => 6,
        "product" => 7,
        "resources" => 2,
        "language" => 3,
        "description" => 8
    );
      
    foreach($data as $doc)
    {
      $import = new stdClass();
      $import->title = $doc[$map["title"]];
      $import->link = $doc[$map["link"]];
        
      //Product Category
      $import->category = $doc[$map["category"]];
        
      //Model
      $import->product = $doc[$map["product"]];
        
      //Document Type
      $import->resources = $doc[$map["resources"]];
    
      //Language
      $import->language = $doc[$map["language"]];
        
      $import->description = $doc[$map["description"]];
        
      //Get the slug
      $import->slug = basename($import->link, '.pdf');
        
      //Now that we made an object, lets merge them
      if (!$docs->{$import->slug})
      {
          $docs->{$import->slug} = new stdClass();
          $docs->{$import->slug}->category = array();
          $docs->{$import->slug}->product = array();
          $docs->{$import->slug}->resources = array();
      }
        
      $docs->{$import->slug}->title = $import->title;
      $docs->{$import->slug}->link = $import->link;
      $docs->{$import->slug}->slug = $import->slug;
      $docs->{$import->slug}->category[] = $import->category;
      $docs->{$import->slug}->product[] = $import->product;
      $docs->{$import->slug}->resources[] = $import->resources;
      $docs->{$import->slug}->language = $import->language;
      $docs->{$import->slug}->description = $import->description;      
    }
      
              
    //Now loop through all the docs, create the directory and download the files
    foreach ($docs as $doc)
    {
        $dir = ABSPATH."techdocs/pdf/".$doc->slug;
        mkdir($dir);
        
        $meta_content = "Title=".$doc->title."
Product=".implode(",", $doc->product)."
Category=".implode(",", $doc->category)."
Resources=".implode(",", $doc->resources)."
Language=".$doc->language."";
        
        $meta_file = $dir."/meta.txt";
        
        file_put_contents($meta_file, $meta_content);
        
        //Skip downloading PDFs while we are hosting on techdocs.
//        $pdf_file = $dir."/".$doc->slug.".pdf";
//        file_put_contents($pdf_file, file_get_contents($doc->link));
    }
  }
      
  /**
   * Sync techdocs data from S3
   */
  function sync($args) {
            
      if (count($args) >= 5)
      {
         $branch = $args[0];
         $bucket = $args[1];
         $region = $args[2];
         $aws_access_key_id = $args[3];
         $aws_secret_access_key = $args[4];
          
          $s3 = new Aws\S3\S3Client([
                'version' => 'latest',
                'region' => $region,
                'credentials' => array(
                    'key' => $aws_access_key_id,
                    'secret' => $aws_secret_access_key
                )
          ]);
          
          $directory = $branch;
          
          //Find out which directory to use a staging directory
          
          //Temporary storage directory for seamless switching between jobs
                    
          $current_dir = readlink(ABSPATH."techdocs/flare");
          
          $directories = array_values(array_diff(array("/td/a/", "/td/b/"), array($current_dir)));
          
          if (count($directories) <= 0)
          {
              WP_CLI::line( 'Error: not enough techdoc storage directories specified' ); 
              exit();
          }
          
          $basePath = $directories[0];

          //Make the tmp directory if it doesn't exist
          if (!file_exists($basePath))
          {
            mkdir($basePath);  
          }
          
          //sync the entire bucket (only changed files)
          $s3->downloadBucket($basePath . $directory, $bucket, $directory);
          
          //Remove any deleted files from s3
          $objects = $s3->getIterator('ListObjects', array(
                "Bucket" => $bucket,
                "Prefix" => $branch."/"
            )); 
          
          
          //List everything in the bucket to compare to local
          $s3Files = array();
          foreach ($objects as $object) {
            $s3Files[] = $object['Key'];
          }
          
          //Get a list of local files to see what we need to delete
          $localFiles = getDirContents($basePath.$branch, $results = array(), $basePath);
          
          $deleteList = array_diff($localFiles, $s3Files);
          
          foreach ($deleteList as $fileToDelete)
          {
              $file = $basePath.$fileToDelete;
              
              if (file_exists($file))
              {
                  unlink($file);
              }
          }

          //Now copy in the directory payload data
          $payload_directory = plugin_dir_path(__FILE__).'flare_payload';
          $payload_files = glob($payload_directory.'/{,.}[!.,!..]*', GLOB_MARK|GLOB_BRACE);
          
          foreach($payload_files as $payload)
          {
              if (file_exists($payload))
              {
                  $filename = basename($payload);
                  $dest = $basePath.$branch."/".$filename;
                  copy($payload, $dest);
              }
          }
          
          
          //Now switch the symlink to make all the changes live
          if (is_link(ABSPATH."techdocs/flare"))
          {
              unlink(ABSPATH."techdocs/flare");
              symlink($basePath.$branch, ABSPATH."techdocs/flare");
              WP_CLI::line( 'Finished: '.$branch ); 
          }
          else
          {
              WP_CLI::line( 'Error: flare folder is not a symlink!' ); 
              exit();
          }
          
      }
      else
      {
         switch (count($args)) {
            case 0:
                WP_CLI::line( 'Error: you must specify a branch to sync' ); 
                break;
            case 1:
                WP_CLI::line( 'Error: you must specify an S3 bucket to sync' ); 
                break;
            case 2:
                WP_CLI::line( 'Error: you must specify an AWS region to sync' ); 
                break;
            case 3:
                WP_CLI::line( 'Error: you must specify an AWS Access Key ID to sync' ); 
                break;
            case 4:
                WP_CLI::line( 'Error: you must specify an AWS Secret Access Key to sync' ); 
                break;
            default:
                WP_CLI::line( 'Error: unknown' ); 
         }
          
          
        exit();
          
      }
     
  } 
}

    WP_CLI::add_command( 'techdocs', 'cli_techdocs' );
}


function getDirContents($dir, &$results = array(), $strip_path){
    $files = scandir($dir);

    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if(!is_dir($path)) {
            if (substr($path, 0, strlen($strip_path)) == $strip_path) {
                $path = substr($path, strlen($strip_path));
            } 
            $results[] = $path;
        } else if($value != "." && $value != "..") {
            //Recurse through the directory
            getDirContents($path, $results, $strip_path);
        }
    }

    return $results;
}