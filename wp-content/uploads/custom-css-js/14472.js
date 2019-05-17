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




jQuery(document).ready(function($) {
    //	SonicWall Country - Region Selector Script
	
	$("#input_45_14").addClass('crs-country');
	$("#input_45_132").addClass('crs-state');

    $('.crs-state').attr('autocomplete','off'); //turn off autocomplete to prevent bad data here

    //	Set Country selector field attributes as required by the script
    $("select.crs-country").attr("data-region-id", "crs-state").attr("data-value", "shortcode").attr("data-default-option", "Select Country").attr("data-show-default-option", "true");

    //	Set State selector field attributes as required by the script.  By default this is disabled and says N/A
    $("select.crs-state").attr("data-blank-option", "N/A").attr("data-value", "shortcode").attr("data-default-option", "N/A").attr("data-show-default-option", "true").prop("disabled", true).attr("title", "You have not selected a country yet, or your selection does not require a state entry.").removeClass("required");


    $('select.crs-country').change(function() {
		
        if($(this).val() == "US"){
            //$('.canada-optin').show();
            //$('.canada-optin').hide();
            // $('.canada-optin input').rules('add','required');
        }
        else {
            //$('.canada-optin').hide();
            //$('.canada-optin').show();
            //$('.canada-optin input').rules('remove','required');
        }
		
        // If the country is one that has subregions, then enable the state field, if not disable it.
        switch ($(this).val()) {
            case 'AU':
            case 'BR':
            case 'CA':
            case 'CN':
            case 'DE':
            case 'ES':
            case 'FR':
            case 'GB':
            case 'IE':
            case 'IN':
            case 'IT':
            case 'JP':
            case 'MX':
            case 'SE':
            case 'US':
                $("select.crs-state").attr("data-blank-option", "State/Province").attr("data-value", "shortcode").attr("data-default-option", "State/Province").attr("data-show-default-option", "true").prop("disabled", false).attr("title", "Please select the state/province you are in..").addClass("required");
                break;

            default:
                $("select.crs-state").attr("data-blank-option", "N/A").attr("data-value", "shortcode").attr("data-default-option", "N/A").attr("data-show-default-option", "true").prop("disabled", true).attr("title", "You have not selected a country yet, or your selection does not require a state entry.").removeClass("required");
                break;
        }
    });

    /*!
     * country-region-selector
     * ------------------------
     * 0.3.6
     * @author Ben Keen
     * @repo https://github.com/benkeen/country-region-selector
     * @licence MIT
     *
     * Note:	Modified state selector to use class instead of id.
     *			Modified States & Regions to match SonicWall list
     */

    ! function(a, b) {
        if ("function" == typeof define && define.amd) define([], b);
        else if ("object" == typeof exports) try {
            module.exports = b(require())
        } catch (c) {
            module.exports = b()
        } else a.crs = b(a)
    }(this, function() {
        "use strict";
        var a = "crs-country",
            b = "Select country",
            c = "Select region",
            d = !0,
            e = !0,
            f = [],
            g = [
                ["United States of America", "US", "Alabama~AL|Alaska~AK|Arizona~AZ|Arkansas~AR|California~CA|Colorado~CO|Connecticut~CT|Delaware~DE|District of Columbia~DC|Florida~FL|Georgia~GA|Guam~GU|Hawaii~HI|Idaho~ID|Illinois~IL|Indiana~IN|Iowa~IA|Kansas~KS|Kentucky~KY|Louisiana~LA|Maine~ME|Maryland~MD|Massachusetts~MA|Michigan~MI|Minnesota~MN|Mississippi~MS|Missouri~MO|Montana~MT|Nebraska~NE|Nevada~NV|New Hampshire~NH|New Jersey~NJ|New Mexico~NM|New York~NY|North Carolina~NC|North Dakota~ND|Ohio~OH|Oklahoma~OK|Oregon~OR|Pennsylvania~PA|Puerto Rico~PR|Rhode Island~RI|South Carolina~SC|South Dakota~SD|Tennessee~TN|Texas~TX|US Virgin Islands~VI|Utah~UT|Vermont~VT|Virginia~VA|Washington~WA|West Virginia~WV|Wisconsin~WI|Wyoming~WY"],
                ["United Kingdom", "GB", "England~ENG|Northern Ireland~NIR|Scotland~SCT|Wales~WL"],
                ["Brazil", "BR", "Acre~AC|Alagoas~AL|Amapá~AP|Amazonas~AM|Bahia~BA|Ceará~CE|Distrito Federal~DF|Espírito Santo~ES|Goiás~GO|Maranhão~MA|Mato Grosso~MT|Mato Grosso do Sul~MS|Minas Gerais~MG|Pará~PA|Paraíba~PB|Paraná~PR|Pernambuco~PE|Piauí~PI|Rio de Janeiro~RJ|Rio Grande do Norte~RN|Rio Grande do Sul~RS|Rondônia~RO|Roraima~RR|Santa Catarina~SC|São Paulo~SP|Sergipe~SE|Tocantins~TO"],
                ["Canada", "CA", "Alberta~AB|British Columbia~BC|Manitoba~MB|New Brunswick~NB|Newfoundland and Labrador~NL|Northwest Territories~NT|Nova Scotia~NS|Nunavut~NU|Ontario~ON|Prince Edward Island~PE|Quebec~QC|Saskatchewan~SK|Yukon Territories~YT"],
                ["France", "FR", "Alsace~A|Aquitaine~B|Auvergne~C|Burgundy~D|Brittany~E|Centre-Val de Loire~F|Champagne-Ardenne~G|Corsica~H|Franche-Comté~I|Ile-de-France~J|Languedoc-Roussillon~K|Limousin~L|Lorraine~M|Lower Normandy~P|Midi-Pyrénées~N|Nord-Pas-de-Calais~O|Pays de la Loire~R|Picardy~S|Poitou-Charentes~T|Provence-Alpes-Cote d'Azur~U|Rhône-Alpes~V|Upper Normandy~Q"],
                ["Germany", "DE", "Baden-Wurttemberg~BW|Bavaria~BY|Berlin~BE|Brandenburg~BB|Bremen~HB|Hamburg~HH|Hesse~HE|Lower Saxony~NI|Mecklenburg-Vorpommern~MV|North Rhine-Westphalia~NW|Rhineland-Palatinate~RP|Saarland~SL|Saxony~SN|Saxony-Anhalt~ST|Schleswig-Holstein~SH|Thuringia~TH"],
                ["India", "IN", "Andaman and Nicobar Islands~AN|Andhra Pradesh~AP|Arunachal Pradesh~AR|Assam~AS|Bihar~BR|Chandigarh~CH|Chhattisgarh~CT|Dadra and Nagar Haveli~DN|Daman and Diu~DD|Delhi~DL|Goa~GA|Gujarat~GJ|Haryana~HR|Himachal Pradesh~HP|Jammu and Kashmir~JK|Jharkhand~JH|Karnataka~KA|Kerala~KL|Lakshadweep~LD|Madhya Pradesh~MP|Maharashtra~MH|Manipur~MN|Meghalaya~ML|Mizoram~MZ|Nagaland~NL|Odisha~OR|Puducherry~PY|Punjab~PB|Rajasthan~RJ|Sikkim~SK|Tamil Nadu~TN|Telangana~TS|Tripura~TR|Uttar Pradesh~UP|Uttarakhand~UT|West Bengal~WB"],
                ["Italy", "IT", "Agrigento~AG|Alessandria~AL|Ancona~AN|Aosta~AO|Arezzo~AR|Ascoli Piceno~AP|Asti~AT|Avellino~AV|Bari~BA|Barletta-Andria-Trani~BT|Belluno~BL|Benevento~BN|Bergamo~BG|Biella~BI|Bologna~BO|Bolzano~BZ|Brescia~BS|Brindisi~BR|Cagliari~CA|Caltanissetta~CL|Campobasso~CB|Carbonia-Iglesias~CI|Caserta~CE|Catania~CT|Catanzaro~CZ|Chieti~CH|Como~CO|Cosenza~CS|Cremona~CR|Crotone~KR|Cuneo~CN|Enna~EN|Fermo~FM|Ferrara~FE|Florence~FI|Foggia~FG|Forlì-Cesena~FC|Frosinone~FR|Genoa~GE|Gorizia~GO|Grosseto~GR|Imperia~IM|Isernia~IS|L'Aquila~AQ|La Spezia~SP|Latina~LT|Lecce~LE|Lecco~LC|Livorno~LI|Lodi~LO|Lucca~LU|Macerata~MC|Mantua~MN|Massa and Carrara~MS|Matera~MT|Medio Campidano~VS|Messina~ME|Milan~MI|Modena~MO|Monza and Brianza~MB|Naples~NA|Novara~NO|Nuoro~NU|Ogliastra~OG|Olbia-Tempio~OT|Oristano~OR|Padua~PD|Palermo~PA|Parma~PR|Pavia~PV|Perugia~PG|Pesaro and Urbino~PU|Pescara~PE|Piacenza~PC|Pisa~PI|Pistoia~PT|Pordenone~PN|Potenza~PZ|Prato~PO|Ragusa~RG|Ravenna~RA|Reggio Calabria~RC|Reggio Emilia~RE|Rieti~RI|Rimini~RN|Rome~RM|Rovigo~RO|Salerno~SA|Sassari~SS|Savona~SV|Siena~SI|Sondrio~SO|Syracuse~SR|Taranto~TA|Teramo~TE|Terni~TR|Trapani~TP|Trento~TN|Treviso~TV|Trieste~TS|Turin~TO|Udine~UD|Varese~VA|Venice~VE|Verbano-Cusio-Ossola~VB|Vercelli~VC|Verona~VR|Vibo Valentia~VV|Vicenza~VI|Viterbo~VT"],
                ["Spain", "ES", "Andalucía~AN|Aragon~AR|Asturias Principado de~AS|Canarias~CN|Cantabria~CB|Castilla y Leon~CL|Castilla-La Mancha~CM|Catalunya~CT|Ceuta~CE|Extremadura~EX|Galicia~GA|Illes Balears~IB|La Rioja~RI|Madrid, Comunidad de~MD|Melilla~ML|Murcia~MC|Navarra~NC|Pais Vasco~PV|Valenciana~VC"],
                ["──────────────────", "", ""],
                ["Afghanistan", "AF", "N/A~"],
                ["Aland Islands", "AX", "N/A~"],
                ["Albania", "AL", "N/A~"],
                ["Algeria", "DZ", "N/A~"],
                ["American Samoa", "AS", "N/A~"],
                ["Andora", "AD", "N/A~"],
                ["Angola", "AO", "N/A~"],
                ["Anguilla", "AI", "N/A~"],
                ["Antarctica", "AQ", "N/A~"],
                ["Antigua and Barbuda", "AG", "N/A~"],
                ["Argentina", "AR", "N/A~"],
                ["Armenia", "AM", "N/A~"],
                ["Aruba", "AW", "N/A~"],
                ["Australia", "AU", "Australian Capital Territory~ACT|New South Wales~NSW|Northern Territory~NT|Queensland~QLD|South Australia~SA|Tasmania~TAS|Victoria~VIC|Western Australia~WA"],
                ["Austria", "AT", "N/A~"],
                ["Azerbaijan", "AZ", "N/A~"],
                ["Bahamas", "BS", "N/A~"],
                ["Bahrain", "BH", "N/A~"],
                ["Bangladesh", "BD", "N/A~"],
                ["Barbados", "BB", "N/A~"],
                ["Belarus", "BY", "N/A~"],
                ["Belgium", "BE", "N/A~"],
                ["Belize", "BZ", "N/A~"],
                ["Benin", "BJ", "N/A~"],
                ["Bermuda", "BM", "N/A~"],
                ["Bhutan", "BT", "N/A~"],
                ["Bolivia, Plurinational State of", "BO", "N/A~"],
                ["Bonaire, Sint Eustatius and Saba", "BQ", "N/A~"],
                ["Bosnia And Herzegovina", "BA", "N/A~"],
                ["Botswana", "BW", "N/A~"],
                ["Bouvet Island", "BV", "N/A~"],
                ["Brazil", "BR", "Acre~AC|Alagoas~AL|Amapá~AP|Amazonas~AM|Bahia~BA|Ceará~CE|Distrito Federal~DF|Espírito Santo~ES|Goiás~GO|Maranhão~MA|Mato Grosso~MT|Mato Grosso do Sul~MS|Minas Gerais~MG|Pará~PA|Paraíba~PB|Paraná~PR|Pernambuco~PE|Piauí~PI|Rio de Janeiro~RJ|Rio Grande do Norte~RN|Rio Grande do Sul~RS|Rondônia~RO|Roraima~RR|Santa Catarina~SC|São Paulo~SP|Sergipe~SE|Tocantins~TO"],
                ["British Indian Ocean Territory", "IO", "N/A~"],
                ["Brunei Darussalam", "BN", "N/A~"],
                ["Bulgaria", "BG", "N/A~"],
                ["Burkina Faso", "BF", "N/A~"],
                ["Burundi", "BI", "N/A~"],
                ["Cambodia", "KH", "N/A~"],
                ["Cameroon", "CM", "N/A~"],
                ["Canada", "CA", "Alberta~AB|British Columbia~BC|Manitoba~MB|New Brunswick~NB|Newfoundland and Labrador~NL|Northwest Territories~NT|Nova Scotia~NS|Nunavut~NU|Ontario~ON|Prince Edward Island~PE|Quebec~QC|Saskatchewan~SK|Yukon Territories~YT"],
                ["Cape Verde", "CV", "N/A~"],
                ["Cayman Islands", "KY", "N/A~"],
                ["Central African Republic", "CF", "N/A~"],
                ["Chad", "TD", "N/A~"],
                ["Chile", "CL", "N/A~"],
                ["China", "CN", "Anhui~34|Beijing~11|Chinese Taipei~71|Chongqing~50|Fujian~35|Gansu~62|Guangdong~44|Guangxi~45|Guizhou~52|Hainan~46|Hebei~13|Heilongjiang~23|Henan~41|Hong Kong~91|Hubei~42|Hunan~43|Jiangsu~32|Jiangxi~36|Jilin~22|Liaoning~21|Macao~92|Nei Mongol~15|Ningxia~64|Qinghai~63|Shaanxi~61|Shandong~37|Shanghai~31|Shanxi~14|Sichuan~51|Tianjin~12|Xinjiang~65|Xizang~54|Yunnan~53|Zhejiang~33"],
                ["Chinese Taipei", "TW", "N/A~"],
                ["Christmas Island", "CX", "N/A~"],
                ["Cocos (Keeling) Islands", "CC", "N/A~"],
                ["Colombia", "CO", "N/A~"],
                ["Comoros", "KM", "N/A~"],
                ["Congo", "CG", "N/A~"],
                ["Congo, the Democratic Republic of the", "CD", "N/A~"],
                ["Cook Islands", "CK", "N/A~"],
                ["Costa Rica", "CR", "N/A~"],
                ["Cote d'Ivoire", "CI", "N/A~"],
                ["Croatia", "HR", "N/A~"],
                ["Cuba", "CU", "N/A~"],
                ["Curaçao", "CW", "N/A~"],
                ["Cyprus", "CY", "N/A~"],
                ["Czech Republic", "CZ", "N/A~"],
                ["Denmark", "DK", "N/A~"],
                ["Djibouti", "DJ", "N/A~"],
                ["Dominica", "DM", "N/A~"],
                ["Dominican Republic", "DO", "N/A~"],
                ["Ecuador", "EC", "N/A~"],
                ["Egypt", "EG", "N/A~"],
                ["El Salvador", "SV", "N/A~"],
                ["Equatorial Guinea", "GQ", "N/A~"],
                ["Eritrea", "ER", "N/A~"],
                ["Estonia", "EE", "N/A~"],
                ["Ethiopia", "ET", "N/A~"],
                ["Falkland Islands (Malvinas)", "FK", "N/A~"],
                ["Faroe Islands", "FO", "N/A~"],
                ["Fiji", "FJ", "N/A~"],
                ["Finland", "FI", "N/A~"],
                ["France", "FR", "Alsace~A|Aquitaine~B|Auvergne~C|Burgundy~D|Brittany~E|Centre-Val de Loire~F|Champagne-Ardenne~G|Corsica~H|Franche-Comté~I|Ile-de-France~J|Languedoc-Roussillon~K|Limousin~L|Lorraine~M|Lower Normandy~P|Midi-Pyrénées~N|Nord-Pas-de-Calais~O|Pays de la Loire~R|Picardy~S|Poitou-Charentes~T|Provence-Alpes-Cote d'Azur~U|Rhône-Alpes~V|Upper Normandy~Q"],
                ["French Guiana", "GF", "N/A~"],
                ["French Polynesia", "PF", "N/A~"],
                ["French Southern Territories", "TF", "N/A~"],
                ["Gabon", "GA", "N/A~"],
                ["Gambia", "GM", "N/A~"],
                ["Georgia", "GE", "N/A~"],
                ["Germany", "DE", "Baden-Wurttemberg~BW|Bavaria~BY|Berlin~BE|Brandenburg~BB|Bremen~HB|Hamburg~HH|Hesse~HE|Lower Saxony~NI|Mecklenburg-Vorpommern~MV|North Rhine-Westphalia~NW|Rhineland-Palatinate~RP|Saarland~SL|Saxony~SN|Saxony-Anhalt~ST|Schleswig-Holstein~SH|Thuringia~TH"],
                ["Ghana", "GH", "N/A~"],
                ["Gibraltar", "GI", "N/A~"],
                ["Greece", "GR", "N/A~"],
                ["Greenland", "GL", "N/A~"],
                ["Grenada", "GD", "N/A~"],
                ["Guadeloupe", "GP", "N/A~"],
                ["Guam", "GU", "N/A~"],
                ["Guatemala", "GT", "N/A~"],
                ["Guernsey", "GG", "N/A~"],
                ["Guinea", "GN", "N/A~"],
                ["Guinea-Bissau", "GW", "N/A~"],
                ["Guyana", "GY", "N/A~"],
                ["Haiti", "HT", "N/A~"],
                ["Heard Island and McDonald Islands", "HM", "N/A~"],
                ["Holy See (Vatican City State)", "VA", "N/A~"],
                ["Honduras", "HN", "N/A~"],
                ["Hong Kong China", "HK", "N/A~"],
                ["Hungary", "HU", "N/A~"],
                ["Iceland", "IS", "N/A~"],
                ["India", "IN", "Andaman and Nicobar Islands~AN|Andhra Pradesh~AP|Arunachal Pradesh~AR|Assam~AS|Bihar~BR|Chandigarh~CH|Chhattisgarh~CT|Dadra and Nagar Haveli~DN|Daman and Diu~DD|Delhi~DL|Goa~GA|Gujarat~GJ|Haryana~HR|Himachal Pradesh~HP|Jammu and Kashmir~JK|Jharkhand~JH|Karnataka~KA|Kerala~KL|Lakshadweep~LD|Madhya Pradesh~MP|Maharashtra~MH|Manipur~MN|Meghalaya~ML|Mizoram~MZ|Nagaland~NL|Odisha~OR|Puducherry~PY|Punjab~PB|Rajasthan~RJ|Sikkim~SK|Tamil Nadu~TN|Telangana~TS|Tripura~TR|Uttar Pradesh~UP|Uttarakhand~UT|West Bengal~WB"],
                ["Indonesia", "ID", "N/A~"],
                ["Iran, Islamic Republic of", "IR", "N/A~"],
                ["Iraq", "IQ", "N/A~"],
                ["Ireland", "IE", "Carlow~CW|Cavan~CN|Clare~CE|Cork~CO|Donegal~DL|Dublin~D|Galway~GA|Kerry~KY|Kildare~KE|Kilkenny~KK|Laois~LS|Leitrim~LM|Limerick~LK|Longford~LD|Louth~LH|Mayo~MO|Meath~MH|Monaghan~MN|Offaly~OY|Roscommon~RN|Sligo~SO|Tipperary~TA|Waterford~WD|Westmeath~WH|Wexford~WX|Wicklow~WW"],
                ["Isle of Man", "IM", "N/A~"],
                ["Israel", "IL", "N/A~"],
                ["Italy", "IT", "Agrigento~AG|Alessandria~AL|Ancona~AN|Aosta~AO|Arezzo~AR|Ascoli Piceno~AP|Asti~AT|Avellino~AV|Bari~BA|Barletta-Andria-Trani~BT|Belluno~BL|Benevento~BN|Bergamo~BG|Biella~BI|Bologna~BO|Bolzano~BZ|Brescia~BS|Brindisi~BR|Cagliari~CA|Caltanissetta~CL|Campobasso~CB|Carbonia-Iglesias~CI|Caserta~CE|Catania~CT|Catanzaro~CZ|Chieti~CH|Como~CO|Cosenza~CS|Cremona~CR|Crotone~KR|Cuneo~CN|Enna~EN|Fermo~FM|Ferrara~FE|Florence~FI|Foggia~FG|Forlì-Cesena~FC|Frosinone~FR|Genoa~GE|Gorizia~GO|Grosseto~GR|Imperia~IM|Isernia~IS|L'Aquila~AQ|La Spezia~SP|Latina~LT|Lecce~LE|Lecco~LC|Livorno~LI|Lodi~LO|Lucca~LU|Macerata~MC|Mantua~MN|Massa and Carrara~MS|Matera~MT|Medio Campidano~VS|Messina~ME|Milan~MI|Modena~MO|Monza and Brianza~MB|Naples~NA|Novara~NO|Nuoro~NU|Ogliastra~OG|Olbia-Tempio~OT|Oristano~OR|Padua~PD|Palermo~PA|Parma~PR|Pavia~PV|Perugia~PG|Pesaro and Urbino~PU|Pescara~PE|Piacenza~PC|Pisa~PI|Pistoia~PT|Pordenone~PN|Potenza~PZ|Prato~PO|Ragusa~RG|Ravenna~RA|Reggio Calabria~RC|Reggio Emilia~RE|Rieti~RI|Rimini~RN|Rome~RM|Rovigo~RO|Salerno~SA|Sassari~SS|Savona~SV|Siena~SI|Sondrio~SO|Syracuse~SR|Taranto~TA|Teramo~TE|Terni~TR|Trapani~TP|Trento~TN|Treviso~TV|Trieste~TS|Turin~TO|Udine~UD|Varese~VA|Venice~VE|Verbano-Cusio-Ossola~VB|Vercelli~VC|Verona~VR|Vibo Valentia~VV|Vicenza~VI|Viterbo~VT"],
                ["Jamaica", "JM", "N/A~"],
                ["Japan", "JP", "Aichi~23|Akita~05|Aomori~02|Chiba~12|Ehime~38|Fukui~18|Fukuoka~40|Fukushima~07|Gifu~21|Gunma~10|Hiroshima~34|Hokkaido~01|Hyogo~28|Ibaraki~08|Ishikawa~17|Iwate~03|Kagawa~37|Kagoshima~46|Kanagawa~14|Kochi~39|Kumamoto~43|Kyoto~26|Mie~24|Miyagi~04|Miyazaki~45|Nagano~20|Nagasaki~42|Nara~29|Niigata~15|Oita~44|Okayama~33|Okinawa~47|Osaka~27|Saga~41|Saitama~11|Shiga~25|Shimane~32|Shizuoka~22|Tochigi~09|Tokushima~36|Tokyo~13|Tottori~31|Toyama~16|Wakayama~30|Yamagata~06|Yamaguchi~35|Yamanashi~19"],
                ["Jersey", "JE", "N/A~"],
                ["Jordan", "JO", "N/A~"],
                ["Kazakhstan", "KZ", "N/A~"],
                ["Kenya", "KE", "N/A~"],
                ["Kiribati", "KI", "N/A~"],
                ["Korea, Democratic People's Republic of", "KP", "N/A~"],
                ["Korea, Republic of", "KR", "N/A~"],
                ["Kuwait", "KW", "N/A~"],
                ["Kyrgyzstan", "KG", "N/A~"],
                ["Lao People's Democratic Republic", "LA", "N/A~"],
                ["Latvia", "LV", "N/A~"],
                ["Lebanon", "LB", "N/A~"],
                ["Lesotho", "LS", "N/A~"],
                ["Liberia", "LR", "N/A~"],
                ["Libyan Arab Jamahiriya", "LY", "N/A~"],
                ["Liechtenstein", "LI", "N/A~"],
                ["Lithuania", "LT", "N/A~"],
                ["Luxembourg", "LU", "N/A~"],
                ["Macao China", "MO", "N/A~"],
                ["Macedonia, the former Yugoslav Republic of", "MK", "N/A~"],
                ["Madagascar", "MG", "N/A~"],
                ["Malawi", "MW", "N/A~"],
                ["Malaysia", "MY", "N/A~"],
                ["Maldives", "MV", "N/A~"],
                ["Mali", "ML", "N/A~"],
                ["Malta", "MT", "N/A~"],
                ["Marshall Islands", "MH", "N/A~"],
                ["Martinique", "MQ", "N/A~"],
                ["Mauritania", "MR", "N/A~"],
                ["Mauritius", "MU", "N/A~"],
                ["Mayotte", "YT", "N/A~"],
                ["Mexico", "MX", "Aguascalientes~AG|Baja California~BC|Baja California Sur~BS|Campeche~CM|Chiapas~CS|Chihuahua~CH|Coahuila~CO|Colima~CL|Durango~DG|Federal District~DF|Guanajuato~GT|Guerrero~GR|Hidalgo~HG|Jalisco~JA|Mexico State~ME|Michoacán~MI|Morelos~MO|Nayarit~NA|Nuevo León~NL|Oaxaca~OA|Puebla~PB|Querétaro~QE|Quintana Roo~QR|San Luis Potosí~SL|Sinaloa~SI|Sonora~SO|Tabasco~TB|Tamaulipas~TM|Tlaxcala~TL|Veracruz~VE|Yucatán~YU|Zacatecas~ZA"],
                ["Micronesia", "FM", "N/A~"],
                ["Moldova, Republic of", "MD", "N/A~"],
                ["Monaco", "MC", "N/A~"],
                ["Mongolia", "MN", "N/A~"],
                ["Montenegro", "ME", "N/A~"],
                ["Montserrat", "MS", "N/A~"],
                ["Morocco", "MA", "N/A~"],
                ["Mozambique", "MZ", "N/A~"],
                ["Myanmar", "MM", "N/A~"],
                ["Namibia", "NA", "N/A~"],
                ["Nauru", "NR", "N/A~"],
                ["Nepal", "NP", "N/A~"],
                ["Netherlands", "NL", "N/A~"],
                ["New Caledonia", "NC", "N/A~"],
                ["New Zealand", "NZ", "N/A~"],
                ["Nicaragua", "NI", "N/A~"],
                ["Niger", "NE", "N/A~"],
                ["Nigeria", "NG", "N/A~"],
                ["Niue", "NU", "N/A~"],
                ["Norfolk Island", "NF", "N/A~"],
                ["Northern Mariana Islands", "MP", "N/A~"],
                ["Norway", "NO", "N/A~"],
                ["Oman", "OM", "N/A~"],
                ["Pakistan", "PK", "N/A~"],
                ["Palau", "PW", "N/A~"],
                ["Palestinian Territory, Occupied", "PS", "N/A~"],
                ["Panama", "PA", "N/A~"],
                ["Papua New Guinea", "PG", "N/A~"],
                ["Paraguay", "PY", "N/A~"],
                ["Peru", "PE", "N/A~"],
                ["Philippines", "PH", "N/A~"],
                ["Pitcairn", "PN", "N/A~"],
                ["Poland", "PL", "N/A~"],
                ["Portugal", "PT", "N/A~"],
                ["Puerto Rico", "PR", "N/A~"],
                ["Qatar", "QA", "N/A~"],
                ["Reunion", "RE", "N/A~"],
                ["Romania", "RO", "N/A~"],
                ["Russian Federation", "RU", "N/A~"],
                ["Rwanda", "RW", "N/A~"],
                ["Saint Barthélemy", "BL", "N/A~"],
                ["Saint Helena, Ascension and Tristan da Cunha", "SH", "N/A~"],
                ["Saint Kitts and Nevis", "KN", "N/A~"],
                ["Saint Lucia", "LC", "N/A~"],
                ["Saint Martin (French part)", "MF", "N/A~"],
                ["Saint Pierre and Miquelon", "PM", "N/A~"],
                ["Saint Vincent and the Grenadines", "VC", "N/A~"],
                ["Samoa", "WS", "N/A~"],
                ["San Marino", "SM", "N/A~"],
                ["Sao Tome and Principe", "ST", "N/A~"],
                ["Saudi Arabia", "SA", "N/A~"],
                ["Senegal", "SN", "N/A~"],
                ["Serbia", "RS", "N/A~"],
                ["Seychelles", "SC", "N/A~"],
                ["Sierra Leone", "SL", "N/A~"],
                ["Singapore", "SG", "N/A~"],
                ["Sint Maarten (Dutch part)", "SX", "N/A~"],
                ["Slovakia", "SK", "N/A~"],
                ["Slovenia", "SI", "N/A~"],
                ["Solomon Islands", "SB", "N/A~"],
                ["Somalia", "SO", "N/A~"],
                ["South Africa", "ZA", "N/A~"],
                ["South Georgia and the South Sandwich Islands", "GS", "N/A~"],
                ["South Sudan", "SS", "N/A~"],
                ["Spain", "ES", "Andalucía~AN|Aragon~AR|Asturias Principado de~AS|Canarias~CN|Cantabria~CB|Castilla y Leon~CL|Castilla-La Mancha~CM|Catalunya~CT|Ceuta~CE|Extremadura~EX|Galicia~GA|Illes Balears~IB|La Rioja~RI|Madrid, Comunidad de~MD|Melilla~ML|Murcia~MC|Navarra~NC|Pais Vasco~PV|Valenciana~VC"],
                ["Sri Lanka", "LK", "N/A~"],
                ["Sudan", "SD", "N/A~"],
                ["Suriname", "SR", "N/A~"],
                ["Svalbard and Jan Mayen", "SJ", "N/A~"],
                ["Swaziland", "SZ", "N/A~"],
                ["Sweden", "SE", "Blekinge~K|Dalarna~W|Gavleborg~X|Gotland~I|Halland~N|Jamtland~Z|Jonkoping~F|Kalmar~H|Kronoberg~G|Norrbotten~BD|Orebro~T|Ostergötland~E|Skane~M|Sodermanland~D|Stockholm~AB|Uppsala~C|Varmland~S|Vasterbotten~AS|Vastmanland~U|Vastra Gotaland~O"],
                ["Switzerland", "CH", "N/A~"],
                ["Syrian Arab Republic", "SY", "N/A~"],
                ["Tajikistan", "TJ", "N/A~"],
                ["Tanzania, United Republic of", "TZ", "N/A~"],
                ["Thailand", "TH", "N/A~"],
                ["Timor-Leste", "TL", "N/A~"],
                ["Togo", "TG", "N/A~"],
                ["Tokelau", "TK", "N/A~"],
                ["Tonga", "TO", "N/A~"],
                ["Trinidad and Tobago", "TT", "N/A~"],
                ["Tunisia", "TN", "N/A~"],
                ["Turkey", "TR", "N/A~"],
                ["Turkmenistan", "TM", "N/A~"],
                ["Turks and Caicos Islands", "TC", "N/A~"],
                ["Tuvalu", "TV", "N/A~"],
                ["Uganda", "UG", "N/A~"],
                ["Ukraine", "UA", "N/A~"],
                ["United Arab Emirates", "AE", "N/A~"],
                ["United Kingdom", "GB", "England~ENG|Northern Ireland~NIR|Scotland~SCT|Wales~WL"],
                ["United States of America", "US", "Alabama~AL|Alaska~AK|American Samoa~AS|Arizona~AZ|Arkansas~AR|Armed Forces Americas~AA|Armed Forces Europe~AE|Armed Forces Pacific~AP|California~CA|Colorado~CO|Connecticut~CT|Delaware~DE|District of Columbia~DC|Federated Micronesia~FM|Florida~FL|Georgia~GA|Guam~GU|Hawaii~HI|Idaho~ID|Illinois~IL|Indiana~IN|Iowa~IA|Kansas~KS|Kentucky~KY|Louisiana~LA|Maine~ME|Marshall Islands~MH|Maryland~MD|Massachusetts~MA|Michigan~MI|Minnesota~MN|Mississippi~MS|Missouri~MO|Montana~MT|Nebraska~NE|Nevada~NV|New Hampshire~NH|New Jersey~NJ|New Mexico~NM|New York~NY|North Carolina~NC|North Dakota~ND|Northern Mariana Islands~MP|Ohio~OH|Oklahoma~OK|Oregon~OR|Palau~PW|Pennsylvania~PA|Puerto Rico~PR|Rhode Island~RI|South Carolina~SC|South Dakota~SD|Tennessee~TN|Texas~TX|United States Minor Outlying Islands~UM|US Virgin Islands~VI|Utah~UT|Vermont~VT|Virginia~VA|Washington~WA|West Virginia~WV|Wisconsin~WI|Wyoming~WY"],
                ["Uruguay", "UY", "N/A~"],
                ["Uzbekistan", "UZ", "N/A~"],
                ["Vanuatu", "VU", "N/A~"],
                ["Venezuela, Bolivarian Republic of", "VE", "N/A~"],
                ["Viet Nam", "VN", "N/A~"],
                ["Virgin Islands, British", "VG", "N/A~"],
                ["Virgin Islands, U.S.", "VI", "N/A~"],
                ["Wallis and Futuna", "WF", "N/A~"],
                ["Western Sahara", "EH", "N/A~"],
                ["Yemen", "YE", "N/A~"],
                ["Zambia", "ZM", "N/A~"],
                ["Zimbabwe", "ZW", ""]
            ],
            h = function() {
                $("." + a).each(i)
            },
            i = function() {
                var a = this,
                    c = a.getAttribute("data-crs-loaded");
                if ("true" !== c) {
                    a.length = 0;
                    var e = $(a).attr("data-default-option"),
                        g = e ? e : b,
                        h = a.getAttribute("data-show-default-option");
                    d = null === h ? !0 : "true" === h;
                    var i = $(a).attr("data-default-value"),
                        l = $(a).attr("data-value"),
                        o = 0;
                    d && (this.options[0] = new Option(g, "")), k({
                        whitelist: a.getAttribute("data-whitelist"),
                        blacklist: a.getAttribute("data-blacklist")
                    });
                    for (var p = 0; p  0) {
                            n(a, s);
                            var t = $(s).attr("data-default-value"),
                                u = "shortcode" === s.getAttribute("data-value");
                            if (null !== t) {
                                var v = d ? a.selectedIndex - 1 : a.selectedIndex,
                                    w = f[v][3];
                                m(s, w, t, u)
                            }
                        } else d === !1 && n(a, s);
                    else console.error("Region dropdown DOM node with ID " + r + " not found.");
                    a.setAttribute("data-crs-loaded", "true")
                }
            },
            j = function(a) {
                var b = $(a).attr("data-blank-option"),
                    c = b ? b : "-",
                    d = a.getAttribute("data-show-default-option");
                e = null === d ? !0 : "true" === d, a.length = 0, e && (a.options[0] = new Option(c, ""), a.selectedIndex = 0)
            },
            k = function(a) {
                var b = g,
                    c = [],
                    d = 0;
                if (a.whitelist) {
                    var e = a.whitelist.split(",");
                    for (d = 0; d < g.length; d++) - 1 !== e.indexOf(g[d][1]) && c.push(g[d]);
                    b = c
                } else if (a.blacklist) {
                    var h = a.blacklist.split(",");
                    for (d = 0; d < g.length; d++) - 1 === h.indexOf(g[d][1]) && c.push(g[d]);
                    b = c
                }
                f = b, l()
            },
            l = function() {
                for (var a = 0; a < f.length; a++) {
                    for (var b = {
                        hasShortcodes: /~/.test(f[a][2]),
                        regions: []
                    }, c = f[a][2].split("|"), d = 0; d < c.length; d++) {
                        var e = c[d].split("~");
                        b.regions.push([e[0], e[1]])
                    }
                    f[a][3] = b
                }
            },
            m = function(a, b, c, d) {
                for (var f = 0; f < b.regions.length; f++) {
                    var g = d && b.hasShortcodes && b.regions[f][1] ? b.regions[f][1] : b.regions[f][0];
                    if (g === c) {
                        a.selectedIndex = e ? f + 1 : f;
                        break
                    }
                }
            },
            n = function(a, b) {
                var g = d ? a.selectedIndex - 1 : a.selectedIndex,
                    h = $(b).attr("data-default-option"),
                    i = b.getAttribute("data-value"),
                    k = h ? h : c;
                if ("" === a.value) j(b);
                else {
                    b.length = 0, e && (b.options[0] = new Option(k, ""));
                    for (var l = f[g][3], m = 0; m < l.regions.length; m++) {
                        var n = "shortcode" === i && l.hasShortcodes ? l.regions[m][1] : l.regions[m][0];
                        b.options[b.length] = new Option(l.regions[m][0], n)
                    }
                    b.selectedIndex = 0
                }
            };
        return $(h), {
            init: h
        }
    });

});


</script>
<!-- end Simple Custom CSS and JS -->
