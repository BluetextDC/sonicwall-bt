<?php
defined('_VALID_AI') or die('Direct Access to this location is not allowed.');

include_once('php-browser-detection-plugin/php-browser-detection.php');

function is_selected_browser($browser,$id) {
  $allowed_settings = array('firefox', 'safari', 'opera', 'chrome', 'ie', 'ipad', 'ipod', 'iphone', 'android', 'androidtablet', 'mobile', 'tablet', 'desktop', 'browser', 'iosmobile', 'os', 'default');
  if (!empty($browser)) {
     if ($browser == 'default') {
        // Check if we have already created an iframe with this id
        if (!isset($GLOBALS["ai_" . $id])) {
           return true;
        }
     } else {
         if (isset($GLOBALS["ai_" . $id])) {
             return false;
         }
         // split with , 
         $browser_array = explode(",", $browser); 
         $browser_array = array_map('trim',$browser_array);
         $is_ok = false;
         
         foreach($browser_array as $browser_element) {
           // check for () with version
           $pos = strpos($browser_element, "(");
           if ($pos === false) {
               $browser_base = $browser_element;
               $browser_version = '';
           } else {
              $browser_base = substr($browser_element,0,$pos);
              $browser_version = trim(substr($browser_element, ($pos +1), -1)); 
           }
           if (in_array($browser_base, $allowed_settings)) {
              // if browser is o.k. we print the code and  set a global             
               if (call_user_func('ai_is_' . $browser_base, $browser_version)) {
                  $GLOBALS["ai_" . $id] = 'done';
                  $is_ok = true; 
               }  
           } else {
               echo 'Invalid browser configuration. Please read the configuration how to use the browser detection.';
           }
         }
         if ($is_ok) {
           return true;
         }
     }
  } else {
     return true;
  }
return false;
}
?>