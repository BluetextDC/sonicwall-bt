<?php
/*
Plugin Name: SW Redirects
Plugin URI: https://www.sonicwall.com
Description: Advanced Redirects
Version: 0.1.0
Author: Brad Kendall*/

//Add minimized bootstrap css for styling
add_action('wp_enqueue_scripts', 'sw_load_bootstrap');
function sw_load_bootstrap() {
    wp_register_style('sw_bootstrap', '/wp-content/bootstrap.css');
    wp_enqueue_style( 'sw_bootstrap' );
}


add_filter('w3tc_minify_urls_for_minification_to_minify_filename', 'w3tc_filename_filter', 20, 3);
function w3tc_filename_filter($minify_filename, $files, $type ){
    
    $version = "1.16";
    
    $ver = sanitize_title( str_replace('.','', $version) );
    
    $minify_filename = $ver.$minify_filename;
    
    return $minify_filename;
    
}

add_filter("gform_confirmation", "set_threat_report_cookie", 10, 4);
function set_threat_report_cookie($confirmation, $form, $lead, $ajax){

    if ($form["id"] == 72)
    {
        session_start();
        $_SESSION['threat_report'] = true;
    }
    
    return $confirmation;
}

add_action( 'setup_theme', 'redirect_override' );

function redirect_override() {  

    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    $query = "/resources/2020-cyber-threat-report-pdf/";
    
    if (substr($path, 0, strlen($query)) === $query)
    {
        session_start();
        if (isset($_SESSION['threat_report']))
        {
           $pdf_url = get_post_meta(54437, "wpcf-gated-content")[0];
    
           $pdf = file_get_contents($pdf_url);
        
            header('Content-type: application/pdf');
            header('Content-Disposition: inline; filename="2020-sonicwall-cyber-threat-report.pdf"');

            echo $pdf;

            exit(); 
        }
        else
        {
            wp_redirect("/2020-cyber-threat-report/");
            exit();
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
    
    $query = "/support/product-notification/";
    
    if (substr($path, 0, strlen($query)) === $query)
    {
        $parts = explode("/", trim($path, "/"));
        
        if (count($parts) == 3 && $parts[0] == "support" && $parts[1] == "product-notification" && is_numeric($parts[2]))
        {
            $url = "/support/product-notification/?sol_id=".$parts[2];
            
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
