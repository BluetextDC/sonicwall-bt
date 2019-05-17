jQuery(document).ready(function(){
	// Create template for the button
	jQuery.fancybox.defaults.btnTpl.copy = '<input id="lightbox_clipboard_content" value="" style="opacity: 0;"><div class="copy-tooltip" style="float: right;"><button data-fancybox-copy id="fancybox-button--copy" class="fancybox-button fancybox-button--copy" title="Copy to clipboard" data-clipboard-target="#lightbox_clipboard_content">' +
	    '<svg id="copy-button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30"><path d="M7 13h10v1h-10v-1zm15-11v22h-20v-22h3c1.229 0 2.18-1.084 3-2h8c.82.916 1.771 2 3 2h3zm-11 1c0 .552.448 1 1 1s1-.448 1-1-.448-1-1-1-1 .448-1 1zm9 15.135c-1.073 1.355-2.448 2.763-3.824 3.865h3.824v-3.865zm0-14.135h-4l-2 2h-3.898l-2.102-2h-4v18h7.362c4.156 0 2.638-6 2.638-6s6 1.65 6-2.457v-9.543zm-13 12h5v-1h-5v1zm0-4h10v-1h-10v1zm0-2h10v-1h-10v1z"/></svg>' + 
	'</button> <span class="tooltiptext"><small>Copied To Clipboard</small></span></div>';

	jQuery.fancybox.defaults.buttons.push("copy");

	jQuery(document).on("click", "#fancybox-button--copy", function(){
		jQuery(".copy-tooltip .tooltiptext").css({visibility: 'visible'});
		setTimeout(function(){
			jQuery(".copy-tooltip .tooltiptext").css({visibility: 'hidden'});
		}, 1000);
	});
    
    window.initFancybox = function()
    {
       jQuery("[data-fancybox]").fancybox({
			beforeLoad: function( instance ) {

			var src = false;

            if (instance.current.opts.hideCopyButton)
            {
                src = false;        
            }
            else if (instance.current.opts.srcOverride)
            {
                src = instance.current.opts.srcOverride;        
            }
            else if (instance.current.opts.videoResource)
            {
                src = instance.current.opts.videoResource  
            }
            else
            {
                src = instance.current.src;     
            }
                
            if (src)
            {
                
                if (src.charAt(0) == "/")
                {
                    src = window.location.protocol + "//" + window.location.hostname + src;        
                }
                
                jQuery("#lightbox_clipboard_content").val(src);
            }
            else
            {
                //No source, hide button
                jQuery("#fancybox-button--copy").hide();
            }

	        

			}
		}); 
    }

    window.initFancybox();
	

	new ClipboardJS('.fancybox-button--copy');
});

