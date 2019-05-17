;(function( $ ){
    $( function(){
        $( '.notice.wicked-dismissable .wicked-dismiss' ).click( function(){
            $( this ).parents( '.notice' ).slideUp();
            var key = $( this ).attr( 'data-key' );
            $.ajax(
                ajaxurl,
                {
                    data: {
                        'action':   'wicked_folders_dismiss_message',
                        'key':      key
                    },
                    method: 'POST',
                    dataType: 'json'
                }
            );
            return false;
        } );
    } );
})( jQuery );
