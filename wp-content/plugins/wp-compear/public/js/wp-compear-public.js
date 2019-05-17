(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 *
	 * using http://jscompress.com/ for minimization
	 *
	 */

	 /**
	* jquery.matchHeight-min.js master
	* http://brm.io/jquery-match-height/
	* License: MIT
	*/
	(function(c){var n=-1,f=-1,g=function(a){return parseFloat(a)||0},r=function(a){var b=null,d=[];c(a).each(function(){var a=c(this),k=a.offset().top-g(a.css("margin-top")),l=0<d.length?d[d.length-1]:null;null===l?d.push(a):1>=Math.floor(Math.abs(b-k))?d[d.length-1]=l.add(a):d.push(a);b=k});return d},p=function(a){var b={byRow:!0,property:"height",target:null,remove:!1};if("object"===typeof a)return c.extend(b,a);"boolean"===typeof a?b.byRow=a:"remove"===a&&(b.remove=!0);return b},b=c.fn.matchHeight=
	function(a){a=p(a);if(a.remove){var e=this;this.css(a.property,"");c.each(b._groups,function(a,b){b.elements=b.elements.not(e)});return this}if(1>=this.length&&!a.target)return this;b._groups.push({elements:this,options:a});b._apply(this,a);return this};b._groups=[];b._throttle=80;b._maintainScroll=!1;b._beforeUpdate=null;b._afterUpdate=null;b._apply=function(a,e){var d=p(e),h=c(a),k=[h],l=c(window).scrollTop(),f=c("html").outerHeight(!0),m=h.parents().filter(":hidden");m.each(function(){var a=c(this);
	a.data("style-cache",a.attr("style"))});m.css("display","block");d.byRow&&!d.target&&(h.each(function(){var a=c(this),b="inline-block"===a.css("display")?"inline-block":"block";a.data("style-cache",a.attr("style"));a.css({display:b,"padding-top":"0","padding-bottom":"0","margin-top":"0","margin-bottom":"0","border-top-width":"0","border-bottom-width":"0",height:"100px"})}),k=r(h),h.each(function(){var a=c(this);a.attr("style",a.data("style-cache")||"")}));c.each(k,function(a,b){var e=c(b),f=0;if(d.target)f=
	d.target.outerHeight(!1);else{if(d.byRow&&1>=e.length){e.css(d.property,"");return}e.each(function(){var a=c(this),b={display:"inline-block"===a.css("display")?"inline-block":"block"};b[d.property]="";a.css(b);a.outerHeight(!1)>f&&(f=a.outerHeight(!1));a.css("display","")})}e.each(function(){var a=c(this),b=0;d.target&&a.is(d.target)||("border-box"!==a.css("box-sizing")&&(b+=g(a.css("border-top-width"))+g(a.css("border-bottom-width")),b+=g(a.css("padding-top"))+g(a.css("padding-bottom"))),a.css(d.property,
	f-b))})});m.each(function(){var a=c(this);a.attr("style",a.data("style-cache")||null)});b._maintainScroll&&c(window).scrollTop(l/f*c("html").outerHeight(!0));return this};b._applyDataApi=function(){var a={};c("[data-match-height], [data-mh]").each(function(){var b=c(this),d=b.attr("data-mh")||b.attr("data-match-height");a[d]=d in a?a[d].add(b):b});c.each(a,function(){this.matchHeight(!0)})};var q=function(a){b._beforeUpdate&&b._beforeUpdate(a,b._groups);c.each(b._groups,function(){b._apply(this.elements,
	this.options)});b._afterUpdate&&b._afterUpdate(a,b._groups)};b._update=function(a,e){if(e&&"resize"===e.type){var d=c(window).width();if(d===n)return;n=d}a?-1===f&&(f=setTimeout(function(){q(e);f=-1},b._throttle)):q(e)};c(b._applyDataApi);c(window).bind("load",function(a){b._update(!1,a)});c(window).bind("resize orientationchange",function(a){b._update(!0,a)})})(jQuery);

	/*!
	 * jQuery UI Touch Punch 0.2.3
	 *
	 * Copyright 2011–2014, Dave Furfero
	 * Dual licensed under the MIT or GPL Version 2 licenses.
	 *
	 * Depends:
	 *  jquery.ui.widget.js
	 *  jquery.ui.mouse.js
	 */
	!function(a){function f(a,b){if(!(a.originalEvent.touches.length>1)){a.preventDefault();var c=a.originalEvent.changedTouches[0],d=document.createEvent("MouseEvents");d.initMouseEvent(b,!0,!0,window,1,c.screenX,c.screenY,c.clientX,c.clientY,!1,!1,!1,!1,0,null),a.target.dispatchEvent(d)}}if(a.support.touch="ontouchend"in document,a.support.touch){var e,b=a.ui.mouse.prototype,c=b._mouseInit,d=b._mouseDestroy;b._touchStart=function(a){var b=this;!e&&b._mouseCapture(a.originalEvent.changedTouches[0])&&(e=!0,b._touchMoved=!1,f(a,"mouseover"),f(a,"mousemove"),f(a,"mousedown"))},b._touchMove=function(a){e&&(this._touchMoved=!0,f(a,"mousemove"))},b._touchEnd=function(a){e&&(f(a,"mouseup"),f(a,"mouseout"),this._touchMoved||f(a,"click"),e=!1)},b._mouseInit=function(){var b=this;b.element.bind({touchstart:a.proxy(b,"_touchStart"),touchmove:a.proxy(b,"_touchMove"),touchend:a.proxy(b,"_touchEnd")}),c.call(b)},b._mouseDestroy=function(){var b=this;b.element.unbind({touchstart:a.proxy(b,"_touchStart"),touchmove:a.proxy(b,"_touchMove"),touchend:a.proxy(b,"_touchEnd")}),d.call(b)}}}(jQuery);


	$(function() {

		/**** slider js ****/

		$('.wp-compear-tool-slider').each(function (idx, item) {

		   var carouselId = "carousel" + idx;
		   this.id = carouselId;

		   var lg_show = parseInt($(this).attr('data-lg-show'));
		   var lg_scroll = parseInt($(this).attr('data-lg-scroll'));
		   var lg_prevnext = $(this).attr('data-lg-prevnext');
		   if(lg_prevnext=='on'){var lg_prevnext_check=true;}else{var lg_prevnext_check=false;}
		   var lg_counter = $(this).attr('data-lg-counter');
		   if(lg_counter=='on'){var lg_counter_check=true;}else{var lg_counter_check=false;}

		   var md_show = parseInt($(this).attr('data-md-show'));
		   var md_scroll = parseInt($(this).attr('data-md-scroll'));

			$(this).slick({
				infinite: true,
				arrows: lg_prevnext_check,
				dots: lg_counter_check,
				slidesToShow: lg_show,
				slidesToScroll: lg_scroll,
				responsive: [
					{
					    breakpoint: 960,
					    settings: {
						slidesToShow: md_show,
						slidesToScroll: md_scroll
					  }
					},
					{
						breakpoint: 540,
						settings: {
						slidesToShow: 1,
						slidesToScroll: 1,
						centerMode: true
					  }
					 
					}
				]
		    });

			$('.wp-compear-slider-slide').matchHeight();

		});



		/**** general WP ComPEAR js ****/

		// $('.wp-compear-tool-wrapper').animate({
		// 	opacity: 1
		// }, 500, function() {
		// 	// Animation complete.
		// });



		/**** Drag and drop tool js ****/

		$('.wp-compear-tool-draganddrop .box').matchHeight();

		$( ".wp-compear-tool-draganddrop .drag-product .ghost" ).draggable({ 
	        helper: "clone",
	        cursor: "crosshair"
	    });

	    $( ".wp-compear-tool-draganddrop .comparison .comparison-inner .box.product" ).stop().droppable({ 
	        drop: Drop,
	        tolerance: "pointer"
	    });

	    function Drop(event, ui) {
	      
	        ui.draggable.parent().addClass('chosen').css('box-shadow', 'none');

	        var outer_parent = $(this).closest('#wp-compear-tool-draganddrop');

	        var draggableProdId = ui.draggable.attr("data-topprod-number");
	        var draggableTarget = $(this);
	        var new_product_info ='<div class="specs-table">';

	        var chosen_product_info = outer_parent.find('.dragondrop-hidden-product-info span.'+draggableProdId).html();

	        new_product_info += chosen_product_info;
	        new_product_info +='</div>';

	        //draggableTarget.fadeOut('fast').html( new_product_info ).fadeIn('fast');


	        var callback = function() { 


                draggableTarget.html( new_product_info );
				draggableTarget.animate({opacity: 1}, 100);

				outer_parent.find('span.data-spec-order-1').matchHeight();
		        outer_parent.find('span.data-spec-order-2').matchHeight();
		        outer_parent.find('span.data-spec-order-3').matchHeight();
		        outer_parent.find('span.data-spec-order-4').matchHeight();
		        outer_parent.find('span.data-spec-order-5').matchHeight();
		        outer_parent.find('span.data-spec-order-6').matchHeight();
		        outer_parent.find('span.data-spec-order-7').matchHeight();
		        outer_parent.find('span.data-spec-order-8').matchHeight();
		        outer_parent.find('span.data-spec-order-9').matchHeight();
		        outer_parent.find('span.data-spec-order-10').matchHeight();
		        outer_parent.find('span.data-spec-order-11').matchHeight();
		        outer_parent.find('span.data-spec-order-12').matchHeight();
		        outer_parent.find('span.data-spec-order-13').matchHeight();
		        outer_parent.find('span.data-spec-order-14').matchHeight();
		        outer_parent.find('span.data-spec-order-15').matchHeight();
		        outer_parent.find('span.data-spec-order-16').matchHeight();
		        outer_parent.find('span.data-spec-order-17').matchHeight();
		        outer_parent.find('span.data-spec-order-18').matchHeight();
		        outer_parent.find('span.data-spec-order-19').matchHeight();
		        outer_parent.find('span.data-spec-order-20').matchHeight();

		        $('.box').matchHeight();

		        //$.fn.matchHeight._update();
        	};

	         

        	// speed must match that in CSS to make sure otherr CSS does not override it
	        draggableTarget.animate({
				opacity: 0
			}, {
	           duration : 100,
	           easing: "swing",
	           complete: callback
	        });

	        

	    }

	    $(document).on("mouseenter", ".specs-table span.table-row > span", function() {
	        var spec_number = $(this).attr("data-spec-order");
	        $(this).closest('.draganddrop-outer').find("span.data-spec-order-"+spec_number).addClass('spec-hovered');
	    });

	    $(document).on("mouseleave", ".specs-table span.table-row > span", function() {
	        var spec_number = $(this).attr("data-spec-order");
	        $(this).closest('.draganddrop-outer').find("span.data-spec-order-"+spec_number).removeClass('spec-hovered');
	    });



	});


})( jQuery );
