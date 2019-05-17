// Make the DIV element draggable:
var circularMenu = document.getElementById("circular-menu");

document.addEventListener("DOMContentLoaded", function(event) {
    circularMenu.style.display = 'block';

    if (hasAdminBar())
    {
      circularMenu.classList.add("wpadminbar");
    }
    window.setTimeout(function(){
      openCircularMenu();
    }, 1000);
});

var toggler = document.getElementById("menu-toggler");

var circular_menu_auto_opened = false;

function isMobile()
{
  var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

  return width <= 1023;
}

function hasAdminBar()
{
  return document.getElementById("wpadminbar") ? true: false;
}

function openCircularMenu()
{
  
  var opened = docCookies.getItem("circular-menu-autoopened") ? parseInt(docCookies.getItem("circular-menu-autoopened")) : 0;

  if (opened <= 3 && !isMobile())
  {
    circular_menu_auto_opened = true;
    toggler.checked = true;
    docCookies.setItem("circular-menu-autoopened", opened + 1);

    //Add a scroll listener if the circular menu has been autopened.
    document.addEventListener("scroll", closeCircularMenuOnScroll);
  }
}

function closeCircularMenuOnScroll()
{
  //Remove the listener after first scroll;
  document.removeEventListener("scroll", closeCircularMenuOnScroll);

  if (toggler.checked && circular_menu_auto_opened)
  {
    setTimeout(function(){
        toggler.checked = false;
    }, 250);
  }
}

window.onresize = function(event) {
    bounce(toggler);
};

toggler.addEventListener('change', (event) => {
  if (event.target.checked) {
    //Check to see if it overlaps.
    bounce(event.target);

  }
});

function bounce(elem)
{
  var bounds = elem.getBoundingClientRect();

  if (bounds.x < bounds.width * 4)
  {
    circularMenu.style.left = (bounds.width * 4) + "px";
  }
  else if (bounds.x + (bounds.width * 5) > window.innerWidth)
  {
    circularMenu.style.left = window.innerWidth - (bounds.width * 5) + "px";
  }

  if (bounds.y < bounds.height * 4)
  {
    circularMenu.style.top = (bounds.height * 4) + "px";
  }
  else if (bounds.y + (bounds.height * 5) > window.innerHeight)
  {
    circularMenu.style.top = window.innerHeight - (bounds.height * 5) + "px";
  }
}

dragElement(circularMenu);

function dragElement(elmnt) {

  var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
  if (document.getElementById(elmnt.id + "header")) {
    // if present, the header is where you move the DIV from:
    document.getElementById(elmnt.id + "header").onmousedown = dragMouseDown;
  } else {
    // otherwise, move the DIV from anywhere inside the DIV: 
    elmnt.ondragstart = dragMouseDown;
  }

  function disableClickOnDrag(event){
    event.preventDefault();
    return false;
  }

  function dragMouseDown(e) {
    //Disable the click while dragging
    toggler.addEventListener("click", disableClickOnDrag);

    e = e || window.event;
    e.preventDefault();
    // get the mouse cursor position at startup:
    pos3 = e.clientX;
    pos4 = e.clientY;
    document.onmouseup = closeDragElement;
    // call a function whenever the cursor moves:
    document.onmousemove = elementDrag;
  }

  function elementDrag(e) {
    e = e || window.event;
    e.preventDefault();

    // calculate the new cursor position:
    pos1 = pos3 - e.clientX;
    pos2 = pos4 - e.clientY;
    pos3 = e.clientX;
    pos4 = e.clientY;
    // set the element's new position:
    elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
    elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
  }

  function closeDragElement() {
    // stop moving when mouse button is released:
    document.onmouseup = null;
    document.onmousemove = null;
    //Reenable the click when we are done
    setTimeout(function(){
      toggler.removeEventListener("click", disableClickOnDrag)
    });
    
  }
}


/*\
|*|
|*|  :: cookies.js ::
|*|
|*|  A complete cookies reader/writer framework with full unicode support.
|*|
|*|  Revision #3 - July 13th, 2017
|*|
|*|  https://developer.mozilla.org/en-US/docs/Web/API/document.cookie
|*|  https://developer.mozilla.org/User:fusionchess
|*|  https://github.com/madmurphy/cookies.js
|*|
|*|  This framework is released under the GNU Public License, version 3 or later.
|*|  http://www.gnu.org/licenses/gpl-3.0-standalone.html
|*|
|*|  Syntaxes:
|*|
|*|  * docCookies.setItem(name, value[, end[, path[, domain[, secure]]]])
|*|  * docCookies.getItem(name)
|*|  * docCookies.removeItem(name[, path[, domain]])
|*|  * docCookies.hasItem(name)
|*|  * docCookies.keys()
|*|
\*/

var docCookies = {
  getItem: function (sKey) {
    if (!sKey) { return null; }
    return decodeURIComponent(document.cookie.replace(new RegExp("(?:(?:^|.*;)\\s*" + encodeURIComponent(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=\\s*([^;]*).*$)|^.*$"), "$1")) || null;
  },
  setItem: function (sKey, sValue, vEnd, sPath, sDomain, bSecure) {
    if (!sKey || /^(?:expires|max\-age|path|domain|secure)$/i.test(sKey)) { return false; }
    var sExpires = "";
    if (vEnd) {
      switch (vEnd.constructor) {
        case Number:
          sExpires = vEnd === Infinity ? "; expires=Fri, 31 Dec 9999 23:59:59 GMT" : "; max-age=" + vEnd;
          /*
          Note: Despite officially defined in RFC 6265, the use of `max-age` is not compatible with any
          version of Internet Explorer, Edge and some mobile browsers. Therefore passing a number to
          the end parameter might not work as expected. A possible solution might be to convert the the
          relative time to an absolute time. For instance, replacing the previous line with:
          */
          /*
          sExpires = vEnd === Infinity ? "; expires=Fri, 31 Dec 9999 23:59:59 GMT" : "; expires=" + (new Date(vEnd * 1e3 + Date.now())).toUTCString();
          */
          break;
        case String:
          sExpires = "; expires=" + vEnd;
          break;
        case Date:
          sExpires = "; expires=" + vEnd.toUTCString();
          break;
      }
    }
    document.cookie = encodeURIComponent(sKey) + "=" + encodeURIComponent(sValue) + sExpires + (sDomain ? "; domain=" + sDomain : "") + (sPath ? "; path=" + sPath : "") + (bSecure ? "; secure" : "");
    return true;
  },
  removeItem: function (sKey, sPath, sDomain) {
    if (!this.hasItem(sKey)) { return false; }
    document.cookie = encodeURIComponent(sKey) + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT" + (sDomain ? "; domain=" + sDomain : "") + (sPath ? "; path=" + sPath : "");
    return true;
  },
  hasItem: function (sKey) {
    if (!sKey || /^(?:expires|max\-age|path|domain|secure)$/i.test(sKey)) { return false; }
    return (new RegExp("(?:^|;\\s*)" + encodeURIComponent(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=")).test(document.cookie);
  },
  keys: function () {
    var aKeys = document.cookie.replace(/((?:^|\s*;)[^\=]+)(?=;|$)|^\s*|\s*(?:\=[^;]*)?(?:\1|$)/g, "").split(/\s*(?:\=[^;]*)?;\s*/);
    for (var nLen = aKeys.length, nIdx = 0; nIdx < nLen; nIdx++) { aKeys[nIdx] = decodeURIComponent(aKeys[nIdx]); }
    return aKeys;
  }
};