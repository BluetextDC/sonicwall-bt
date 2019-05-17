<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
//pointer keeps a reference to the type node in product list
    var dataset = null, pricelistdescription = null, pointer = [];

    jQuery(document).on('gform_post_render',function() {

        
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
