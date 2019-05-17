(function($){
    
$(document).ready(function(){
    
    var last_sort = 'desc';
    
    var send_editor = function( content ){
        if( typeof parent.send_to_editor === 'function' ){
            parent.send_to_editor( content );
        }else{
            alert( 'Editor does not exist. Cannot insert content !' );
        }
    }
    
    var close_window = function(){
        if( typeof parent.tb_remove === 'function' ){
            parent.tb_remove();
        }
    }
    
    var sort = function( ele, orderby ){
        var total = ele.length;
        while( total ){
            ele.each(function(){
                var $cur = $(this);
                var $next = $cur.next();
                if( $next.length ){
                    var cur_name = $cur.attr( 'data-name' ).toLowerCase();
                    var nxt_name = $next.attr( 'data-name' ).toLowerCase();
                    if( ( orderby == 'asc' && cur_name > nxt_name ) || ( orderby == 'desc' && cur_name < nxt_name ) ){
                        $next.after( $cur );
                    }
                }
            });
            total--;
        }
    }
    
    $('.sc_shortcode_name').append('<span class="sc_toggle"></span>');
    
    $( document ).on( 'click', '.sc_insert', function(){
        
        var params = '';
        var scname = $(this).closest( '.sc_shortcode' ).attr( 'data-name' );
        var sc = '';
        
        $(this).parent().children().find('input[type="text"]').each(function(){
            if($(this).val() != ''){
                attr = $(this).attr('data-param');
                val = $(this).val().replace( /\"/g, '' );
                params += attr + '="' + val + '" ';
            }
        });
        
        sc = '[sc name="' + scname + '" ' + params + ']';
        send_editor( sc );
        close_window();
        
    });
    
    $( document ).on( 'click', '.sc_quick_insert', function(){
        
        var scname = $(this).closest( '.sc_shortcode' ).attr( 'data-name' );
        var sc = '[sc name="' + scname + '"]';
        
        send_editor( sc );
        close_window();
        
    });
    
    $( document ).on( 'click', '.sc_shortcode_name', function(e){
        $('.sc_params').slideUp();
        if($(this).next('.sc_params').is(':visible')){
            $(this).next('.sc_params').slideUp();
        }else{
            $(this).next('.sc_params').slideDown();
        }
    });
    
    $( document ).on( 'change', '.coffee_amt', function(){
        var btn = $( '.buy_coffee_btn' );
        btn.attr( 'href', btn.data( 'link' ) + $(this).val() );
    });
    
    $( document ).on( 'click', '.sort_btn', function(){
        last_sort = ( last_sort == 'asc' ) ? 'desc' : 'asc';
        sort( $( '.sc_shortcode' ), last_sort );
    });
    
    $( document ).on( 'keyup', '.search_box', function(){
        var re = new RegExp($(this).val(), 'gi');
        $('.sc_wrap .sc_shortcode').each(function(){
            var name = $(this).attr('data-name');
            if( name.match(re) === null ){
                $(this).hide();
            }else{
                $(this).show();
            }
        });
        
        var visible = $('.sc_wrap .sc_shortcode:visible').length;
        var $no_scs_msg = $('.sc_wrap').find('p');
        if( visible == 0 ){
            if( $no_scs_msg.length == 0 ){
                $('.sc_wrap').append( '<p align="center"><i>No shortcodes match search term !</i></p>' );
            }
        }else{
            $no_scs_msg.remove();
        }
    });
    
    $( document ).on( 'click', '.tags_filter_btn', function(){
        $( '.sc_tags' ).slideToggle();
    });
    
    $( document ).on( 'click', '.sc_tags li', function(){
        
        $(this).toggleClass( 'active' );
        var tags_sel = [];
        
        $('.sc_tags li.active').each(function(){
            var tag = $(this).attr('data-value');
            tags_sel.push(tag);
        });
        
        if(tags_sel.length == 0){
            $('.sc_wrap .sc_shortcode').show();
            return true;
        }
        
        $('.sc_wrap .sc_shortcode').each(function(){
            var tags = $(this).attr('data-tags');
            var tags_split = $.map(tags.split(','), $.trim);
            var has_tag = false;
            
            $.each(tags_sel, function(i, tag){
                if(tags_split.includes(tag)){
                    has_tag = true;
                    return true;
                }
            });
            
            if(has_tag){
                $(this).show();
            }else{
                $(this).hide();
            }
            
        });
        
    });
    
});
    
})( jQuery );