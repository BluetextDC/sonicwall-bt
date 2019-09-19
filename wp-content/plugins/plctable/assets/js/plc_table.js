jQuery(function ($) {
setTimeout(function(){
    if ($("pre.plc-formatted-data").length > 0) {
        var urlHash = window.location.hash;
        window.addEventListener('hashchange', function (e) { // url hashchange event
            if (urlHash !== window.location.hash) {
                urlParser(true);
            }
        });
        const jsonData = $.parseJSON($("pre.plc-formatted-data").get(0).innerHTML.replace(/<[^>]*>/gi, ''));
        $("#plc-product-list-holder").append('<select name="product_list" id="product-selector"><option value="" selected="true" disabled="disabled" style="opacity:0.1;">Select a product to view its lifecycle</option></select>');
        $.parseJSON($("pre.plc-product-list-array").get(0).innerHTML).forEach(function (val) {
            $("select#product-selector").append('<option value="' + val.trim().toLowerCase().replace(/\(|\)/gi, '').replace(/\s/gi, '-') + '">' + val + '</option>');
        });
        $("pre.plc-product-list-array").remove();
        $("pre.plc-formatted-data").remove();
        const tableConstJson = {"Hardware":{"headers":{"sortid":"","model":"Model","lod":"Last Order Day","armb":". ARM Begin.","arme":". ARM End.","oneyldo":".1 Year LDO.","lrmb":". LRM Begin.","lrme":". LRM End.","eos":"End of Support"}},"Software":{"headers":{"sortid":"","version":"Version","fsaof":"Full Support Date","lsaof":"Limited Support Date","sd":"Support Discontinued Date"}},"Firmware":{"headers":{"sortid":"","release":"Release","model":".     Model     .","type":". Type .","rd":"Release Date","eos":". EOS Date .","status":".  Status  .","upgrade":"Recommended Upgrade"}}};

        $("#plc-product-list-holder #product-selector").on("change", function (e) {
            $("div.sw-product-content-container").length ? $("div.sw-product-content-container").remove() : '';
            $("#plc-product-list-holder").after('<div class="sw-product-content-container"></div>');
            productTitleRender($(this).val());
        });

        function productTitleRender(prodName) {
            const prodTitle = Object.keys(jsonData).find(function (val) {
                return val.toLocaleLowerCase().replace(/-|\(|\)/gi, " ").replace(/\s/gi, "") === prodName.replace(/-|\(|\)/gi, " ").replace(/\s/gi, "");
            });
            if (prodTitle) {
                $("div.sw-product-title-container").length ? $("div.sw-product-title-container").remove() : '';
                $("div.sw-product-content-container").prepend('<div class="sw-product-title-container"><b>' + prodTitle + '</b></div>');
                productTabsRender(jsonData[prodTitle]);
            }
        }

        function productTabsRender(prodTypes) {
            const tabOrd = Object.keys(tableConstJson);
            const prodTabs = Object.keys(prodTypes).sort(function(a,b) {
                return tabOrd.indexOf(a) - tabOrd.indexOf(b);
            });
            $("div.sw-product-title-container").after('<div class="sw-plc-product-type-holder"><div class="tabcontainer top_tab el_after_av_one_full  el_before_av_one_full" id="sw-custom-tab"><div class="tab_titles"></div></div></div>');
            prodTabs.forEach(function (val, ind) {
                $("#sw-custom-tab div.tab_titles").append('<div class="tab sw-plc-product-type tab_counter_' + ind + '">' + val + '</div>');
                $("#sw-custom-tab").append('<section class="av_tab_section"><div class="tab fullsize-tab sw-plc-product-type tab_counter_' + ind + '">' + val + '</div><div id="tab-id-container ' + val.toLowerCase() + '" class="tab_content ' + val.toLowerCase() + '"><div class="tab_inner_content invers-color"></div></div></section>');
                $("#sw-custom-tab div.tab").on("click", function (e) {
                    e.stopImmediatePropagation();
                    const trgt = e.target.innerHTML;
                    const tempKey = Object.keys(prodTypes).find(function (v) {
                        return v.toLowerCase() === trgt.toLowerCase();
                    });
                    $("#sw-custom-tab .tab").removeClass('active_tab');
                    $("#sw-custom-tab .tab.tab_counter_" + ind).addClass('active_tab');
                    $('#sw-custom-tab .av_tab_section .tab_content').removeClass('active_tab_content');
                    $('#sw-custom-tab .av_tab_section .tab_content.' + tempKey.toLowerCase()).addClass('active_tab_content');
                    tableRender(prodTypes[tempKey], tempKey);
                    urlParser(false);
                });
                if (ind === 0) {
                    $("div.sw-plc-product-type.tab_counter_" + ind).trigger('click');
                }
            });
        }

        function tableRender(tableContent, target) {
            $("div.product-table-holder").length ? $("div.product-table-holder").remove() : '';
            const tooltip = {
                Software: {
                    fsaof: "Fully supported, generally available release/version. Enhancement requests for this release are accepted and may be considered for future releases. Maintenance releases and/or hot fixes are periodically made available for this release. Release/version is fully supported by both Support and Development. Release/version is available for download from Support Portal.", 
                    lsaof: "Limited Support Support is available for this release/version, and we will use best efforts to provide known workarounds or fixes. No new code fixes will be generated except under extreme circumstances and at SonicWall\'s discretion. Enhancement requests are not accepted. Customers are encouraged to plan an upgrade to a release/version on  Full Support .", 
                    sd: "Discontinued versions which are retired or discontinued. No new patches or fixes will be created for this release. Not available for download from Support Portal. Support will be provided to assist with upgrading to a supported version. Support is not obligated to provide assistance on this version of the product."
                }, 
                Hardware: {
                    lod: "Last Order Day is the last day to order the product from SonicWall and signifies SonicWall\'s intent to start the end of life process. The duration of this phase is variable and depends on numerous factors including material availability, SonicWall and channel inventory and end-user demand. Last Day Order is informational only; products in this phase are active. SonicWall continues to sell support contracts.", 
                    armb: "Active Retirement Mode is an announcement by SonicWall to indicate that it is no longer actively manufacturing or selling the product. Products in ARM are removed from all price lists and marketing collateral at this time. Support contracts for products in this phase will remain on price lists and will continue to be available for purchase until the phase has ended. During this time SonicWall may release a limited number of new features and will issue bug fixes only to the latest version of firm", 
                    arme: "Active Retirement Mode is an announcement by SonicWall to indicate that it is no longer actively manufacturing or selling the product. Products in ARM are removed from all price lists and marketing collateral at this time. Support contracts for products in this phase will remain on price lists and will continue to be available for purchase until the phase has ended. During this time SonicWall may release a limited number of new features and will issue bug fixes only to the latest version of firm", 
                    oneyldo: "1-Year Support Last Day Order represents the final day to purchase a 1-year support contract or subscription service that bundles support from SonicWall. Partners and customers may purchase and activate the 1-year support contract so that the product will be eligible to receive support until the product has reached End of Support.", 
                    lrmb: "Limited Retirement Mode (LRM) is an announcement by SonicWall to indicate that it will no longer develop or release firmware updates or new features for these products. Software and firmware support for products in LRM is limited to critical bugs and security vulnerabilities. Software/firmware support and hardware warranty are available throughout LRM for products with an active support contract. The duration of this phase is three years beginning one day after the end of Active Retirement Mode.", 
                    lrme: "Limited Retirement Mode (LRM) is an announcement by SonicWall to indicate that it will no longer develop or release firmware updates or new features for these products. Software and firmware support for products in LRM is limited to critical bugs and security vulnerabilities. Software/firmware support and hardware warranty are available throughout LRM for products with an active support contract. The duration of this phase is three years beginning one day after the end of Active Retirement Mode.", 
                    eos: "End of Support (EOS) is an announcement by SonicWall to indicate that it will no longer provide technical support, firmware updates/upgrades or hardware replacement for the product, and that all remaining unique inventory or materials will become unavailable. SonicWall may continue to offer security service subscriptions such as Content Filtering and Intrusion Prevention during the End of Support phase, but it will no longer provide technical support for the product or any security service running on it. Should a technical issue arise on one of the subscription services that is offered during the End of Support phase, customers may be required to transition to an upgrade product at their own cost. Certain remaining entitlements on the End of Support appliance may be transitioned to the upgrade appliance upon request.", 
                }, 
                Firmware: {
                    type: "A complete explanation of release types is available at the bottom of this page. (GR) General Release: General Release software is a mature, widely deployed and proven release, used for production environments. (FR) Feature Release: Feature Release software is a new release that introduces major new features in the product. (MR) Maintenance Release: Maintenance Release software includes bug fixes and enhancements made to a previous release (IR) Initial Release: Initial release software is the first release of a new product. (ER) Early Release: This is software that includes incremental changes to a previous release. (Hotfix) Hotfix Release: Contains the latest fixes and patches, and are provided to customers who are looking to address specific issues.", 
                    rd: "The date the firmware was released", 
                    status: "The status field indicates whether or not a firmware version is considered Active. - Active:  This firmware version is considered current and is fully supported. - Upgrade Recommended: This firmware version is approaching its EOS (End of Support) date. A recommended upgrade path is provided for all firmware versions approaching EOS.", 
                    upgrade: "The recommended upgrade section shows the supported upgrade path from an earlier version of firmware to the latest version of SonicOS firmware. Please consult our Upgrade Guides before completing any firmware upgrades.", 
                    eos: "End of Support (EOS) is the date which SonicWall will cease support for the related firmware including fixes or upgrades. A recommended upgrade path is suggested for any firmware version approaching the EOS phase. Should a technical issue arise with firmware during the EOS phase, customers will be required to upgrade to a supported firmware version.", 
                }, 
            };
            const tableData = Object.values(tableContent);
            const headers = tableConstJson[target]['headers'];
            
            if (tableData.length > 0) {
                $("div.sw-plc-product-type-holder div." + target.toLowerCase() + " div.tab_inner_content").append('<div class="product-table-holder"><table id="plc-table" class="plc-table"><thead></thead><tbody></tbody><tfoot></tfoot></table></div>');
                // header render
                $("div.product-table-holder thead").append("<tr></tr>");
                const headKeys = Object.keys(headers);
                Object.values(headers).forEach(function (val, ind) {
                    $("div.product-table-holder thead tr").append('<th class="column-'+(ind+1)+'"style="text-align:center;position:relative">'+val.replace(/\s\s|\./gi, '')+(tooltip[target][headKeys[ind]] ? '<span class="sw-tooltip-plc"><span class="av_font_icon avia_animate_when_visible av-icon-style-  avia-icon-pos-right  avia_start_animation avia_start_delayed_animation" style="color:#ff791a; border-color:#ff791a; margin:0px;"><span class="av-icon-char" style="font-size:12px;line-height:12px;" aria-hidden="true" data-av_icon="\ue81f" data-av_iconfont="entypo-fontello"></span></span><span class="title-tooltip">'+ tooltip[target][headKeys[ind]] +'</span></span>' : '')+'</th>');
            });
                // data rows render

                tableData.forEach(function (value, index) {
                    $("div.product-table-holder tbody").append('<tr class="row-' + (index + 1) + '"></tr>');
                    headKeys.forEach(function (val, ind) {
                        let rowVal = value[val];
                        if (ind === 0 && rowVal) {
                            rowVal = rowVal.match(/\d+$/gi).toString();
                        }

                        $("div.product-table-holder tbody tr.row-" + (index + 1)).append('<td class="column-' + (ind + 1) + '">' + (rowVal === null ? ' - ' : rowVal) + '</td>');
                    });
                });
                initiateDatatable(Object.values(headers));
            }
        }
        function initiateDatatable(cols) {
            // width calc
            const hiddenCols = ['sortid'];
            const totalWidth = cols.reduce(function (s, v) {
                return s + (hiddenCols.includes(v) ? 0 : v.length);
            }, 0);
            const widthCol = cols.map(function (v, i) {
                const totWid = (v.length / totalWidth) * 100;
                return hiddenCols.includes(v) ? { width: "0%", targets: i } : { width: totWid.toFixed(2) + "%", targets: i };
            });
            // table initialize
            const tableConfig = Object.assign({
                responsive: true,
                paging: false,
                searching: false,
                lengthChange: false,
                info: false,
                select: false,
                order: [[0, 'desc'], [1, 'desc']],
                columnDefs: widthCol,
            });
            // sortid column hidden
            $("#plc-table tr .column-1").hide();
            // table initialize
            $('#plc-table').DataTable(tableConfig);
        }
        if (urlHash.length > 0) {
            urlParser(true);
        }
        function urlParser(urlCond) {
            const hash = window.location.hash.match(/#.+/gi) ? window.location.hash.match(/#.+/gi)[0].split('/') : [];
            const condNav = hash[1] !== undefined && hash[2] !== undefined;
            if (urlCond && hash.length > 0 && condNav) {
                if (condNav && hash[1].length > 0 && hash[2].length > 0) {
                    if ($('select#product-selector option[value="' + hash[1] + '"]').length > 0) {
                        $('select#product-selector').val(hash[1]);
                        $('select#product-selector').trigger("change");
                        $(".sw-plc-product-type-holder .tab_titles .tab").each(function (i, el) {
                            if ($(el).get(0).innerHTML.toLowerCase() === hash[2].toLowerCase()) {
                                $(".sw-plc-product-type-holder .tab_titles .tab_counter_" + i).trigger("click");
    
                            }
                        });
                    }
                }
            } else {
               const newHash = window.location.hash === '' ? '/#/' : hash[0];
                window.location.hash = newHash + "/" + $("select#product-selector").children("option:selected").val() + "/" + $(".sw-plc-product-type-holder .tab_titles .active_tab").get(0).innerHTML.toLowerCase() + "/";         
            }
            urlHash = window.location.hash;
        }
    }
 }, 500);
});