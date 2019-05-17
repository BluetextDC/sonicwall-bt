(function ($) {
    "use strict";

    // admin interface object js
    $.customizer = function () {
        var ctm = {
            defaults: {
                isSideOpen: true, // is sidebar customizer open
                xAnimationName: $('.lpw_customizer_sidebar .lpw_animationName').val(), // default animation name of lightpost
                showTitle: true,
                showCategories: true,
                showAuthorThumb: true,
                showAuthor: true,
                showDate: true
            },
            // initialise all needed function for customizer
            init: function () {
                ctm.openEventCustomizer();
                ctm.initCustomizer();
                ctm.customizerListner();
                ctm.cancelCustomizerListner();

            },
            openEventCustomizer: function () {

                //add event click to open/close customizer
                $('.toggle_bttn').on('click', function () {
                    if (!ctm.defaults.isSideOpen) {
                        $('.lpw_customizer .lpw_customizer_sidebar ').addClass('show');
                    }
                    else {
                        $('.lpw_customizer .lpw_customizer_sidebar ').removeClass('show');
                    }
                    ctm.defaults.isSideOpen = !ctm.defaults.isSideOpen;
                    setTimeout(function(){ $.currentLightPost.refresh();  }, 600);

                })
            },
            initCustomizer: function () {
                // init field color
                $('.alpha-color-picker').alphaColorPicker();

                // init tooltip
                $('.tooltip').popup({
                    transition: 'scale',
                    inline: true,
                    lastResort: 'top left',
                });

                // init dropdown
                $('.ui.dropdown').dropdown({
                    onChange: ctm.onChangeDropdown
                });

                // init accordion menu
                $('.accordion').accordion({
                    selector: {
                        trigger: '.title'
                    }
                });

                // set field after focus on customizer menu
                $('.accordion').on('mouseenter', function () {
                    setTimeout(function () {
                        $('.range').each(function () {
                            var start = $(this).attr('data-start');
                            $(this).range('set value', start);
                            $(this).range('set value', parseInt(start));
                            $(this).trigger('focus')
                            $(this).find('.track-fill span').text(start);
                            $(this).next().val(parseInt(start));
                        });

                      //  $('.accordion').off('mouseenter');
                    }, 1000);
                });
                $('.accordion').on('mouseleave', function () {
                    $('.accordion').off('mouseenter');
                })

                // init slider field
                $('.range').each(function () {
                    var min = $(this).attr('data-min');
                    var max = $(this).attr('data-max');
                    var start = $(this).attr('data-start');

                    $(this).range({
                        min: parseInt(min),
                        max: parseInt(max),
                        start: 100,
                        onChange: ctm.onSliderChange
                    });

                    // update tooltip slider
                    $(this).range('set value', start);
                    $(this).trigger('focus');
                    $(this).find('.track-fill').append('<span>' + start + '</span>');
                    $(this).next().val(parseInt(start));

                });

                // init checkbox field
                $('.ui.checkbox').checkbox();

                // init tabs section
                $('.menu .item').tab({onLoad: function(){
                    setTimeout(function(){
                        $('.ui.checkbox').checkbox();
                    }, 1000)
                }});

                //fix checked input
                $('.toggle.checkbox').on('click', function() {
                    var checked = $(this).hasClass('checked') ? true : false;
                    $(this).find('input').attr('checked', checked);
                });

                ctm.openResponsivePanel();

            },
            onChangeDropdown: function (val, text, el) {
                if ($(el).closest('.lpw_animationName').length > 0) {
                    if (val.length > 0)
                        $('.npl_layer_scrolling .npl_wrap').removeClass(ctm.defaults.xAnimationName).addClass(val);
                    ctm.defaults.xAnimationName = val;
                    $.currentLightPost.update({animation: val});
                }
            },
            onSliderChange: function (val, el) {

                $(el).find('.track-fill span').text(val);
                $(el).next().val(val);

                if ($(el).hasClass('lpw_width')) {
                    if (parseInt(val) >= 0) {
                        $('.npl_layer_scrolling').css('width', val);
                        $.currentLightPost.update({width: val });

                    }
                }

                if ($(el).hasClass('lpw_grayscaleValue')) {
                    var blur = $('input.lpw_blurValue').val();
                    if (val >= 0) {
                        $('.npl_blurring>:not(.npl_layer)').css({filter: 'blur('+ blur +'px) grayscale('+ (parseInt(val)/100) +')'});
                        $.currentLightPost.update({grayscaleValue: val});
                    }

                }

                if ($(el).hasClass('lpw_blurValue')) {
                    var grayscale = $('input.lpw_grayscaleValue').val();

                    if (val >= 0) {
                        $('.npl_blurring>:not(.npl_layer)').css({filter: 'blur(' + val + 'px) grayscale('+ (parseInt(grayscale)/100) +')'});
                        $.currentLightPost.update({blurValue: val});
                    }
                }

                if ($(el).hasClass('lpw_boxShadow')) {
                    $('.npl_layer .npl_wrap').css('boxShadow', '0 0 ' + val + 'px ' + $('.lpw_boxShadowColor').val());
                    $.currentLightPost.update({boxShadow: val });
                }

                if ($(el).hasClass('lpw_borderRadius')) {
                    $('.npl_layer .npl_wrap').css('borderRadius', val + 'px');
                    $.currentLightPost.update({borderRadius: val });
                }

                if ($(el).hasClass('lpw_fontSize')) {
                    $('.npl_layer .npl_wrap').css('fontSize', val + 'px');
                    $.currentLightPost.update({fontSize: val });
                }

                if ($(el).hasClass('lpw_horizentalPadding')) {
                    $('.npl_layer .npl_body_section').css('paddingLeft', val + 'px').css('paddingRight', val+ 'px');
                    $('.npl_layer .npl_comments_body').css('marginLeft', val+ 'px').css('marginRight', val+ 'px');
                    $('.npl_layer .npl_comments_head').css('paddingLeft', val+ 'px').css('paddingRight', val+ 'px');
                    $('.npl_layer .npl_header_section').css('paddingLeft', val+ 'px').css('paddingRight', val+ 'px');
                    $('.npl_layer .npl_body_action').css('paddingLeft', val+ 'px').css('paddingRight', val+ 'px');
                    $.currentLightPost.update({horizentalPadding: val });
                }
                $.currentLightPost.refresh();

            },
            customizerListner: function(){

                $('.lpw_customizer_sidebar .lpw_showThumbAuthor').on('change', function () {
                    var val = $(this).prop("checked");
                    if (!val){
                        $('.npl_layer_scrolling .npl_thumb_user.big').addClass('hide');
                        $.currentLightPost.update({showThumbAuthor: false });
                    }
                    else{
                        $('.npl_layer_scrolling .npl_thumb_user.big').removeClass('hide');
                        $.currentLightPost.update({showThumbAuthor: true });
                    }

                    $.currentLightPost.refresh();

                    var optionsInst =  $.currentLightPost.options;
                    if(!optionsInst.showAuthor && !optionsInst.showAuthorThumb && !optionsInst.showTitle && !optionsInst.showCategories && !optionsInst.showDate && !optionsInst.showContent && !optionsInst.showSocialShare) $('.npl_header_section').addClass('hide');
                    else  $('.npl_header_section').removeClass('hide');
                });

                $('.lpw_customizer_sidebar .lpw_showTitle').on('change', function () {
                    var val = $(this).prop("checked");
                    if (!val){
                        $('.npl_layer_scrolling .npl_header_title').addClass('hide');
                        $.currentLightPost.update({showTitle: false});
                    } else{
                        $('.npl_layer_scrolling .npl_header_title').removeClass('hide');
                        $.currentLightPost.update({showTitle: true});
                    }
                    $.currentLightPost.refresh();

                    var optionsInst =  $.currentLightPost.options;
                    if(!optionsInst.showAuthor && !optionsInst.showAuthorThumb && !optionsInst.showTitle && !optionsInst.showCategories && !optionsInst.showDate && !optionsInst.showContent && !optionsInst.showSocialShare) $('.npl_header_section').addClass('hide');
                    else  $('.npl_header_section').removeClass('hide');
                });

                $('.lpw_customizer_sidebar .lpw_overlayClose').on('change', function () {
                    var val = $(this).prop("checked");
                    if (!val){
                        $.currentLightPost.update({overlayClose: false});
                    } else{
                        $.currentLightPost.update({overlayClose: true});
                    }
                });

                $('.lpw_customizer_sidebar .lpw_showAuthor').on('change', function () {
                    var val = $(this).prop("checked");
                    if (!val){
                        $('.npl_layer_scrolling .npl_author_name').addClass('hide');
                        $.currentLightPost.update({showAuthor: false});
                    } else{
                        $('.npl_layer_scrolling .npl_author_name').removeClass('hide');
                        $.currentLightPost.update({showAuthor: true});
                    }
                    $.currentLightPost.refresh();

                    var optionsInst =  $.currentLightPost.options;
                    if(!optionsInst.showAuthor && !optionsInst.showAuthorThumb && !optionsInst.showTitle && !optionsInst.showCategories && !optionsInst.showDate && !optionsInst.showContent && !optionsInst.showSocialShare) $('.npl_header_section').addClass('hide');
                    else  $('.npl_header_section').removeClass('hide');
                });

                $('.lpw_customizer_sidebar .lpw_showCategories').on('change', function () {
                    var val = $(this).prop("checked");
                    if (!val){
                        $('.npl_layer_scrolling .npl_categories').addClass('hide');
                        $.currentLightPost.update({showCategories: false});
                    } else{
                        $('.npl_layer_scrolling .npl_categories').removeClass('hide');
                        $.currentLightPost.update({showCategories: true});
                    }
                    $.currentLightPost.refresh();

                    var optionsInst =  $.currentLightPost.options;
                    if(!optionsInst.showAuthor && !optionsInst.showAuthorThumb && !optionsInst.showTitle && !optionsInst.showCategories && !optionsInst.showDate && !optionsInst.showContent && !optionsInst.showSocialShare) $('.npl_header_section').addClass('hide');
                    else  $('.npl_header_section').removeClass('hide');
                });

                $('.lpw_customizer_sidebar .lpw_showDate').on('change', function () {
                    var val = $(this).prop("checked");
                    if (!val){
                        $('.npl_layer_scrolling .npl_postdate').addClass('hide');
                        $.currentLightPost.update({showDate: false});
                    } else{
                        $('.npl_layer_scrolling .npl_postdate').removeClass('hide');
                        $.currentLightPost.update({showDate: true});
                    }
                    $.currentLightPost.refresh();

                    var optionsInst =  $.currentLightPost.options;
                    if(!optionsInst.showAuthor && !optionsInst.showAuthorThumb && !optionsInst.showTitle && !optionsInst.showCategories && !optionsInst.showDate && !optionsInst.showContent && !optionsInst.showSocialShare) $('.npl_header_section').addClass('hide');
                    else  $('.npl_header_section').removeClass('hide');
                });

                $('.lpw_customizer_sidebar .lpw_showMedia').on('change', function () {
                    var val = $(this).prop("checked");
                    if (!val){
                        $('.npl_layer_scrolling .npl_media_section').addClass('hide');
                        $.currentLightPost.update({showMedia: false});
                    } else{
                        $('.npl_layer_scrolling .npl_media_section').removeClass('hide');
                        $.currentLightPost.update({showMedia: true});
                    }
                    $.currentLightPost.refresh();

                    var optionsInst =  $.currentLightPost.options;
                    if(!optionsInst.showAuthor && !optionsInst.showAuthorThumb && !optionsInst.showTitle && !optionsInst.showCategories && !optionsInst.showDate && !optionsInst.showContent && !optionsInst.showSocialShare) $('.npl_header_section').addClass('hide');
                    else  $('.npl_header_section').removeClass('hide');
                });

                $('.lpw_customizer_sidebar .lpw_showSocialShare').on('change', function () {
                    var val = $(this).prop("checked");
                    if (!val){
                        $('.npl_layer_scrolling .npl_body_action').addClass('hide');
                        $.currentLightPost.update({showSocialShare: false});
                    } else{
                        $('.npl_layer_scrolling .npl_body_action').removeClass('hide');
                        $.currentLightPost.update({showSocialShare: true});
                    }
                    $.currentLightPost.refresh();

                    var optionsInst =  $.currentLightPost.options;
                    if(!optionsInst.showAuthor && !optionsInst.showAuthorThumb && !optionsInst.showTitle && !optionsInst.showCategories && !optionsInst.showDate && !optionsInst.showContent && !optionsInst.showSocialShare) $('.npl_header_section').addClass('hide');
                    else  $('.npl_header_section').removeClass('hide');
                });

                $('.lpw_customizer_sidebar .lpw_showContent').on('change', function () {
                    var val = $(this).prop("checked");
                    if (!val){
                        $('.npl_layer_scrolling .npl_body_section').addClass('hide');
                        $.currentLightPost.update({showContent: true});
                    } else {
                        $('.npl_layer_scrolling .npl_body_section').removeClass('hide');
                        $.currentLightPost.update({showContent: true});
                    }
                    $.currentLightPost.refresh();

                    var optionsInst =  $.currentLightPost.options;
                    if(!optionsInst.showAuthor && !optionsInst.showAuthorThumb && !optionsInst.showTitle && !optionsInst.showCategories && !optionsInst.showDate && !optionsInst.showContent && !optionsInst.showSocialShare) $('.npl_header_section').addClass('hide');
                    else  $('.npl_header_section').removeClass('hide');
                });


                $('.lpw_customizer_sidebar .lpw_enableNavigation').on('change', function () {
                    var val = $(this).prop("checked");
                    if (!val){
                        $(' .npl_navigation').addClass('hide');
                        $.currentLightPost.update({enableNavigation: false});
                    }  else{
                        $(' .npl_navigation').removeClass('hide')
                        $.currentLightPost.update({enableNavigation: true});
                    }
                    $.currentLightPost.refresh();
                });

                $('.lpw_customizer_sidebar .lpw_showNavigationTitle').on('change', function () {
                    var val = $(this).prop("checked");
                    if (!val){
                        $('.npl_layer .npl_previous_title, .npl_layer .npl_next_title').addClass('hide');
                        $.currentLightPost.update({showNavigationTitle: true});
                    } else {
                        $('.npl_layer .npl_previous_title, .npl_layer .npl_next_title').removeClass('hide');
                        $.currentLightPost.update({showNavigationTitle: true});
                    }
                    $.currentLightPost.refresh();
                });

                $('.lpw_customizer_sidebar .lpw_blurStyle').on('keyup change', function () {
                    var val = $(this).prop("checked");
                    if (!val) {
                        $('.npl_blurring>:not(.npl_layer)').css('-webkit-filter', 'blur(0px) grayscale(.7)').css('filter', 'blur(0px) grayscale(.7)');
                        $('.lpw_customizer_body').removeClass('npl_blurring');
                        $.currentLightPost.update({blurStyle: false});
                    } else {
                        $('.lpw_customizer_body').addClass('npl_blurring');
                        $('.npl_blurring>:not(.npl_layer)').css('-webkit-filter', 'blur(' + $('.lpw_customizer_sidebar .lpw_blurValue').val() + 'px) grayscale(.7)').css('filter', 'blur(' + $('.lpw_customizer_sidebar .lpw_blurValue').val() + 'px) grayscale(.7)');
                        $.currentLightPost.update({blurStyle: true});
                    }
                    $.currentLightPost.refresh();
                });

                $('.lpw_customizer_sidebar .lpw_blurValue').on('keyup change', function () {
                    var val = $(this).val();
                    if (val >= 0) {
                        $('.npl_blurring>:not(.npl_layer)').css('-webkit-filter', 'blur(' + val + 'px) grayscale(.7)').css('filter', 'blur(' + val + 'px) grayscale(.7)')
                        $.currentLightPost.update({blurValue: val});
                    }else{
                        $.currentLightPost.update({blurValue: 0});
                    }
                    $.currentLightPost.refresh();
                });


                $('.lpw_customizer_sidebar .lpw_boxShadow').on('keyup change', function () {
                    var val = $(this).val();
                    $('.npl_layer .npl_wrap').css('boxShadow', '0 0 ' + val + 'px ' + $('.lpw_boxShadowColor').val());
                    $.currentLightPost.update({boxShadow: val});
                    $.currentLightPost.refresh();
                });

                $('.field_boxShadowColor .alpha-color-picker-submit').on('click', function () {
                    $('.lpw_customizer_sidebar .lpw_boxShadow').trigger('change');
                    $.currentLightPost.refresh();
                });

                $('.field_boxShadowColor .alpha-color-picker ').on('change', function () {
                    var val = $(this).val();
                    $('.npl_layer .npl_wrap').css('boxShadow', '0 0 ' + $('.field_boxShadowColor .lpw_boxShadowColor').val() + 'px ' + val);
                    $.currentLightPost.update({boxShadowColor: val});
                    $.currentLightPost.refresh();
                });

                $('.field_colorBackground .alpha-color-picker-submit').on('click', function () {
                    $('.lpw_customizer_sidebar .lpw_colorBackground').trigger('change');
                });

                $('.field_colorBackground .alpha-color-picker ').on('change', function () {
                    var val = $(this).val();
                    $('.npl_layer .npl_wrap').css('background', val );
                    $.currentLightPost.update({colorBackground: val});
                    $.currentLightPost.refresh();
                });

                $('.field_colorTitle .alpha-color-picker-submit').on('click', function () {
                    $('.lpw_customizer_sidebar .lpw_colorTitle').trigger('change');
                    $.currentLightPost.refresh();
                });

                $('.field_colorTitle .alpha-color-picker ').on('change', function () {
                    var val = $(this).val();
                    $('.npl_layer .npl_header_title').css('color', val );
                    $.currentLightPost.update({colorTitle: val});
                    $.currentLightPost.refresh();
                });

                $('.field_colorSubTitle .alpha-color-picker-submit').on('click', function () {
                    $('.lpw_customizer_sidebar .lpw_colorSubTitle').trigger('change');
                });

                $('.field_colorSubTitle .alpha-color-picker ').on('change', function () {
                    var val = $(this).val();
                    $('.npl_layer .npl_header_sub_extra').css('color', val );
                    $.currentLightPost.update({colorSubTitle: val});
                    $.currentLightPost.refresh();
                });

                $('.field_colorText .alpha-color-picker-submit').on('click', function () {
                    $('.lpw_customizer_sidebar .lpw_colorText').trigger('change');
                });

                $('.field_colorLink .alpha-color-picker ').on('change', function () {
                    var val = $(this).val();
                    $('.npl_layer a').css('color', val );
                    $.currentLightPost.update({colorSubTitle: val});
                    $.currentLightPost.refresh();
                });

                $('.field_colorLink .alpha-color-picker-submit').on('click', function () {
                    $('.lpw_customizer_sidebar .lpw_colorLink').trigger('change');
                });

                $('.field_colorText .alpha-color-picker ').on('change', function () {
                    var val = $(this).val();
                    $('.npl_layer .npl_body_section *, .npl_layer .npl_body_section ').css('color', val );
                    $.currentLightPost.update({colorText: val});
                    console.log(val)
                    $.currentLightPost.refresh();
                });

                $('.field_layerBackground .alpha-color-picker-submit').on('click', function () {
                    $('.lpw_customizer_sidebar .lpw_layerBackground').trigger('change');
                });

                $('.field_layerBackground .alpha-color-picker ').on('change', function () {
                    var val = $(this).val();
                    $(' .npl_overlay').css('backrgoundColor', val);
                    $.currentLightPost.update({colorSubTitle: val});
                    $.currentLightPost.refresh();

                });

                $('.lpw_customizer_sidebar .lpw_layerBackground').on('keyup change', function () {
                    var val = $(this).val();
                    if (val != '')
                        $('.npl_overlay').css('backgroundColor', val);
                    $.currentLightPost.update({layerBackground: val});
                    $.currentLightPost.refresh();
                });

                $('.lpw_customizer_sidebar .lpw_animated').on('change', function () {
                    var val = $(this).prop("checked");
                    if (!val){
                        $('.npl_layer_scrolling .npl_wrap').removeClass('animated');
                        $.currentLightPost.update({animated: false});
                    } else{
                        $('.npl_layer_scrolling .npl_wrap').addClass('animated');
                        $.currentLightPost.update({animated: true});
                    }
                    $.currentLightPost.refresh();
                });

                $('.lpw_customizer_sidebar .lpw_openSpeed').on('keyup change', function () {

                    var val = $(this).val();
                    if (val >= 0)
                        $('.npl_layer_scrolling .npl_wrap').css('animationDuration', (val * 0.001 ) + 's');
                    $.currentLightPost.update({speed: parseInt(val)});
                    $('.npl_layer_scrolling .npl_wrap').removeClass(ctm.defaults.xAnimationName);
                    $.currentLightPost.refresh();

                    setTimeout(function () { $('.npl_layer_scrolling .npl_wrap').addClass(ctm.defaults.xAnimationName);}, 200);
                });

                $('.viewport_bttn .mobile').on('click', function () {
                    $('.viewport_bttn >*').removeClass('active');
                    $(this).addClass('active');
                    if($(this).hasClass('portrait'))
                    $('.lpw_customizer_body').addClass('portrait');
                    else  $('.lpw_customizer_body').removeClass('portrait');

                    $('.npl_navigation').hide();

                    $('.lpw_customizer_body').removeClass('tablette').addClass('mobile');
                    $.currentLightPost.refresh();

                    setTimeout(function(){ $.currentLightPost.refresh($.currentLightPost.options); }, 300)
                });

                $('.viewport_bttn .tablette').on('click', function () {
                    $('.viewport_bttn >*').removeClass('active');
                    $(this).addClass('active');
                    if($(this).hasClass('portrait'))
                        $('.lpw_customizer_body').removeClass('portrait');
                    else  $('.lpw_customizer_body').addClass('portrait');

                    $('.npl_navigation').hide();

                    $('.lpw_customizer_body').removeClass('mobile').addClass('tablette');
                    $.currentLightPost.refresh();
                });

                $('.viewport_bttn .desktop').on('click', function () {
                    $('.viewport_bttn >*').removeClass('active');
                    $(this).addClass('active');

                    $('.npl_navigation').show();

                    $('.lpw_customizer_body').removeClass('tablette').removeClass('mobile').removeClass('portrait');
                    $.currentLightPost.refresh();
                });

                ctm.setImage();
            },
            // function to open/close responsive panel
            openResponsivePanel: function(){
                var isOpen = false;
                $('.lpw_footer .responsive_bttn').on('click', function(){
                    if(isOpen){
                        $('.panel_responsive').removeClass('show');
                        $(this).removeClass('active');
                    }else{
                        $('.panel_responsive').addClass('show');
                        $(this).addClass('active');
                    }

                    isOpen = !isOpen;
                })
            },
            // add cancel event to cancel button
            cancelCustomizerListner: function(){
                var isOpen = false;
                $('.lpw_footer .cancel_bttn').on('click', function(){
                    location.reload();
                });
            },
            // Init image field
            setImage: function(){

                // Event Remove image
                if ($('.remove_custom_images').length > 0) {
                    $('.remove_custom_images').on('click', function(){
                        var input = $(this).prevAll('input');
                        $('.process_custom_images').attr('value', '');
                        $(this).closest('.field_thumb').removeClass('show').addClass('hide');
                        $(this).closest('.field_image').find('.add_img').removeClass('hide').addClass('show');
                        $('.npl_background').css({backgroundImage:  '' });
                        return false;
                    });
                }

                // Event open & select image
                if ($('.set_custom_images').length > 0) {
                    if ( typeof wp !== 'undefined' && wp.media && wp.media.editor) {
                        $('.wrap').on('click', '.set_custom_images', function(e) {
                            e.preventDefault();
                            var button = $(this);
                            var input = button.prev();
                            var img = button.closest('.field_image').find('img');
                            var that = this;
                            wp.media.editor.send.attachment = function(props, attachment) {
                                $(that).closest('.field_image').find('.field_thumb').removeClass('hide').addClass('show');
                                $(that).closest('.field_image').find('.add_img').removeClass('show').addClass('hide');

                                if(attachment.url.length == '') return;
                                input.val(attachment.id);
                                img.attr('src', attachment.url)
                                $.currentLightPost.update({overlayImage: attachment.url});
                                if(input.hasClass('lpw_overlayImage')) $('.npl_background').css({backgroundImage:  'url("'+ attachment.url +'")' });
                            };
                            wp.media.editor.open(button);
                            return false;
                        });
                    }
                }
            }

        };

        return ctm;
    };

})(jQuery);

jQuery(document).ready(function () {

    // Instance & initialise Customizer
    jQuery.customizerLightpost = jQuery.customizer();
    jQuery.customizerLightpost.init();

    jQuery('body').on('onNavig', '.npl_layer',  function(){
        jQuery.customizerLightpost.customizerListner();
    })
});

