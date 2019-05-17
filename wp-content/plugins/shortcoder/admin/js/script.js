(function($){
$(document).ready(function(){
    
    var delete_ctext = 'Are you sure want to delete this shortcode ?';
    var last_sort = 'desc';
    var tags_filt_api = false;
    
    var init = function(){
        if(window.sc_cm_editor){
            window.sc_cm = CodeMirror.fromTextArea( document.getElementById( 'sc_content' ), {
                lineNumbers: true,
                mode: "htmlmixed",
                indentWithTabs: false,
                lineWrapping: true,
                styleActiveLine: true
            });
            sc_cm.setSize( null, 500 );
            sc_cm.on( 'change', function(){
                sc_cm.save();
            });
        }
        
        if( $.fn.selectize ){
            
            $('.sc_edit_tags').selectize({
                create: true
            });
            
            if( $('.sc_tags_filter').length ){
                
                $tags_filter_ele = $('.sc_tags_filter').selectize({
                    onChange: filter_list_tags
                });
                
                tags_filt_api = $tags_filter_ele[0].selectize;
                
            }
        }
        
        var $sc_copy_field = $('.sc_copy_field');
        if( $sc_copy_field.length ){
            var sc_code = $sc_copy_field.val();
            $sc_copy_field.width(sc_code.length * 6);
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
    
    var insert_in_editor = function( data ){
        if( window.sc_cm_editor ){
            var doc = window.sc_cm.getDoc();
            doc.replaceRange( data, doc.getCursor() );
        }else{
            send_to_editor( data );
        }
    }
    
    var filter_list_tags = function(){
        
        var sel_tags = tags_filt_api.items;
        var $sc_list = $( '.sc_list > li' );
        
        if( sel_tags.length == 0){
            $sc_list.show();
            return true;
        }
        
        $sc_list.each(function(){
            var $sc_item = $( this );
            var sc_tags = $sc_item.attr( 'data-tags' );
            
            if( typeof sc_tags === 'undefined' ){
                $sc_item.hide();
                return true;
            }else{
                $sc_item.show();
            }
            
            var sc_tags_split = sc_tags.split( ',' );
            var has_tag = false;
            
            $.each( sel_tags, function( i, tag ){
                if( sc_tags_split.includes( tag ) ){
                    has_tag = true;
                    return true;
                }
            });
            
            if( has_tag ){
                $sc_item.show();
            }else{
                $sc_item.hide();
            }
            
        });
    }
    
    $( document ).on( 'click', '.sc_delete', function(e){
        
        e.preventDefault();
        
        var del_btn = $(this);
        var href = del_btn.attr( 'href' );
        var confirm_user = confirm( delete_ctext );
        
        if( confirm_user ){
            
            var ajax = $.get( href );
            del_btn.addClass( 'spin' );
            
            ajax.done(function( data ){
                if( data.search( 'DELETED' ) != -1 ){
                    del_btn.closest( 'li' ).fadeOut( 'slow', function(){
                        $(this).remove();
                    });
                }else{
                    alert( 'Delete failed ! - ' + data );
                }
            });
            
            ajax.fail(function(){
                alert( 'Auth failed !' );
            });
            
        }
        
    });
    
    $( document ).on( 'click', '.sc_delete_ep', function(e){
        
        e.preventDefault();
        
        var $delete_btn = $(this);
        var href = $delete_btn.attr( 'href' );
        var confirm_user = confirm( delete_ctext );
        
        if( confirm_user ){
            
            var ajax = $.get( href );
            $delete_btn.addClass( 'spin' );
            
            ajax.done(function( data ){
                if( data.search( 'DELETED' ) != -1 ){
                    var back_href = $( '.sc_back_btn' ).attr( 'href' );
                    window.location = back_href + '&msg=3';
                }else{
                    alert( 'Delete failed ! - ' + data );
                }
            });
            
            ajax.fail(function(){
                alert( 'Auth failed !' );
            });
            
            $delete_btn.removeClass( 'spin' );
            
        }
        
    });
    
    $( document ).on( 'click', '.sc_copy', function(e){
        
        e.preventDefault();
        
        var btn = $(this);
        var box = btn.closest( 'li' ).find( '.sc_copy_box' );
        
        $( '.sc_copy_box' ).not( box ).hide();
        
        box.fadeToggle();
        box.select();
        
    });
    
    $(window).load(function(){
        
        var insert_button = function(){
            return '<button class="button button-primary sc_insert_params"><span class="dashicons dashicons-plus"></span> Insert shortcode paramerters <span class="dashicons dashicons-arrow-down"></span></button>';
        }
        
        $( '.wp-media-buttons' ).append( insert_button );
        $( '.sc_editor_list' ).appendTo( '.wp-media-buttons' );
        
        if( window.sc_cm_editor ){
            $( '.sc_cm_menu' ).append( insert_button );
            $( '.sc_editor_list' ).appendTo( '.sc_cm_menu' );
        }
        
        $( '.params_wrap' ).appendTo( 'body' );
        
    });
    
    $( document ).on( 'click', '.sc_insert_params', function(e){
        
        e.preventDefault();
        
        var offset = $(this).offset();
        var mtop = offset.top + $(this).outerHeight();
        
        $( '.params_wrap' ).css({
            top: mtop,
            left: offset.left
        }).toggle();
    });
    
    $( document ).on( 'click', '.sc_tags_filt_icon', function(e){
        
        e.preventDefault();
        $( this ).closest( '.sc_tags_filt_btn' ).toggleClass( 'active' );
        
    });
    
    $( document ).on( 'click', '.sc_tags_list li', function(e){
        
        if(tags_filt_api){
            var tag = $(this).attr( 'data-tag-id' );
            tags_filt_api.addItem( tag );
            $( '.sc_tags_filt_btn' ).addClass( 'active' );
        }
        
    });
    
    $( document ).on( 'click', '.sc_copy_field', function(e){
        e.preventDefault();
        $(this).select();
    });
    
    $( document ).on( 'click', '.cp_btn', function(){
        
        var $cp_box = $( '.cp_box' );
        var $cp_info = $( '.cp_info' );
        var param_val = $cp_box.val().trim();
        
        if( param_val != '' && $cp_box[0].checkValidity() ){
            insert_in_editor( '%%' + param_val + '%%' );
            $cp_info.removeClass( 'red' );
            $( '.params_wrap' ).hide();
        }else{
            $cp_info.addClass( 'red' );
        }
        
    });
    
    $( document ).on( 'click', '.cf_btn', function(){
        
        var $cf_box = $( '.cf_box' );
        var $cf_info = $( '.cf_info' );
        var param_val = $cf_box.val().trim();
        
        if( param_val != '' && $cf_box[0].checkValidity() ){
            insert_in_editor( '$$custom_field:' + param_val + '$$' );
            $cf_info.removeClass( 'red' );
            $( '.params_wrap' ).hide();
        }else{
            $cf_info.addClass( 'red' );
        }
        
    });
    
    $( document ).on( 'click', '.wp_params li', function(){
        insert_in_editor('$$' + $(this).data( 'id' ) + '$$');
        $( '.params_wrap' ).hide();
    });
    
    $( document ).on( 'change', '.coffee_amt', function(){
        var btn = $( '.buy_coffee_btn' );
        btn.attr( 'href', btn.data( 'link' ) + $(this).val() );
    });
    
    $( document ).on( 'click', '.sort_btn', function(){
        last_sort = ( last_sort == 'asc' ) ? 'desc' : 'asc';
        sort( $( '.sc_list > li' ), last_sort );
        $( '.sort_icon' ).toggleClass( 'dashicons-arrow-down-alt' );
        $( '.sort_icon' ).toggleClass( 'dashicons-arrow-up-alt' );
    });
    
    $( document ).on( 'change', '#import', function(){
        if( !confirm( $( '.import_desc' ).text() ) ){
            return false;
        }
        
        if( confirm( $( '.import_desc2' ).text() ) ){
            $( '#fresh_import' ).prop( 'checked', true );
        }else{
            $( '#fresh_import' ).prop( 'checked', false );
        }
        
        $( '#import_form' ).submit();
    });
    
    $( document ).on( 'change', '.sc_editor', function(e){
        window.location = window.location + '&editor=' + $(this).val();
    });
    
    $( document ).on( 'click', '.search_btn', function(e){
        var $search_box = $(this).find('.search_box');
        if(e.target === $search_box[0]){
            return false;
        }
        $(this).toggleClass('active');
        $search_box.focus();
        
    });
    
    $( document ).on( 'keyup', '.search_box', function(){
        var search_term = $(this).val();
        var re = new RegExp(search_term, 'gi');
        $('.sc_list > li').each(function(){
            var name = $(this).attr('data-name');
            if(name.match(re) === null){
                $(this).hide();
            }else{
                $(this).show();
            }
        });
        
        if(search_term){
            $(this).parent().addClass('filtered');
        }else{
            $(this).parent().removeClass('filtered');
        }
        
        var visible = $('.sc_list > li:visible').length;
        var $no_scs_msg = $('.sc_list').find('p');
        if( visible == 0 ){
            if( $no_scs_msg.length == 0 ){
                $('.sc_list').append( '<p align="center" class="search_empty_msg"><i>No shortcodes match search term !</i></p>' );
            }
        }else{
            $no_scs_msg.remove();
        }
        
    });
    
    $( document ).on( 'click', '.sc_note_btn', function(e){
        $('.sc_note').slideToggle();
    });
    
    init();
    
});
})( jQuery );