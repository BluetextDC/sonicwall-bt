
// declare global variable will used for instance
jQuery.currentLightPost = null;

(function ($) {
    // LightPost Object
    $.lightPostPopup = function (options) {
        var lpp = {
            _translator: null, // well used for instance jquery translation
            _data: null, // used to stock json data for post
            _isOpen: false, // used to check lightPost is open
            _isDemo: false, //used to know if LightPost used in customizer to add all section even if is desabled
            options: $.extend({
                // default value LightPost
                theme: 'theme1', // theme1, theme2
                root: 'body', //  Where to append LightBox
                language: 'en', // default language
                selector: '.lightpost', // selector used to trigger event open lightpost
                url: null, // link to load post data
                data: {}, // if theres json data to send like post_id
                method: 'POST', // GET or POST
                animated: true,
                speed: 1000, // speed animation on ms
                animation: 'fadeInDown',
                showThumbAuthor: true,
                showAuthor: true,
                showCategories: true,
                showTitle: true,
                showDate: true,
                showMedia: true,
                showSocialShare: true,
                showContent: true,
                showComment: true,
                hide_desktop: false,
                hide_tablette: false,
                hide_mobile: false,
                enableNavigation: true, // show next/prev button
                showNavigationTitle: true,
                overlayClose: true, // close on click outside lightbox
                colorTitle: 'rgba(0,0,0, .9)',
                colorSubTitle: 'rgba(0,0,0, .4)',
                colorLink: 'rgba(16,162,124,1)',
                colorText: 'rgba(0,0,0, .9)',
                colorBackground: '#fff',
                layerBackground: 'rgba(0,0,0, .5)',
                overlayImage: '',
                grayscaleValue: 70,
                blurStyle: true,
                blurValue: 3,
                width: 600,
                boxShadow: 30, // on pixel
                boxShadowColor: 'rgba(0,0,0, .4)', // on pixel
                fontFamily: 'sans-serif',
                fontSize: 15, // on pixel
                borderRadius: 6, //on %
                sizeBigThumb: 30,
                sizeMedThumb: 32,
                sizeMinThumb: 22,
                radiusTumb: 50,
                backgroundMedia: 'black',
                zIndex: 100000,
                horizentalPadding: 18
            }, options),
            init: function () {

                //
                lpp.openOnlanding();

                // init jQuery Translate
                lpp.initLanguage();
                //add open event LightPost
                lpp.addListnerOpenLightPost();

                //init SDK Facebook & twitter for share button
                (function(d, s, id) {
                    var js, fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id)) return;
                    js = d.createElement(s); js.id = id;
                    js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8&appId=274110106032741";
                    fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));
                !function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?"http":"https";if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document, "script", "twitter-wjs");

            },
            initLanguage: function () {
                // var dict is all text used with  translation declared in i18n.js
                lpp._translator = $('body').translate({lang: lpp.options.language, t: dict});
            },
            addListnerOpenLightPost: function () {
                // open LightPost on click specified selector
                $('body').on('click', lpp.options.selector, function () {
                    var widthWin = $(document).width();
                    if (lpp.options.hide_desktop && widthWin > 1024)
                        return true;

                    if (lpp.options.hide_tablette && widthWin < 780 && widthWin > 425)
                        return true;

                    if (lpp.options.hide_mobile && widthWin < 425)
                        return true;
                    var postId = $(this).attr('data-id');
                    if (postId && postId != '')
                        lpp.open(postId);
                });
                // open LightPost on click any link have 'lpw' param
                $("a").on("click", function (e) {

                    var widthWin = $(document).width();
                    if (lpp.options.hide_desktop && widthWin > 1024)
                        return true;

                    if (lpp.options.hide_tablette && widthWin < 780 && widthWin > 425)
                        return true;

                    if (lpp.options.hide_mobile && widthWin < 425)
                        return true;

                    var $url = $(this).attr("href");
                    var params = getSearchParameters($url);

                    if (typeof params.lpw != 'undefined') {
                        lpp.open(params.lpw);
                        return false;
                    }


                });
            },
            openOnlanding: function(){
                var urlParams = getSearchParameters(window.location.href );

                if(typeof urlParams == 'object' && 'llpw' in urlParams){
                    lpp.open(urlParams.llpw);
                }
            },
            open: function (idPost) { // open LightBox function
                if (lpp.isOpen()) return false;
                lpp.isDemo = false;
                if (lpp.options.blurStyle)
                    $(lpp.options.root).addClass('npl_blurring');
                $(lpp.options.root).addClass('noScroll');
                var data = {action: "lightpost_load_data", post_id: idPost};
                lpp.loaddata(data);
                lpp._isOpen = true;

            },
            openDemo: function (idPost) { // open Demo LightPost function
                lpp.isDemo = true;
                if (lpp.isOpen()) return false;
                if (lpp.options.blurStyle)
                    $(lpp.options.root).addClass('npl_blurring');
                $(lpp.options.root).addClass('noScroll');
                var data = {action: "lightpost_load_data", post_id: idPost};
                lpp.loaddata(data);
                lpp._isOpen = true;
            },
            loaddata: function (data) { // load data post function

                // if next post url dosen't exist we use Ajax Api Wordpress url
                if (lpp.options.url == null)
                    lpp.options.url = lightpost.ajax_url;

                $('.spinner.big').show();

                $.ajax({
                    type: lpp.options.method,
                    url: lpp.options.url,
                    data: data,
                    success: function (response) {
                        lpp._data = JSON.parse($.trim(response ));
                        lpp.generateLightBox();
                        lpp.generateStyle();
                        lpp.navigation();
                        lpp.addEventClose();
                        lpp.showAfterLoadImg();
                    },
                    error: function (error) {
                        // todo: handle error
                        console.log("Error: " + error);
                        $('.spinner.big').hide();
                    }
                }).responseJSON;
            },
            isOpen: function () { // check LightPost already open and return false else return true
                if (lpp._isOpen || $('body').find('.npl_layer').length > 0) return true;
                else return false;
            },
            showAfterLoadImg: function(){
                var srcBg = $('.npl_background').css('backgroundImage').replace('url(','').replace(')','').replace(/\"/gi, "");
                //not display background image before it is loaded
                $('.npl_background').css('backgroundImage', '' );

                var $img = $('.npl_layer_scrolling img');
                var $imgBG = $('<img/>').attr('src', srcBg);
                $img.push($imgBG);

                totalImg = $img.length;

                var waitImgDone = function() {
                    totalImg--;
                    if (totalImg <= 1){
                        $(' .npl_layer_scrolling').removeClass('lpw_hide');
                        $('.npl_background').css('backgroundImage', 'url('+ srcBg +')' );
                        $('.spinner.big').hide();
                        // show and position close & navigation buttons after finish animation lightbox
                        setTimeout(lpp.positionControlLightBox, parseInt(lpp.options.speed) + 1000);
                    }
                };

                $img.each(function() {
                    $(this).on('load', waitImgDone).on('error', waitImgDone);
                });
            },
            generateLightBox: function () {  // generate LightPost content
                if ($('.npl_background').length == 0)
                    var layerBgEl = $('<div class="lpw npl_background" ><div class="npl_overlay"></div></div>');

                var mainLayerEl = ($('.npl_layer').length == 0) ? $('<div  class="lpw npl_layer" ></div>') : $('.npl_layer');

                /*if (lpp.options.animated) $('.npl_layer').addClass('animated');
                 else $('.npl_layer').removeClass('animated');
                 if (lpp.options.animation) $('.npl_layer').addClass(lpp.options.animation);*/

                var wrapperEl = $('<div class="npl_wrap"></div>');

                // add type animation
                if (lpp.options.animated) {
                    wrapperEl.addClass('animated')
                    if (lpp.options.animation.length > 0)
                        wrapperEl.addClass(lpp.options.animation);
                }

                // add spinner to lightbox
                if ($('.npl_layer .spinner.big').length == 0)
                    mainLayerEl.append('<div class="spinner big"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>');

                // add Scroller wrapper to lightbox
                if ($('.npl_layer_scrolling').length == 0)
                    mainLayerEl.append('<div class="npl_layer_scrolling lpw_hide"></div>');

                // add main content to lightbox
                mainLayerEl.find('.npl_layer_scrolling').append(wrapperEl);

                var controlsBttn = '<div class="npl_controls_in"> <div class="npl_controls_cancel"><i class="icon-cancel"></i></div> </div>';
                mainLayerEl.find('.npl_layer_scrolling').append(controlsBttn);

                // Add Controls Button (cancel & navigation button)
                mainLayerEl.append(lpp.getControlsButtonsElement());
                if (lpp.options.theme == 'theme1')
                    wrapperEl.append(lpp.generateTheme1());
                if (lpp.options.theme == 'theme2')
                    wrapperEl.append(lpp.generateTheme2());

                // Add LightBox to Page
                $(lpp.options.root).append(layerBgEl);
                $(lpp.options.root).append(mainLayerEl);

                window.BttnAsyncInit = function() {
                    // Wait until FB object is loaded and initialized to refresh the embeds.
                    if(typeof FB !== 'undefined')
                        FB.XFBML.parse();
                    if(typeof twttr !== 'undefined')
                    twttr.widgets.load();
                }

                window.BttnAsyncInit();


            },
            generateTheme1: function () {  // get LightPost theme 1
                var html = '';
                var header = lpp.getHeaderSection();
                var media = lpp.getMediaSection();
                var body = lpp.getBodySection();
                var share = lpp.getShareSection();
                html+= html+header+media+share+body;
                return $(html);
            },
            generateTheme2: function () { // get LightPost theme 2

                var html = '';
                var header = lpp.getHeaderSection();
                var media = lpp.getMediaSection();
                var body = lpp.getBodySection();
                var share = lpp.getShareSection();
                html+= html+media+header+share+body;
                return $(html);
            },
            getHeaderSection: function () { // generate header Content of LightPost
                var titleEl = '';
                var subTitle = '';
                var authorThumb = '';

                var fullname = "";
                var image_uri = "";
                var avatarEl = "";
                var urlAuthorProfile = "#";

                if (!lpp.options.showTitle && !lpp.options.showThumbAuthor && !lpp.options.showAuthor && !lpp.options.showCategories && !lpp.options.showDate && !lpp.isDemo) return '';

                var cssclass = '';

                if (lpp.options.showTitle || lpp.isDemo) {
                    if (lpp.isDemo && lpp.options.showTitle == false )  cssclass = ' hide '; else cssclass = '';
                    if ("title" in lpp._data) {
                        titleEl = '<h1 class="npl_header_title '+ cssclass +'">' + lpp._data.title + '</h1>'
                    }
                }

                subTitle = '<div class="npl_header_sub_extra">';

                if (lpp.options.showThumbAuthor || lpp.isDemo) {
                    if (lpp.isDemo && lpp.options.showThumbAuthor == false )  cssclass = ' hide '; else cssclass = '';

                    if ("author" in lpp._data) {
                        if ("fullname" in lpp._data.author) fullname = lpp._data.author.fullname;
                        if ("image_uri" in lpp._data.author) avatarEl = '<img src="' + lpp._data.author.image_uri + '" alt="' + fullname + '"/>';
                        else avatarEl = '<i class="icon-peopel"></i>';
                        if ("url" in lpp._data.author) urlAuthorProfile = lpp._data.author.url;

                        subTitle += '<a href="' + urlAuthorProfile + '" title="' + fullname + '" class="npl_thumb_user big '+ cssclass +'">' + avatarEl + '</a>';
                    }
                }

                if (lpp.options.showAuthor || lpp.isDemo) {
                    if (lpp.isDemo && lpp.options.showAuthor == false )  cssclass = ' hide '; else cssclass = '';

                    subTitle += '<span class="npl_author_name '+ cssclass +'">';
                    subTitle += lpp._translator.get('by');
                    subTitle += ' <a href="' + urlAuthorProfile + '" class="npl_header_name" title="' + fullname + '">' + fullname + '</a> ';
                    subTitle += "</span>";
                }

                if ((lpp.options.showCategories && "categories" in lpp._data) || lpp.isDemo) {
                    if (lpp.isDemo && lpp.options.showCategories == false )  cssclass = ' hide ';

                    subTitle += '<span class="npl_categories'+ cssclass +'">';
                    if(lpp._data.categories != null && Object.keys(lpp._data.categories).length > 0)
                        subTitle += lpp._translator.get('in');
                    $.each(lpp._data.categories, function (index, value) {
                        subTitle += ' <a href="' + lpp._data.categories[index].url + '">' + lpp._data.categories[index].name + '</a>';
                        if (Object.keys(lpp._data.categories).length > (index + 1)) subTitle += ', ';
                        else subTitle += ' ';

                    });
                    subTitle += "</span>";
                }

                if (lpp.options.showDate || lpp.isDemo) {
                    if (lpp.isDemo && lpp.options.showDate == false )  cssclass = ' hide '; else cssclass = '';

                    subTitle += '<span class="npl_postdate '+ cssclass +'">';
                    subTitle += lpp.getTimeDate(lpp._data.publishedAt);
                    subTitle += "</span>";
                }

                subTitle += '</div>';

                var headerSectionEl = '<div class="npl_header_section">'+ authorThumb+ '<div class="npl_header_extra">' + titleEl + subTitle + '</div>' +'</div>'

                return headerSectionEl;
            },
            getMediaSection: function () { // generate media section of LightPost
                if (!lpp.options.showMedia && !lpp.isDemo) return '';

                var mediaEl = '';
                var cssclass = '';
                if (lpp.isDemo && lpp.options.showMedia == false )  cssclass = ' hide ';

                mediaEl += '<div class="npl_media_section '+ cssclass +'">';
                if ("media" in lpp._data) {
                    switch (lpp._data.media.type) {
                        case 'image':
                            mediaEl += '<div class="npl_media_object ' + lpp._data.media.type + '"><img src="' + lpp._data.media.url + '" alt="Example Title Of Image"/></div>';
                            break;
                        case 'youtube':
                            mediaEl += '<div class="npl_media_object ' + lpp._data.media.type + '"><iframe src="' + lpp._data.media.url + '" frameborder="0" allowfullscreen></iframe></div>';
                            break;
                        case 'vimeo':
                            mediaEl += '<div class="npl_media_object ' + lpp._data.media.type + '"><iframe src="' + lpp._data.media.url + '" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe></div>';
                            break;
                        case 'embed':
                            mediaEl += '<div class="npl_media_object ' + lpp._data.media.type + '">' + lpp._data.media.embed + '</div>';
                            break;
                        default:
                            return '';
                    }

                }
                mediaEl += '</div>';

                return mediaEl;
            },
            getShareSection: function(){
                if(!lpp.options.showSocialShare && !lpp.isDemo) return '';

                var shareEl = '<div class="npl_body_action"><div class="fb-like" data-href="'+ lpp._data.link +'" data-layout="button" data-action="like" data-size="small" data-show-faces="false" data-share="false"></div><div class="fb-send" data-href="'+ lpp._data.link +'"></div>';
                shareEl +='<a href="https://twitter.com/share" class="twitter-share-button" data-text="'+ lpp._data.title +'" data-url="'+ lpp.options.link +'"></a> ';
                shareEl +='<div class="g-plusone" data-size="medium" data-annotation="none" data-href="'+ lpp._data.link +'"></div>  <script type="text/javascript"> window.___gcfg = {lang: "'+ lpp.options.language +'"}; (function() { var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;po.src = "https://apis.google.com/js/platform.js";var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);})();</script>';
                shareEl += '</div>';
                return shareEl;
            },
            getBodySection: function () { // generate body section of LightPost
                if ((!lpp.options.showContent && !lpp.isDemo)  || !"content" in lpp._data )  return;

                var cssclass = '';
                if (lpp.isDemo && lpp.options.showContent == false )  cssclass = ' hide ';

                var bodySectionEl = '<div class="npl_body_section '+ cssclass +'">'+ lpp._data.content +'</div>';

                return bodySectionEl;
            },
            getControlsButtonsElement: function () { // generate cancel button of LightPost
                var innerHtml = '<div class="npl_controls"> <div class="npl_controls_cancel"><i class="icon-cancel"></i></div></div>';

                if (!lpp.options.enableNavigation) return innerHtml;
                innerHtml += '<div class="npl_navigation">';

                if ("previous_post" in lpp._data && lpp._data.previous_post != null) {
                    innerHtml += '<div class="npl_nav_left"><i class="icon-left"></i>';
                    if (lpp.options.showNavigationTitle && Object.keys(lpp._data.previous_post).length > 0 && lpp._data.previous_post.id != null)
                        innerHtml += '<div class="npl_previous_title animated slideInLeft">' + lpp._data.previous_post.title + '</div>';
                    innerHtml += '</div>';
                }

                if ("next_post" in lpp._data && lpp._data.next_post != null) {
                    innerHtml += '<div class="npl_nav_right"><i class="icon-right"></i>';
                    if (lpp.options.showNavigationTitle && Object.keys(lpp._data.next_post).length > 0 && lpp._data.next_post.id != null)
                        innerHtml += '<div class="npl_previous_title animated slideInRight ">' + lpp._data.next_post.title + '</div>';
                    innerHtml += '</div>';
                }

                innerHtml += '</div>';

                return innerHtml;
            },
            getTimeDate: function (previous) { // generate published date content

                var niceDate = '';
                var current = new Date().getTime();
                var msPerDay = 24 * 60 * 60 * 1000;
                var elapsed = current - previous;

                var date = new Date(previous * 1000);
                niceDate = lpp._translator.get('at');
                niceDate += ' ' + ( '0' + date.getHours()).slice(-2) + ':' + ( '0' + date.getMinutes()).slice(-2);

                if (elapsed > msPerDay) {
                    niceDate = lpp._translator.get('on') + ' ' + ( '0' + date.getDate()).slice(-2) + '/' + ( '0' + (date.getMonth() + 1)).slice(-2) + '/' + date.getFullYear() + ' ' + niceDate;
                }

                return niceDate;
            },
            addEventClose: function () {
                $('.npl_controls .npl_controls_cancel').on('click', function () {
                    lpp.closeLightPost();
                });
                $(document).keyup(function (e) {
                    if (e.which == 27) {
                        lpp.closeLightPost();
                    }
                });

                if (lpp.options.overlayClose)
                    $('.npl_layer').on('click', function (e) {
                        if (!$(e.target).hasClass('.npl_wrap') && $(e.target).closest('.npl_wrap').length == 0 && $(e.target).closest('.npl_navigation').length == 0 && !$(e.target).hasClass('.npl_navigation') && $(e.target).closest('.npl_controls').length == 0 && !$(e.target).hasClass('.npl_controls') && !$(e.target).hasClass('toggle_bttn'))
                            lpp.closeLightPost();
                    });

                lpp.onClose();

            },
            positionControlLightBox: function () { //position buttons: cancel & navigation
                if ($('.npl_wrap').position() == undefined) return false;
                var mainWrapTop = $('.npl_wrap').position().top;
                var mainWrapWidth = $('.npl_wrap').width();
                var rootEl = $(lpp.options.root);
                var posExtra = ((rootEl.width() - mainWrapWidth) / 2 - 34);

                // if we are on mobile device put the close button  inside box
                $('.npl_controls').css('right', posExtra + 'px').css('top', mainWrapTop + 'px').css('opacity', 1);
                if (rootEl.width() < (mainWrapWidth + 40)) {
                    $('.npl_layer .npl_layer_scrolling').addClass('boxed');
                    $('.npl_layer .npl_navigation').addClass('boxed');
                    $('.npl_controls').addClass('hide');
                }
                else {
                    $('.npl_layer .npl_layer_scrolling').removeClass('boxed');
                    $('.npl_layer .npl_navigation').removeClass('boxed');
                    $('.npl_controls').removeClass('hide');
                }


                $('.npl_nav_left').css('left', (posExtra + rootEl.position().left) + 'px');
                $('.npl_nav_right').css('right', (posExtra ) + 'px');
                setTimeout(function () {
                    $('.npl_navigation').css('zIndex', 1).css('opacity', 1);
                }, 200);


                var posCroll = $('.npl_layer').scrollTop();
                var div_top = $('.npl_controls').offset().top;

                if ((posCroll + 10) > div_top) {
                    $('.npl_controls').addClass('sticky');
                } else {
                    $('.npl_controls').removeClass('sticky');
                }

                // on scroll fix position of cancel button
                $('.npl_layer').on('scroll', function () {
                    var posCroll = $('.npl_layer').scrollTop();
                    var div_top = $('.npl_controls').offset().top;

                    if ((posCroll + 10) > div_top) {
                        $('.npl_controls').addClass('sticky');
                    } else {
                        $('.npl_controls').removeClass('sticky');
                    }
                });

                $(window).on('resize', function () {
                    lpp.positionControlLightBox();
                });

            },
            navigation: function () {  // navigation between post
                $('.npl_layer .npl_nav_left').on('click', function () {
                    lpp.reloadLightPost(lpp._data.previous_post.id);
                    // call callback on navigation
                    lpp.onNavig();
                    $('.npl_layer').trigger('onNavig');
                });

                $('.npl_layer .npl_nav_right').on('click', function () {
                    lpp.reloadLightPost(lpp._data.next_post.id);
                    // call callback on navigation
                    lpp.onNavig();
                    $('.npl_layer').trigger('onNavig');
                });

                $(document).keyup(function (e) {

                    if (e.which == 37) {
                        $('.npl_layer .npl_nav_left').trigger('click');
                    }
                    if (e.which == 39) {
                        $('.npl_layer .npl_nav_right').trigger('click');
                    }
                });

            },
            reloadLightPost: function (idPost) { // reinitialise LightPost

                //show spinner
                $('.spinner.big').show();

                $('.npl_wrap').removeClass('slideInDown').addClass('fadeOut');

                //remove container after finish animation
                setTimeout(function () {
                    $('.npl_wrap').remove();
                }, lpp.options.speed );
                $('.npl_navigation').remove();
                $('.npl_controls').remove();
                var data = {action: "lightpost_load_data", post_id: idPost};

                $.ajax({
                    type: lpp.options.method,
                    url: lpp.options.url,
                    data: data,
                    success: function (response) {
                        response = JSON.parse($.trim(response ));
                        setTimeout(function () {
                            lpp._data = response;
                            lpp.generateLightBox();
                            lpp.addEventClose();
                            lpp.generateStyle();
                            lpp.navigation();
                            lpp.showAfterLoadImg();
                        }, lpp.options.speed );
                    },
                    error: function (error) {
                        // todo: handle error
                        console.log("Error: " + error);
                        $('.spinner.big').hide();
                    }
                }).responseJSON;
            },
            generateStyle: function () { // add style CSS for LightPost

                $('.npl_background').css({zIndex: lpp.options.zIndex});
                if ("overlayImage" in lpp._data) $('.npl_background').css({backgroundImage: 'url("' + lpp._data.overlayImage + '")'});
                else  $('.npl_background').css({backgroundImage: 'url("' + lpp.options.overlayImage + '")'});
                $('.npl_overlay').css({backgroundColor: lpp.options.layerBackground});

                $('.npl_layer').css({
                    fontFamily: lpp.options.fontFamily,
                    fontSize: lpp.options.fontSize + 'px',
                    zIndex: lpp.options.zIndex
                });

                $('.npl_layer .npl_layer_scrolling').css('width', lpp.options.width + 'px')
                $('.npl_layer .npl_wrap').css('boxShadow', '0 0 ' + lpp.options.boxShadow + 'px ' + lpp.options.boxShadowColor)
                    .css('borderRadius', lpp.options.borderRadius + 'px').css('animationDuration', (lpp.options.speed * 0.001 ) + 's');

                $('.npl_layer .npl_thumb_user').css('borderRadius', lpp.options.radiusTumb + '%');
                $('.npl_layer .npl_thumb_user.big').css('width', lpp.options.sizeBigThumb + 'px').css('height', lpp.options.sizeBigThumb + 'px');
                $('.npl_layer .npl_thumb_user.med').css('width', lpp.options.sizeMedThumb + 'px').css('height', lpp.options.sizeMedThumb + 'px');
                $('.npl_layer .npl_thumb_user.min').css('width', lpp.options.sizeMinThumb + 'px').css('height', lpp.options.sizeMinThumb + 'px');
                $('.npl_layer .npl_media_object').css('background', lpp.options.backgroundMedia);
                $('.npl_layer .npl_body_section').css('paddingLeft', lpp.options.horizentalPadding + 'px').css('paddingRight', lpp.options.horizentalPadding + 'px');
                $('.npl_layer .npl_comments_body').css('marginLeft', lpp.options.horizentalPadding + 'px').css('marginRight', lpp.options.horizentalPadding + 'px');
                $('.npl_layer .npl_comments_head').css('paddingLeft', lpp.options.horizentalPadding + 'px').css('paddingRight', lpp.options.horizentalPadding + 'px');
                $('.npl_layer .npl_header_section').css('paddingLeft', lpp.options.horizentalPadding + 'px').css('paddingRight', lpp.options.horizentalPadding + 'px');
                $('.npl_layer .npl_body_action').css('paddingLeft', lpp.options.horizentalPadding + 'px').css('paddingRight', lpp.options.horizentalPadding + 'px');
                $('.npl_layer .npl_header_title').css('color', lpp.options.colorTitle);
                $('.npl_layer .npl_header_sub_extra').css('color', lpp.options.colorSubTitle);
                $('.npl_layer a').css('color', lpp.options.colorLink);
                $('.npl_layer .npl_body_section, .npl_layer .npl_body_section *  ').css('color', lpp.options.colorText);
                $('.npl_wrap').css('background', lpp.options.colorBackground);
                $('.npl_blurring >*:not(.npl_layer)').css('filter', 'blur(' + lpp.options.blurValue + 'px) grayscale(' + (lpp.options.grayscaleValue * 0.01) + ')');

                //add boxed class if width lightbox exceeds container
                var rootElWidth = $(lpp.options.root).width();
                if (rootElWidth < (parseInt(lpp.options.width) + 40)){
                    //  $(' .npl_layer_scrolling', lpp.options.root).addClass('boxed');
                    // $(' .npl_navigation', lpp.options.root).addClass('boxed');
                }

            },
            closeLightPost: function () { // close LightPost function
                lpp._isOpen = false;
                $('.npl_wrap').removeClass('slideInDown').addClass('fadeOut');
                $('.npl_navigation').remove();
                $('.npl_controls').remove();
                $(lpp.options.root).removeClass('noScroll');
                setTimeout(function () {
                    $('.npl_layer').remove();
                    $('.npl_background').remove();
                    $('.npl_blurring>:not(.npl_layer)').css('-webkit-filter', 'blur(0px) grayscale(0)').css('filter', 'blur(0px) grayscale(0)')

                    $('body').removeClass('npl_blurring');


                }, 1000)
            },
            updateOption: function (options) { // change options LightPost function
                lpp.options = $.extend(lpp.options, options);

            },
            onNavig: function(callback){
                if(typeof callback =="function")
                    callback();
            },
            onClose: function(callback){
                if(typeof callback =="function")
                    callback();
            }
        }
        lpp.init()
        return { // object returned for instance
            open: lpp.open,
            options: lpp.options,
            openDemo: lpp.openDemo,
            update: lpp.updateOption,
            onNavig: lpp.onNavig,
            onClose: lpp.onClose,
            refresh: lpp.positionControlLightBox

        };
    }


})(jQuery);

// check if params exist and returned value of params
function getSearchParameters($url) {
    return $url != null && $url != "" ? getIdPost($url) : {};
}


// get value param passed on post link
function getIdPost(prmstr) {
    var params = {};
    prmstr = prmstr.split("?");
    if (!1 in prmstr || prmstr[1] == undefined)
        return true;

    var prmarr = prmstr[1].split("&");
    for (var i = 0; i < prmarr.length; i++) {
        var tmparr = prmarr[i].split("=");
        params[tmparr[0]] = tmparr[1];
    }
    return params;
}