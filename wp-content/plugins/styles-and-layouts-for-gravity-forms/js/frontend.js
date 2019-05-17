(function($){
wp.customize.bind( 'preview-ready', function() {
	wp.customize.preview.bind( 'stlaFormSelectionStatus', function( message ) {
	   if( message === 1){
		   $('.stla-partial-formwrapper-shortcut').css('display', 'none');
		   $('.gform_wrapper').addClass('stla-live-edit-disabled');
	   }
	} );
  } );
})(jQuery);