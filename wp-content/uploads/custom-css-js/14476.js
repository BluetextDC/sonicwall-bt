<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
    //pointer keeps a reference to the type node in product list
    var dataset = null, pricelistdescription = null, pointer = [];

    jQuery(document).ready(function () {

        disableRegForm(); //disable registration form by default

        //make copies of textbox inputs with dropdowns
        var upgradeTypeName = $('.upgrade-type').attr('name');
        $('.upgrade-type').attr('name',"upgradeType").hide();;
        $('Choose Your Type').insertBefore('.upgrade-type');

        var upgradeBrandName = $('.upgrade-brand').attr('name');
        $('.upgrade-brand').attr('name','upgradeBrand').hide();;
        $('Choose Your Brand & Model').insertBefore('.upgrade-brand');

        var upgradeAvailabilityName = $('.upgrade-availability').attr('name');
        $('.upgrade-availability').attr('name',"upgradeAvailability").hide();;
        $('Available SonicWall Upgrades').insertBefore('.upgrade-availability');

        //disable all dropdowns initially until logic enables them
        $('.newUpgradeBrand').attr("disabled", true);
        $('.newUpgradeAvailability').attr("disabled", true);

        $.ajax({
            url: '/wp-content/uploads/sites/6/xml/sonicwall-upgrade-productlist-v2.xml',
            dataType: 'xml',
            beforeSend: function () {
                $('.newUpgradeType').attr("disabled", true);
            },
            success: function (data) {
                $('.newUpgradeType').attr("disabled", false);

                dataset = data;

                $(dataset).find('Types').children().each(function () {
                    $('.newUpgradeType').append('' + $(this)[0].getAttribute('name') + '');
                });
            }
        });

        $.ajax({
            url: '/wp-content/uploads/sites/6/xml/sonicwall-upgrade-pricelist-v2.xml',
            dataType: 'xml',
            success: function (data) {
                pricelistdescription = data;
            }
        });


        $('.newUpgradeType').on('change', function () {
            disableRegForm(); //disables the form

            $('.newUpgradeBrand').attr("disabled", true).children().not(":first").remove();
            $('.newUpgradeAvailability').attr("disabled", true).children().not(":first").remove();
            $('#optionalDescription').html('');

            $('.newUpgradeBrand').attr("disabled", false);
            populate($(this).val(), '.newUpgradeBrand');

        });

        $('.newUpgradeBrand').on('change', function () {
            disableRegForm();
            $('.newUpgradeAvailability').attr("disabled", true).children().not(":first").remove();
            $('#optionalDescription').html('');


            $('.newUpgradeAvailability').attr("disabled", false);
            populate($(this).val(), '.newUpgradeAvailability');

        });

        $('.newUpgradeAvailability').on('change', function () {
            if ($(this).val() == '') {
                disableRegForm();
            }
            else {
                enableRegForm(); //enable the form

                var productName = $(this).val().replace(/\s/g, ''), description = '', sku = ' ';

                $('#optionalDescription').html('');

                $(pricelistdescription).find("Products").find('Product').each(function () {

                    var productXMLName = $(this)[0].getAttribute('name').replace(/\s/g, '');

                    if (productXMLName == productName || productXMLName.indexOf(productName) !== -1) {
                        var html = '';
                        $(this).find('UpgradeOption').each(function () {
                            html += '<div style="margin-bottom:10px"><p>' + $(this).find('description').text() + '</p>'
                            var pricelist = '', arr = [
                                ['Oneyrprice', '1 year'],
                                ['Twoyrprice', '2 years'],
                                ['Threeyrprice', '3 years']
                            ];

                            for (var i in arr) {
                                if ($(this).find(arr[i][0]).length && $(this).find(arr[i][0]).text().length) {
                                    if ($(this).find(arr[i][0]).length > 1) {//if more than one node with the same number of years
                                        $(this).find(arr[i][0]).each(function () {
                                            sku = ' ';
                                            if (this.getAttribute('sku')) {
                                                sku = ' SKU: ' + this.getAttribute('sku') + '  ';
                                            }
                                            if ($('html').hasClass('ie8') || $('html').hasClass('ie9')) {
                                                pricelist += ' <li><p>' + sku + ' - <span>' + $(this).text() + '</span></p></li>';
                                            } else {
                                                pricelist += ' <li><p>' + sku + ' - <span>' + this.textContent + '</span></p></li>';
                                            }
                                        });
                                    } else {
                                        sku = ' ';
                                        if ($(this).find(arr[i][0])[0].getAttribute('sku')) {
                                            sku = ' SKU: ' + $(this).find(arr[i][0])[0].getAttribute('sku') + '  ';
                                        }
                                        pricelist += ' <li><p>' + sku + ' - <span>' + $(this).find(arr[i][0]).text() + '</span> for ' + arr[i][1] + '</p></li>';

                                    }
                                }
                            }

                            if (pricelist != '') {
                                html += '<ul>' + pricelist + '</ul>';
                            }

                         //   if ($(this).find('DataSheet').length && $(this).find('DataSheet').text() != '') {
                         //       html += '<a href="' + $(this).find('DataSheet').text() + '" target="_blank">Read Datasheet</a>';
                         //   }

                            if ($(this).find('ComparisonDocs').length) {
                                $(this).find('ComparisonDocs').find('ComparisonDoc').each(function () {
                                    if ($(this)[0].getAttribute('name') == $('#slct1').val()) {
                                        html += ' | <a href="' + $(this).text() + '" target="_blank">Read Comparison Document</a>';
                                    }
                                });
                            }
                            html += '</div>';
                        });
                        $('#optionalDescription').html(html);
						$('#input_9_96').val('Product Type: ' + $('.newUpgradeType').val() + " - Currently Owned: " + $('.newUpgradeBrand').val() + " - Selected Upgrade: " + $('.newUpgradeAvailability').val());
                        return false;
                    }
                });

                $('#optionalDescription').find('li').each(function () {
                    description += $(this).text() + "n";
                });

                $('#regadditionalinfo').val('Type: ' + $('.newUpgradeType').val() + "\nBrand Model: " + $('.newUpgradeBrand').val() + "\nUpgrading To: " + $('.newUpgradeAvailability').val() + "\nDescription: " + description);
            }
        });

        $('#banner-link').append('<span>\
	  <span>Get a great deal on deeper <br />network security </span>\
	  <span>Join the Dell Security Customer Advantage Program.</span>\
	  <span>Read Data Sheet<span>›</span></span>\
	</span>');
    });

    $(window).load(function () {
        $('.newUpgradeType, .newUpgradeBrand, .newUpgradeAvailability').val('');
    });

    function populate(product, target) {

        $(target).children().not(':first').remove(); //remove all options except the first

        var show = false;

        if (target == '.newUpgradeBrand') {
            $(dataset).find('Type').each(function () {
                if ($(this)[0].getAttribute('name') == product) {

                    $(this).find('Product').each(function () {
                         $(target).append('' + $(this).attr('name') + ''); //print each model under the brand
                    });

//                    $(this).find('Product').each(function () {
//
//
//                            $(target).append(''); //print the brand
//
//                            $(this).find("UpgradeProduct").each(function () {
//                                $(target).append('' + $(this).text() + ''); //print each model under the brand
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

            $(dataset).find('Product').each(function () {
                if ($(this)[0].getAttribute('name') == product) {

                    $(this).find('UpgradeProduct').each(function () {
                        $(target).append('' + $(this).text() + ''); //print each model under the brand
                    });
                }
            });
        }
    }

    function enableRegForm() {
        var form = $('.registration-form');

        form.find(':input').each(function () {
            if ($(this).is(':visible')) {
                $(this).prop('disabled', false);
            }
        });

    }

    function disableRegForm() {

        var form = $('.registration-form');

        form.find(':input').each(function () {
            if ($(this).is(':visible')) {
                $(this).prop('disabled', true);
            }
        });
    }

</script>
<!-- end Simple Custom CSS and JS -->
