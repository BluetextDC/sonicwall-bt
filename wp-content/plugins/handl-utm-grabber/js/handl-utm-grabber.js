jQuery(function($) {
	$.each([ 'utm_source','utm_medium','utm_term', 'utm_content', 'utm_campaign', 'gclid', 'email', 'username' ], function( i,v ) {
		var curval = Cookies.get(v)
		
		if (curval != undefined) {
			curval = decodeURIComponent(curval)
			if (v == 'username') {
				//Maybe this should apply to all... We'll see...
				curval = curval.replace(/\+/g, ' ')
			}
			
			$('input[name=\"'+v+'\"]').val(curval)
			$('input#'+v).val(curval)
			$('input.'+v).val(curval)
		}
		
	});
	
	$('.utm-out').each(function(){
		var merged = $.extend( {}, handl_utm, getSearchParams(this.href) )
		this.search = ""
		if ( !$.isEmptyObject(merged) )
			this.href += "?"+$.param(merged)
		
	});
});

function getSearchParams(url,k){
	var p={};
	var a = document.createElement('a');
    a.href = url;
	a.search.replace(/[?&]+([^=&]+)=([^&]*)/gi,function(s,k,v){p[k]=v})
	return k?p[k]:p;
}
