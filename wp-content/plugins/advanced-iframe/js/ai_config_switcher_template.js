/**
 *
 * This template is an example if you want to load different configurations
 * depending on an url parameter. This is needed if you have different 
 * setup of the same page.
 * 
 * Also this can be used to set different callback domains if needed.
 * This is important if you include the same page to completely 
 * different installations. Make sure you use the same id for all 
 * setups.          
 *  
 * Usage: 
 * 1. Implement the switch of the parameters below 
 *   - conf is use a parameter in the example
 *   - Set the settings for all iframe
 *   - Set the settings depending on the conf parameter   
 * 2. Include this file before the ai_external.js 
 * 3. Append ?conf=1 to the url of the first iframe
 * 4. Append ?conf=2 to the url if the 2nd iframe
 *   
 */  
/*jshint unused:false*/

/**
 * Helper function to extract the custom id from the url
 * So if you add ?conf=1  aiGetUrlParameter("conf") does
 * return 1   
 */ 
function aiGetUrlParameter( name )
{
  name = name.replace(/[\[]/,'\\\[').replace(/[\]]/,'\\\]');
  var regexS = '[\\?&]'+name+'=([^&#]*)';
  var regex = new RegExp( regexS );
  var results = regex.exec( window.location.href );
  
  if( results == null ) {
    return '';
  } else {
    var allowedChars = new RegExp('^[a-zA-Z0-9_\-]+$');
    if (!allowedChars.test(results[1])) {
        return '';
    } 
    return results[1];
    }
}

// Read the config parameter
var configId = aiGetUrlParameter('conf');

// Settings for all iframes
var updateIframeHeight = 'true';

// Settings depending on an url parameter
switch (configId) {
  case '1':
    // var domain_advanced_iframe is NOT needed if you use postMessage for communication  
    var domain_advanced_iframe = 'http://domain1/wordpress/wp-content/plugins/advanced-iframe';
    var iframe_hide_elements = '#iframe-header,#iframe-footer,#some-images';
    break;
  case '2':
    // var domain_advanced_iframe is NOT needed if you use postMessage for communication  
    var domain_advanced_iframe = 'http://domain2/wp-content/plugins/advanced-iframe';
    var iframe_hide_elements = '#iframe-header,#iframe-footer';
    break;
  default:
    // var domain_advanced_iframe is NOT needed if you use postMessage for communication  
    var domain_advanced_iframe = 'http://domain1/wordpress/wp-content/plugins/advanced-iframe'; 
    var iframe_hide_elements = '#iframe-header,#iframe-footer,#some-images';
}