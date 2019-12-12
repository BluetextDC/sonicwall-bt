jQuery(document).ready(function(){
    
    window.td_current_view = [];
    window.td_per_page = 20;
    window.td_current_page = 1;
    window.td_view_type = 'card';

    window.td_current_filters = [];
    
    window.default_languages = [
        "English",
        "Chinese",
        "French",
        "German",
        "Japanese",
        "Korean",
        "Portuguese",
        "Spanish"
    ];
    
    
    //Load from hash
    if (window.location && window.location.search)
    {
        var urlFilters = getJsonFromUrl();
        
        if (Object.keys(urlFilters).length > 0)
        {
            for (var key in urlFilters)
            {
                addFilter(key, urlFilters[key], lookupKey(key));  
            }
        }
    }
    else
    {
        setDefaultLang();   
    }
    
    function setDefaultLang()
    {
        //Set default language
        if (window.td_default_langauge)
        {
            jQuery('.td-filter-language').find("[data-value='" + window.td_default_langauge + "']").prop("checked", true);
            addFilter('language', window.td_default_langauge, lookupKey('language'));
        }
    }

   jQuery(document).on("click", ".td_filter_expand", function(){
        jQuery(this).addClass('sw-td-hide').removeClass('sw-td-show').nextAll('.sw-filter-value').removeClass('td_filter_hidden');
        jQuery(this).nextAll('.td_filter_contract').addClass('sw-td-show').removeClass('sw-td-hide');
   });
    
    jQuery(document).on("click", ".td_filter_contract", function(){
       
        jQuery(this).addClass('sw-td-hide').removeClass('sw-td-show');
        var expander = jQuery(this).prevAll('.td_filter_expand')
        jQuery(expander).addClass('sw-td-show').removeClass('sw-td-hide').nextAll('.sw-filter-value').addClass('td_filter_hidden');
   });
    
    
   jQuery(document).on("click", ".td-view-toggle button", function(){
      jQuery(".td-view-toggle button").removeClass("selected");
      jQuery(this).addClass("selected");
      window.td_view_type = jQuery(this).data("view-type");
      redraw_td_table();
   });
    
    buildCurrentView();
    
    jQuery(document).on("click", ".td_page_toggle", function(){
        
        if (!jQuery(this).hasClass('active'))
        {
            var clicked_page = jQuery(this).data("page");
            window.td_current_page = parseInt(clicked_page);
            redraw_td_table();
        }
    });
    
    jQuery(document).on("click", ".td_toc_prev", function(){
        
        if (!jQuery(this).find('a').hasClass('disable'))
        {
            var the_page = window.td_current_page - 1;
            window.td_current_page = the_page;
            redraw_td_table();
        }
    });
    
    jQuery(document).on("click", ".td_toc_next", function(){
        
        if (!jQuery(this).find('a').hasClass('disable'))
        {
            var the_page = window.td_current_page + 1;
            window.td_current_page = the_page;
            redraw_td_table();
        }
    });

    jQuery(document).on("change", ".td-filter-checkbox", function(){
        
       var filter_type = jQuery(this).data('type');
       var filter_value = jQuery(this).val();
       var filter_display_value = jQuery(this).data('display-value');
        
        
        if (jQuery(this).is(':checked'))
        {
             addFilter(filter_type, filter_value, filter_display_value);
        }
        else
        {
             removeFilter(filter_type, filter_value);
        }
    });
    
    jQuery(document).on("click", ".filter-btn", function(){
         var filter_type = jQuery(this).data('filter-type');
         var filter_value = jQuery(this).data('filter-value');
         removeFilter(filter_type, filter_value); 
    });
    
    jQuery(document).on("click", ".td-search-btn", function(){
        buildCurrentView(); 
    });
    
    jQuery(document).on("change", ".td_text_search", function(){
       buildCurrentView(); 
    });
});

function lookupKey(key)
{
    if (window.filter_lang_map)
    {
        return window.filter_lang_map[key];   
    }
    else
    {
        //Default to english if the translations are not available
        switch(key) {
          case 'category':
            return 'Product Category';
            break;
          case 'product':
            return 'Model';
            break;
        case 'resources':
            return 'Document Type';
            break;
        case 'language':
            return 'Language';
            break;
        }
    }
    
    return key;
}

function getJsonFromUrl(url) {
  if(!url) url = location.search;
  var query = url.substr(1);
  var result = {};
  query.split("&").forEach(function(part) {
    var item = part.split("=");
    result[item[0]] = decodeURIComponent(item[1]);
  });
  return result;
}

 function resetFilterExpander()
   {
       jQuery(".td_filter_expand").each(function(){
           jQuery(this).addClass('sw-td-hide').removeClass('sw-td-show').nextAll('.sw-filter-value').removeClass('td_filter_hidden');
           jQuery(this).nextAll('.td_filter_contract').addClass('sw-td-show').removeClass('sw-td-hide');
       });
       
       jQuery(".td_filter_contract").each(function(){
           jQuery(this).addClass('sw-td-hide').removeClass('sw-td-show');
        var expander = jQuery(this).prevAll('.td_filter_expand')
        jQuery(expander).addClass('sw-td-show').removeClass('sw-td-hide').nextAll('.sw-filter-value').addClass('td_filter_hidden');
       });
       
       
       //Update collapse toggles && counts
        jQuery(".td-filter-root").each(function(){
            var visible_filters = jQuery(this).find(".sw-filter-value.show-filtered-td");

            if (visible_filters.length <= 10)
            {
                jQuery(this).find('.td_filter_expand').addClass('sw-td-hide').removeClass('sw-td-show').nextAll('.sw-filter-value').removeClass('td_filter_hidden');
                jQuery(this).find('.td_filter_contract').addClass('sw-td-hide').removeClass('sw-td-show');
            }
            else
            {
                jQuery(this).find('.td_filter_expand').addClass('sw-td-show').removeClass('sw-td-hide');
            }
        });
   }

function addFilter(type, value, display_value, skip_hash)
{
    if (type == "language")
    {
        //Delect the other select filted
        var selected_lang_filters = [];
        
         for (var key in window.td_current_filters) {
             if (window.td_current_filters.hasOwnProperty(key)) {
                 var f = window.td_current_filters[key];
                 
                 if (f.type == "language")
                 {
                     removeFilter(f.type, f.value);
                 }
             }
         }
    }
    
    var key = type + value;
    
    window.td_current_filters[key] = {
        type: type,
        value: value,
        display_value: display_value
    };
    
    jQuery(".td-filter-checkbox[data-value='" + value + "']").prop("checked", true);
    
    buildCurrentView(skip_hash);
    
    if (type == "language")
    {
         jQuery(".td-filter-checkbox[data-type='language']").prop("disabled", false);
         jQuery(".td-filter-checkbox[data-value='" + value + "']").prop("disabled", true);
    }
    
    resetFilterExpander();
}

function removeFilter(type, value, skip_add, skip_build)
{    
    var key = type + value;
    
    if (window.td_current_filters[key])
    {
        delete window.td_current_filters[key];        
    }
    
    //Uncheck the box:
        
    jQuery(".td-filter-checkbox[data-value='" + value + "']").prop("checked", false);
    
    if (!skip_build)
    {
        buildCurrentView();
        langIt(skip_add);
    }
    
    resetFilterExpander();
}

function langIt(skip_add)
{
    //check for language filter bug

    var language_found = false;

    for (var key in window.td_current_filters) {
        if (window.td_current_filters.hasOwnProperty(key)) {  
            var filter = window.td_current_filters[key];

            if (filter.type == "language")
            {
                language_found = true;
            }
        }
    }

    if (!language_found && !skip_add)
    {
        addFilter("language", td_default_langauge, lookupKey("language"), true);
        removeFilter("language", td_default_langauge, true);        
    } 
}

function buildCurrentView(skip_hash)
{
    window.td_current_page = 1;
    window.td_current_view = [];
    
    for (var key in window.td_all) {
      if (window.td_all.hasOwnProperty(key)) {
          if (Object.keys(window.td_current_filters).length > 0)
          {
              var doc = window.td_all[key];
              
              var doc_failed_filter = false;
              
              for (var filter_key in window.td_current_filters)
              {
                  if (window.td_current_filters.hasOwnProperty(filter_key))
                  {
                      var filter = window.td_current_filters[filter_key];
                      
                      if (filter)
                      {          
                          var pieces = doc[filter.type].split(",");
                          
                          var piece_passed_filter = false;
                          
                          for (var i = 0; i < pieces.length; i++)
                          {
                              var piece = pieces[i].trim();
                              
                              if ( piece == filter.value)
                              {
                                  piece_passed_filter = true;
                                  break;
                              }
                          } 
                          
                          if (!piece_passed_filter)
                          {
                              doc_failed_filter = true;
                          }
                      }
                  }
              }
              
              if (!doc_failed_filter)
              {
                  window.td_current_view.push(doc);
              }
          }
          else
          {
            //No filters, add everything    
            window.td_current_view.push(window.td_all[key]);
          } 
      }
    }
    
    if (window.td_current_view.length > 0)
    {
        jQuery(".td-no-results-found").hide();
    }
    else
    {
        jQuery(".td-no-results-found").show();
    }
    
    redraw_td_table();
    if (!skip_hash)
    {
        set_filter_hash();
    }
    
}

function set_filter_hash()
{
    if (Object.keys(window.td_current_filters).length > 0)
    {
        var filter_hash = "?";
        for (var filter_key in window.td_current_filters)
        {
            var filter = window.td_current_filters[filter_key];

            filter_hash += filter.type + "=" + filter.value + "&";
        }

        filter_hash = filter_hash.replace(/(^&)|(&$)/g, "");
        window.history.pushState(null,null, filter_hash);
    }
    else
    {
        window.history.pushState(null,null, window.location.pathname);
    }
}

function redraw_td_filters()
{
    if (Object.keys(window.td_current_filters).length > 0) {
    
        jQuery(".sw-filter-value").each(function(){
        
            var type = jQuery(this).data("filter-type");
            var value = jQuery(this).data("filter-value");

            var found = false;

            for (var i = 0; i < window.td_current_view.length; i++)
            {
                var doc = window.td_current_view[i];

                if (doc[type].indexOf(value) > -1)
                {
                    found = true;
                    break;
                }
            }

            if (found)
            {
                jQuery(this).addClass('show-filtered-td');
                jQuery(this).removeClass('hide-filtered-td');
            }
            else
            {
                jQuery(this).addClass('hide-filtered-td'); 
                jQuery(this).removeClass('show-filtered-td');
            }
            
            //Always show Language
            if (type == 'language')
            {
                jQuery(this).addClass('show-filtered-td');
                jQuery(this).removeClass('hide-filtered-td');
            }
        });   
    }
    else
    {
        //jQuery(".sw-filter-value").show();
        jQuery(".sw-filter-value").addClass('sw-td-show').removeClass('sw-td-hide');
    }
    
    //Update collapse toggles && counts
    jQuery(".td-filter-root").each(function(){
        var visible_filters = jQuery(this).find(".sw-filter-value.show-filtered-td");
        
        if (visible_filters.length <= 10)
        {
            jQuery(this).find('.td_filter_expand').addClass('sw-td-hide').removeClass('sw-td-show').nextAll('.sw-filter-value').removeClass('td_filter_hidden');
            jQuery(this).find('.td_filter_contract').addClass('sw-td-hide').removeClass('sw-td-show');
        }
        else
        {
            jQuery(this).find('.td_filter_expand').addClass('sw-td-show').removeClass('sw-td-hide');
        }
        
        //Update count here
        for (var i = 0; i < visible_filters.length; i++)
        {
            var type = jQuery(visible_filters[i]).data("filter-type");
            var value = jQuery(visible_filters[i]).data("filter-value"); 
            var count = getDocCount(type, value);
            
            jQuery(visible_filters[i]).find(".sw-filter-count").html(count);
            
            if (i >= 10 && type != "language")
            {
                jQuery(visible_filters[i]).addClass('td_filter_hidden');
            }
        }
    });
    
    //Update the filter view
    jQuery(".td-filter-display").html('');
    
    if (Object.keys(window.td_current_filters).length > 0) {
        for (var key in window.td_current_filters) {
            var filter = window.td_current_filters[key];
            
            if (filter.type != "language")
            {
                 jQuery(".td-filter-display").append('<div class="filter-display"><span>' + filter.display_value + ': </span><span class="filter-btn" data-filter-type="' + filter.type + '" data-filter-value="' + filter.value + '">' + filter.value + '</span></div>');
            }
           
        }
    }
}

function getDocCount(type, value)
{
    var count = 0;
  
    for (var i = 0; i < window.td_current_view.length; i++)
    {
        var doc = window.td_current_view[i];

        if (doc[type].indexOf(value) > -1)
        {
            count = count + 1;
        }
    }    
    
    return count;
}

function buildDataWithoutLangFilter()
{
    for (var key in window.td_all) {
      if (window.td_all.hasOwnProperty(key)) {
          if (Object.keys(window.td_current_filters).length > 0)
          {
              var doc = window.td_all[key];
              
              var doc_failed_filter = false;
              
              for (var filter_key in window.td_current_filters)
              {
                  if (window.td_current_filters.hasOwnProperty(filter_key))
                  {
                      var filter = window.td_current_filters[filter_key];
                      
                      if (filter)
                      {          
                          var pieces = doc[filter.type].split(",");
                          
                          var piece_passed_filter = false;
                          
                          for (var i = 0; i < pieces.length; i++)
                          {
                              var piece = pieces[i].trim();
                              
                              if ( piece == filter.value)
                              {
                                  piece_passed_filter = true;
                                  break;
                              }
                          } 
                          
                          if (!piece_passed_filter)
                          {
                              doc_failed_filter = true;
                          }
                      }
                  }
              }
              
              if (!doc_failed_filter)
              {
                  window.td_current_view.push(doc);
              }
          }
          else
          {
            //No filters, add everything    
            window.td_current_view.push(window.td_all[key]);
          } 
      }
    }
}

function redraw_td_table()
{
    apply_text_search();
    
    var page = window.td_current_page;
    var per_page = window.td_per_page;
    
    var current_doc_1 = (page * per_page) - per_page + 1;
    
    if (window.td_current_view.length == 0)
    {
        current_doc_1 = 0;
    }
    jQuery(".current_doc_1").html(current_doc_1);
    
    var current_doc_2 = Math.min(page * per_page, window.td_current_view.length);
    
    jQuery(".current_doc_2").html(current_doc_2);
    jQuery(".total_docs").html(window.td_current_view.length);
    jQuery(".td-content").html('');
    
    var td_doc_html = '';
    
    if (window.td_view_type == 'list')
    {
        td_doc_html += '<table class="table table-responsive">';
        td_doc_html += '<thead>';
        td_doc_html += '<th>Title</th>';
        td_doc_html += '<th>Product Category</th>';
        td_doc_html += '<th>Model</th>';
        td_doc_html += '<th>File Type</th>';
        td_doc_html += '</thead>';
        td_doc_html += '<tbody>';
    }
    
    if (window.td_view_type == 'card')
    {
        td_doc_html += '<div class="td-card-container">';
    }

    for (var i = ((page * per_page) - per_page); i < (per_page * page); i++)
    {
        var doc = window.td_current_view[i];
        
        if (doc)
        {
            if (window.td_view_type == 'list')
            {
                td_doc_html += '<tr>';
                td_doc_html += '<td><a target="_blank" href="' + doc.url + '">' + doc.title + '</a></td>';
                td_doc_html += '<td>' + doc.category + '</td>';
                td_doc_html += '<td>' + doc.product + '</td>';
                td_doc_html += '<td>' + doc.file_type + '</td>';
                td_doc_html += '</tr>';
            }
            
            if (window.td_view_type == 'card')
            {
                var file_type = '<span class="file-type">' + doc.file_type + '</span>';
                
                if (doc.file_type == "pdf")
                {
                    file_type = '<a class="file-type-icon" target="_blank" href="' + doc.url + '"><img src="/wp-content/plugins/swtechdocs/img/pdf-icon.png"></a>';
                }
                
                td_doc_html += '<div class="row td-card-row">';
                td_doc_html += '<div class="col-xs-12">';
                td_doc_html += '<h3><a target="_blank" href="' + doc.url + '">' + doc.title +'</a></h3>';
                td_doc_html += '<p><a target="_blank" href="' + doc.url + '"><span class="td-italics">' + lookupKey('resources') + ':</span> ' + doc.resources + '</a></p>';
                
                td_doc_html += file_type;
                
                td_doc_html += '</div>';
                td_doc_html += '</div>';
            }

        }
    }
    
    if (window.td_view_type == 'list')
    {
        td_doc_html += '</tbody>';
        td_doc_html += '</table>';
    }
    
    if (window.td_view_type == 'card')
    {
        td_doc_html += '</div>';
    }
    
    jQuery(".td-main-content").html(td_doc_html);
    
    if (window.td_current_view.length == 0)
    {
        td_doc_html = "<tr><td>No results found...</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>";
        jQuery(".td-content").append(td_doc_html);
    }
    
    redraw_td_filters();
    build_pagination(page, per_page);
}

function apply_text_search()
{
    var text_search = jQuery('.td_text_search').val();
    
    if (text_search.length > 0)
    {
        //Update the search term for the error
        jQuery(".td-search-results-term").html(text_search);
        var filtered_results = [];
        
        for (var i = 0; i < window.td_current_view.length; i++)
        {
            var doc = window.td_current_view[i];
            
            if (doc && doc.title && doc.title.toUpperCase().indexOf(text_search.toUpperCase()) > -1) {
              filtered_results.push(doc);
            }
        }
        
        window.td_current_view = filtered_results;
    }
    else
    {
      //check for language filter bug

        var language_found = false;

        for (var key in window.td_current_filters) {
            if (window.td_current_filters.hasOwnProperty(key)) {  
                var filter = window.td_current_filters[key];

                if (filter.type == "language")
                {
                    language_found = true;
                }
            }
        }

        if (!language_found)
        {
            addFilter("language", td_default_langauge, lookupKey("language"), true);  
            removeFilter("language", td_default_langauge, true, true);
        } 
    }

    if (window.td_current_view.length > 0)
    {
        jQuery(".td-no-results-found").hide();
    }
    else
    {
        jQuery(".td-no-results-found").show();
    }

}

function build_pagination(page, per_page)
{
    var pages = Math.ceil(window.td_current_view.length / per_page);
    
    if (page <= 1)
    {
        jQuery(".td_toc_prev a").addClass('disable');        
    }
    else
    {
        jQuery(".td_toc_prev a").removeClass('disable');        
    }

    if (page >= pages)
    {
        jQuery(".td_toc_next a").addClass('disable');
    }
    else
    {
        jQuery('.td_toc_next a').removeClass('disable');        
    }
    
    var page_html = "";
    
    var nice_pages = pagination(page, pages);
    
    for (var i = 0; i < nice_pages.length; i++)
    {
        var active = '';

        var p = nice_pages[i];
        
        if (p == "...")
        {
             page_html += '<span>' + p + '</span>';
        }
        else
        {
            if (p == page)
            {
                active = 'active';        
            }

             page_html += '<a class="td_page_toggle ' + active + '" data-page="' + p + '">' + p + '</a>';
        }
        
    }
    
//    for (var i = 1; i <= pages; i++)
//    {
//        var active = '';
//        
//        if (i == page)
//        {
//            active = 'active';        
//        }
//        
//        page_html += '<a class="td_page_toggle ' + active + '" data-page="' + i + '">' + i + '</a>';    
//    }
    
    jQuery(".td_toc_pages").html(page_html);
}

function pagination(c, m) {
  var current = c,
      last = m,
      delta = 2,
      left = current - delta,
      right = current + delta + 1,
      range = [],
      rangeWithDots = [],
      l;

  for (var i = 1; i <= last; i++) {
    if (i == 1 || i == last || i >= left && i < right) {
      range.push(i);
    }
  }

  for (var _i = 0, _range = range; _i < _range.length; _i++) {
    var _i2 = _range[_i];

    if (l) {
      if (_i2 - l === 2) {
        rangeWithDots.push(l + 1);
      } else if (_i2 - l !== 1) {
        rangeWithDots.push('...');
      }
    }

    rangeWithDots.push(_i2);
    l = _i2;
  }

  return rangeWithDots;
}

//Beta login
jQuery(document).ready(function(){    
    jQuery("#betaLoginModalBtn").click(function(){
        jQuery('#betaLoginModal').show();
    });
    
    jQuery(".beta-modal span.close").click(function(){
        jQuery('#betaLoginModal').hide();
    });
    
    jQuery("#betaLoginForm").submit(function(e){
        e.preventDefault();
        jQuery(this).find(':submit').val('Loading...').prop( "disabled", true );
        
        jQuery.ajax({
             type : "post",
             dataType : "json",
             url : "/wp-admin/admin-ajax.php",
             data : {action: "techdocs_beta_login"},
             success: function(response) {
                window.location.reload();
             }
          });
    });
    
    jQuery("#betaLogoutBtn").click(function(){
       jQuery(this).html('Loading...').prop( "disabled", true );
        
        jQuery.ajax({
             type : "post",
             dataType : "json",
             url : "/wp-admin/admin-ajax.php",
             data : {action: "techdocs_beta_login"},
             success: function(response) {
                window.location.reload();
             }
          });
    });
});
