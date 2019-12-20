<div class="td-container">
    <div class="row">
        <div class="col-md-4 td-filters">
            <?php include(plugin_dir_path(__FILE__).'/techdocs-filter.php');?>
        </div>
        <div class="col-md-8">
            <div class="filter-heading">
                <h3 class="filter-title-hide"><?php echo td_translate_str('Search Results', 'UI-Search-Results');?>:</h3>
                <div class="row">
                    <div class="col-xs-10">
                        <div class="td-filter-display"></div>
                        <p><?php echo td_translate_str('Results', 'UI-Results');?> <span class="current_doc_1"></span>-<span class="current_doc_2"></span> <?php echo td_translate_str('of', 'UI-of');?> <span class="total_docs"></span></p>
                    </div>
                    <div class="col-xs-2">
<!--
                        <div class="td-view-toggle pull-right" role="group">
                            <button class="btn selected" style="float: left;" data-view-type="list">List</button>
                            <button class="btn" style="float: left;" data-view-type="card">Card</button>
                        </div>
-->
                    </div>             
                </div>
            </div>
            <div class="row">
                <div class="td-main-content">
                    <p class="text-center">Loading....</p>
                </div>
                
                <div class="td-no-results-found">
                    <p class="text-center"><?php echo td_translate_str('No results for', 'UI-No-results-for');?> <span class="td-search-results-term"></span></p>
                    <p class="text-center"><?php echo td_translate_str('Check the spelling of your keywords', 'UI-Check-the-spelling-of-your-keywords');?> </p>
                    <p class="text-center"><?php echo td_translate_str('Try using fewer, different or more general keywords', 'UI-Try-using-fewer,-different-or-more-general-keywords');?></p>
                    
                </div>
                
                <p class="text-center">
                    <span class="td_toc_toggle td_toc_prev"><a><?php echo td_translate_str('Previous', 'UI-Previous');?></a> </span> <span class="td_toc_toggle td_toc_pages"></span> <span class="td_toc_toggle td_toc_next"><a><?php echo td_translate_str('Next', 'UI-Next');?></a></span>
                </p>
            </div>
        </div>
    </div>
</div>

<script>

    var td_default_langauge = "<?php echo td_translate_str(TD_get_wpml_lang(), "Language-".TD_get_wpml_lang());?>";
    var td_all = <?php echo json_encode($td_data);?>;
    
    var filter_lang_map = <?php echo json_encode($filter_lang_map);?>

</script>