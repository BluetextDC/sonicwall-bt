var otgsSwitcher=otgsSwitcher||{};otgsSwitcher.otgsPopoverTooltip=otgsSwitcher.otgsPopoverTooltip||{},otgsSwitcher.otgsPopoverTooltip.otgsTableStickyHeader=function(e){var t={};function i(o){if(t[o])return t[o].exports;var n=t[o]={i:o,l:!1,exports:{}};return e[o].call(n.exports,n,n.exports,i),n.l=!0,n.exports}return i.m=e,i.c=t,i.d=function(e,t,o){i.o(e,t)||Object.defineProperty(e,t,{configurable:!1,enumerable:!0,get:o})},i.r=function(e){Object.defineProperty(e,"__esModule",{value:!0})},i.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return i.d(t,"a",t),t},i.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},i.p="",i(i.s=2)}([function(e,t){!function(e,t,i){"use strict";var o="stickyTableHeaders",n=0,r={fixedOffset:0,leftOffset:0,marginTop:0,objDocument:document,objHead:"head",objWindow:t,scrollableArea:t,cacheHeaderHeight:!1,zIndex:3};e.fn[o]=function(i){return this.each(function(){var l=e.data(this,"plugin_"+o);l?"string"==typeof i?l[i].apply(l):l.updateOptions(i):"destroy"!==i&&e.data(this,"plugin_"+o,new function(i,l){var s=this;s.$el=e(i),s.el=i,s.id=n++,s.$el.bind("destroyed",e.proxy(s.teardown,s)),s.$clonedHeader=null,s.$originalHeader=null,s.cachedHeaderHeight=null,s.isSticky=!1,s.hasBeenSticky=!1,s.leftOffset=null,s.topOffset=null,s.init=function(){s.setOptions(l),s.$el.each(function(){var t=e(this);t.css("padding",0),s.$originalHeader=e("thead:first",this),s.$clonedHeader=s.$originalHeader.clone(),t.trigger("clonedHeader."+o,[s.$clonedHeader]),s.$clonedHeader.addClass("tableFloatingHeader"),s.$clonedHeader.css({display:"none",opacity:0}),s.$originalHeader.addClass("tableFloatingHeaderOriginal"),s.$originalHeader.after(s.$clonedHeader),s.$printStyle=e('<style type="text/css" media="print">.tableFloatingHeader{display:none !important;}.tableFloatingHeaderOriginal{position:static !important;}</style>'),s.$head.append(s.$printStyle)}),s.$clonedHeader.find("input, select").attr("disabled",!0),s.updateWidth(),s.toggleHeaders(),s.bind()},s.destroy=function(){s.$el.unbind("destroyed",s.teardown),s.teardown()},s.teardown=function(){s.isSticky&&s.$originalHeader.css("position","static"),e.removeData(s.el,"plugin_"+o),s.unbind(),s.$clonedHeader.remove(),s.$originalHeader.removeClass("tableFloatingHeaderOriginal"),s.$originalHeader.css("visibility","visible"),s.$printStyle.remove(),s.el=null,s.$el=null},s.bind=function(){s.$scrollableArea.on("scroll."+o,s.toggleHeaders),s.isWindowScrolling||(s.$window.on("scroll."+o+s.id,s.setPositionValues),s.$window.on("resize."+o+s.id,s.toggleHeaders)),s.$scrollableArea.on("resize."+o,s.toggleHeaders),s.$scrollableArea.on("resize."+o,s.updateWidth)},s.unbind=function(){s.$scrollableArea.off("."+o,s.toggleHeaders),s.isWindowScrolling||(s.$window.off("."+o+s.id,s.setPositionValues),s.$window.off("."+o+s.id,s.toggleHeaders)),s.$scrollableArea.off("."+o,s.updateWidth)},s.debounce=function(e,t){var i=null;return function(){var o=this,n=arguments;clearTimeout(i),i=setTimeout(function(){e.apply(o,n)},t)}},s.toggleHeaders=s.debounce(function(){s.$el&&s.$el.each(function(){var t,i,n,r=e(this),l=s.isWindowScrolling?isNaN(s.options.fixedOffset)?s.options.fixedOffset.outerHeight():s.options.fixedOffset:s.$scrollableArea.offset().top+(isNaN(s.options.fixedOffset)?0:s.options.fixedOffset),a=r.offset(),d=s.$scrollableArea.scrollTop()+l,c=s.$scrollableArea.scrollLeft(),f=s.isWindowScrolling?d>a.top:l>a.top;f&&(i=s.options.cacheHeaderHeight?s.cachedHeaderHeight:s.$clonedHeader.height(),n=(s.isWindowScrolling?d:0)<a.top+r.height()-i-(s.isWindowScrolling?0:l)),f&&n?(t=a.left-c+s.options.leftOffset,s.$originalHeader.css({position:"fixed","margin-top":s.options.marginTop,top:0,left:t,"z-index":s.options.zIndex}),s.leftOffset=t,s.topOffset=l,s.$clonedHeader.css("display",""),s.isSticky||(s.isSticky=!0,s.updateWidth(),r.trigger("enabledStickiness."+o)),s.setPositionValues()):s.isSticky&&(s.$originalHeader.css("position","static"),s.$clonedHeader.css("display","none"),s.isSticky=!1,s.resetWidth(e("td,th",s.$clonedHeader),e("td,th",s.$originalHeader)),r.trigger("disabledStickiness."+o))})},0),s.setPositionValues=s.debounce(function(){var e=s.$window.scrollTop(),t=s.$window.scrollLeft();!s.isSticky||e<0||e+s.$window.height()>s.$document.height()||t<0||t+s.$window.width()>s.$document.width()||s.$originalHeader.css({top:s.topOffset-(s.isWindowScrolling?0:e),left:s.leftOffset-(s.isWindowScrolling?0:t)})},0),s.updateWidth=s.debounce(function(){if(s.isSticky){s.$originalHeaderCells||(s.$originalHeaderCells=e("th,td",s.$originalHeader)),s.$clonedHeaderCells||(s.$clonedHeaderCells=e("th,td",s.$clonedHeader));var t=s.getWidth(s.$clonedHeaderCells);s.setWidth(t,s.$clonedHeaderCells,s.$originalHeaderCells),s.$originalHeader.css("width",s.$clonedHeader.width()),s.options.cacheHeaderHeight&&(s.cachedHeaderHeight=s.$clonedHeader.height())}},0),s.getWidth=function(i){var o=[];return i.each(function(i){var n,r=e(this);if("border-box"===r.css("box-sizing")){var l=r[0].getBoundingClientRect();n=l.width?l.width:l.right-l.left}else if("collapse"===e("th",s.$originalHeader).css("border-collapse"))if(t.getComputedStyle)n=parseFloat(t.getComputedStyle(this,null).width);else{var a=parseFloat(r.css("padding-left")),d=parseFloat(r.css("padding-right")),c=parseFloat(r.css("border-width"));n=r.outerWidth()-a-d-c}else n=r.width();o[i]=n}),o},s.setWidth=function(e,t,i){t.each(function(t){var o=e[t];i.eq(t).css({"min-width":o,"max-width":o})})},s.resetWidth=function(t,i){t.each(function(t){var o=e(this);i.eq(t).css({"min-width":o.css("min-width"),"max-width":o.css("max-width")})})},s.setOptions=function(t){s.options=e.extend({},r,t),s.$window=e(s.options.objWindow),s.$head=e(s.options.objHead),s.$document=e(s.options.objDocument),s.$scrollableArea=e(s.options.scrollableArea),s.isWindowScrolling=s.$scrollableArea[0]===s.$window[0]},s.updateOptions=function(e){s.setOptions(e),s.unbind(),s.bind(),s.updateWidth(),s.toggleHeaders()},s.init()}(this,i))})}}(jQuery,window)},function(e,t,i){"use strict";!function(e){e&&e.__esModule}(i(0));window.addEventListener("DOMContentLoaded",function(){var e=document.querySelectorAll(".js-otgs-table-sticky-header"),t={fixedOffset:jQuery("#wpadminbar")};e.forEach(function(e){jQuery(e).stickyTableHeaders(t)})})},function(e,t,i){e.exports=i(1)}]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9vdGdzU3dpdGNoZXIub3Rnc1BvcG92ZXJUb29sdGlwLm90Z3NUYWJsZVN0aWNreUhlYWRlci93ZWJwYWNrL2Jvb3RzdHJhcCIsIndlYnBhY2s6Ly9vdGdzU3dpdGNoZXIub3Rnc1BvcG92ZXJUb29sdGlwLm90Z3NUYWJsZVN0aWNreUhlYWRlci8uL25vZGVfbW9kdWxlcy9zdGlja3ktdGFibGUtaGVhZGVycy9qcy9qcXVlcnkuc3RpY2t5dGFibGVoZWFkZXJzLmpzIiwid2VicGFjazovL290Z3NTd2l0Y2hlci5vdGdzUG9wb3ZlclRvb2x0aXAub3Rnc1RhYmxlU3RpY2t5SGVhZGVyLy4vc3JjL2pzL290Z3NUYWJsZVN0aWNreUhlYWRlci5qcyJdLCJuYW1lcyI6WyJpbnN0YWxsZWRNb2R1bGVzIiwiX193ZWJwYWNrX3JlcXVpcmVfXyIsIm1vZHVsZUlkIiwiZXhwb3J0cyIsIm1vZHVsZSIsImkiLCJsIiwibW9kdWxlcyIsImNhbGwiLCJtIiwiYyIsImQiLCJuYW1lIiwiZ2V0dGVyIiwibyIsIk9iamVjdCIsImRlZmluZVByb3BlcnR5IiwiY29uZmlndXJhYmxlIiwiZW51bWVyYWJsZSIsImdldCIsInIiLCJ2YWx1ZSIsIm4iLCJfX2VzTW9kdWxlIiwib2JqZWN0IiwicHJvcGVydHkiLCJwcm90b3R5cGUiLCJoYXNPd25Qcm9wZXJ0eSIsInAiLCJzIiwiJCIsIndpbmRvdyIsInVuZGVmaW5lZCIsImlkIiwiZGVmYXVsdHMiLCJmaXhlZE9mZnNldCIsImxlZnRPZmZzZXQiLCJtYXJnaW5Ub3AiLCJvYmpEb2N1bWVudCIsImRvY3VtZW50Iiwib2JqSGVhZCIsIm9ialdpbmRvdyIsInNjcm9sbGFibGVBcmVhIiwiY2FjaGVIZWFkZXJIZWlnaHQiLCJ6SW5kZXgiLCJmbiIsIm9wdGlvbnMiLCJ0aGlzIiwiZWFjaCIsImluc3RhbmNlIiwiZGF0YSIsImFwcGx5IiwidXBkYXRlT3B0aW9ucyIsImVsIiwiYmFzZSIsIiRlbCIsImJpbmQiLCJwcm94eSIsInRlYXJkb3duIiwiJGNsb25lZEhlYWRlciIsIiRvcmlnaW5hbEhlYWRlciIsImNhY2hlZEhlYWRlckhlaWdodCIsImlzU3RpY2t5IiwiaGFzQmVlblN0aWNreSIsInRvcE9mZnNldCIsImluaXQiLCJzZXRPcHRpb25zIiwiJHRoaXMiLCJjc3MiLCJjbG9uZSIsInRyaWdnZXIiLCJhZGRDbGFzcyIsImRpc3BsYXkiLCJvcGFjaXR5IiwiYWZ0ZXIiLCIkcHJpbnRTdHlsZSIsIiRoZWFkIiwiYXBwZW5kIiwiZmluZCIsImF0dHIiLCJ1cGRhdGVXaWR0aCIsInRvZ2dsZUhlYWRlcnMiLCJkZXN0cm95IiwidW5iaW5kIiwicmVtb3ZlRGF0YSIsInJlbW92ZSIsInJlbW92ZUNsYXNzIiwiJHNjcm9sbGFibGVBcmVhIiwib24iLCJpc1dpbmRvd1Njcm9sbGluZyIsIiR3aW5kb3ciLCJzZXRQb3NpdGlvblZhbHVlcyIsIm9mZiIsImRlYm91bmNlIiwiZGVsYXkiLCJ0aW1lciIsImNvbnRleHQiLCJhcmdzIiwiYXJndW1lbnRzIiwiY2xlYXJUaW1lb3V0Iiwic2V0VGltZW91dCIsIm5ld0xlZnQiLCJoZWFkZXJIZWlnaHQiLCJub3RTY3JvbGxlZFBhc3RCb3R0b20iLCJuZXdUb3BPZmZzZXQiLCJpc05hTiIsIm91dGVySGVpZ2h0Iiwib2Zmc2V0IiwidG9wIiwic2Nyb2xsVG9wIiwic2Nyb2xsTGVmdCIsInNjcm9sbGVkUGFzdFRvcCIsImhlaWdodCIsImxlZnQiLCJwb3NpdGlvbiIsIm1hcmdpbi10b3AiLCJ6LWluZGV4IiwicmVzZXRXaWR0aCIsIndpblNjcm9sbFRvcCIsIndpblNjcm9sbExlZnQiLCIkZG9jdW1lbnQiLCJ3aWR0aCIsIiRvcmlnaW5hbEhlYWRlckNlbGxzIiwiJGNsb25lZEhlYWRlckNlbGxzIiwiY2VsbFdpZHRocyIsImdldFdpZHRoIiwic2V0V2lkdGgiLCIkY2xvbmVkSGVhZGVycyIsIndpZHRocyIsImluZGV4IiwiYm91bmRpbmdDbGllbnRSZWN0IiwiZ2V0Qm91bmRpbmdDbGllbnRSZWN0IiwicmlnaHQiLCJnZXRDb21wdXRlZFN0eWxlIiwicGFyc2VGbG9hdCIsImxlZnRQYWRkaW5nIiwicmlnaHRQYWRkaW5nIiwiYm9yZGVyIiwib3V0ZXJXaWR0aCIsIiRvcmlnSGVhZGVycyIsImVxIiwibWluLXdpZHRoIiwibWF4LXdpZHRoIiwiZXh0ZW5kIiwialF1ZXJ5IiwiYWRkRXZlbnRMaXN0ZW5lciIsImVsZW1lbnRzIiwicXVlcnlTZWxlY3RvckFsbCIsImZvckVhY2giLCJlbGVtZW50Iiwic3RpY2t5VGFibGVIZWFkZXJzIl0sIm1hcHBpbmdzIjoid0tBQ0EsSUFBQUEsS0FHQSxTQUFBQyxFQUFBQyxHQUdBLEdBQUFGLEVBQUFFLEdBQ0EsT0FBQUYsRUFBQUUsR0FBQUMsUUFHQSxJQUFBQyxFQUFBSixFQUFBRSxJQUNBRyxFQUFBSCxFQUNBSSxHQUFBLEVBQ0FILFlBVUEsT0FOQUksRUFBQUwsR0FBQU0sS0FBQUosRUFBQUQsUUFBQUMsSUFBQUQsUUFBQUYsR0FHQUcsRUFBQUUsR0FBQSxFQUdBRixFQUFBRCxRQTJDQSxPQXRDQUYsRUFBQVEsRUFBQUYsRUFHQU4sRUFBQVMsRUFBQVYsRUFHQUMsRUFBQVUsRUFBQSxTQUFBUixFQUFBUyxFQUFBQyxHQUNBWixFQUFBYSxFQUFBWCxFQUFBUyxJQUNBRyxPQUFBQyxlQUFBYixFQUFBUyxHQUNBSyxjQUFBLEVBQ0FDLFlBQUEsRUFDQUMsSUFBQU4sS0FNQVosRUFBQW1CLEVBQUEsU0FBQWpCLEdBQ0FZLE9BQUFDLGVBQUFiLEVBQUEsY0FBaURrQixPQUFBLEtBSWpEcEIsRUFBQXFCLEVBQUEsU0FBQWxCLEdBQ0EsSUFBQVMsRUFBQVQsS0FBQW1CLFdBQ0EsV0FBMkIsT0FBQW5CLEVBQUEsU0FDM0IsV0FBaUMsT0FBQUEsR0FFakMsT0FEQUgsRUFBQVUsRUFBQUUsRUFBQSxJQUFBQSxHQUNBQSxHQUlBWixFQUFBYSxFQUFBLFNBQUFVLEVBQUFDLEdBQXNELE9BQUFWLE9BQUFXLFVBQUFDLGVBQUFuQixLQUFBZ0IsRUFBQUMsSUFHdER4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QUFBQyxHQUFBWCxHQUFBN0QsS0FDQXlFLFlBQUFwQixFQUNBcUIsWUFBQXJCLE9BS0FuRSxFQUFBK0QsV0FBQSxTQUFBVSxFQUFBWSxHQUNBWixFQUFBL0UsS0FBQSxTQUFBaUYsR0FDQSxJQUFBOUQsRUFBQXJDLEVBQUFpQixNQUNBNEYsRUFBQUMsR0FBQVgsR0FBQTdELEtBQ0F5RSxZQUFBMUUsRUFBQUMsSUFBQSxhQUNBMEUsWUFBQTNFLEVBQUFDLElBQUEsa0JBS0FkLEVBQUFZLFdBQUEsU0FBQXBCLEdBQ0FRLEVBQUFSLFFBQUFoQixFQUFBaUgsVUFBNkI3RyxFQUFBWSxHQUM3QlEsRUFBQW9DLFFBQUE1RCxFQUFBd0IsRUFBQVIsUUFBQUwsV0FDQWEsRUFBQXNCLE1BQUE5QyxFQUFBd0IsRUFBQVIsUUFBQU4sU0FDQWMsRUFBQWtFLFVBQUExRixFQUFBd0IsRUFBQVIsUUFBQVIsYUFDQWdCLEVBQUFpQyxnQkFBQXpELEVBQUF3QixFQUFBUixRQUFBSixnQkFDQVksRUFBQW1DLGtCQUFBbkMsRUFBQWlDLGdCQUFBLEtBQUFqQyxFQUFBb0MsUUFBQSxJQUdBcEMsRUFBQUYsY0FBQSxTQUFBTixHQUNBUSxFQUFBWSxXQUFBcEIsR0FFQVEsRUFBQTZCLFNBQ0E3QixFQUFBRSxPQUNBRixFQUFBMEIsY0FDQTFCLEVBQUEyQixpQkFJQTNCLEVBQUFXLE9BZUEsQ0FBQWxCLEtBQUFELE9BNVRDLENBaVVBa0csT0FBQWpILG9FQ2xVRDlCLEVBQUEsSUFFQThCLE9BQU9rSCxpQkFBaUIsbUJBQW9CLFdBSzNDLElBQU1DLEVBQVczRyxTQUFTNEcsaUJBQWlCLGdDQUNyQ2xELEdBQ0w5RCxZQUFhNkcsT0FBTyxnQkFNckJFLEVBQVNFLFFBQVEsU0FBQUMsR0FFaEJMLE9BQU9LLEdBQVNDLG1CQUFtQnJEIiwiZmlsZSI6ImpzL290Z3MtdGFibGUtc3RpY2t5LWhlYWRlci5qcz92ZXI9MDgxMDdkNDM2YWVkOTUwMGY0MDE0N2Q0ZTc5NjhjMzkiLCJzb3VyY2VzQ29udGVudCI6WyIgXHQvLyBUaGUgbW9kdWxlIGNhY2hlXG4gXHR2YXIgaW5zdGFsbGVkTW9kdWxlcyA9IHt9O1xuXG4gXHQvLyBUaGUgcmVxdWlyZSBmdW5jdGlvblxuIFx0ZnVuY3Rpb24gX193ZWJwYWNrX3JlcXVpcmVfXyhtb2R1bGVJZCkge1xuXG4gXHRcdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuIFx0XHRpZihpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSkge1xuIFx0XHRcdHJldHVybiBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXS5leHBvcnRzO1xuIFx0XHR9XG4gXHRcdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG4gXHRcdHZhciBtb2R1bGUgPSBpbnN0YWxsZWRNb2R1bGVzW21vZHVsZUlkXSA9IHtcbiBcdFx0XHRpOiBtb2R1bGVJZCxcbiBcdFx0XHRsOiBmYWxzZSxcbiBcdFx0XHRleHBvcnRzOiB7fVxuIFx0XHR9O1xuXG4gXHRcdC8vIEV4ZWN1dGUgdGhlIG1vZHVsZSBmdW5jdGlvblxuIFx0XHRtb2R1bGVzW21vZHVsZUlkXS5jYWxsKG1vZHVsZS5leHBvcnRzLCBtb2R1bGUsIG1vZHVsZS5leHBvcnRzLCBfX3dlYnBhY2tfcmVxdWlyZV9fKTtcblxuIFx0XHQvLyBGbGFnIHRoZSBtb2R1bGUgYXMgbG9hZGVkXG4gXHRcdG1vZHVsZS5sID0gdHJ1ZTtcblxuIFx0XHQvLyBSZXR1cm4gdGhlIGV4cG9ydHMgb2YgdGhlIG1vZHVsZVxuIFx0XHRyZXR1cm4gbW9kdWxlLmV4cG9ydHM7XG4gXHR9XG5cblxuIFx0Ly8gZXhwb3NlIHRoZSBtb2R1bGVzIG9iamVjdCAoX193ZWJwYWNrX21vZHVsZXNfXylcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubSA9IG1vZHVsZXM7XG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlIGNhY2hlXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmMgPSBpbnN0YWxsZWRNb2R1bGVzO1xuXG4gXHQvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9uIGZvciBoYXJtb255IGV4cG9ydHNcbiBcdF9fd2VicGFja19yZXF1aXJlX18uZCA9IGZ1bmN0aW9uKGV4cG9ydHMsIG5hbWUsIGdldHRlcikge1xuIFx0XHRpZighX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIG5hbWUpKSB7XG4gXHRcdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIG5hbWUsIHtcbiBcdFx0XHRcdGNvbmZpZ3VyYWJsZTogZmFsc2UsXG4gXHRcdFx0XHRlbnVtZXJhYmxlOiB0cnVlLFxuIFx0XHRcdFx0Z2V0OiBnZXR0ZXJcbiBcdFx0XHR9KTtcbiBcdFx0fVxuIFx0fTtcblxuIFx0Ly8gZGVmaW5lIF9fZXNNb2R1bGUgb24gZXhwb3J0c1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5yID0gZnVuY3Rpb24oZXhwb3J0cykge1xuIFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgJ19fZXNNb2R1bGUnLCB7IHZhbHVlOiB0cnVlIH0pO1xuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCJcIjtcblxuXG4gXHQvLyBMb2FkIGVudHJ5IG1vZHVsZSBhbmQgcmV0dXJuIGV4cG9ydHNcbiBcdHJldHVybiBfX3dlYnBhY2tfcmVxdWlyZV9fKF9fd2VicGFja19yZXF1aXJlX18ucyA9IDIpO1xuIiwiLyohIENvcHlyaWdodCAoYykgSm9uYXMgTW9zYmVjaCAtIGh0dHBzOi8vZ2l0aHViLmNvbS9qbW9zYmVjaC9TdGlja3lUYWJsZUhlYWRlcnNcclxuXHRNSVQgbGljZW5zZSBpbmZvOiBodHRwczovL2dpdGh1Yi5jb20vam1vc2JlY2gvU3RpY2t5VGFibGVIZWFkZXJzL2Jsb2IvbWFzdGVyL2xpY2Vuc2UudHh0ICovXHJcblxyXG47KGZ1bmN0aW9uICgkLCB3aW5kb3csIHVuZGVmaW5lZCkge1xyXG5cdCd1c2Ugc3RyaWN0JztcclxuXHJcblx0dmFyIG5hbWUgPSAnc3RpY2t5VGFibGVIZWFkZXJzJyxcclxuXHRcdGlkID0gMCxcclxuXHRcdGRlZmF1bHRzID0ge1xyXG5cdFx0XHRmaXhlZE9mZnNldDogMCxcclxuXHRcdFx0bGVmdE9mZnNldDogMCxcclxuXHRcdFx0bWFyZ2luVG9wOiAwLFxyXG5cdFx0XHRvYmpEb2N1bWVudDogZG9jdW1lbnQsXHJcblx0XHRcdG9iakhlYWQ6ICdoZWFkJyxcclxuXHRcdFx0b2JqV2luZG93OiB3aW5kb3csXHJcblx0XHRcdHNjcm9sbGFibGVBcmVhOiB3aW5kb3csXHJcblx0XHRcdGNhY2hlSGVhZGVySGVpZ2h0OiBmYWxzZSxcclxuXHRcdFx0ekluZGV4OiAzXHJcblx0XHR9O1xyXG5cclxuXHRmdW5jdGlvbiBQbHVnaW4gKGVsLCBvcHRpb25zKSB7XHJcblx0XHQvLyBUbyBhdm9pZCBzY29wZSBpc3N1ZXMsIHVzZSAnYmFzZScgaW5zdGVhZCBvZiAndGhpcydcclxuXHRcdC8vIHRvIHJlZmVyZW5jZSB0aGlzIGNsYXNzIGZyb20gaW50ZXJuYWwgZXZlbnRzIGFuZCBmdW5jdGlvbnMuXHJcblx0XHR2YXIgYmFzZSA9IHRoaXM7XHJcblxyXG5cdFx0Ly8gQWNjZXNzIHRvIGpRdWVyeSBhbmQgRE9NIHZlcnNpb25zIG9mIGVsZW1lbnRcclxuXHRcdGJhc2UuJGVsID0gJChlbCk7XHJcblx0XHRiYXNlLmVsID0gZWw7XHJcblx0XHRiYXNlLmlkID0gaWQrKztcclxuXHJcblx0XHQvLyBMaXN0ZW4gZm9yIGRlc3Ryb3llZCwgY2FsbCB0ZWFyZG93blxyXG5cdFx0YmFzZS4kZWwuYmluZCgnZGVzdHJveWVkJyxcclxuXHRcdFx0JC5wcm94eShiYXNlLnRlYXJkb3duLCBiYXNlKSk7XHJcblxyXG5cdFx0Ly8gQ2FjaGUgRE9NIHJlZnMgZm9yIHBlcmZvcm1hbmNlIHJlYXNvbnNcclxuXHRcdGJhc2UuJGNsb25lZEhlYWRlciA9IG51bGw7XHJcblx0XHRiYXNlLiRvcmlnaW5hbEhlYWRlciA9IG51bGw7XHJcblxyXG5cdFx0Ly8gQ2FjaGUgaGVhZGVyIGhlaWdodCBmb3IgcGVyZm9ybWFuY2UgcmVhc29uc1xyXG5cdFx0YmFzZS5jYWNoZWRIZWFkZXJIZWlnaHQgPSBudWxsO1xyXG5cclxuXHRcdC8vIEtlZXAgdHJhY2sgb2Ygc3RhdGVcclxuXHRcdGJhc2UuaXNTdGlja3kgPSBmYWxzZTtcclxuXHRcdGJhc2UuaGFzQmVlblN0aWNreSA9IGZhbHNlO1xyXG5cdFx0YmFzZS5sZWZ0T2Zmc2V0ID0gbnVsbDtcclxuXHRcdGJhc2UudG9wT2Zmc2V0ID0gbnVsbDtcclxuXHJcblx0XHRiYXNlLmluaXQgPSBmdW5jdGlvbiAoKSB7XHJcblx0XHRcdGJhc2Uuc2V0T3B0aW9ucyhvcHRpb25zKTtcclxuXHJcblx0XHRcdGJhc2UuJGVsLmVhY2goZnVuY3Rpb24gKCkge1xyXG5cdFx0XHRcdHZhciAkdGhpcyA9ICQodGhpcyk7XHJcblxyXG5cdFx0XHRcdC8vIHJlbW92ZSBwYWRkaW5nIG9uIDx0YWJsZT4gdG8gZml4IGlzc3VlICM3XHJcblx0XHRcdFx0JHRoaXMuY3NzKCdwYWRkaW5nJywgMCk7XHJcblxyXG5cdFx0XHRcdGJhc2UuJG9yaWdpbmFsSGVhZGVyID0gJCgndGhlYWQ6Zmlyc3QnLCB0aGlzKTtcclxuXHRcdFx0XHRiYXNlLiRjbG9uZWRIZWFkZXIgPSBiYXNlLiRvcmlnaW5hbEhlYWRlci5jbG9uZSgpO1xyXG5cdFx0XHRcdCR0aGlzLnRyaWdnZXIoJ2Nsb25lZEhlYWRlci4nICsgbmFtZSwgW2Jhc2UuJGNsb25lZEhlYWRlcl0pO1xyXG5cclxuXHRcdFx0XHRiYXNlLiRjbG9uZWRIZWFkZXIuYWRkQ2xhc3MoJ3RhYmxlRmxvYXRpbmdIZWFkZXInKTtcclxuXHRcdFx0XHRiYXNlLiRjbG9uZWRIZWFkZXIuY3NzKHtkaXNwbGF5OiAnbm9uZScsIG9wYWNpdHk6IDAuMH0pO1xyXG5cclxuXHRcdFx0XHRiYXNlLiRvcmlnaW5hbEhlYWRlci5hZGRDbGFzcygndGFibGVGbG9hdGluZ0hlYWRlck9yaWdpbmFsJyk7XHJcblxyXG5cdFx0XHRcdGJhc2UuJG9yaWdpbmFsSGVhZGVyLmFmdGVyKGJhc2UuJGNsb25lZEhlYWRlcik7XHJcblxyXG5cdFx0XHRcdGJhc2UuJHByaW50U3R5bGUgPSAkKCc8c3R5bGUgdHlwZT1cInRleHQvY3NzXCIgbWVkaWE9XCJwcmludFwiPicgK1xyXG5cdFx0XHRcdFx0Jy50YWJsZUZsb2F0aW5nSGVhZGVye2Rpc3BsYXk6bm9uZSAhaW1wb3J0YW50O30nICtcclxuXHRcdFx0XHRcdCcudGFibGVGbG9hdGluZ0hlYWRlck9yaWdpbmFse3Bvc2l0aW9uOnN0YXRpYyAhaW1wb3J0YW50O30nICtcclxuXHRcdFx0XHRcdCc8L3N0eWxlPicpO1xyXG5cdFx0XHRcdGJhc2UuJGhlYWQuYXBwZW5kKGJhc2UuJHByaW50U3R5bGUpO1xyXG5cdFx0XHR9KTtcclxuXHRcdFx0XHJcblx0XHRcdGJhc2UuJGNsb25lZEhlYWRlci5maW5kKFwiaW5wdXQsIHNlbGVjdFwiKS5hdHRyKFwiZGlzYWJsZWRcIiwgdHJ1ZSk7XHJcblxyXG5cdFx0XHRiYXNlLnVwZGF0ZVdpZHRoKCk7XHJcblx0XHRcdGJhc2UudG9nZ2xlSGVhZGVycygpO1xyXG5cdFx0XHRiYXNlLmJpbmQoKTtcclxuXHRcdH07XHJcblxyXG5cdFx0YmFzZS5kZXN0cm95ID0gZnVuY3Rpb24gKCl7XHJcblx0XHRcdGJhc2UuJGVsLnVuYmluZCgnZGVzdHJveWVkJywgYmFzZS50ZWFyZG93bik7XHJcblx0XHRcdGJhc2UudGVhcmRvd24oKTtcclxuXHRcdH07XHJcblxyXG5cdFx0YmFzZS50ZWFyZG93biA9IGZ1bmN0aW9uKCl7XHJcblx0XHRcdGlmIChiYXNlLmlzU3RpY2t5KSB7XHJcblx0XHRcdFx0YmFzZS4kb3JpZ2luYWxIZWFkZXIuY3NzKCdwb3NpdGlvbicsICdzdGF0aWMnKTtcclxuXHRcdFx0fVxyXG5cdFx0XHQkLnJlbW92ZURhdGEoYmFzZS5lbCwgJ3BsdWdpbl8nICsgbmFtZSk7XHJcblx0XHRcdGJhc2UudW5iaW5kKCk7XHJcblxyXG5cdFx0XHRiYXNlLiRjbG9uZWRIZWFkZXIucmVtb3ZlKCk7XHJcblx0XHRcdGJhc2UuJG9yaWdpbmFsSGVhZGVyLnJlbW92ZUNsYXNzKCd0YWJsZUZsb2F0aW5nSGVhZGVyT3JpZ2luYWwnKTtcclxuXHRcdFx0YmFzZS4kb3JpZ2luYWxIZWFkZXIuY3NzKCd2aXNpYmlsaXR5JywgJ3Zpc2libGUnKTtcclxuXHRcdFx0YmFzZS4kcHJpbnRTdHlsZS5yZW1vdmUoKTtcclxuXHJcblx0XHRcdGJhc2UuZWwgPSBudWxsO1xyXG5cdFx0XHRiYXNlLiRlbCA9IG51bGw7XHJcblx0XHR9O1xyXG5cclxuXHRcdGJhc2UuYmluZCA9IGZ1bmN0aW9uKCl7XHJcblx0XHRcdGJhc2UuJHNjcm9sbGFibGVBcmVhLm9uKCdzY3JvbGwuJyArIG5hbWUsIGJhc2UudG9nZ2xlSGVhZGVycyk7XHJcblx0XHRcdGlmICghYmFzZS5pc1dpbmRvd1Njcm9sbGluZykge1xyXG5cdFx0XHRcdGJhc2UuJHdpbmRvdy5vbignc2Nyb2xsLicgKyBuYW1lICsgYmFzZS5pZCwgYmFzZS5zZXRQb3NpdGlvblZhbHVlcyk7XHJcblx0XHRcdFx0YmFzZS4kd2luZG93Lm9uKCdyZXNpemUuJyArIG5hbWUgKyBiYXNlLmlkLCBiYXNlLnRvZ2dsZUhlYWRlcnMpO1xyXG5cdFx0XHR9XHJcblx0XHRcdGJhc2UuJHNjcm9sbGFibGVBcmVhLm9uKCdyZXNpemUuJyArIG5hbWUsIGJhc2UudG9nZ2xlSGVhZGVycyk7XHJcblx0XHRcdGJhc2UuJHNjcm9sbGFibGVBcmVhLm9uKCdyZXNpemUuJyArIG5hbWUsIGJhc2UudXBkYXRlV2lkdGgpO1xyXG5cdFx0fTtcclxuXHJcblx0XHRiYXNlLnVuYmluZCA9IGZ1bmN0aW9uKCl7XHJcblx0XHRcdC8vIHVuYmluZCB3aW5kb3cgZXZlbnRzIGJ5IHNwZWNpZnlpbmcgaGFuZGxlIHNvIHdlIGRvbid0IHJlbW92ZSB0b28gbXVjaFxyXG5cdFx0XHRiYXNlLiRzY3JvbGxhYmxlQXJlYS5vZmYoJy4nICsgbmFtZSwgYmFzZS50b2dnbGVIZWFkZXJzKTtcclxuXHRcdFx0aWYgKCFiYXNlLmlzV2luZG93U2Nyb2xsaW5nKSB7XHJcblx0XHRcdFx0YmFzZS4kd2luZG93Lm9mZignLicgKyBuYW1lICsgYmFzZS5pZCwgYmFzZS5zZXRQb3NpdGlvblZhbHVlcyk7XHJcblx0XHRcdFx0YmFzZS4kd2luZG93Lm9mZignLicgKyBuYW1lICsgYmFzZS5pZCwgYmFzZS50b2dnbGVIZWFkZXJzKTtcclxuXHRcdFx0fVxyXG5cdFx0XHRiYXNlLiRzY3JvbGxhYmxlQXJlYS5vZmYoJy4nICsgbmFtZSwgYmFzZS51cGRhdGVXaWR0aCk7XHJcblx0XHR9O1xyXG5cclxuXHRcdC8vIFdlIGRlYm91bmNlIHRoZSBmdW5jdGlvbnMgYm91bmQgdG8gdGhlIHNjcm9sbCBhbmQgcmVzaXplIGV2ZW50c1xyXG5cdFx0YmFzZS5kZWJvdW5jZSA9IGZ1bmN0aW9uIChmbiwgZGVsYXkpIHtcclxuXHRcdFx0dmFyIHRpbWVyID0gbnVsbDtcclxuXHRcdFx0cmV0dXJuIGZ1bmN0aW9uICgpIHtcclxuXHRcdFx0XHR2YXIgY29udGV4dCA9IHRoaXMsIGFyZ3MgPSBhcmd1bWVudHM7XHJcblx0XHRcdFx0Y2xlYXJUaW1lb3V0KHRpbWVyKTtcclxuXHRcdFx0XHR0aW1lciA9IHNldFRpbWVvdXQoZnVuY3Rpb24gKCkge1xyXG5cdFx0XHRcdFx0Zm4uYXBwbHkoY29udGV4dCwgYXJncyk7XHJcblx0XHRcdFx0fSwgZGVsYXkpO1xyXG5cdFx0XHR9O1xyXG5cdFx0fTtcclxuXHJcblx0XHRiYXNlLnRvZ2dsZUhlYWRlcnMgPSBiYXNlLmRlYm91bmNlKGZ1bmN0aW9uICgpIHtcclxuXHRcdFx0aWYgKGJhc2UuJGVsKSB7XHJcblx0XHRcdFx0YmFzZS4kZWwuZWFjaChmdW5jdGlvbiAoKSB7XHJcblx0XHRcdFx0XHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxyXG5cdFx0XHRcdFx0XHRuZXdMZWZ0LFxyXG5cdFx0XHRcdFx0XHRuZXdUb3BPZmZzZXQgPSBiYXNlLmlzV2luZG93U2Nyb2xsaW5nID8gKFxyXG5cdFx0XHRcdFx0XHRcdFx0XHRpc05hTihiYXNlLm9wdGlvbnMuZml4ZWRPZmZzZXQpID9cclxuXHRcdFx0XHRcdFx0XHRcdFx0YmFzZS5vcHRpb25zLmZpeGVkT2Zmc2V0Lm91dGVySGVpZ2h0KCkgOlxyXG5cdFx0XHRcdFx0XHRcdFx0XHRiYXNlLm9wdGlvbnMuZml4ZWRPZmZzZXRcclxuXHRcdFx0XHRcdFx0XHRcdCkgOlxyXG5cdFx0XHRcdFx0XHRcdFx0YmFzZS4kc2Nyb2xsYWJsZUFyZWEub2Zmc2V0KCkudG9wICsgKCFpc05hTihiYXNlLm9wdGlvbnMuZml4ZWRPZmZzZXQpID8gYmFzZS5vcHRpb25zLmZpeGVkT2Zmc2V0IDogMCksXHJcblx0XHRcdFx0XHRcdG9mZnNldCA9ICR0aGlzLm9mZnNldCgpLFxyXG5cclxuXHRcdFx0XHRcdFx0c2Nyb2xsVG9wID0gYmFzZS4kc2Nyb2xsYWJsZUFyZWEuc2Nyb2xsVG9wKCkgKyBuZXdUb3BPZmZzZXQsXHJcblx0XHRcdFx0XHRcdHNjcm9sbExlZnQgPSBiYXNlLiRzY3JvbGxhYmxlQXJlYS5zY3JvbGxMZWZ0KCksXHJcblxyXG5cdFx0XHRcdFx0XHRoZWFkZXJIZWlnaHQsXHJcblxyXG5cdFx0XHRcdFx0XHRzY3JvbGxlZFBhc3RUb3AgPSBiYXNlLmlzV2luZG93U2Nyb2xsaW5nID9cclxuXHRcdFx0XHRcdFx0XHRcdHNjcm9sbFRvcCA+IG9mZnNldC50b3AgOlxyXG5cdFx0XHRcdFx0XHRcdFx0bmV3VG9wT2Zmc2V0ID4gb2Zmc2V0LnRvcCxcclxuXHRcdFx0XHRcdFx0bm90U2Nyb2xsZWRQYXN0Qm90dG9tO1xyXG5cclxuXHRcdFx0XHRcdGlmIChzY3JvbGxlZFBhc3RUb3ApIHtcclxuXHRcdFx0XHRcdFx0aGVhZGVySGVpZ2h0ID0gYmFzZS5vcHRpb25zLmNhY2hlSGVhZGVySGVpZ2h0ID8gYmFzZS5jYWNoZWRIZWFkZXJIZWlnaHQgOiBiYXNlLiRjbG9uZWRIZWFkZXIuaGVpZ2h0KCk7XHJcblx0XHRcdFx0XHRcdG5vdFNjcm9sbGVkUGFzdEJvdHRvbSA9IChiYXNlLmlzV2luZG93U2Nyb2xsaW5nID8gc2Nyb2xsVG9wIDogMCkgPFxyXG5cdFx0XHRcdFx0XHRcdChvZmZzZXQudG9wICsgJHRoaXMuaGVpZ2h0KCkgLSBoZWFkZXJIZWlnaHQgLSAoYmFzZS5pc1dpbmRvd1Njcm9sbGluZyA/IDAgOiBuZXdUb3BPZmZzZXQpKTtcclxuXHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHRpZiAoc2Nyb2xsZWRQYXN0VG9wICYmIG5vdFNjcm9sbGVkUGFzdEJvdHRvbSkge1xyXG5cdFx0XHRcdFx0XHRuZXdMZWZ0ID0gb2Zmc2V0LmxlZnQgLSBzY3JvbGxMZWZ0ICsgYmFzZS5vcHRpb25zLmxlZnRPZmZzZXQ7XHJcblx0XHRcdFx0XHRcdGJhc2UuJG9yaWdpbmFsSGVhZGVyLmNzcyh7XHJcblx0XHRcdFx0XHRcdFx0J3Bvc2l0aW9uJzogJ2ZpeGVkJyxcclxuXHRcdFx0XHRcdFx0XHQnbWFyZ2luLXRvcCc6IGJhc2Uub3B0aW9ucy5tYXJnaW5Ub3AsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJ3RvcCc6IDAsXHJcblx0XHRcdFx0XHRcdFx0J2xlZnQnOiBuZXdMZWZ0LFxyXG5cdFx0XHRcdFx0XHRcdCd6LWluZGV4JzogYmFzZS5vcHRpb25zLnpJbmRleFxyXG5cdFx0XHRcdFx0XHR9KTtcclxuXHRcdFx0XHRcdFx0YmFzZS5sZWZ0T2Zmc2V0ID0gbmV3TGVmdDtcclxuXHRcdFx0XHRcdFx0YmFzZS50b3BPZmZzZXQgPSBuZXdUb3BPZmZzZXQ7XHJcblx0XHRcdFx0XHRcdGJhc2UuJGNsb25lZEhlYWRlci5jc3MoJ2Rpc3BsYXknLCAnJyk7XHJcblx0XHRcdFx0XHRcdGlmICghYmFzZS5pc1N0aWNreSkge1xyXG5cdFx0XHRcdFx0XHRcdGJhc2UuaXNTdGlja3kgPSB0cnVlO1xyXG5cdFx0XHRcdFx0XHRcdC8vIG1ha2Ugc3VyZSB0aGUgd2lkdGggaXMgY29ycmVjdDogdGhlIHVzZXIgbWlnaHQgaGF2ZSByZXNpemVkIHRoZSBicm93c2VyIHdoaWxlIGluIHN0YXRpYyBtb2RlXHJcblx0XHRcdFx0XHRcdFx0YmFzZS51cGRhdGVXaWR0aCgpO1xyXG5cdFx0XHRcdFx0XHRcdCR0aGlzLnRyaWdnZXIoJ2VuYWJsZWRTdGlja2luZXNzLicgKyBuYW1lKTtcclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0XHRiYXNlLnNldFBvc2l0aW9uVmFsdWVzKCk7XHJcblx0XHRcdFx0XHR9IGVsc2UgaWYgKGJhc2UuaXNTdGlja3kpIHtcclxuXHRcdFx0XHRcdFx0YmFzZS4kb3JpZ2luYWxIZWFkZXIuY3NzKCdwb3NpdGlvbicsICdzdGF0aWMnKTtcclxuXHRcdFx0XHRcdFx0YmFzZS4kY2xvbmVkSGVhZGVyLmNzcygnZGlzcGxheScsICdub25lJyk7XHJcblx0XHRcdFx0XHRcdGJhc2UuaXNTdGlja3kgPSBmYWxzZTtcclxuXHRcdFx0XHRcdFx0YmFzZS5yZXNldFdpZHRoKCQoJ3RkLHRoJywgYmFzZS4kY2xvbmVkSGVhZGVyKSwgJCgndGQsdGgnLCBiYXNlLiRvcmlnaW5hbEhlYWRlcikpO1xyXG5cdFx0XHRcdFx0XHQkdGhpcy50cmlnZ2VyKCdkaXNhYmxlZFN0aWNraW5lc3MuJyArIG5hbWUpO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH0pO1xyXG5cdFx0XHR9XHJcblx0XHR9LCAwKTtcclxuXHJcblx0XHRiYXNlLnNldFBvc2l0aW9uVmFsdWVzID0gYmFzZS5kZWJvdW5jZShmdW5jdGlvbiAoKSB7XHJcblx0XHRcdHZhciB3aW5TY3JvbGxUb3AgPSBiYXNlLiR3aW5kb3cuc2Nyb2xsVG9wKCksXHJcblx0XHRcdFx0d2luU2Nyb2xsTGVmdCA9IGJhc2UuJHdpbmRvdy5zY3JvbGxMZWZ0KCk7XHJcblx0XHRcdGlmICghYmFzZS5pc1N0aWNreSB8fFxyXG5cdFx0XHRcdFx0d2luU2Nyb2xsVG9wIDwgMCB8fCB3aW5TY3JvbGxUb3AgKyBiYXNlLiR3aW5kb3cuaGVpZ2h0KCkgPiBiYXNlLiRkb2N1bWVudC5oZWlnaHQoKSB8fFxyXG5cdFx0XHRcdFx0d2luU2Nyb2xsTGVmdCA8IDAgfHwgd2luU2Nyb2xsTGVmdCArIGJhc2UuJHdpbmRvdy53aWR0aCgpID4gYmFzZS4kZG9jdW1lbnQud2lkdGgoKSkge1xyXG5cdFx0XHRcdHJldHVybjtcclxuXHRcdFx0fVxyXG5cdFx0XHRiYXNlLiRvcmlnaW5hbEhlYWRlci5jc3Moe1xyXG5cdFx0XHRcdCd0b3AnOiBiYXNlLnRvcE9mZnNldCAtIChiYXNlLmlzV2luZG93U2Nyb2xsaW5nID8gMCA6IHdpblNjcm9sbFRvcCksXHJcblx0XHRcdFx0J2xlZnQnOiBiYXNlLmxlZnRPZmZzZXQgLSAoYmFzZS5pc1dpbmRvd1Njcm9sbGluZyA/IDAgOiB3aW5TY3JvbGxMZWZ0KVxyXG5cdFx0XHR9KTtcclxuXHRcdH0sIDApO1xyXG5cclxuXHRcdGJhc2UudXBkYXRlV2lkdGggPSBiYXNlLmRlYm91bmNlKGZ1bmN0aW9uICgpIHtcclxuXHRcdFx0aWYgKCFiYXNlLmlzU3RpY2t5KSB7XHJcblx0XHRcdFx0cmV0dXJuO1xyXG5cdFx0XHR9XHJcblx0XHRcdC8vIENvcHkgY2VsbCB3aWR0aHMgZnJvbSBjbG9uZVxyXG5cdFx0XHRpZiAoIWJhc2UuJG9yaWdpbmFsSGVhZGVyQ2VsbHMpIHtcclxuXHRcdFx0XHRiYXNlLiRvcmlnaW5hbEhlYWRlckNlbGxzID0gJCgndGgsdGQnLCBiYXNlLiRvcmlnaW5hbEhlYWRlcik7XHJcblx0XHRcdH1cclxuXHRcdFx0aWYgKCFiYXNlLiRjbG9uZWRIZWFkZXJDZWxscykge1xyXG5cdFx0XHRcdGJhc2UuJGNsb25lZEhlYWRlckNlbGxzID0gJCgndGgsdGQnLCBiYXNlLiRjbG9uZWRIZWFkZXIpO1xyXG5cdFx0XHR9XHJcblx0XHRcdHZhciBjZWxsV2lkdGhzID0gYmFzZS5nZXRXaWR0aChiYXNlLiRjbG9uZWRIZWFkZXJDZWxscyk7XHJcblx0XHRcdGJhc2Uuc2V0V2lkdGgoY2VsbFdpZHRocywgYmFzZS4kY2xvbmVkSGVhZGVyQ2VsbHMsIGJhc2UuJG9yaWdpbmFsSGVhZGVyQ2VsbHMpO1xyXG5cclxuXHRcdFx0Ly8gQ29weSByb3cgd2lkdGggZnJvbSB3aG9sZSB0YWJsZVxyXG5cdFx0XHRiYXNlLiRvcmlnaW5hbEhlYWRlci5jc3MoJ3dpZHRoJywgYmFzZS4kY2xvbmVkSGVhZGVyLndpZHRoKCkpO1xyXG5cclxuXHRcdFx0Ly8gSWYgd2UncmUgY2FjaGluZyB0aGUgaGVpZ2h0LCB3ZSBuZWVkIHRvIHVwZGF0ZSB0aGUgY2FjaGVkIHZhbHVlIHdoZW4gdGhlIHdpZHRoIGNoYW5nZXNcclxuXHRcdFx0aWYgKGJhc2Uub3B0aW9ucy5jYWNoZUhlYWRlckhlaWdodCkge1xyXG5cdFx0XHRcdGJhc2UuY2FjaGVkSGVhZGVySGVpZ2h0ID0gYmFzZS4kY2xvbmVkSGVhZGVyLmhlaWdodCgpO1xyXG5cdFx0XHR9XHJcblx0XHR9LCAwKTtcclxuXHJcblx0XHRiYXNlLmdldFdpZHRoID0gZnVuY3Rpb24gKCRjbG9uZWRIZWFkZXJzKSB7XHJcblx0XHRcdHZhciB3aWR0aHMgPSBbXTtcclxuXHRcdFx0JGNsb25lZEhlYWRlcnMuZWFjaChmdW5jdGlvbiAoaW5kZXgpIHtcclxuXHRcdFx0XHR2YXIgd2lkdGgsICR0aGlzID0gJCh0aGlzKTtcclxuXHJcblx0XHRcdFx0aWYgKCR0aGlzLmNzcygnYm94LXNpemluZycpID09PSAnYm9yZGVyLWJveCcpIHtcclxuXHRcdFx0XHRcdHZhciBib3VuZGluZ0NsaWVudFJlY3QgPSAkdGhpc1swXS5nZXRCb3VuZGluZ0NsaWVudFJlY3QoKTtcclxuXHRcdFx0XHRcdGlmKGJvdW5kaW5nQ2xpZW50UmVjdC53aWR0aCkge1xyXG5cdFx0XHRcdFx0XHR3aWR0aCA9IGJvdW5kaW5nQ2xpZW50UmVjdC53aWR0aDsgLy8gIzM5OiBib3JkZXItYm94IGJ1Z1xyXG5cdFx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdFx0d2lkdGggPSBib3VuZGluZ0NsaWVudFJlY3QucmlnaHQgLSBib3VuZGluZ0NsaWVudFJlY3QubGVmdDsgLy8gaWU4IGJ1ZzogZ2V0Qm91bmRpbmdDbGllbnRSZWN0KCkgZG9lcyBub3QgaGF2ZSBhIHdpZHRoIHByb3BlcnR5XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdHZhciAkb3JpZ1RoID0gJCgndGgnLCBiYXNlLiRvcmlnaW5hbEhlYWRlcik7XHJcblx0XHRcdFx0XHRpZiAoJG9yaWdUaC5jc3MoJ2JvcmRlci1jb2xsYXBzZScpID09PSAnY29sbGFwc2UnKSB7XHJcblx0XHRcdFx0XHRcdGlmICh3aW5kb3cuZ2V0Q29tcHV0ZWRTdHlsZSkge1xyXG5cdFx0XHRcdFx0XHRcdHdpZHRoID0gcGFyc2VGbG9hdCh3aW5kb3cuZ2V0Q29tcHV0ZWRTdHlsZSh0aGlzLCBudWxsKS53aWR0aCk7XHJcblx0XHRcdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHRcdFx0Ly8gaWU4IG9ubHlcclxuXHRcdFx0XHRcdFx0XHR2YXIgbGVmdFBhZGRpbmcgPSBwYXJzZUZsb2F0KCR0aGlzLmNzcygncGFkZGluZy1sZWZ0JykpO1xyXG5cdFx0XHRcdFx0XHRcdHZhciByaWdodFBhZGRpbmcgPSBwYXJzZUZsb2F0KCR0aGlzLmNzcygncGFkZGluZy1yaWdodCcpKTtcclxuXHRcdFx0XHRcdFx0XHQvLyBOZWVkcyBtb3JlIGludmVzdGlnYXRpb24gLSB0aGlzIGlzIGFzc3VtaW5nIGNvbnN0YW50IGJvcmRlciBhcm91bmQgdGhpcyBjZWxsIGFuZCBpdCdzIG5laWdoYm91cnMuXHJcblx0XHRcdFx0XHRcdFx0dmFyIGJvcmRlciA9IHBhcnNlRmxvYXQoJHRoaXMuY3NzKCdib3JkZXItd2lkdGgnKSk7XHJcblx0XHRcdFx0XHRcdFx0d2lkdGggPSAkdGhpcy5vdXRlcldpZHRoKCkgLSBsZWZ0UGFkZGluZyAtIHJpZ2h0UGFkZGluZyAtIGJvcmRlcjtcclxuXHRcdFx0XHRcdFx0fVxyXG5cdFx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdFx0d2lkdGggPSAkdGhpcy53aWR0aCgpO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0d2lkdGhzW2luZGV4XSA9IHdpZHRoO1xyXG5cdFx0XHR9KTtcclxuXHRcdFx0cmV0dXJuIHdpZHRocztcclxuXHRcdH07XHJcblxyXG5cdFx0YmFzZS5zZXRXaWR0aCA9IGZ1bmN0aW9uICh3aWR0aHMsICRjbG9uZWRIZWFkZXJzLCAkb3JpZ0hlYWRlcnMpIHtcclxuXHRcdFx0JGNsb25lZEhlYWRlcnMuZWFjaChmdW5jdGlvbiAoaW5kZXgpIHtcclxuXHRcdFx0XHR2YXIgd2lkdGggPSB3aWR0aHNbaW5kZXhdO1xyXG5cdFx0XHRcdCRvcmlnSGVhZGVycy5lcShpbmRleCkuY3NzKHtcclxuXHRcdFx0XHRcdCdtaW4td2lkdGgnOiB3aWR0aCxcclxuXHRcdFx0XHRcdCdtYXgtd2lkdGgnOiB3aWR0aFxyXG5cdFx0XHRcdH0pO1xyXG5cdFx0XHR9KTtcclxuXHRcdH07XHJcblxyXG5cdFx0YmFzZS5yZXNldFdpZHRoID0gZnVuY3Rpb24gKCRjbG9uZWRIZWFkZXJzLCAkb3JpZ0hlYWRlcnMpIHtcclxuXHRcdFx0JGNsb25lZEhlYWRlcnMuZWFjaChmdW5jdGlvbiAoaW5kZXgpIHtcclxuXHRcdFx0XHR2YXIgJHRoaXMgPSAkKHRoaXMpO1xyXG5cdFx0XHRcdCRvcmlnSGVhZGVycy5lcShpbmRleCkuY3NzKHtcclxuXHRcdFx0XHRcdCdtaW4td2lkdGgnOiAkdGhpcy5jc3MoJ21pbi13aWR0aCcpLFxyXG5cdFx0XHRcdFx0J21heC13aWR0aCc6ICR0aGlzLmNzcygnbWF4LXdpZHRoJylcclxuXHRcdFx0XHR9KTtcclxuXHRcdFx0fSk7XHJcblx0XHR9O1xyXG5cclxuXHRcdGJhc2Uuc2V0T3B0aW9ucyA9IGZ1bmN0aW9uIChvcHRpb25zKSB7XHJcblx0XHRcdGJhc2Uub3B0aW9ucyA9ICQuZXh0ZW5kKHt9LCBkZWZhdWx0cywgb3B0aW9ucyk7XHJcblx0XHRcdGJhc2UuJHdpbmRvdyA9ICQoYmFzZS5vcHRpb25zLm9ialdpbmRvdyk7XHJcblx0XHRcdGJhc2UuJGhlYWQgPSAkKGJhc2Uub3B0aW9ucy5vYmpIZWFkKTtcclxuXHRcdFx0YmFzZS4kZG9jdW1lbnQgPSAkKGJhc2Uub3B0aW9ucy5vYmpEb2N1bWVudCk7XHJcblx0XHRcdGJhc2UuJHNjcm9sbGFibGVBcmVhID0gJChiYXNlLm9wdGlvbnMuc2Nyb2xsYWJsZUFyZWEpO1xyXG5cdFx0XHRiYXNlLmlzV2luZG93U2Nyb2xsaW5nID0gYmFzZS4kc2Nyb2xsYWJsZUFyZWFbMF0gPT09IGJhc2UuJHdpbmRvd1swXTtcclxuXHRcdH07XHJcblxyXG5cdFx0YmFzZS51cGRhdGVPcHRpb25zID0gZnVuY3Rpb24gKG9wdGlvbnMpIHtcclxuXHRcdFx0YmFzZS5zZXRPcHRpb25zKG9wdGlvbnMpO1xyXG5cdFx0XHQvLyBzY3JvbGxhYmxlQXJlYSBtaWdodCBoYXZlIGNoYW5nZWRcclxuXHRcdFx0YmFzZS51bmJpbmQoKTtcclxuXHRcdFx0YmFzZS5iaW5kKCk7XHJcblx0XHRcdGJhc2UudXBkYXRlV2lkdGgoKTtcclxuXHRcdFx0YmFzZS50b2dnbGVIZWFkZXJzKCk7XHJcblx0XHR9O1xyXG5cclxuXHRcdC8vIFJ1biBpbml0aWFsaXplclxyXG5cdFx0YmFzZS5pbml0KCk7XHJcblx0fVxyXG5cclxuXHQvLyBBIHBsdWdpbiB3cmFwcGVyIGFyb3VuZCB0aGUgY29uc3RydWN0b3IsXHJcblx0Ly8gcHJldmVudGluZyBhZ2FpbnN0IG11bHRpcGxlIGluc3RhbnRpYXRpb25zXHJcblx0JC5mbltuYW1lXSA9IGZ1bmN0aW9uICggb3B0aW9ucyApIHtcclxuXHRcdHJldHVybiB0aGlzLmVhY2goZnVuY3Rpb24gKCkge1xyXG5cdFx0XHR2YXIgaW5zdGFuY2UgPSAkLmRhdGEodGhpcywgJ3BsdWdpbl8nICsgbmFtZSk7XHJcblx0XHRcdGlmIChpbnN0YW5jZSkge1xyXG5cdFx0XHRcdGlmICh0eXBlb2Ygb3B0aW9ucyA9PT0gJ3N0cmluZycpIHtcclxuXHRcdFx0XHRcdGluc3RhbmNlW29wdGlvbnNdLmFwcGx5KGluc3RhbmNlKTtcclxuXHRcdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdFx0aW5zdGFuY2UudXBkYXRlT3B0aW9ucyhvcHRpb25zKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH0gZWxzZSBpZihvcHRpb25zICE9PSAnZGVzdHJveScpIHtcclxuXHRcdFx0XHQkLmRhdGEodGhpcywgJ3BsdWdpbl8nICsgbmFtZSwgbmV3IFBsdWdpbiggdGhpcywgb3B0aW9ucyApKTtcclxuXHRcdFx0fVxyXG5cdFx0fSk7XHJcblx0fTtcclxuXHJcbn0pKGpRdWVyeSwgd2luZG93KTtcclxuIiwiLypnbG9iYWwgalF1ZXJ5Ki9cblxuaW1wb3J0IHN0aWNreVRhYmxlSGVhZGVycyBmcm9tICdzdGlja3ktdGFibGUtaGVhZGVycyc7XG5cbndpbmRvdy5hZGRFdmVudExpc3RlbmVyKFwiRE9NQ29udGVudExvYWRlZFwiLCAoKSA9PiB7XG5cblx0LyoqXG5cdCAqIEBwYXJhbSB7Tm9kZUxpc3R9IGVsZW1lbnRTXG5cdCAqL1xuXHRjb25zdCBlbGVtZW50cyA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJy5qcy1vdGdzLXRhYmxlLXN0aWNreS1oZWFkZXInKTtcblx0Y29uc3QgYXJncyA9IHtcblx0XHRmaXhlZE9mZnNldDogalF1ZXJ5KCcjd3BhZG1pbmJhcicpXG5cdH07XG5cblx0LyoqXG5cdCAqIEBwYXJhbSB7RWxlbWVudH0gZWxlbWVudFxuXHQgKi9cblx0ZWxlbWVudHMuZm9yRWFjaChlbGVtZW50ID0+IHtcblxuXHRcdGpRdWVyeShlbGVtZW50KS5zdGlja3lUYWJsZUhlYWRlcnMoYXJncyk7XG5cblx0fSk7XG59KTtcbiJdLCJzb3VyY2VSb290IjoiIn0=