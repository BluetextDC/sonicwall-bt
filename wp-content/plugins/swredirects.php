<?php
/*
Plugin Name: SW Redirects
Plugin URI: https://www.sonicwall.com
Description: Advanced Redirects
Version: 0.1.0
Author: Brad Kendall*/
add_filter('w3tc_minify_urls_for_minification_to_minify_filename', 'w3tc_filename_filter', 20, 3);
function w3tc_filename_filter($minify_filename, $files, $type ){
    
    $version = "1.9";
    
    $ver = sanitize_title( str_replace('.','', $version) );
    
    $minify_filename = $ver.$minify_filename;
    
    return $minify_filename;
    
}

add_action( 'setup_theme', 'redirect_override' );

function redirect_override() {  

    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    $query = "/support/technical-documentation/";
    if (substr($path, 0, strlen($query)) === $query && substr($_SERVER["REQUEST_URI"], -strlen(".pdf")) != ".pdf")
    {
        $parts = explode("/", trim($path, "/"));
        
        if (count($parts) > 2)
        {
            $parts = array_slice($parts, 0, 3);
            
            if (endsWith($parts[2], "-(1)"))
            {
                $parts[2] = replaceEnd($parts[2], "-(1)");                
            }
            
            $url = "/".implode("/", $parts).".pdf";
            
            if (wp_redirect($url))
            {
                exit();
            }
        }
    }

    
    $query = "/support/knowledge-base/";
    
if (substr($path, 0, strlen($query)) === $query)
    {
        $parts = explode("/", trim($path, "/"));
        
        if (count($parts) == 3 && $parts[0] == "support" && $parts[1] == "knowledge-base" && is_numeric($parts[2]))
        {
            $url = "/support/knowledge-base/?sol_id=".$parts[2];
            
            if (wp_redirect($url))
            {
                exit();
            }
        }
    }

    if ($path == "/lp/2019-cyber-threat-report/")
    {
        $qs = $_SERVER['QUERY_STRING'];
        $url = "https://www.sonicwall.com/lp/2019-cyber-threat-report-lp/?".$qs;
        if (wp_redirect($url))
        {
            exit();
        }
    }
 
    
    $redirects = array(
        "/communication-preferences",
        "/en-us/communication-preferences",
        "/fr-fr/communication-preferences",
        "/de-de/communication-preferences",
        "/pt-br/communication-preferences",
        "/es-mx/communication-preferences",
        "/zh-cn/communication-preferences",
        "/ko-kr/communication-preferences"
    );
    
    foreach ($redirects as $redirect)
    {
        if (substr($path, 0, strlen($redirect)) === $redirect)
        {
            $qs = $_SERVER['QUERY_STRING'];
            
            //Check if there are two slashes
            if (substr_count($redirect, "/") > 1 && strpos($redirect, 'en-us') == false)
            {
                $lang = false;
                if (strpos($redirect, 'zh-cn') !== false || strpos($redirect, 'ko-kr') !== false )
                {
                    $lang = explode("/", explode("-", $redirect)[1])[0];
                }
                else
                {
                    $lang = explode("/", explode("-", $redirect)[0])[1];
                }
                
                $url = "https://message.sonicwall.com/{$lang}-communication-preferences?".$qs;
            }
            else
            {
                //English
                $url = "https://message.sonicwall.com/communication-preferences?".$qs;
            }
            
            if (wp_redirect($url))
            {
                exit();
            }
        }
    }
}


function replaceEnd($haystack, $replace)
{
    if (substr($haystack,-strlen($replace))===$replace) $haystack = substr($haystack, 0, strlen($haystack)-strlen($replace));
    
    return $haystack;
}

function startsWith($haystack, $needle)
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}
