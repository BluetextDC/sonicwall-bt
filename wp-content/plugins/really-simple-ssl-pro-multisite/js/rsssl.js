jQuery(document).ready(function($) {
    'use strict';

    var rsssl_interval = 3000;
    var progress = rsssl_ajax.progress;
    var progressBar = $('#rsssl-scan-list').find('.progress-bar');

    progressBar.css({width: progress+'%'});
    setup_scan();
    function setup_scan() {
        get_scan_progress();
        window.setInterval(function () {
            get_scan_progress()
        }, rsssl_interval);
    }

    function get_scan_progress() {

        if (progress>=100) return;

        progressBar.removeClass('progress-bar-success');

        $('#rsssl-scan-output').html("");
        $.post(
            rsssl_ajax.ajaxurl,
            {
                action: 'get_scan_progress'
            },
            function (response) {
                var obj;

                if (response) {

                    obj = jQuery.parseJSON( response );

                    progress = parseInt(obj['progress']);
                    // var getPercent = progress / 100;
                    // var getProgressWrapWidth = $('#rsssl-scan-list').width();
                    // var progressTotal = getPercent * getProgressWrapWidth;

                    if (progress>=100) {
                        progressBar.html(obj['action']);
                        progressBar.css({width: progress+'%'});
                        progressBar.addClass('progress-bar-success');
                        $('#rsssl-scan-output').html(obj['output']);

                    } else {
                        progressBar.html(obj['action']);
                        progressBar.css({width: progress+'%'});

                        //rssslAnimateProgress(progress, obj['action'])
                    }
                }
            }
        );

    }


    $(document).on('click', '.glyphdown', function(){
//$(".glyphdown").click(function(){
        var panel = $(this).closest(".blockedurls").find(".panel-body");
        var icon = $(this).find(".glyphicon");
        if (panel.is(':visible')) {
            icon.removeClass("glyphicon-menu-up");
            icon.addClass("glyphicon-menu-down");
            panel.slideUp();
        } else {
            icon.removeClass("glyphicon-menu-down");
            icon.addClass("glyphicon-menu-up");
            panel.slideDown();
        }

    });

    /*tooltips*/

    $("body").tooltip({
        selector: '.tooltip',
        placement: 'bottom',
    });


    $('#fix-post-modal').on('show.bs.modal', function(e) {
        $(this).find("#start-fix-post").data('id', $(e.relatedTarget).data('id'));
        $(this).find("#start-fix-post").data('url', $(e.relatedTarget).data('url'));
        $(this).find("#start-fix-post").data('path', $(e.relatedTarget).data('path'));
    });

    $('#fix-postmeta-modal').on('show.bs.modal', function(e) {
        $(this).find("#start-fix-postmeta").data('id', $(e.relatedTarget).data('id'));
        $(this).find("#start-fix-postmeta").data('url', $(e.relatedTarget).data('url'));
        $(this).find("#start-fix-postmeta").data('path', $(e.relatedTarget).data('path'));
    });

    $('#fix-file-modal').on('show.bs.modal', function(e) {
        $(this).find("#start-fix-file").data('id', $(e.relatedTarget).data('id'));
        $(this).find("#start-fix-file").data('url', $(e.relatedTarget).data('url'));
        $(this).find("#start-fix-file").data('path', $(e.relatedTarget).data('path'));
    });

    $('#ignore-url-modal').on('show.bs.modal', function(e) {
        $(this).find("#start-ignore-url").data('url', $(e.relatedTarget).data('url'));
        $(this).find("#start-ignore-url").data('id', $(e.relatedTarget).data('id'));
        $(this).find("#start-ignore-url").data('path', $(e.relatedTarget).data('path'));

    });

    $('#fix-widget-modal').on('show.bs.modal', function(e) {
        $(this).find("#start-fix-widget").data('id', $(e.relatedTarget).data('id'));
        $(this).find("#start-fix-widget").data('url', $(e.relatedTarget).data('url'));
        $(this).find("#start-fix-widget").data('path', $(e.relatedTarget).data('path'));
    });

//the ignore button in mixed content scanner

//$ (".ignore").click(function() {
//	alert( "Hier komt een bazenfunctie die ze ook echt ingored" );
//	var url = $(this).data('url');
//	var action = 'ignore_url';

//		$.post(
//			rsssl_ajax.ajaxurl,
//			{}
//		)
//});

//$ (".deletefile").click(function() {
//	alert( "Are you sure? This will replace the following string [STRING] with this [STRING2]]" );
//});

    $(document).on('click', "#start-fix-post",function(e){

        $(this).prop('disabled', true);
        var post_id = $(this).data('id');
        var path = $(this).data('path');
        var url = $(this).data('url');
        var action = 'fix_post';
        var token = $(this).data("token");

        $.post(
            rsssl_ajax.ajaxurl,
            {
                action : action,
                token : token,
                url : url,
                path : path,
                post_id: post_id,
            },

            function( response ) {

                if (response.success){
                    rsssl_remove_from_results(url, path, post_id);
                    $("#fix-post-modal").modal('hide');
                } else {
                    $("#fix-post-modal").find(".modal-body").prepend(response.error);
                }

                //$("#folder-"+folder_id).remove();
                // $("#folder-content-"+folder_id).remove();
            });
        $(this).prop('disabled', false);
    });

    $(document).on('click', "#start-fix-postmeta",function(e){
        $(this).prop('disabled', true);
        var post_id = $(this).data('id');
        var path = $(this).data('path');
        var url = $(this).data('url');
        var action = 'fix_postmeta';
        var token = $(this).data("token");

        $.post(
            rsssl_ajax.ajaxurl,
            {
                action : action,
                token : token,
                url : url,
                path : path,
                post_id: post_id,
            },
            function( response ) {
                if (response.success){
                    rsssl_remove_from_results(url, path, post_id);
                    $("#fix-postmeta-modal").modal('hide');
                } else {
                    $("#fix-postmeta-modal").find(".modal-body").prepend(response.error);
                }

                //$("#folder-"+folder_id).remove();
                //$("#folder-content-"+folder_id).remove();
            }
        );
        $(this).prop('disabled', false);
    });

    $(document).on('click', "#start-fix-file",function(e){

        $(this).prop('disabled', true);

        var post_id = $(this).data('id');
        var path = $(this).data('path');
        var url = $(this).data('url');
        var action = 'fix_file';
        var token = $(this).data("token");

        $.post(
            rsssl_ajax.ajaxurl,
            {
                action : action,
                post_id: post_id,
                token : token,
                url : url,
                path : path,
            },
            function( response ) {
                if (response.success){
                    rsssl_remove_from_results(url, path, post_id);
                    $("#fix-file-modal").modal('hide');
                } else {
                    $("#fix-file-modal").find(".modal-body").prepend(response.error);
                }
                //$("#folder-"+folder_id).remove();
                //$("#folder-content-"+folder_id).remove();
            }
        );
        $(this).prop('disabled', false);
    });

    $(document).on('click', "#start-ignore-url",function(e){

        $(this).prop('disabled', true);
        var post_id = $(this).data('id');
        var path = $(this).data('path');
        var action = 'ignore_url';
        var url = $(this).data('url');
        var token = $(this).data("token");
        $.post(
            rsssl_ajax.ajaxurl,
            {
                action : action,
                path: path,
                token : token,
                url : url,
                post_id: post_id,
            },

            function( response ) {
                if (response.success){
                    rsssl_remove_from_results(url, path, post_id);
                    $("#ignore-url-modal").modal('hide');
                } else {
                    $("#ignore-url-modal").find(".modal-body").prepend(response.error);
                }
            }
        );
        $(this).prop('disabled', false);
    });

    $(document).on('click', "#start-fix-widget",function(e){
        $(this).prop('disabled', true);
        var widget_id = $(this).data('id');
        var path = $(this).data('path');
        var url = $(this).data('url');
        var action = 'fix_widget';
        var token = $(this).data("token");
        //var post_id = $(this).data("post_id");

        $.post(
            rsssl_ajax.ajaxurl,
            {
                action : action,
                //post_id: post_id,
                token : token,
                url : url,
                path : path,
                widget_id: widget_id,
            },
            function( response ) {
                if (response.success){
                    rsssl_remove_from_results(url, path, widget_id);
                    $("#fix-widget-modal").modal('hide');
                } else {
                    $("#fix-widget-modal").find(".modal-body").prepend(response.error);
                }

                //$("#folder-"+folder_id).remove();
                //$("#folder-content-"+folder_id).remove();
            }
        );
        $(this).prop('disabled', false);
    });


    //remove alerts after closing
    $("#fix-file-modal").on("hidden.bs.modal", function () {
        $("#fix-file-modal").find("#rsssl-alert").remove();
    });

    //remove alerts after closing
    $("#fix-post-modal").on("hidden.bs.modal", function () {
        $("#fix-post-modal").find("#rsssl-alert").remove();
    });

    //remove alerts after closing
    $("#fix-postmeta-modal").on("hidden.bs.modal", function () {
        $("#fix-postmeta-modal").find("#rsssl-alert").remove();
    });

    //remove alerts after closing
    $("#fix-widget-modal").on("hidden.bs.modal", function () {
        $("#fix-widget-modal").find("#rsssl-alert").remove();
    });

    $(document).on('click', "#start-roll-back",function(e){
        $(this).prop('disabled', true);
        var token = $(this).data("token");
        $.post(
            rsssl_ajax.ajaxurl,
            {
                action : 'roll_back',
                token : token,
            },
            function( response ) {
                $("#roll-back-modal").find(".modal-body").prepend(response.error);
            }
        );
        $(this).prop('disabled', false);
    });

    $("#roll-back-modal").on("hidden.bs.modal", function () {
        $("#roll-back-modal").find("#rsssl-alert").remove();
    });

    $('#fix-cssjs-modal').on('show.bs.modal', function(e) {
        $(this).find("#start-fix-cssjs").data('url', $(e.relatedTarget).data('url'));
        $(this).find("#start-fix-cssjs").data('path', $(e.relatedTarget).data('path'));

    });

    $('#editor-modal').on('show.bs.modal', function(e) {

        if ($(e.relatedTarget).data('url') == 'FILE_EDIT_BLOCKED' || $(e.relatedTarget).data('url') == '') {
            $(this).find('#edit-files-blocked').show();
            $(this).find('#edit-files').hide();
            $(this).find("#open-editor").attr("disabled", true);
        } else {
            $(this).find("#open-editor").data('url', $(e.relatedTarget).data('url'));
        }
    });

    $("#open-editor").click(function(e){
        window.location.href = $("#open-editor").data('url');
        $('#editor-modal').modal('hide');
    });

    $("#start-fix-cssjs").click(function(e){
        $(this).prop('disabled', true);

        var path = $(this).data('path');
        var url = $(this).data('url');
        var token = $(this).data("token");

        $.post(
            rsssl_ajax.ajaxurl,
            {
                action : 'fix_cssjs',
                token : token,
                url : url,
                path : path,
            },
            function( response ) {
                if (response.success){
                    rsssl_remove_from_results(url, path);
                    //$('a[data-url="'+url+'"][data-path="'+path+'"]').closest(".rsssl-files").remove();
                    $("#fix-cssjs-modal").modal('hide');
                } else {
                    $("#fix-cssjs-modal").find(".modal-body").prepend(response.error);
                }

                //$("#folder-"+folder_id).remove();
                //$("#folder-content-"+folder_id).remove();
            }
        );
        $(this).prop('disabled', false);
    });

    $("#fix-cssjs-modal").on("hidden.bs.modal", function () {
        $("#fix-cssjs-modal").find("#rsssl-alert").remove();
    });

    //show the 'advanced settings' in 'scan for issues'.

    $("#rsssl-more-options-btn").click(function(e){
        e.preventDefault();
        var panel = $("#rsssl-more-options-container");
        var icon = $(this).find(".glyphicon");
        if (panel.is(':visible')) {
            panel.slideUp();
        } else {
            panel.slideDown();
        }

    });
    function rsssl_remove_from_results(url, path, post_id=0) {
        var btn;
        if (post_id!=0) {
            btn = $('a[data-url="'+url+'"][data-id="'+post_id+'"]');
        } else {
            btn = $('a[data-url="'+url+'"][data-path="'+path+'"]');
        }
        var nr_of_results = btn.closest(".blockedurls").find(".rsssl-files").length;
        if (nr_of_results<=1) {
            var img_src = btn.closest(".blockedurls").find(".panel-heading .panel-title img").attr('src');
            img_src = img_src.replace('cross', 'check');
            btn.closest(".blockedurls").find(".panel-heading .panel-title img").attr('src',img_src);
        }

        btn.closest(".rsssl-files").remove();
    }

    /*handle options change in advance field*/
    $(document).on('change', '#rsssl_show_ignore_urls', function(){
        $("#rsssl_scan_form").append('<input type="hidden" name="rsssl_no_scan" value="rsssl_no_scan" />');
        $("#rsssl_scan_form").submit();
    });

});