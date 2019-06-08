jQuery(function($){

	var no_sol = $('#ra-no-solution-available').html();
	if ( no_sol != undefined ){
		$('#ra-no-solution-available').insertBefore( $('#main_ra_search_form') );
	}

	$('.ra-search-submit-button').html('<img src="/wp-content/plugins/right-answers/img/search-icon.png" />');

	$(document).ready(function() {

         jQuery(document).on('click', '#RA_article_downvote', function(){
            jQuery("#RA_article_downvote_form").show(); 
            jQuery('.ra-helpfulness-voting a.no svg').css("fill", "#ff6c0c");
            jQuery('.ra-helpfulness-voting a.yes svg').css("fill", "#e1e1e1");
        });
        
        jQuery(document).on('click', '.ra-helpfulness-voting .yes', function(){
            jQuery("#RA_article_downvote_form").hide(); 
            jQuery('.ra-helpfulness-voting a.no svg').css("fill", "#e1e1e1");
            jQuery('.ra-helpfulness-voting a.yes svg').css("fill", "#ff6c0c");
        });

        
		var url = window.location.href;

		if ( $('.alerts-bar').length < 1 ){
			$('#filter-sidebar').show();
			$('#cats-results-holder').show();
		} 
		window.addEventListener('popstate', function(e){
			if ( $('#results-content-holder').length > 0 ) {
				$('#cats-results-holder').html(e.state);
			}
			else if ( $('#video-container').length > 0 ){
				window.location.assign(url);
			}
	   }); 

		$(document).on('click', '#support-home-url', function(){
			window.location.assign('/support/');
		});

		function alert_block_resize(){
//
//			if ( $('.alert-block').length > 0 ){
//				var cheight = 0;
//				$( '.alert-block-header').each(function(){
//					var divheight = $(this).height();
//					if ( divheight > cheight ){
//						cheight = divheight;	
//					}
//				})			
//				// console.log(cheight);
//				$('.alert-block-header').height(cheight);
//
//
//				var aheight = 0;
//				$( '.alert-title-link').each(function(){
//					var theight = $(this).height();
//					if ( theight > aheight ){
//						aheight = theight;	
//					}
//				})			
//				// console.log(aheight);
//				$('.alert-title-link').height(aheight);
//
//			}	
            
//            $('.alert-title-link').height("198px");

		}
		alert_block_resize();

		$(document).on('click', '.ra-search-submit-button', function(e){ 
			var search_text = $('.ra-search-field').val();
			window.location.assign(window.lang_home_url + 'support/search-results/?searchtext=' + search_text);
		});

		$(document).on('change', '.cat-list', function(e){
			var cat_search_name = $(this).val();
			window.location.assign( '/support/knowledge-base/' + cat_search_name );
		});
        
		$(document).on('change', '.filter-list', function(e){

			var results_area = '';
			var cat_search_name = $(this).val();
			
			$('#cats-results-holder').prepend('<div id="loading-gif"><img src="/wp-content/plugins/right-answers/img/loading-gif-png-5.gif" /></div>');
			$('.page-numbers').addClass('hid');
			
			var data = {
				'action': 'get_ra_cat',
				'cnam': cat_search_name,   
				'cpg': 1
			};
			// We can also pass the url value separately from ajaxurl for front end AJAX implementations
			$.post(ra_ajax_object.ajaxurl, data, function(response) {
				// $('#loading-gif').remove();
				$('#cats-results-holder').html(response);
				// var state = '<div id="cats-results-holder" class="three-quarters">' + response + '</div>'; 
				history.pushState(response, '', url);

			});

			$('html, body').animate({
		        scrollTop: $("#main").offset().top
		    }, 100);

		});

	
		$(document).on('click', '#next-page-link', function(e){
			e.preventDefault();
			var current_page = $('#curpage').html();
			var next_page = parseInt(current_page) + 1;
			var search_term = $('#search-name').html();

			if ( $('#search-name').hasClass('alert') ) {
				if ( parseInt(current_page) == 1 ) {
					var curstate = $('#cats-results-holder').html();
					history.pushState(curstate, '', url);
				}
				
				$('#cats-results-holder').prepend('<div id="loading-gif"><img src="/wp-content/plugins/right-answers/img/loading-gif-png-5.gif" /></div>');
				var data = {
					'action': 'np_alert_search',
					'cpg': next_page
				}
				$.post(ra_ajax_object.ajaxurl, data, function(response){
					$('#cats-results-holder').html(response);
					history.pushState(response, '', url);

					alert_block_resize();
					
				});
				
				
			}

			if ( $('#search-name').hasClass('ra-cat-search') ){
				$('#cats-results-holder').prepend('<div id="loading-gif"><img src="/wp-content/plugins/right-answers/img/loading-gif-png-5.gif" /></div>');
				
				var data = {
					'action': 'get_ra_cat',
					'cnam': search_term,
					'cpg': next_page
				}
				$.post(ra_ajax_object.ajaxurl, data, function(response){
					$('#cats-results-holder').html(response);
					history.pushState(response, '', url);
					
				});

			}

			if ( $('#search-name').hasClass('gen-ra-search') ){
				$('#cats-results-holder').prepend('<div id="loading-gif"><img src="/wp-content/plugins/right-answers/img/loading-gif-png-5.gif" /></div>');
				var data = {
					'action': 'gen_ra_search',
					'qterm': search_term,
					'cpage': next_page
				}
				$.post(ra_ajax_object.ajaxurl, data, function(response){
                    console.log("Revieved search response!");
					$('#cats-results-holder').html(response);
					history.pushState(response, '', url);			
				});

			}

			$('html, body').animate({
		        scrollTop: $("#main").offset().top
		    }, 100);

		});

		$(document).on('click', '#prev-page-link', function(e){
			e.preventDefault();
			var current_page = $('#curpage').html();
			var prev_page = parseInt(current_page) - 1;
			var search_term = $('#search-name').html();
			var sidebar = ''; 
			var results_area = '';

			if ( $('#search-name').hasClass('alert') ) {
				$('#cats-results-holder').prepend('<div id="loading-gif"><img src="/wp-content/plugins/right-answers/img/loading-gif-png-5.gif" /></div>');
				var data = {
					'action': 'np_alert_search',
					'cpg': prev_page
				}
				$.post(ra_ajax_object.ajaxurl, data, function(response){
					$('#cats-results-holder').html(response);
					history.pushState(response, '', url);

					alert_block_resize();
					
				});
				
			}

			if ( $('#search-name').hasClass('ra-cat-search') ){
				$('#cats-results-holder').prepend('<div id="loading-gif"><img src="/wp-content/plugins/right-answers/img/loading-gif-png-5.gif" /></div>');
				
				var data = {
					'action': 'get_ra_cat',
					'cnam': search_term,
					'cpg': prev_page
				}
				$.post(ra_ajax_object.ajaxurl, data, function(response){
					$('#cats-results-holder').html(response);
					history.pushState(response, '', url);
					
				});

			}

			if ( $('#search-name').hasClass('gen-ra-search') ){
				$('#cats-results-holder').prepend('<div id="loading-gif"><img src="/wp-content/plugins/right-answers/img/loading-gif-png-5.gif" /></div>');
				var data = {
					'action': 'gen_ra_search',
					'qterm': search_term,
					'cpage': prev_page
				}
				$.post(ra_ajax_object.ajaxurl, data, function(response){
					$('#cats-results-holder').html(response);
					history.pushState(response, '', url);					
				});

			}

			$('html, body').animate({
		        scrollTop: $("#main").offset().top
		    }, 100);
			
		});


		$('.ra-helpfulness-voting a').each(function(){

			var link = $(this).closest('.ra-helpfulness-voting');
			var so_id = link.attr("id");

			var vote_check = localStorage.getItem(so_id);

			// console.log(vote_check);

//			if ( vote_check == 'yes' ){
//				$('.ra-helpfulness-voting a.yes svg').css("fill", "#ff6c0c");
//			}
//			else if ( vote_check == 'no' ) {
//				$('.ra-helpfulness-voting a.no svg').css("fill", "#ff6c0c");
//			}


		})

		$(document).on('click', '.ra-helpfulness-voting a.yes', function(e){
			e.preventDefault();
			var link = $(this).closest('.ra-helpfulness-voting');
			var so_id = link.attr("id");

			// console.log(so_id);

			var vote_check = localStorage.getItem(so_id);
			if ( vote_check == 'yes' ){
				$('#ra-upvote-response').html('You indicated this response was helpful');
				// console.log('second vote blocked');
			}
			else if ( vote_check == 'no' ){
				$('#ra-upvote-response').html('Vote once per answer please');
				// $('#ra-downvote-feedback-form').hide();
				// console.log( 'no vote already recorded. form already presented once' );
			}
			else {
				var data = {
					'action': 'single_upvote',
					'the-sol-id': so_id,
				}
				$.post(ra_ajax_object.ajaxurl, data, function(response){
					if ( response == 'true' ){
						localStorage.setItem(so_id, 'yes');
						$('#ra-upvote-response').html('You indicated this response was helpful');
						$('.ra-helpfulness-voting a.yes').css("fill", "#ff6c0c");
					}
					else {
						// console.log(response);
					}
				});
			}
		});

		$(document).on('click', '.ra-helpfulness-voting a.no', function(e){
			e.preventDefault();
			var link = $(this).closest('.ra-helpfulness-voting');
			var so_id = link.attr("id");
			var vote_check = localStorage.getItem(so_id);
			if ( vote_check == 'no' ){
				$('#ra-upvote-response').html('Vote once per answer please');
				// $('#ra-downvote-feedback-form').hide();
				// console.log( 'no vote already recorded. form already presented once' );
			}
			else if ( vote_check == 'yes' ){
				$('#ra-upvote-response').html('Vote once per answer please');
				// $('#ra-downvote-feedback-form').hide();
				// console.log( 'yes vote already recorded. stopped complaint form' );
			}
			else {
				// $('#ra-downvote-feedback-form').show();
				localStorage.setItem(so_id, 'no');
				$('.ra-helpfulness-voting a.no').css("fill", "#ff6c0c");
			}
		});

//		if ( $('.ra-solution-footer').length > 0 ){
//			var controlheight = 0;
//			$( '.ra-solution-footer div').each(function(){
//				
//				var divheight = $(this).height();
//				if ( divheight > controlheight ){
//					controlheight = divheight;
//					
//				}
//			})
//			$('.ra-solution-footer div').height(controlheight);
//		}

		function video_overlay(){
			// $('.video-holder a').css('');
			$('.video-holder a span.image-overlay').remove();
		}

		$(document).on('hover', '.video-holder', function(){
			video_overlay();
		});

		$(document).on('hover', '.video-holder a', function(){
			video_overlay();
		});

		$(document).on('hover', '.video-holder a img.sw-support-vid', function(){
			video_overlay();
		});

		function reposition_video_player(){
			var st = ( parseInt($(document).scrollTop()) + (parseInt(window.innerHeight) / 2) ) - 80;
			$('#video_frame').css("top", st);
			// console.log(st);
		}

		$(document).on('scroll', function(){
			reposition_video_player();
		});

		$(document).on('click', '.video-holder a.video-image-link', function(e){
			e.preventDefault();

			var vid_id = $(this).children(".sw-support-vid");
			vid_id = vid_id.attr("id");
			var vid_title = $(this).next('.vid-name').html();
			var vid_desc = $(this).nextAll('.vid-description').html();

			console.log(vid_desc);

			var data = {
				'action': 'video_popup',
				'video_id': vid_id
			}
			$.post(ra_ajax_object.ajaxurl, data, function(response){
				if ( $('#mobile-video-frame').length > 0 ){
					history.pushState(null, '', url);
					$('#video-container').html(response);
				}
				else {
					var vidtop = ( parseInt( $(document).scrollTop() )  + (parseInt(window.innerHeight) / 2 ) ) - 80;
					$('#video_frame').css("top",  );
					$('#video_frame').html(response);
					$('#video_frame .video-player').prepend('<div id="video-close-button">x</div>');
					$('#video_frame .video-player').append(vid_title);
					$('#video_frame .video-player').append(vid_desc);

					$('#wrap_all').prepend( $('#video_frame') );
					$('#wrap_all').prepend( $('#video_background') );
					$('#video_background').show();
					$('#video_frame').show();

					// reposition_video_player();
					// $('html, body').animate({
				 //        scrollTop: $("#main").offset().top
				 //    }, 2000);
				}
			});
		})

		$(document).on('click', '#video_background', function(){
			$(this).hide();
			$('#video_frame').hide();
			$('.video-player').remove();
		});

		$(document).on('click', '#video_frame .video-player #video-close-button', function(){
			$('#video_background').hide();
			$('#video_frame').hide();
			$('.video-player').remove();
		});

		if ( $('#video-container').length > 0 ){
			var curpg = parseInt( $('#video-cur-page').html() );
			var totpg = parseInt( $('#video-total-pages').html() );
			if ( curpg == 1 ){
				$('.video-prev-page').hide();
			}
			if ( curpg == totpg ){
				$('.video-next-page').hide();
			}
		}

		var vid_tax = $('#cur-vid-tax').html();
		if ( vid_tax != '' && vid_tax != undefined ){
			vid_tax = vid_tax.replace(/\+/g, ' ');
			$('#video-taxonomy-selector').val( vid_tax );
		}

		$('#video-taxonomy-selector').change(function(e){
			$('#video-taxonomy-form').submit();
		});


		// single video page stuff
		// get the video holder h4 html and lowercase it and replace all spaces with dashes for slug
		// replace the state with the slug
		// push the new state url so back button works (if needed)


		$('.plc-product-name').hide();
		$('.plc-product-type').hide();
		$('.plc-product-data-table').hide();
		$('.firmware-product-info-display').hide();

		$('#product-selector').on('change', function(){
			var prod = $(this).val();
			$('.plc-product-name').hide();
			$('.plc-product-type').hide();
			$('.plc-product-data-table').hide();
			if ( $(this).val() != ''){
				$('#' + prod).show();
				$('.' + prod).show();

				$('#product-type-holder').children().each(function(){
					var pth = $(this);
					if ( pth.css('display') == 'block' && pth.hasClass( 'software') ) {
						var prod_name = pth.children('a').attr("id");
						$('#' + prod_name + '-software').show();
						return false;
					}
					else if ( pth.css('display') == 'block' && pth.hasClass( 'hardware') ) {
						var prod_name = pth.children('a').attr("id");
						$('#' + prod_name + '-hardware').show();
						return false;
					}
					else if ( pth.css('display') == 'block' && pth.hasClass( 'firmware') ) {
						var prod_name = pth.children('a').attr("id");
						$('#' + prod_name + '-firmware').show();
						return false;
					}
				});
			}
		});

		$(document).on('click', '.plc-product-type a', function(e){
			e.preventDefault();
			$('.plc-product-data-table').hide();
			var table_id = $(this).attr('id');
			var table_type = $(this).html();
			table_type = table_type.toLowerCase();
			if ( table_type == 'firmware' ){
				$('.hardware-software-toggles').hide();
				$('.firmware-product-info-display').show();
			}
			else {
				$('.hardware-software-toggles').show();
				$('.firmware-product-info-display').hide();
			}

			// console.log( '#' + table_id + '-' + table_type );
			$('#' + table_id + '-' + table_type).show();

		});

		$('.has-tip').hover(function(){
			var tip_key = $(this).parent().attr('id');
			var ttip = $('#' + tip_key  + '-tooltip').html();
			$(this).parent().prepend('<div class="tooltip-holder">' + ttip + '</div>');
		}, function(){
			$('.tooltip-holder').remove();
		});

		// Here is the stuff where tech docs functions will go -------------

		$(document).on('change', '#td-main-selector', function(){
			$('.tech-doc-models-holder div#tech-docs-models').hide();
			$('div.entry-content').prepend('<h3 id="working-label">Working......</h3>');
			$('.sn_result_area').html(' ');
			var parent_term = $(this).val();
			if ( parent_term != '' ){
				var data = {
					'action': 'child_cat_getter',
					'parent-term': parent_term,
				}
				$.post(ra_ajax_object.ajaxurl, data, function(response){
					if ( response != ''){
						$('.tech-doc-models-holder div#tech-docs-models').html(response);
						$('.tech-doc-models-holder div#tech-docs-models').show();
					}
				}).done(function(){
					var title_data = {
							'action': 'main_topic_titles',
							'parent-term' : parent_term,
						}
						$.post(ra_ajax_object.ajaxurl, title_data, function(resp){
							// console.log(resp);
							// need to put this in a space under the search bar
							$('.sn_result_area').html(resp);
						}).done(function(){
							$('#working-label').remove();
                            window.initFancybox();
						})
						
						// console.log('td-main-done');
				})
			}
		});

		$(document).on('change', '#sub_cat_selector', function(){
			$('div.entry-content').prepend('<h3 id="working-label">Working......</h3>');
			$('.sn_result_area').html(' ');
		
			var parent_term = $('#td-main-selector').val();
			var child_term = $(this).val();

			var datum = {
				'action': 'main_topic_titles',
				'parent-term' : parent_term,
				'model-term': child_term,
			}

			$.post(ra_ajax_object.ajaxurl, datum, function(respt){
				// console.log(respt);
				// need to put this in a space under the search bar
				$('.sn_result_area').html(respt);
			}).done(function(){
				$('#working-label').remove();
                window.initFancybox();
			})
		});


		$('.tdbg a:not(.full-link)').each(function(){
			var curlink = $(this).attr('href');
            if (curlink == "#")
            {
                jQuery(this).removeAttr('href');
                jQuery(this).addClass('disabled');
            }
			else if ( curlink != undefined ){
               
				$(this).attr('href', '/support/technical-documentation' + curlink);
			}
			
			// console.log($(this).attr('href'));
		});

		
	    $('#tocdiv ul.tocul li').each(function(){
	        if ( $(this).children().hasClass('second-level') ){
	            $(this).prepend('<span class="sublist">&#9658;</span>');
	        }
	    });

        window.toc_state = [];
        
	    $(document).on('click', '#tocdiv ul.tocul li span.sublist', function(){
            
	        var parent = $(this).parent();
            var href = $(this).next().attr("href");
            
            if (parent.children('.second-level').is(":hidden"))
            {
                parent.children('.second-level').show();
                $(this).html('&#9660;');
                
                window.toc_state.push(href);
            }
            else
            {
                parent.children('.second-level').hide();
                $(this).html('&#9658;');
                
                var index = window.toc_state.indexOf(href);
                
                if (index > -1)
                {
                    window.toc_state.splice(index, 1);        
                }
            }
            
            storeTOCState();
	    });
        
        
        
        $(document).ready(function(){
            restoreTOCState();
            setTOCActive();
            initTOCToggle();
        });
        
        function initTOCToggle()
        {
            jQuery("#toc-toggle").click(function(){
                var toc_container = jQuery("#techdocs-container .toc_container");
                
                if (toc_container.hasClass('visible'))
                {
                    //hide
                    toc_container.removeClass('visible');
                    jQuery(this).removeClass('visible');
                }
                else
                {
                    //show
                    toc_container.addClass('visible');
                    jQuery(this).addClass('visible');
                }
            });  
        }
        
        function setTOCActive()
        {
            jQuery("a.WebWorks_TOC_Link").each(function(){
                
                var two_deep_path = window.location.pathname.replace(/^\/|\/$/g, '');
                
                var parts = two_deep_path.split("/");
                
                if (parts.length > 5)
                {
                    parts = parts.slice(0,5);
                    two_deep_path = parts.join("/");
                }
      
               if (jQuery(this).attr('href').replace(/^\/|\/$/g, '') == two_deep_path)
               {
                   jQuery(this).addClass('active');
                   
                   var parent = jQuery(this).parent();
                   if (parent.hasClass("depth-2"))
                    {
                        var linker = parent.parent();
                        console.log(linker);
                        var toggler = linker.prev().prev();
                        toggler.html('&#9660;');
                        parent = linker.parent();       
                    }
                    else
                    {
                        jQuery(this).prev().html('&#9660;');
                    }
                  
                    parent.children('.second-level').show();
                    
               }
            });
        }
        
        function restoreTOCState()
        {
            var key = getTOCStateKey();
            
            if (window.sessionStorage && key)
            {
                var payload = JSON.parse(sessionStorage.getItem(key));
                
                if (payload && Array.isArray(payload) && payload.length > 0)
                {
                    for (var i = 0; i < payload.length; i++)
                    {
                        var url = payload[i];
                        var toggler = jQuery("a.WebWorks_TOC_Link[href='" + url + "'").prev();
                        var parent = toggler.parent();
                        parent.children('.second-level').show();
                        toggler.html('&#9660;');
                    }
                    
                    window.toc_state = payload;         
                }
            }
        }
        function storeTOCState()
        {
            var key = getTOCStateKey();
            
            if (window.sessionStorage && key)
            {
              var payload = JSON.stringify(window.toc_state);
              sessionStorage.setItem(key, payload);
            }     
        }
        
        function getTOCStateKey()
        {
           //Extract the pathname for the key
            var pathname = window.location.pathname;
            var key_parts = pathname.replace("/support/technical-documentation/", "").split("/");
            if (key_parts.length > 0)
            {    
                var key = key_parts[0];
                return key;
            }
        }

	    

	});
});