/******* Do not edit this file *******
Simple Custom CSS and JS - by Silkypress.com
Saved: Dec 28 2018 | 11:41:57 */
jQuery(function($){

$(document).ready(function(){

	// instantly hide sub menu on next item click
    $('#mega-menu-avia &gt; .mega-menu-item &gt; a').click(function() {
		$('#mega-menu-avia &gt; .mega-menu-item').removeClass('on');
    	$(this).parent().removeClass('mega-toggle-on').addClass('on');
    });
	
    // height of custom solutions mega menu
    $('#mega-menu-avia .solutions').next().css({'height':'449'});
    
    // height of custom partners mega menu
    $('#mega-menu-avia .partners').next().css({'height':'470'});
    
});

});