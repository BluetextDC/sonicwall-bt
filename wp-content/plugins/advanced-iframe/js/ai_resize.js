/**
 *  Advanced iframe pro external resize script.
 *  
 *  This file should only be included if you want to share your content to a different domain. 
 *  This feature does send the height as postMessage to the parent. 
 *  The script should to be included right before the iframe and the parameters
 *  advanced_iframe_id or advanced_iframe_debug should be set if needed. 
 *   
 *  Please see the demos on 
 *  http://www.tinywebgallery.com/blog/advanced-iframe/advanced-iframe-pro-demo/share-content-from-your-domain-content-filter
 *  http://www.tinywebgallery.com/blog/advanced-iframe/advanced-iframe-pro-demo/share-content-from-your-domain-add-ai_external-js-local
 */
 /* jslint devel: true */
if (typeof advanced_iframe_debug === 'undefined') {
    var advanced_iframe_debug = false;
} 
 
function receiveMessage(event) {
   if (advanced_iframe_debug && console && console.log) {
       console.log('postMessage received: ' + event.data);
   } 
   
   var jsObject = JSON.parse(event.data);
   var type = jsObject.aitype; 
      // check if the data is of the expected
      if (type === 'height') {
        aiProcessHeight(jsObject);
      }
}

function aiProcessHeight(jsObject) {     
    var nHeight = jsObject.height;
    var advanced_iframe_id = jsObject.id;
    
    if (nHeight != null) {
      try { 
             var height = parseInt(nHeight,10) + 4;
             var iframe = document.getElementById('iframe-' + advanced_iframe_id);
             iframe.style.height = (height + 'px');
             // make the iframe visiable again.
             iframe.style.visibility = 'visible';
    	} catch(e) {
        if (console && console.log) {
          console.log(e);
        }
      }
    } 
}

 /* jshint ignore:start */
if (window.addEventListener) {
    window.addEventListener('message', receiveMessage)
} else if (el.attachEvent)  {
    // needed for IE9 and below compatibility
    el.attachEvent('message', receiveMessage);
}
 /* jshint ignore:end */