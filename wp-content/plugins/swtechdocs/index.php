<?php
/*
Plugin Name: SW Techdocs
Plugin URI: https://www.sonicwall.com
Description: Advanced Techdocs Override

*/




require 'vendor/autoload.php';
require 'sw_td_data.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

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
    
    $sw_td_data = new SW_TD_Data();
    $td_data = $sw_td_data->getTDData();
    
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
                  <span class="pull-right td-filter-addition">+</span>
              </label>
            </p>
        ';
        
        $output = $output.$content;
        $i++;
    }
    
    return $output;
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


function set_pdf_meta($pdf_file, $techdoc)
{
    $pdf_title = $techdoc->title;
    $pdf_description = $techdoc->description;
    
    $cmd = "exiftool -Title=\"{$pdf_title}\" -Subject=\"{$pdf_description}\" {$pdf_file}";
    
    $output=shell_exec($cmd.' 2>&1');
    
    //Remove backup file
    if (file_exists($pdf_file."_original"))
    {
        unlink($pdf_file."_original");
    }
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
   * Register WPML strings for translation of dynamic fields
   */
  function register_wpml_strings($args) {
      
      $sw_td_data = new SW_TD_Data();
      $techdocs = $sw_td_data->getTDData();
        
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
   * Fix missing titles bug
   */
  function fix_titles($args) {
      
      $sw_td_data = new SW_TD_Data();
      $techdocs = $sw_td_data->getTDData();
      
      //Get all the metadata from the CSVs
      
      $csv_files = array(
        "output-zh.csv",
        "output-tw.csv",
        "output-pt.csv",
        "output-master.csv",
        "output-kr.csv",
        "output-jp.csv"
      );
      
      $docs = new stdClass();
      
      foreach($csv_files as $csv_file)
      {
            $csv_file = plugin_dir_path(__FILE__).$csv_file;
            $data = array_map('str_getcsv', file($csv_file));

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

      }
      

      foreach ($techdocs as $techdoc)
      {
          if ($techdoc && (!$techdoc->title || strlen($techdoc->title) <= 0))
          {
               $doc = $docs->{$techdoc->slug};
               $meta_content = "Title=".$doc->title."
Product=".$techdoc->product."
Category=".$techdoc->category."
Resources=".$techdoc->resources."
Language=".$techdoc->language."
Description=".$techdoc->description;
              
            $dir = ABSPATH."techdocs/pdf/".$techdoc->slug;
            $meta_file = $dir."/meta.txt";
         
           
            file_put_contents($meta_file, $meta_content); 
              
          }
      }
      
      
      WP_CLI::line( 'Title Fix complete' ); 
  }
    
    
   /**
   * Download PDF files from techdocs.sonicwall.com and process meta data
   */
  function download_pdf($args) {
      
      $sw_td_data = new SW_TD_Data();
      $techdocs = $sw_td_data->getTDData();
      
      //Get all the metadata from the CSVs
      
      $csv_files = array(
        "output-zh.csv",
        "output-tw.csv",
        "output-pt.csv",
        "output-master.csv",
        "output-kr.csv",
        "output-jp.csv"
      );
      
      $docs = new stdClass();
      
      foreach($csv_files as $csv_file)
      {
            $csv_file = plugin_dir_path(__FILE__).$csv_file;
            $data = array_map('str_getcsv', file($csv_file));

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

      }
      

      foreach ($techdocs as $techdoc)
      {
          //Add description to meta.txt
          if ($docs->{$techdoc->slug} && $docs->{$techdoc->slug}->description && strlen($docs->{$techdoc->slug}->description) > 0)
          {
              
              $doc = $docs->{$techdoc->slug};
              $meta_content = "Title=".$techdoc->title."
Product=".$techdoc->product."
Category=".$techdoc->category."
Resources=".$techdoc->resources."
Language=".$techdoc->language."
Description=".$doc->description;
        
            $dir = ABSPATH."techdocs/pdf/".$techdoc->slug;
            $meta_file = $dir."/meta.txt";
         
           
            file_put_contents($meta_file, $meta_content); 
          }
          
          
          if ($techdoc->file_type === "pdf")
          {
              //Download the PDF and add the meta
                      

              $pdf_file = $dir."/".$techdoc->slug.".pdf";
              
              //Force the td server URL
              $td_server_url = "https://techdocs.sonicwall.com/wp-content/uploads/pdf/" . $techdoc->slug . ".pdf";
              
              file_put_contents($pdf_file, file_get_contents($td_server_url));
              
              set_pdf_meta($pdf_file, $techdoc);
          }
          
      }
      
      
      WP_CLI::line( 'PDF download complete.' ); 
  }
  
    
  /**
   * Import PDFs from CSV
   */
  function import_pdf($args) {
    $csv_file = plugin_dir_path(__FILE__).'output.csv';
    $data = array_map('str_getcsv', file($csv_file));
      
    $docs = new stdClass();
      
      
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
          
          $dirs = array("/td/".$branch."/a/", "/td/".$branch."/b/");
          
          //Check for the main dir
          if (!file_exists("/td"))
          {
              mkdir("/td");
          }
          //Create the branch dir
          if (!file_exists("/td/".$branch))
          {
              mkdir("/td/".$branch);
          }
          
          //Create the directories if they don't exist
          foreach($dirs as $dir)
          {
              if (!file_exists($dir))
              {
                  mkdir($dir);
              }
          }
          
          $directory = $branch;
          
          //Find out which directory to use a staging directory
          
          //Temporary storage directory for seamless switching between jobs
          
          $current_dir = false;
          
          if (file_exists(ABSPATH."techdocs"))
          {
             $current_dir = readlink(ABSPATH."techdocs");   
          }
         
          $directories = array_values(array_diff($dirs, array($current_dir)));
          
          if (count($directories) <= 0)
          {
              WP_CLI::line( 'Error: not enough techdoc storage directories specified' ); 
              exit();
          }
          
          $basePath = $directories[0];

          //sync the entire bucket (only changed files)
          $s3->downloadBucket($basePath . $directory, $bucket, $directory);
          
          //Remove any deleted files from s3
          $objects = $s3->getIterator('ListObjects', array(
                "Bucket" => $bucket,
                "Prefix" => $branch."/"
            )); 
          
          
          //List everything in the bucket to compare to local
          $s3Files = array();
          //Need to store last modified for sitemaps
          $last_mod = array();
          
          foreach ($objects as $object) {
              
              
          //Check for the filetype and slug    
          $s = explode("/", $object['Key']);
          if ($s && count($s) >= 3)
          {
                $mod = new stdClass();
                $mod->modified =  strtotime($object['LastModified']);
                $mod->type = $s[1];
                $mod->slug = $s[2];

                //Set the modified if it doesn't exist or if our time is greater
                if (!isset($last_mod[$mod->slug]))
                {
                   $last_mod[$mod->slug] = $mod; 
                }
                else if ($mod->modified > $last_mod[$mod->slug]->modified)
                {
                    $last_mod[$mod->slug] = $mod;
                }
                
          }
              
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
                  $dest = $basePath.$branch."/html/".$filename;
                  
                  //Make the dir
                  if (!file_exists($basePath.$branch."/html/"))
                  {
                      mkdir($basePath.$branch."/html/");
                  }
                  
                  copy($payload, $dest);
              }
          }
          
          
          //Fix url encoded filenames
          glob_dir($basePath.$branch);
          
          
          //Now add the last_mod file to each meta.txt
          foreach($last_mod as $mod)
          {
              $meta_file = $basePath.$branch."/".$mod->type."/".$mod->slug."/meta.txt";
              
              if ($mod->type == "html")
              {
                  //Change the path of meta.txt
                  $meta_file = $basePath.$branch."/".$mod->type."/".$mod->slug."/Content/Resources/meta.txt";
              }
              
              $last_mod_line = "\nModified=".$mod->modified."\n";
              
              if (file_exists($meta_file)) {
                  $fh = fopen($meta_file, 'a');
                  fwrite($fh, $last_mod_line);
              } else {
                  $fh = fopen($meta_file, 'w');
                  fwrite($fh, $last_mod_line);
              }
              fclose($fh);
          }

          //Now switch the symlink to make all the changes live
          if (is_link(ABSPATH."techdocs"))
          {
              unlink(ABSPATH."techdocs");
          }
          
          symlink($basePath.$branch, ABSPATH."techdocs");
          WP_CLI::line( 'Finished: '.$branch ); 
          
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

    function glob_dir($dir)
    {
       foreach (glob($dir."/*") as $filename) {

           $decoded_filename = urldecode($filename);

           if ($filename != $decoded_filename)
           {
               echo "Change: ".$filename." to: ".$decoded_filename.PHP_EOL;
               rename($filename, $decoded_filename);
               $filename = $decoded_filename;
           }

           if (is_dir($filename))
           {
               //Recurse
               glob_dir($filename);
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