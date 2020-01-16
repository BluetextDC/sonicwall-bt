jQuery(document).ready(function(){
    
    window.td_current_view = [];
    window.td_per_page = 20;
    window.td_current_page = 1;
    window.td_view_type = 'card';
    
    window.td_current_language = window.td_default_langauge;
    
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
                var pieces = urlFilters[key].split(',');
                
                for (var i = 0; i < pieces.length; i++)
                {
                    var piece = pieces[i];
                    addFilter(key, piece, lookupKey(key));  
                }
            }
        }
    }
    
    setDefaultLang();
    
    function setDefaultLang()
    {
        //Set default language
        if (window.td_default_language)
        {
            jQuery('.td-filter-language').find("[data-value='" + window.td_default_language + "']").prop("checked", true);
            addFilter('language', window.td_default_language, lookupKey('language'));
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
            
            var visible_filters = 0;
            
            jQuery(this).find(".sw-filter-count").each(function(){
               if (parseInt(jQuery(this).html()) > 0)
               {
                   visible_filters = visible_filters + 1;
               }
            });
 
            if (visible_filters <= 10)
            {
                jQuery(this).find('.td_filter_expand').addClass('sw-td-hide').removeClass('sw-td-show').nextAll('.sw-filter-value').removeClass('td_filter_hidden');
                jQuery(this).find('.td_filter_contract').addClass('sw-td-hide').removeClass('sw-td-show');
            }
            else
            {
                //Show filter expander
                jQuery(this).find('.td_filter_expand').addClass('sw-td-show').removeClass('sw-td-hide');
            }
        });
   }

function addFilter(type, value, display_value, skip_hash)
{
    if (type == "language")
    {
        window.td_current_language = value;
        jQuery(".td-filter-checkbox[data-type='language']").prop("checked", false);
        jQuery(".td-filter-checkbox[data-type='language']").prop("disabled", false);
        jQuery(".td-filter-checkbox[data-value='" + value + "']").prop("disabled", true);
        jQuery(".td-filter-checkbox[data-value='" + value + "']").prop("checked", true);
    }
    else
    {
        var key = type + value;
    
        window.td_current_filters[key] = {
            type: type,
            value: value,
            display_value: display_value
        };

        jQuery(".td-filter-checkbox[data-value='" + value + "']").prop("checked", true);
    }
    
    buildCurrentView(skip_hash);

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
    }
    
    resetFilterExpander();
}


function buildFilterGroups(potential_filter)
{
    var filter_groups = { }
    
    if (Object.keys(window.td_current_filters).length > 0)
    {
        for (var filter_key in window.td_current_filters)
        {
          if (window.td_current_filters.hasOwnProperty(filter_key))
          {
              var filter = window.td_current_filters[filter_key];

              if (!filter_groups[filter.type])
              {
                filter_groups[filter.type] = [];        
              }

              filter_groups[filter.type].push(filter);
          }
        }
        
        if (potential_filter)
        {
            if (!filter_groups[potential_filter.type])
            {
                filter_groups[potential_filter.type] = [];        
            }
            filter_groups[potential_filter.type].push(potential_filter);
        }
    }
    
    return filter_groups;
}
function calculateView(potential_filter)
{    
    var filter_groups = buildFilterGroups(potential_filter);
    
    var view = {};
    
    //Filter Language first
    for (var key in window.td_all) {
      if (window.td_all.hasOwnProperty(key)) {
          var doc = window.td_all[key];
          
          if (doc.language == window.td_current_language)
          {
              view[doc.slug] = doc;
          }
      }
    }
    
    //Now loop through each filter type and apply sub filters to the data, slowly dwindling the results
    
    if (Object.keys(filter_groups).length > 0)
    {
      for (var order_key in filter_groups)
      {
          if (filter_groups[order_key])
          {
              var filter_group = filter_groups[order_key];
              
              var sub_view = {};
              
              for (var i = 0; i < filter_group.length; i++)
              {
                  var filter = filter_group[i];
                  
                  for (var key in view) {
                      if (view.hasOwnProperty(key)) {

                          var doc = view[key];
                          
                          var pieces = doc[filter.type].split(",");
                          
                          
                          for (var x = 0; x < pieces.length; x++)
                          {
                              var piece = pieces[x].trim();
                              
                              if ( piece == filter.value )
                              {
                                  sub_view[doc.slug] = doc;
                              }
                          } 
                          
                      }
                  }
              }
              
              view = sub_view;
          }
      }
    }
    
    return view
    
}

function buildCurrentView(skip_hash)
{
    window.td_current_page = 1;
    window.td_current_view = [];
    
    var td_filter = calculateView();
    
    if (Object.keys(td_filter).length > 0)
    {
        for (var key in td_filter) {
            window.td_current_view.push(td_filter[key]);
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
    var filter_hash = "?";
    
    filter_hash += "language=" + window.td_current_language + "&";
    
    var filter_groups = buildFilterGroups();
    
    if (Object.keys(filter_groups).length > 0) {
        for (var key in filter_groups) {
            var filter_group = filter_groups[key];
            
            var vals = [];
            
            for (var i = 0; i < filter_group.length; i++)
            {
                var filter = filter_group[i];
                vals.push(filter.value);
            }
            
            filter_hash += key + "=" + vals.join(',') + '&';
        }
    }
    
    filter_hash = filter_hash.replace(/(^&)|(&$)/g, "");
    window.history.pushState(null,null, filter_hash);
}

function redraw_td_filters()
{
    if (Object.keys(window.td_current_filters).length > 0) {
    
        jQuery(".sw-filter-value").each(function(){
        
            var type = jQuery(this).data("filter-type");
            var value = jQuery(this).data("filter-value");

            
            var root_filter_set = false;
            
            for (var filter_key in window.td_current_filters)
            {
                var filter = window.td_current_filters[filter_key];

                if (filter.type == type)
                {
                    root_filter_set = true;
                    break;
                }
            }

            if (root_filter_set)
            {
                jQuery(".td-filter-" + type).addClass("root-filter-set");
            }
            else
            {
                jQuery(".td-filter-" + type).removeClass("root-filter-set");
            }

           
        });   
    }
    else
    {
        jQuery(".sw-filter-value").addClass('sw-td-show').removeClass('sw-td-hide');
        
        jQuery(".sw-filter-value").each(function(){
        
            var type = jQuery(this).data("filter-type");
            jQuery(".td-filter-" + type).removeClass("root-filter-set");
        });
    }
    
    //Update collapse toggles && counts
    jQuery(".td-filter-root:not(.td-filter-language)").each(function(){      
        
        var filters_to_count = jQuery(this).find(".sw-filter-value");
        
        var valid_filters = 0;
        //Update count here
        for (var i = 0; i < filters_to_count.length; i++)
        {
            var type = jQuery(filters_to_count[i]).data("filter-type");
            var value = jQuery(filters_to_count[i]).data("filter-value"); 
            var count = getDocCount(type, value);
            
            jQuery(filters_to_count[i]).find(".sw-filter-count").html(count);
            
            if (count > 0)
            {
                valid_filters = valid_filters + 1;
                jQuery(filters_to_count[i]).show();
            }
            else
            {
                //Hide any filter with a zero count
                jQuery(filters_to_count[i]).hide();
            }
            
            if (valid_filters >= 10)
            {
                jQuery(filters_to_count[i]).addClass('td_filter_hidden');
            }
        }
        
        if (valid_filters > 10)
        {
            jQuery(this).find('.td_filter_expand').addClass('sw-td-hide').removeClass('sw-td-show').nextAll('.sw-filter-value').removeClass('td_filter_hidden');
            jQuery(this).find('.td_filter_contract').addClass('sw-td-hide').removeClass('sw-td-show');
        }
        else
        {
            jQuery(this).find('.td_filter_expand').addClass('sw-td-show').removeClass('sw-td-hide');
        }
    });
    
    //Update the filter view
    jQuery(".td-filter-display").html('');
    
    
    var filter_groups = buildFilterGroups();
    
    if (Object.keys(filter_groups).length > 0) {
        for (var key in filter_groups) {
            var filter_group = filter_groups[key];
            
            var filter_html = "";
            
            for (var i = 0; i < filter_group.length; i++)
            {
                var filter = filter_group[i];
                
                if (i == 0)
                {
                    filter_html = filter_html + '<div class="filter-btn-parent"><p class="filter-btn-title">' + filter.display_value + ': </p><div class="filter-btn-container">';
                }
                
                filter_html = filter_html + '<span class="filter-btn" data-filter-type="' + filter.type + '" data-filter-value="' + filter.value + '">' + filter.value + '</span>';
            }
            
            if (filter_html.length > 0)
            {
                filter_html = filter_html + '</div></div>';
                jQuery(".td-filter-display").append(filter_html);
            }            
        }
    }
    
    resetFilterExpander();
}

function getDocCount(type, value)
{
    var potential_filter = {
        type: type,
        value: value
    };
    
    var tmp_view = calculateView(potential_filter);
    
    var count = 0;
    
    if (Object.keys(tmp_view).length > 0) {
        for (var key in tmp_view) {
            var doc = tmp_view[key];

            if (doc)
            {
                if (doc[type].indexOf(value) > -1)
                {
                    count = count + 1;
                } 
            }
        }
    }
    return count;
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
                else if (doc.file_type == "html")
                {
                    file_type = "";
   
                     if (doc.pdf)
                     {
                         file_type = file_type + '<a class="file-type-icon" target="_blank" href="' + doc.pdf + '"><img src="/wp-content/plugins/swtechdocs/img/pdf-icon.png"></a>';
                     }
                    
                     file_type = file_type + '<a class="file-type-icon" target="_blank" href="' + doc.url + '"><img src="/wp-content/plugins/swtechdocs/img/html-icon.png"></a>';
                }
                
                td_doc_html += '<div class="row td-card-row">';
                td_doc_html += '<div class="col-xs-12">';
                td_doc_html += '<h3><a target="_blank" href="' + doc.url + '">' + doc.title +'</a></h3>';
                td_doc_html += '<p><a target="_blank" href="' + doc.url + '"><span class="td-italics">' + lookupKey('resources') + ':</span> ' + doc.resources + '</a></p>';
                
                td_doc_html += '<div class="file-type-container">' + file_type + '</div>';
                
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
