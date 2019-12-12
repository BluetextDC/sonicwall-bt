<?php

$req_path = $_REQUEST['path'];
$path = __DIR__."/".$req_path."/index.html";

$meta_file = __DIR__."/".$req_path."/meta";

$meta = process_td_meta($meta_file);

if (file_exists($path))
{
	$content = file_get_contents($path);

	$content = str_replace_first('</head>', '
    <style>.pdf-page {
		display: none;
	}</style>
    <script src="/techdocs/html/sw.js"></script>
    <link href="/techdocs/html/sw.css" rel="stylesheet" type="text/css">
    <meta name="description" content="'.$meta->description.'">
    </head>', $content); 

	$content = str_replace_first('<body>', '<body><div id="book_container">', $content);
	$content = str_replace_first('</body>', '</div><div id="toc_container"></div></body>', $content);


	echo $content;
	exit();	
}
else
{
	exit("404");
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