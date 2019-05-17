/**
 *  Advanced iframe pro external workaround file v7.5.x
 *  Created: 2018-05-04 20:19:16
*/ 
/* jslint devel: true, evil: false */
if (typeof domain_advanced_iframe === 'undefined') { // Wordpress
    alert("Please set domain_advanced_iframe before you include the external Workaround file.");
} 

function trimExtraChars(text) {
    return text == null ? '' : text.toString().replace(/^[\s:;]+|[\s:;]+$/g, '');
}

/**
 * first we modify the iframe content 
 * only once in case the script is included several times.
 */   
function modifyIframe() {
    if (!ia_already_done) { 
      
      // here we add unique keys if css should be modified
      if (iframe_hide_elements !== ''  || onload_show_element_only !== '' || iframe_content_id !== '' || iframe_content_styles !== '' || change_iframe_links !== '' || change_iframe_links_target !== '') {
       if (add_css_class_iframe === 'true') {
         var iframeHref = window.location.toString(); 
         if (iframeHref.substr(-1) === '/') {
             iframeHref = iframeHref.substr(0, iframeHref.length - 1);
         }
         var lastIndex = iframeHref.lastIndexOf('/');
         var result = iframeHref.substring(lastIndex + 1);
         var newClass = result.replace(/[^A-Za-z0-9]/g, '-');
         var iframeBody = jQuery('body');
         iframeBody.addClass('ai-' + newClass);
         
         if (jQuery('#ai_wrapper_div').length) {
           jQuery('#ai_wrapper_div').children('div').each(function(i) {
               jQuery(this).addClass('ai-' + newClass + '-child-' + (i+1)); 
           });
         } else {
           iframeBody.children('div').each(function (i) {
               jQuery(this).addClass('ai-' + newClass + '-child-' + (i+1)); 
           });
         } 
        } 
      } 
      
      if (iframe_hide_elements !== '' && write_css_directly === 'false') {
          jQuery(iframe_hide_elements).css('display', 'none').css('width', '0').css('height','0');
      }
      if (onload_show_element_only !== '') {
          aiShowElementOnly(onload_show_element_only);
      }
      if (write_css_directly === 'false' && (iframe_content_id !== '' || iframe_content_styles !== '')) {
          var elementArray = iframe_content_id.split('|');
          var valuesArray = iframe_content_styles.split('|');
          if (elementArray.length !== valuesArray.length) {
              alert('Configuration error: The attributes iframe_content_id and iframe_content_styles have to have the amount of value sets separated by |.');
              return;
          } else {
              for (var x = 0; x < elementArray.length; ++x) {
                  var valuesArrayPairs = trimExtraChars(valuesArray[x]).split(';');
                  for (var y = 0; y < valuesArrayPairs.length; ++y) {
                      var elements = valuesArrayPairs[y].split(':');
                      jQuery(elementArray[x]).css(elements[0],elements[1]);
                  }
              }
          }
      }
      // Change links targets.
      if (change_iframe_links !== '' || change_iframe_links_target !== '') {
          var linksArray = change_iframe_links.split('|');
          var targetArray = change_iframe_links_target.split('|');
          if (linksArray.length !== targetArray.length) {
              alert('Configuration error: The attributes change_iframe_links and change_iframe_links_target have to have the amount of value sets separated by |.');
              return;
          } else {
              for (var z = 0; z < linksArray.length; ++z) {
                  jQuery(linksArray[z]).attr('target', targetArray[z]);
              }
          }
      }
      ia_already_done = true;
      // we check if we have to set the modify cookie
      if (modify_iframe_if_cookie) {
        setCookie("aiIframeModifications", "true");
      }
    }  
}

/**
 * Removes all elements from an iframe except the given one
 * script tags are also not removed!  
 * 
 * @param iframeId id of the iframe
 * @param showElement the id, class (jQuery syntax) of the element that should be displayed. 
 */ 
function aiShowElementOnly(showElement) {
  if (showElement.indexOf('|') === -1) {
    // this is the old way where the element is attaced directly to the body
    // this changes the dom tree and might breaks css rules
  var iframe = jQuery('body'); 
  var selectedBox = iframe.find(showElement).clone(true,true); 
    iframe.find('*').not(jQuery('script')).remove();
  iframe.prepend(selectedBox);
  } else {
    // This is the new way where everything except the element and the elments 
    // up to the root is hidden. This keeps the dom tree and therefore css will work like before. 
    var showElementSplit = showElement.split('|')[0];
    var element = jQuery(showElementSplit);
    element.siblings().hide();
    var parents = element.parents(); 
    parents.siblings().hide();
    parents.css('padding', '0px').css('margin', '0px');
}
}

/**
 * Init the resize element event.
 */
function aiInitElementResize_advanced_iframe() { 
   if (resize_on_element_resize !== '') {
      if (ia_resize_init_done_advanced_iframe === false) {
        /*! jQuery resize event - v1.1 - 3/14/2010 http://benalman.com/projects/jquery-resize-plugin/ Copyright (c) 2010 "Cowboy" Ben Alman Dual licensed under the MIT and GPL licenses. http://benalman.com/about/license/ */
        /* jshint ignore:start */        
        (function(e,t,n){"$:nomunge";function c(){s=t[o](function(){r.each(function(){var t=e(this),n=t.width(),r=t.height(),i=e.data(this,a);if(i&&n!==i.w||r!==i.h){t.trigger(u,[i.w=n,i.h=r])}});c()},i[f])}var r=e([]),i=e.resize=e.extend(e.resize,{}),s,o="setTimeout",u="resize",a=u+"-special-event",f="delay",l="throttleWindow";i[f]=250;i[l]=false;e.event.special[u]={setup:function(){if(!this.nodeName){return false}if(!i[l]&&this[o]){return false}var t=e(this);r=r.add(t);e.data(this,a,{w:t.width(),h:t.height()});if(r.length===1){c()}},teardown:function(){if(!i[l]&&this[o]){return false}var t=e(this);r=r.not(t);t.removeData(a);if(!r.length){clearTimeout(s)}},add:function(t){function s(t,i,s){var o=e(this),u=e.data(this,a);if(typeof u!=="undefined"){u.w=i!==n?i:o.width();u.h=s!==n?s:o.height()}r.apply(this,arguments)}if(!i[l]&&this[o]){return false}var r;if(e.isFunction(t)){r=t;return s}else{r=t.handler;t.handler=s}}};})(jQuery,this)
        /* jshint ignore:end */
        if (!jQuery().resize) {
            alert('jQuery.resize is not available. Most likely you have included jQuery AFTER the ai_external.js. Please include jQuery before the ai_external.js. If you cannot do this please disable "Resize on element resize"');
        }
        if (resize_on_element_resize_delay !== '' && parseInt(resize_on_element_resize_delay,10) >= 50 ) {
            jQuery.resize.delay=resize_on_element_resize_delay;
        }
        var res_element;
        if  (resize_on_element_resize === 'body') {
            res_element = jQuery('body');
        } else {
            res_element = jQuery('body').find(resize_on_element_resize);
        }  
        if (res_element.length === 0) {
                // show an error if null
                if (console && console.log) {
                     console.log('The configuration of "resize_on_element_resize" is invalid. The specified element ' + encodeURI(resize_on_element_resize) + ' could not be found. Please check your configuration. If your content is loaded dynamically please specify onload_resize_delay with a time that is longer then your content needs to load!');
                }  
        } else {
            res_element.resize(function(){ 
            ia_already_done = false;
                onload_resize_delay = 0;
                aiExecuteWorkaround_advanced_iframe(false);
        });
        }
        ia_resize_init_done_advanced_iframe = true;
      }
   }
}


/**
 * The function creates a hidden iframe and determines the height of the 
 * current page. This is then set as height parameter for the iframe 
 * which triggers the resize function in the parent.  
 */ 
function aiExecuteWorkaround_advanced_iframe(init) {
    init = (init === undefined) ? true : init;
    var modificationCookieSet = false;
    
    if (modify_iframe_if_cookie) {
       modificationCookieSet = getCookie("aiIframeModifications") == "true";    
    }
    if (window!==window.top) { /* I'm in a frame! */
      // first we modify the iframe content - only once in case the script is included several times.
      if (onload_resize_delay === 0 && init) {
      modifyIframe();   
      aiInitElementResize_advanced_iframe();
      }
      if (updateIframeHeight === 'true') {
        
        var url;
        // add the iframe dynamically
        if (!usePostMessage) {
          url = domain_advanced_iframe + '/js/iframe_height.html';
          var empty_url = 'about:blank';
          var newElementStr = '<iframe id="ai_hidden_iframe_advanced_iframe" style="display:none;clear:both" width="0" height="0" src="';
          newElementStr += empty_url +'">Iframes not supported.</iframe>';
          var newElement = aiCreate(newElementStr);
          document.body.appendChild(newElement);
        }    
        if (init) {
        // add a wrapper div below the body to measure - if you remove this you have to measure the height of the body! 
        // See below for this solution. The wrapper is only created if needed
        createAiWrapperDiv();
        
        // remove any margin,padding from the body because each browser handles this differently
        // Overflow hidden is used to avoid scrollbars that can be shown for a milisecond
        aiAddCss('body {margin:0px;padding:0px;overflow:hidden;}');
       }
       var newHeight = 0;
       if (onload_resize_delay === 0) {
        // get the height of the element right below the body or a custom element - Using this solution allows that the iframe shrinks also.
        var wrapperElement = aiGetWrapperElement(element_to_measure);
        var newHeightRaw =  Math.max(wrapperElement.scrollHeight, wrapperElement.offsetHeight);
          newHeight = parseInt(newHeightRaw,10) + element_to_measure_offset;
    
        //  Get the height from the body. The problem with this solution is that an iframe can not shrink anymore.
        //  remove everything from createAiWrapperDiv() until here for the alternative solution. 
        //  var newHeight = Math.max(document.body.scrollHeight, document.body.offsetHeight,
        //    document.documentElement.scrollHeight, document.documentElement.offsetHeight);  
        
        //  This is the width - need to detect a change of the iframe width at a browser resize!
        iframeWidth = getIframeWidth();
        }
        
        if (iframe_advanced_iframe_last_height !== newHeight) { // we only resize if we have a change

            
            // if we have a height < 10 or > 10.000 the resize is done 500ms later because it seems like the 
            // height could not be measured correctly. And if the page is really > 10.000 it does not matter because
            // no one does see that the resize is done later.  
            if (onload_resize_delay === 0 && (newHeight < 10 || newHeight > 10000)) {
               onload_resize_delay = 500;
            } 
 
            if (onload_resize_delay === 0) {
               // 4 pixels extra are needed because of IE! (2 for Chrome)
               // If you still have scrollbars add a little bit more offset.

               if (usePostMessage) {
                 var data = { 'aitype' : 'height',
                              'height' :  (newHeight + 4),
                              'width' : iframeWidth,
                              'id' : iframe_id_advanced_iframe,
                              'data' : {}
                            };
                 if (add_iframe_url_as_param === 'remote') {
                     data.loc = encodeURIComponent(window.location);
                 }  
                 ai_extract_additional_content(dataPostMessage, data);
                 
                 var json_data = JSON.stringify(data);                
                 if (debugPostMessage && console && console.log) {
                     console.log('postMessage sent: ' + json_data + ' - targetOrigin: ' + post_message_domain  );
                 }  
                 parent.postMessage(json_data, post_message_domain);
               } else {
               
                 var iframe = document.getElementById('ai_hidden_iframe_advanced_iframe'); 
                 var send_data = 'height=' + (newHeight + 4) + '&width=' + iframeWidth + '&id=' + iframe_id_advanced_iframe;
                 if (add_iframe_url_as_param === 'remote') {
                     send_data += '&loc=' + encodeURIComponent(window.location);
                 }
                 iframe.src = url + '?' + send_data;
               }
               
            } else {
               setTimeout(function () { resizeLater_advanced_iframe(); }, onload_resize_delay);
            }
            iframe_advanced_iframe_last_height = newHeight;
        }
        
        // set overflow to visible again.
        if (keepOverflowHidden === 'false') {
            var timeoutRemove = onload_resize_delay + 500;
            window.setTimeout(removeOverflowHidden, timeoutRemove);
        }
        
        if (enable_responsive_iframe === 'true' && init) {
            // resize size after resize of window. setup is done 1 sec after first resize to avoid double resize.
            window.setTimeout(initResize_advanced_iframe, onload_resize_delay + 1000);
        }
  
      } else if (hide_page_until_loaded_external === 'true') {  // only one iframe is rendered - if auto height is disabled still the parent has to be informed to show the iframe ;).
        if (usePostMessage) {
           var dataShow = { 'aitype' : 'show',
                        'id' : iframe_id_advanced_iframe
                      };
           ai_extract_additional_content(dataPostMessage, dataShow);
           
           var jsonDataShow = JSON.stringify(dataShow);
           if (debugPostMessage && console && console.log) {
              console.log('postMessage sent: ' + jsonDataShow + ' - targetOrigin: ' + post_message_domain  );
           }  
           parent.postMessage(jsonDataShow, post_message_domain);
        } else {
          // add the iframe dynamically
          var urlShow = domain_advanced_iframe + '/js/iframe_show.html?id='+ iframe_id_advanced_iframe;
          var newElementStrShow = '<iframe id="ai_hidden_iframe_show_advanced_iframe" style="display:none;" width="0" height="0" src="';
          newElementStrShow += urlShow+'">Iframes not supported.</iframe>';
          var newElementShow = aiCreate(newElementStrShow);
          document.body.appendChild(newElementShow);
        }
      }
      // In case html was hidden. 
      document.documentElement.style.visibility = 'visible';   
    } else if (modificationCookieSet) {
       if (onload_resize_delay === 0) {
          modifyIframe();
       } else {
          setTimeout(function () { modifyIframe(); }, onload_resize_delay);
       }
    }
}

function resizeLater_advanced_iframe() {
    // We also modify the iframe later as if the resize is delayed because of dynamic data 
    // most of the time the elements that should be modified also are loaded dynamically 
    if (onload_resize_delay !== 0) {
          modifyIframe();
          aiInitElementResize_advanced_iframe();
      }
   var url = domain_advanced_iframe + '/js/iframe_height.html';
   var wrapperElement = aiGetWrapperElement(element_to_measure);
   var newHeightRaw =  Math.max(wrapperElement.scrollHeight, wrapperElement.offsetHeight);
   var newHeight = parseInt(newHeightRaw,10) + element_to_measure_offset;          
   var iframeWidth = getIframeWidth();
   iframe_advanced_iframe_last_height = newHeight;   
      
   if (newHeight > 10) { // Only resize if the height is > 10   
      if (usePostMessage) {
         var data = { 'aitype' : 'height',
                      'height' :  (newHeight + 4),
                      'width' : iframeWidth,
                      'id' : iframe_id_advanced_iframe,
                      'data' : {}
                    };
         if (add_iframe_url_as_param === 'remote') {
             data.loc = encodeURIComponent(window.location);
         }  
         ai_extract_additional_content(dataPostMessage, data);
         
         var json_data = JSON.stringify(data);                
         if (debugPostMessage && console && console.log) {
             console.log('postMessage sent: ' + json_data + ' - targetOrigin: ' + post_message_domain);
         }  
         parent.postMessage(json_data, post_message_domain);
       } else {  
         var iframe = document.getElementById('ai_hidden_iframe_advanced_iframe'); 
         var send_data = 'height=' + (newHeight + 4) + '&width=' + iframeWidth + '&id=' + iframe_id_advanced_iframe;
         if (add_iframe_url_as_param === 'remote') {
             send_data += '&loc=' + encodeURIComponent(window.location);
         }
         iframe.src = url + '?' + send_data;
     }
    
     if (enable_responsive_iframe === 'true') {
        // this is the width - need to detect a change of the iframe width at a browser resize!
        iframeWidth = getIframeWidth();
     }
   } else {
       if (debugPostMessage && console && console.log) {
           console.log('Advanced iframe configuration problem: The height of the page cannot be detected with the current settings. Please check the documentation of "element_to_measure" how to define an alternative element to detect the height.');
       }  
   }
}

/**
 *  Remove the overflow:hidden from the body which
 *  what avoiding scrollbars during resize. 
 */ 
function removeOverflowHidden() {
    document.body.style.overflow='auto';
}

/**
 *  Gets the text length from text nodes. For other nodes a dummy length is returned
 *  browser do add empty text nodes between elements which should return a length
 *  of 0 because they should not be counted. 
 */ 
function getTextLength( obj ) {
    var value = obj.textContent ? obj.textContent : 'NO_TEXT';
    return value.trim().length;
} 

/**
 * Creates a wrapper div if needed. 
 * It is not created if the body has only one single div below the body.
 * childNdes.length has to be > 2 because the iframe is already attached!    
 */ 
function createAiWrapperDiv() {
    var countElements = 0;   
    // Count tags which are not empty text nodes, no script and no iframe tags
    // because only if we have more than 1 of this tags a wrapper div is needed
    for (var i = 0; i < document.body.childNodes.length; ++i) {
       var nodeName = document.body.childNodes[i].nodeName.toLowerCase(); 
       var nodeLength = getTextLength(document.body.childNodes[i]); 
       if ( nodeLength !== 0 && nodeName !== 'script' && nodeName !== 'iframe') {
           countElements++;  
       }
    }
    if (countElements > 1) {
      var div = document.createElement('div');
  	  div.id = 'ai_wrapper_div';
    	// Move the body's children into this wrapper
    	while (document.body.firstChild) {
    		div.appendChild(document.body.firstChild);
    	}
    	// Append the wrapper to the body
    	document.body.appendChild(div);
      
      // set the style
      div.style.cssText = 'margin:0px;padding:0px;border: none;' + additional_styles_wrapper_div;
      // If we have a wrapper we set this as default 
      if (element_to_measure === 'default') {
          element_to_measure = '#' + div.id;    
      }
    }
}

/**
 *  Creates a new dom fragment from a string
 */ 
function aiCreate(htmlStr) {
    var frag = document.createDocumentFragment(),
    temp = document.createElement('div');
    temp.innerHTML = htmlStr;
    while (temp.firstChild) {
        frag.appendChild(temp.firstChild);
    }
    return frag;
}

function getIframeWidth() { 
  var wrapperElement = aiGetWrapperElement(element_to_measure);
  var newWidthRaw = Math.max(wrapperElement.scrollWidth, wrapperElement.offsetWidth);
 
  // we have a width set and no max-width! 
  var directWidth = jQuery(wrapperElement).css('width');
 
  if (typeof directWidth !== typeof undefined && directWidth !== false) {
      var maxWidth = jQuery(wrapperElement).css('max-width');
      if (!(typeof maxWidth !== typeof undefined && maxWidth !== 'none')) {
         newWidthRaw = directWidth;                               
      }
  } 
  return parseInt(newWidthRaw,10);
} 

function initResize_advanced_iframe() {
// resize the iframe only when the width changes!
jQuery(window).resize(function() {
    if (enable_responsive_iframe === 'true') {
      if (iframeWidth !== getIframeWidth()) {
          iframeWidth = getIframeWidth(); 
          // hide the overflow if not keept
          if (keepOverflowHidden === 'false') {
               document.body.style.overflow='hidden';
          }
          ia_already_done = false;
          onload_resize_delay = 0;
          aiExecuteWorkaround_advanced_iframe(false);
          // set overflow to visible again.
          if (keepOverflowHidden === 'false') {
              window.setTimeout(removeOverflowHidden,500);
          }
      }
    }   
});
}
function setCookie(name,value) {
    document.cookie = name + "=" + (value || "") + "; path=/";
}
function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}       

/**
 *  Adds a css style to the head 
 */         
function aiAddCss(cssCode) {
    var styleElement = document.createElement('style');
    styleElement.type = 'text/css';
    if (styleElement.styleSheet) {
      styleElement.styleSheet.cssText = cssCode;
    } else {
      styleElement.appendChild(document.createTextNode(cssCode));
    }
    document.getElementsByTagName('head')[0].appendChild(styleElement);
}

if(typeof String.prototype.trim !== 'function') {
  String.prototype.trim = function() {
    return this.replace(/^\s+|\s+$/g, ''); 
  };
}

/**
 * Helper function without jQuery to add a onload event 
 * even if there is already one attached. 
 */ 
function addOnloadEvent(fnc){
  if ( typeof window.addEventListener !== 'undefined' ) {
    window.addEventListener( 'load', fnc, false );
  } else if ( typeof window.attachEvent !== 'undefined' ) {
    window.attachEvent( 'onload', fnc );
  }
  else {
    if ( window.onload !== null ) {
      var oldOnload = window.onload;
      window.onload = function ( e ) {
        oldOnload( e );
        window[fnc]();
      };
    }
    else {
      window.onload = fnc;
    }
  }
}

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

/**
 *  Gets the first element or the element you define at element_to_measure
 *  Either by id if no # and . is found or by jquery otherwise.  
 */ 
function aiGetWrapperElement(element_to_measure) {
  var element; 
  if (element_to_measure === 'default') {
    element = document.body.children[0];
  } else {
     if (element_to_measure.indexOf('#') > -1 || element_to_measure.indexOf('.') > -1) {
         element = jQuery(element_to_measure)[0];
     } else {
         element = document.getElementById(element_to_measure);
     }
  }
  // show an error if null and set the default again. 
  if (element == null || element.length === 0) {
         if (console && console.log) {
               console.log('The configuration of "element_to_measure" is invalid. The specified element ' + encodeURI(element_to_measure) + ' could not be found. Please check your configuration. As fallback "default" is used which measures the first child of the body.');
          }
          element = document.body.children[0];
  }
  return element;
}

function writeCssDirectly() {
    var css_output = '';
    
    if (iframe_hide_elements !== '') {
        css_output += iframe_hide_elements + '{ display: none !important; width:0px; height:0px }';
    }
      
    if (iframe_content_id !== '' || iframe_content_styles !== '') {
        var elementArray = iframe_content_id.split('|');
        var valuesArray = iframe_content_styles.split('|');
        if (elementArray.length !== valuesArray.length) {
            alert('Configuration error: The attributes iframe_content_id and iframe_content_styles have to have the amount of value sets separated by |.');
            return;
        } else {
            for (var x = 0; x < elementArray.length; ++x) {
                css_output += elementArray[x] + '{';
                css_output += trimExtraChars(valuesArray[x]);
                css_output += '}'; 
            }
        }
    }

    if (css_output !== '') {
        document.write('<style>' + css_output + '<\/style>');
    }  
}

function loadExternalConfig(path) {
  var scripts = document.getElementsByTagName('script');
  var myScript = scripts[ scripts.length - 1 ];
  var queryString = myScript.src.replace(/^[^\?]+\??/,'');
  if ( queryString ) { // try to load a config
     var id = queryString.substr(10);
     if (id != '') {
     var letters = /^[\w\d_\-/$/]*$/; 
     if(!id.match(letters)) { 
       alert('The value of config_id can only have alphanumeric characters, - and _.'); 
     }
     // load the config.
     if (loadedConfig[id] === undefined) {
       document.write("<script src='"+path+"/../advanced-iframe-custom/ai_external_config_"+id+".js'></script>"); 
       document.write("<script src='"+path+"/js/ai_external.js'></script>"); 
       document.write('\n'); 
       loadedConfig[id] = 'true';
       return false;
     } else {
       return true;
       }
     }
  }
  return true;
}

/**
 * Extract elements from the page and adds this to the json data. 
 */
function ai_extract_additional_content(config, data) {
     if (config !== '') {
        try {
            var elementArray = config.split(',');
       for (var x = 0; x < elementArray.length; ++x) {
                var valuesArrayPairs = trimExtraChars(elementArray[x]).split('|');
                data.data[valuesArrayPairs[0]] = jQuery(valuesArrayPairs[1]).html();
            }
        }  catch(e) {
            if (console && console.log) {
              console.log('dataPostMessage is set to an invalid value. Please check your configuration.');
              console.log(e);
            }
       }  
     }     
}

/* main */

if (typeof loadedConfigs === 'undefined') {
   var loadedConfig = {};
}

// load an external config from the inital site. Param config_id is checked from the url. 
var doIt = loadExternalConfig(domain_advanced_iframe);

if (doIt) {
  // Variables are checked with typeof before because this enables that the user can
  // define this values before and after including this file and they don't have to set 
  // them at all if not needed.
  var iframe_id_advanced_iframe;
  if (typeof iframe_id === 'undefined') {
      iframe_id_advanced_iframe = 'advanced_iframe';
  }  else {
      /* jshint ignore:start */
      iframe_id_advanced_iframe = iframe_id;
      /* jshint ignore:end */
  }
  
  var iframe_advanced_iframe_last_height = -1;
  
  if (typeof iframe_url_id === 'undefined') {
      var iframe_url_id = '';
  }
  // multisite support
  if (typeof domainMultisite === 'undefined') {
      var domainMultisite = 'true';
  } 

  if (typeof post_message_domain === 'undefined') {
      var post_message_domain = '*';
  } 
   
  if (domainMultisite === 'true') {
      // Check the referer
      var ref = document.referrer;
      // If found we exchange the domain.
      if (ref !== '') {
        var domainOrig = domain_advanced_iframe.split('/')[2];
        var multiDomain = ref.split('/')[2];
        domain_advanced_iframe = domain_advanced_iframe.replace(domainOrig, multiDomain); 
      }
      post_message_domain = '*';
  }
  
  if (typeof usePostMessage === 'undefined') {
      var usePostMessage = true;
  } 
  if (typeof debugPostMessage === 'undefined') {
      var debugPostMessage = false;
  }
  if (typeof dataPostMessage === 'undefined') {
      var dataPostMessage = '';
  }  
  
  
  
  if (iframe_url_id !== '') {
     var value_id = aiGetUrlParameter(iframe_url_id);
     if (value_id !== '') {
        iframe_id_advanced_iframe = value_id;
     } else {
        var errorText = 'Configuration error: The id cannot be found in the url at the configured parameter.';
        alert(errorText);
        throw errorText;
     }
  }
  if (typeof updateIframeHeight === 'undefined') {
      var updateIframeHeight = 'true';
  } 
  if (typeof onload_resize_delay === 'undefined') {
      var onload_resize_delay = 0;
  }
  if (typeof keepOverflowHidden === 'undefined') {
      var keepOverflowHidden = 'false';
  }
  if (typeof hide_page_until_loaded_external === 'undefined') {
      var hide_page_until_loaded_external = 'false';
  }
  if (typeof iframe_hide_elements === 'undefined') {
    var iframe_hide_elements = '';
  }
  if (typeof onload_show_element_only === 'undefined') {
      var onload_show_element_only = '';
  }
  if (typeof iframe_content_id === 'undefined') {
      var iframe_content_id = '';
  }
  if (typeof iframe_content_styles === 'undefined') {
      var iframe_content_styles = '';
  }
  if (typeof change_iframe_links === 'undefined') {
      var change_iframe_links = '';
  }
  if (typeof change_iframe_links_target === 'undefined') {
      var change_iframe_links_target = '';
  }
  if (typeof additional_js_file_iframe === 'undefined') {
      var additional_js_file_iframe = '';
  }
  if (typeof additional_js_iframe === 'undefined') {
      var additional_js_iframe = '';
  }
  if (typeof additional_css_file_iframe === 'undefined') {
      var additional_css_file_iframe = '';
  }
  if (typeof iframe_redirect_url === 'undefined') {
      var iframe_redirect_url = '';
  }
  if (typeof enable_responsive_iframe === 'undefined') {
      var enable_responsive_iframe = 'false';
  }
  if (typeof write_css_directly === 'undefined') {
      var write_css_directly = 'false';
  }
  if (typeof resize_on_element_resize === 'undefined') {
      var resize_on_element_resize = '';
  }
  if (typeof resize_on_element_resize_delay === 'undefined') {
      var resize_on_element_resize_delay = '250';
  }
  if (typeof add_iframe_url_as_param === 'undefined') {
      var add_iframe_url_as_param = 'false';
  }
  if (typeof element_to_measure === 'undefined') {
      var element_to_measure = 'default';
  }
  if (typeof element_to_measure_offset === 'undefined') {
      var element_to_measure_offset = 0;
  }
  var modify_iframe_cookie_found = false;
  if (typeof modify_iframe_if_cookie === 'undefined') {
      var modify_iframe_if_cookie = true;
  }
  // This is a feature only mentioned in the readme.txt as it was only needed for a custom solution.
  if (typeof additional_styles_wrapper_div === 'undefined') {
      var additional_styles_wrapper_div = '';
  }
  if (typeof add_css_class_iframe === 'undefined') {
      var add_css_class_iframe = 'false';
  }
   if (typeof send_console_log === 'undefined') {
      var send_console_log = false;
  }
  
  var iframeWidth = 0;
  var ia_resize_init_done_advanced_iframe = false;
  
  // redirect to a given url if the page is NOT in an iframe
  if (iframe_redirect_url !== '') {
          /* Add existing parameters */
      if ('' !== window.location.search && iframe_redirect_url.indexOf('?') ===-1) {
              iframe_redirect_url += window.location.search;      
          }
      if (window===window.top) { /* I'm not in a frame! */
          location.replace(iframe_redirect_url);
      } else {
          /* we check if the referrer is the parent  */
          var ref = document.referrer;
          if (ref !== '') {
            var domainRedirect = iframe_redirect_url.split('/')[2];
            var domainRef = ref.split('/')[2];
            if (domainRedirect !== domainRef) {
                top.location.replace(iframe_redirect_url);   
            } 
          } else {
            /*  If no referer is found we redirect to the defined parent as this seems to be a fake include */
            top.location.replace(iframe_redirect_url); 
          }       
      }
  } 
   
  // load jQuery if not available 
  window.jQuery || document.write("<script src='//code.jquery.com/jquery-1.11.0.min.js'></script>");
   
  // if responsive is enabled auto height has to be enabled as well.
  if (enable_responsive_iframe === 'true') {
      updateIframeHeight = 'true';
  }
  
  if (typeof ia_already_done === 'undefined') {
      if (window!==window.top) { /* I'm in a frame! */
          // dom is not fully loaded therefore jQuery is not used to hide the body!
          if (iframe_hide_elements !== '' || onload_show_element_only !== '' ||
              iframe_content_id !== '' || iframe_content_styles !== '') {
              if (document.documentElement && write_css_directly === 'false') {
                  document.documentElement.style.visibility = 'hidden';
              }
              // Solution if you want to remove the background but you see it for a very short time. 
              // because hiding the iframe content does not help!
              //if (window != window.top) { 
              //    document.write("<style>body { background-image: none; }</style>");
              //}
          }
      }
      var ia_already_done = false;
  }
  
  // add the aiUpdateIframeHeight to the onload of the site.
  addOnloadEvent(aiExecuteWorkaround_advanced_iframe);
  
  if (write_css_directly === 'true' && window!==window.top) {
      writeCssDirectly(); 
  }
  if (additional_css_file_iframe !== '' && window!==window.top) {
      document.write('<link rel="stylesheet" type="text/css" href="' + additional_css_file_iframe + '"/>');       
  } 
  if (additional_js_file_iframe !== '' && window!==window.top) {
      document.write('<script src="'+additional_js_file_iframe+'"></script>');
  }
  if (additional_js_iframe !== '' && window!==window.top) {
      document.write('<script>' + additional_js_iframe + '<\/script>');
  }
  // we overwrite console.log if it exists
  if (usePostMessage && send_console_log && console && console.log) {
     console.log = function() {
        var data = { 'aitype' : 'debug',
                     'id' : iframe_id_advanced_iframe,
                     'data' :  [].map.call(arguments, JSON.stringify)
                   }; 
        var json_data = JSON.stringify(data);                
        parent.postMessage(json_data, post_message_domain);                    
     };
     window.onerror = function (msg, url, lineNo, columnNo, error) {
        var data = { 'aitype' : 'debug',
                     'id' : iframe_id_advanced_iframe,
                     'data' :  'ERROR: ' + msg + ' - ' + lineNo + ':' + columnNo
                   }; 
        var json_data = JSON.stringify(data);                
        parent.postMessage(json_data, post_message_domain);  
    };   
} 
} 