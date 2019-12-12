<?php

require_once('CJSON.php');

$req_path = $_REQUEST['path'];

$root_path = explode("/", $req_path)[0];


$toc_roots = glob($root_path."/Data/Tocs/*.js");
$toc_chunks = glob($root_path."/Data/Tocs/*_Chunk*.js");

$toc_root_file = array_diff($toc_roots, $toc_chunks)[0];

$toc_map = file_get_contents($toc_root_file);

$chunk_map = new stdClass();

foreach($toc_chunks as $chunk)
{
    $chunk = file_get_contents($chunk);
    
    $chunk = get_string_between($chunk, 'define(', ');');
    
    $chunk = CJSON::decode($chunk, false);
    
    foreach($chunk as $url => $c)
    {
        $index = $c->i[0];
        $title = $c->t[0];
      
        $link = new stdClass();
        $link->title = $title;
        $link->url = $url;
        
        $chunk_map->{$index} = $link;
    }
}

$map = get_string_between($toc_map, 'define(', ');');

$map = CJSON::decode($map, false);

$first_link = $map->chunkstart[0];

$first_link = "/techdocs/flare/".$root_path.$first_link;

$tree = $map->tree->n;

$toc_html = "<ul>";
$d = false;

foreach($tree as $t)
{
   $d = process_toc_level($t, $chunk_map);
   $toc_html .= $d->html;
}

$toc_html .="</ul>";

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

$path = $_REQUEST['path'];

$meta_file = $root_path."/Content/Resources/meta.txt";

$meta = process_td_meta($meta_file);

//Check if a PDF exists
$slug = $root_path;
$pdf_link = "/techdocs/pdf/".$slug."/".$slug.".pdf";
$pdf_path = $_SERVER['DOCUMENT_ROOT'].$pdf_link;

$pdf = false;

if (file_exists($pdf_path))
{
    $pdf = $pdf_link;
}


if (file_exists($path))
{
	$content = file_get_contents($path);
    
    if ($content && strlen($content) > 0)
    {
        $content = str_replace_first('</head>', '
        <script src="/techdocs/flare/sw.js"></script>
        <link href="/techdocs/flare/sw.css" rel="stylesheet" type="text/css">
        <meta name="description" content="'.$meta->description.'">
        </head>', $content); 

        $content = str_replace_first('<body>', '<body><div id="book_container">', $content);
        $content = str_replace_first('</body>', '<div id="next_prev_links"><a href="/techdocs/flare/'.$GLOBALS['previous_link'].'">Previous Section</a><a href="/techdocs/flare/'.$GLOBALS['next_link'].'">Next Section</a></div></div><div id="toc_container"><h2>Contents:</h2>'.$toc_html.'</div><script>var pdf = "'.$pdf.'";</script></body>', $content);

        echo $content;
        exit();	   
    }
    else
    {
        //No content, try to load the first doc from the TOC
        header("Location: ".$first_link);
    }
}
else
{
	exit("404");
}



function process_toc_level($toc, $chunk_map)
{
    $ret = "";
    
    $req_path = $_REQUEST['path'];

    $root_path = explode("/", $req_path)[0];
    
    $chunk = $chunk_map->{$toc->i};
    
    $link = $root_path."".$chunk->url;
    
    //Set the first link
    if (!$GLOBALS['last_link'])
    {
        $GLOBALS['last_link'] = $link;
    }
     
    if ($GLOBALS['hit_next'])
    {
        $GLOBALS['next_link'] = $link;
        $GLOBALS['hit_next'] = false;
    }
    
    $active = "";
    $toggle_on_open = "";
    
    $current_active = false;
    
    if ($link == $req_path)
    {
        $active = "active";
        $toggle_on_open = "toggle-on-open";
        
        $show_next = true;
        $GLOBALS['previous_link'] = $GLOBALS['last_link'];
        $GLOBALS['hit_next'] = true;
        
        $current_active = true;
    }
   
    
    $has_sub_level = isset($toc->n) && count($toc->n) > 0;
    
    $sub_nav = "";
    
    if ($has_sub_level)
    {
        $sub_nav_toggle = '<a class="toc-collapse-toggle"></a>';    
    }
    
    $ret .= '<li class="'.$toggle_on_open.'">'.$sub_nav_toggle.'<a href="/techdocs/flare/'.$link.'" class="'.$active.'">'.$chunk->title.'</a>';

    if ($has_sub_level)
    {
        $ret .= '<ul>';
        $d = false;
            
        foreach ($toc->n as $t)
        {   
           $d = process_toc_level($t, $chunk_map);
           $GLOBALS['last_link'] = $d->link;
           $ret .= $d->html;
        }
        $ret .= '</ul>';
    }
    else
    {
        $GLOBALS['last_link'] = $link;
    }
    
    
    
    $data = new stdClass();
    $data->html = $ret;
    $data->link = $link;

    return $data;
}

function page_title($html) 
{      
        $res = preg_match("/<title>(.*)<\/title>/siU", $html, $title_matches);
        if (!$res) 
            return null; 
        // Clean up title: remove EOL's and excessive whitespace.
        $title = preg_replace('/\s+/', ' ', $title_matches[1]);
        $title = trim($title);
        return $title;
}

function str_replace_first($from, $to, $content)
{
    $from = '/'.preg_quote($from, '/').'/';
    return preg_replace($from, $to, $content, 1);
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
              $key = strtolower($parts[0]);
              $meta->{$key} = $parts[1];
          }
      }               
   }           
   return $meta;
}

?>