<?php

class SW_TD_Data {


   function __construct()
   {

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
                  $meta = $this->process_td_meta($meta_file);

                  $d = new stdClass();

                  $d->slug = explode($pdf_glob, $pdf)[1];
                  $d->title = swTDItalics($meta->title);
                  $d->product = $meta->product;
                  $d->category = $meta->category;
                  $d->resources = $meta->resources;
                  $d->language = $meta->language;
                  $d->beta = isset($meta->beta) && strtolower($meta->beta) == "yes";
                  $d->description = isset($meta->description) ? $meta->description : null;
                  $d->file_type = "pdf";
                  $d->modified = isset($meta->modified) ? $meta->modified : time();
                  
                  if (file_exists($pdf."/".$d->slug.".pdf"))
                  {
                      $d->url = "/techdocs/pdf/" . $d->slug . ".pdf";
                  }
                  else
                  {
                      //Link it to techdocs as it doesn't exist
                      $d->url = "https://techdocs.sonicwall.com/wp-content/uploads/pdf/" . $d->slug . ".pdf";
                  }

                  $pdf_links[$d->slug] = $d->url;

                  if (beta_check($d->beta))
                  {
                      $techdocs[$d->slug] = $d;
                  }
              }              
          }


          //Now glob throught the flare (html) docs

          $flare_glob = "techdocs/html/";

          $flare_dirs = array_filter(glob($flare_glob.'*'), 'is_dir');

          foreach ($flare_dirs as $flare)
          {
              $meta_file = $flare."/Content/Resources/meta.txt";

              if (file_exists($meta_file))
              {
                  $meta = $this->process_td_meta($meta_file);

                  $d = new stdClass();

                  $d->slug = explode($flare_glob, $flare)[1];
                  $d->title = swTDItalics($meta->title);
                  $d->product = $meta->product;
                  $d->category = $meta->category;
                  $d->resources = $meta->resources;
                  $d->language = $meta->language;
                  $d->beta = strtolower($meta->beta) == "yes";
                  $d->file_type = "html";
                  $d->url = "/support/technical-documentation/docs/". $d->slug;
                  $d->pdf = $pdf_links[$d->slug];
                  $d->modified = isset($meta->modified) ? $meta->modified : time();

                  if (beta_check($d->beta))
                  {
                      $techdocs[$d->slug] = $d;
                  }
              }              
          }

        $techdocs = bumpOrder(td_localize($techdocs));

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
                  $key = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', strval(trim(strtolower($parts[0]))));//strval(trim(strtolower($parts[0])));
                  $value = mb_convert_encoding(strval(trim($parts[1])), 'UTF-8', 'UTF-8');

                  if ($value && strtolower($value) != "null")
                  {
                    $meta->{$key} = $value;  
                  }

              }
          }               
       }  

        //Product = category
        //Model = product
        //Document Type = resources

        //Override with new fields if they exist (v2)

        //Model
        if ($meta && isset($meta->document) && strlen($meta->document) > 0 && $meta && isset($meta->model) && strlen($meta->model) > 0 && isset($meta->product) && strlen($meta->product) > 0)
        {
          $meta->category = $meta->product;
          $meta->product = $meta->model;
          $meta->resources = $meta->document;
        }

        //Override with even new meta.txt fields if they exist (v3)

        if ($meta && isset($meta->{"product category"}))
        {
            $meta->category = $meta->{"product category"};
            $meta->product = $meta->model;
            $meta->resources = $meta->{"document type"};

            $meta->firmware_version = $meta->{"firmware version"};
            $meta->software_version = $meta->{"software version"};

            //Unset the old fields
            unset($meta->{"product category"});
            unset($meta->{"document type"});
            unset($meta->{"firmware version"});
            unset($meta->{"software version"});
        }

       return $meta;
    }

}
