<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
//pointer keeps a reference to the type node in product list
    var dataset = null, pricelistdescription = null, pointer = [];

    jQuery(document).on('gform_post_render',function() {

        //disableRegForm(); //disable registration form by default

        //make copies of textbox inputs with dropdowns
        var upgradeTypeName = jQuery('.upgrade-type').attr('name');
        jQuery('.upgrade-type').attr('name',"upgradeType").hide();
		upgrade_type = 'select class="newUpgradeType" name="'+upgradeTypeName+'"><option value="">Choose Your Type</option></select>';
        jQuery('<' + upgrade_type).insertBefore('.upgrade-type');

        var upgradeBrandName = jQuery('.upgrade-brand').attr('name');
        jQuery('.upgrade-brand').attr('name','upgradeBrand').hide();
		upgrade_brand = 'select class="newUpgradeBrand" name="'+upgradeBrandName+'"><option value="">Choose Your Brand & Model</option></select>';
        jQuery('<' + upgrade_brand).insertBefore('.upgrade-brand');

        var upgradeAvailabilityName = jQuery('.upgrade-availability').attr('name');
        jQuery('.upgrade-availability').attr('name',"upgradeAvailability").hide();
		upgrade_availablity = 'select class="newUpgradeAvailability" name="'+upgradeAvailabilityName+'"><option value="">Available SonicWall Upgrades</option></select>';
        jQuery('<' + upgrade_availablity).insertBefore('.upgrade-availability');

        //disable all dropdowns initially until logic enables them
        jQuery('.newUpgradeBrand').attr("disabled", true);
        jQuery('.newUpgradeAvailability').attr("disabled", true);

        jQuery.ajax({
            url: '/wp-content/uploads/xml/sonicwall-upgrade-productlist-v2.xml',
            dataType: 'xml',
            beforeSend: function () {
                jQuery('.newUpgradeType').attr("disabled", true);
            },
            success: function (data) {
                jQuery('.newUpgradeType').attr("disabled", false);

                dataset = data;

                jQuery(dataset).find('Types').children().each(function () {
                    jQuery('.newUpgradeType').append('<option value="' + jQuery(this)[0].getAttribute('name') + '">' + jQuery(this)[0].getAttribute('name') + '</option>');
                });
            }
        });

        jQuery.ajax({
            url: '/wp-content/uploads/xml/sonicwall-upgrade-pricelist-v2.xml',
            dataType: 'xml',
            success: function (data) {
                pricelistdescription = data;
            }
        });


        jQuery('.newUpgradeType').on('change', function () {
            disableRegForm(); //disables the form

            jQuery('.newUpgradeBrand').attr("disabled", true).children().not(":first").remove();
            jQuery('.newUpgradeAvailability').attr("disabled", true).children().not(":first").remove();
            jQuery('#optionalDescription').html('');

            jQuery('.newUpgradeBrand').attr("disabled", false);
            populate(jQuery(this).val(), '.newUpgradeBrand');

        });

        jQuery('.newUpgradeBrand').on('change', function () {
            disableRegForm();
            jQuery('.newUpgradeAvailability').attr("disabled", true).children().not(":first").remove();
            jQuery('#optionalDescription').html('');


            jQuery('.newUpgradeAvailability').attr("disabled", false);
            populate(jQuery(this).val(), '.newUpgradeAvailability');

        });

        jQuery('.newUpgradeAvailability').on('change', function () {
            if (jQuery(this).val() == '') {
                disableRegForm();
            }
            else {
                enableRegForm(); //enable the form

                var productName = jQuery(this).val().replace(/\s/g, ''), description = '', sku = ' ';

                jQuery('#optionalDescription').html('');

                jQuery(pricelistdescription).find("Products").find('Product').each(function () {

                    var productXMLName = jQuery(this)[0].getAttribute('name').replace(/\s/g, '');

                    if (productXMLName == productName || productXMLName.indexOf(productName) !== -1) {
                        var html = '';
                        jQuery(this).find('UpgradeOption').each(function () {
                            html += '<div style="margin-bottom:10px"><p>' + jQuery(this).find('description').text() + '</p>'
                            var pricelist = '', arr = [
                                ['Oneyrprice', '1 year'],
                                ['Twoyrprice', '2 years'],
                                ['Threeyrprice', '3 years']
                            ];

                            for (var i in arr) {
                                if (jQuery(this).find(arr[i][0]).length && jQuery(this).find(arr[i][0]).text().length) {
                                    if (jQuery(this).find(arr[i][0]).length > 1) {//if more than one node with the same number of years
                                        jQuery(this).find(arr[i][0]).each(function () {
                                            sku = ' ';
                                            if (this.getAttribute('sku')) {
                                                sku = ' SKU: ' + this.getAttribute('sku') + '  ';
                                            }
                                            if (jQuery('html').hasClass('ie8') || jQuery('html').hasClass('ie9')) {
                                                pricelist += ' <li><p>' + sku + ' - <span class="price">' + jQuery(this).text() + '</span></p></li>';
                                            } else {
                                                pricelist += ' <li><p>' + sku + ' - <span class="price">' + this.textContent + '</span></p></li>';
                                            }
                                        });
                                    } else {
                                        sku = ' ';
                                        if (jQuery(this).find(arr[i][0])[0].getAttribute('sku')) {
                                            sku = ' SKU: ' + jQuery(this).find(arr[i][0])[0].getAttribute('sku') + '  ';
                                        }
                                        pricelist += ' <li><p>' + sku + ' - <span class="price">' + jQuery(this).find(arr[i][0]).text() + '</span> for ' + arr[i][1] + '</p></li>';

                                    }
                                }
                            }

                            if (pricelist != '') {
                                html += '<ul>' + pricelist + '</ul>';
                            }

                         //   if (jQuery(this).find('DataSheet').length && jQuery(this).find('DataSheet').text() != '') {
                         //       html += '<a href="' + jQuery(this).find('DataSheet').text() + '" target="_blank">Read Datasheet</a>';
                         //   }

                            if (jQuery(this).find('ComparisonDocs').length) {
                                jQuery(this).find('ComparisonDocs').find('ComparisonDoc').each(function () {
                                    if (jQuery(this)[0].getAttribute('name') == jQuery('#slct1').val()) {
                                        html += ' | <a href="' + jQuery(this).text() + '" target="_blank">Read Comparison Document</a>';
                                    }
                                });
                            }
                            html += '</div>';
                        });
						jQuery('#optionalDescription').show();
                        jQuery('#optionalDescription').html(html);
						jQuery('#input_45_96').val('Product Type: ' + jQuery('.newUpgradeType').val() + " - Currently Owned: " + jQuery('.newUpgradeBrand').val() + " - Selected Upgrade: " + jQuery('.newUpgradeAvailability').val());
                        return false;
                    }
                });
				jQuery('#optionalDescription').show();
                jQuery('#optionalDescription').find('li').each(function () {
                    description += jQuery(this).text() + "n";
                });
				jQuery('#regadditionalinfo').show();
                jQuery('#regadditionalinfo').val('Type: ' + jQuery('.newUpgradeType').val() + "\nBrand Model: " + jQuery('.newUpgradeBrand').val() + "\nUpgrading To: " + jQuery('.newUpgradeAvailability').val() + "\nDescription: " + description);
            }
        });

        jQuery('#banner-link').append('<span class="" style="top: 61px; width: 905px;">\
	  <span class="headline" style="color: #ffffff;">Get a great deal on deeper <br />network security </span>\
	  <span class="subheadline" style="color: #ffffff;">Join the Dell Security Customer Advantage Program.</span>\
	  <span class="action text" style="color: #ffffff;">Read Data Sheet<span>›</span></span>\
	</span>');
    });

    jQuery(window).load(function () {
        jQuery('.newUpgradeType, .newUpgradeBrand, .newUpgradeAvailability').val('');
		disableRegForm();
    });

    function populate(product, target) {

        jQuery(target).children().not(':first').remove(); //remove all options except the first

        var show = false;

        if (target == '.newUpgradeBrand') {
            jQuery(dataset).find('Type').each(function () {
                if (jQuery(this)[0].getAttribute('name') == product) {

                    jQuery(this).find('Product').each(function () {
                         jQuery(target).append('<option value="' + jQuery(this).attr('name') + '">' + jQuery(this).attr('name') + '</option>'); //print each model under the brand
                    });

//                    jQuery(this).find('Product').each(function () {
//
//
//                            jQuery(target).append('<optgroup label="' + jQuery(this).attr('name') + '">'); //print the brand
//
//                            jQuery(this).find("UpgradeProduct").each(function () {
//                                jQuery(target).append('<option value="' + jQuery(this).text() + '">' + jQuery(this).text() + '</option>'); //print each model under the brand
//                            });
//
//                        show = true;
//
//                    });


                    if (!show) {
                        populate(product, '.newUpgradeAvailability');
                    }

                    return false;
                }
            });

        } else if (target == '.newUpgradeAvailability') { //map the sw product to available upgrades from the selected model

            jQuery(dataset).find('Product').each(function () {
                if (jQuery(this)[0].getAttribute('name') == product) {

                    jQuery(this).find('UpgradeProduct').each(function () {
                        jQuery(target).append('<option value="' + jQuery(this).text() + '">' + jQuery(this).text() + '</option>'); //print each model under the brand
                    });
                }
            });
        }
    }

    function enableRegForm() {
        var form = jQuery('#gform_45');

        form.find(':input').each(function () {
            if (jQuery(this).is(':visible')) {
                jQuery(this).prop('disabled', false);
            }
        });

    }

    function disableRegForm() {

        var form = jQuery('#gform_45');

        form.find(':input').each(function () {
			if (jQuery(this).is(':visible')) {
				jQuery(this).prop('disabled', true);
            }
        });
		jQuery('.newUpgradeType').prop('disabled', false);
		
    }

</script>
<!-- end Simple Custom CSS and JS -->
