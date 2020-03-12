<div class="sw-cust-toggle-container">
    <div class="taglist">
        <div class="main-filter">
            <div class="filter-space">
                <div class="filter-heading">
                    <h3 class="filter-title-hide"><?php echo td_translate_str('Filter Results', 'UI-Filter-Results');?>:</h3>
                </div>
            </div>
        </div>
    </div>


    <div class="sw-search" style="position: relative;">
        <input type="search" class="td_text_search" placeholder="<?php echo td_translate_str('Search documents', 'UI-Search-documents');?>">
        <span class="av-icon-char td-search-btn" style="font-size:25px;line-height:25px;width:25px;padding:3px;color:#868686;position:absolute;top:6px;right:5px;" aria-hidden="true" data-av_icon="" data-av_iconfont="entypo-fontello">
            </span>
    </div>


    <div class="sw-cust-accordion td-filter-root td-filter-category">
        <section class="sw-cust-section">
          <div class="sw-cust-toggle">
            <p class="sw-cust-toggler">
              <span class="sw-cust-heading"><?php echo td_translate_str('Product Category', 'UI-Product-Category');?></span>
              <span class="sw-cust-toggle_inner">
                <span class="sw-cust-vert"></span>
                <span class="sw-cust-hor"></span>
              </span>
            </p>
            <div class="sw-cust-container">
                <div class="sw-cust-content">

                    <?php echo td_build_filter("category", $td_data, td_translate_str('Product Category', 'UI-Product-Category')); ?>

                    <div class="td_filter_contract"></div>
                </div>

            </div>
          </div>
        </section>
    </div>


    <div class="sw-cust-accordion td-filter-root td-filter-product">
        <section class="sw-cust-section">
          <div class="sw-cust-toggle">
            <p class="sw-cust-toggler">
              <span class="sw-cust-heading"><?php echo td_translate_str('Product Series', 'UI-Product-Series');?></span>
              <span class="sw-cust-toggle_inner">
                <span class="sw-cust-vert"></span>
                <span class="sw-cust-hor"></span>
              </span>
            </p>
            <div class="sw-cust-container">
                <div class="sw-cust-content">

                    <?php echo td_build_filter("product", $td_data, td_translate_str('Model', 'UI-Model')); ?>

                    <div class="td_filter_contract"></div>
                </div>

            </div>
          </div>
        </section>
    </div>



    <div class="sw-cust-accordion td-filter-root td-filter-resources">
        <section class="sw-cust-section">
          <div class="sw-cust-toggle">
            <p class="sw-cust-toggler">
              <span class="sw-cust-heading"><?php echo td_translate_str('Document Type', 'UI-Document-Type');?></span>
              <span class="sw-cust-toggle_inner">
                <span class="sw-cust-vert"></span>
                <span class="sw-cust-hor"></span>
              </span>
            </p>
            <div class="sw-cust-container">
                <div class="sw-cust-content">

                    <?php echo td_build_filter("resources", $td_data, td_translate_str('Document Type', 'UI-Document-Type')); ?>

                    <div class="td_filter_contract"></div>
                </div>

            </div>
          </div>
        </section>
    </div>

    <div class="sw-cust-accordion td-filter-root td-filter-language">
        <section class="sw-cust-section">
          <div class="sw-cust-toggle">
            <p class="sw-cust-toggler">
              <span class="sw-cust-heading"><?php echo td_translate_str('Language', 'UI-Language');?></span>
              <span class="sw-cust-toggle_inner">
                <span class="sw-cust-vert"></span>
                <span class="sw-cust-hor"></span>
              </span>
            </p>
            <div class="sw-cust-container">
                <div class="sw-cust-content">

                    <?php echo td_build_filter("language", $td_data, td_translate_str('Language', 'UI-Language')); ?>

                    <div class="td_filter_contract"></div>
                </div>

            </div>
          </div>
        </section>
    </div>


</div>