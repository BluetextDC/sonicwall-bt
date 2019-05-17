/**
 * Javascript used on the media-new.php admin page.
 */
(function($){
    $(function(){

        wpUploaderInit.multipart_params['wicked_folder_id'] = $( '#wicked-upload-folder' ).val();

        $( '#file-form' ).on( 'change', '#wicked-upload-folder', function(){
            var id = $( this ).val();
            wpUploaderInit.multipart_params['wicked_folder_id'] = id;
        } );
        
    });
})(jQuery);
