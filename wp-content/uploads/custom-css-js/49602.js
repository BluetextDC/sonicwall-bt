<script type="application/javascript">
jQuery(document).ready(function() {
    //	SonicWall Country - Region Selector Script
	jQuery(".crs-country-field .gfield_select").addClass('crs-country');
	jQuery(".crs-state-field .gfield_select").addClass('crs-state');
	
	//pre-get the Country - State value;
	p_v_c = jQuery(".crs-country-field .gfield_select").val();
	p_v_s = jQuery(".crs-state-field .gfield_select").val();

    jQuery('.crs-state').attr('autocomplete','off'); //turn off autocomplete to prevent bad data here

    //	Set Country selector field attributes as required by the script
    jQuery("select.crs-country").attr("data-region-id", "crs-state").attr("data-value", "shortcode").attr("data-default-value", p_v_c).attr("data-show-default-option", "true");

    //	Set State selector field attributes as required by the script.  By default this is disabled and says N/A
	state_enabled = true;
	if (p_v_s != '' || p_v_c !=''){
		state_enabled='';
	}
    jQuery("select.crs-state").attr("data-blank-option", "N/A").attr("data-value", "shortcode").attr("data-default-value", p_v_s).attr("data-show-default-option", "true").prop("disabled", state_enabled).attr("title", "You have not selected a country yet, or your selection does not require a state entry.").removeClass("required");
	
	if (p_v_c == 'US' || p_v_c == ''){
		jQuery('.canada-optin').hide();
	}else{
		jQuery('.canada-optin').show();
	}

    jQuery('select.crs-country').change(function() {		
        // If the country is one that has subregions, then enable the state field, if not disable it.
        switch (jQuery(this).val()) {
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
                jQuery("select.crs-state").attr("data-blank-option", "State/Province").attr("data-value", "shortcode").attr("data-default-option", "State/Province").attr("data-show-default-option", "true").prop("disabled", false).attr("title", "Please select the state/province you are in..").addClass("required");
                break;

            default:
                jQuery("select.crs-state").attr("data-blank-option", "State/Province").attr("data-value", "shortcode").attr("data-default-option", "State/Province").attr("data-show-default-option", "true").prop("disabled", false).attr("title", "Please select the state/province you are in..").addClass("required");
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
                ["──────────────────", "PLACEHOLDER", ""],
                ["Afghanistan", "AF", "Not Listed~None"],
                ["Aland Islands", "AX", "Not Listed~None"],
                ["Albania", "AL", "Not Listed~None"],
                ["Algeria", "DZ", "Not Listed~None"],
                ["American Samoa", "AS", "Not Listed~None"],
                ["Andora", "AD", "Not Listed~None"],
                ["Angola", "AO", "Not Listed~None"],
                ["Anguilla", "AI", "Not Listed~None"],
                ["Antarctica", "AQ", "Not Listed~None"],
                ["Antigua and Barbuda", "AG", "Not Listed~None"],
                ["Argentina", "AR", "Not Listed~None"],
                ["Armenia", "AM", "Not Listed~None"],
                ["Aruba", "AW", "Not Listed~None"],
                ["Australia", "AU", "Australian Capital Territory~ACT|New South Wales~NSW|Northern Territory~NT|Queensland~QLD|South Australia~SA|Tasmania~TAS|Victoria~VIC|Western Australia~WA"],
                ["Austria", "AT", "Not Listed~None"],
                ["Azerbaijan", "AZ", "Not Listed~None"],
                ["Bahamas", "BS", "Not Listed~None"],
                ["Bahrain", "BH", "Not Listed~None"],
                ["Bangladesh", "BD", "Not Listed~None"],
                ["Barbados", "BB", "Not Listed~None"],
                ["Belarus", "BY", "Not Listed~None"],
                ["Belgium", "BE", "Not Listed~None"],
                ["Belize", "BZ", "Not Listed~None"],
                ["Benin", "BJ", "Not Listed~None"],
                ["Bermuda", "BM", "Not Listed~None"],
                ["Bhutan", "BT", "Not Listed~None"],
                ["Bolivia, Plurinational State of", "BO", "Not Listed~None"],
                ["Bonaire, Sint Eustatius and Saba", "BQ", "Not Listed~None"],
                ["Bosnia And Herzegovina", "BA", "Not Listed~None"],
                ["Botswana", "BW", "Not Listed~None"],
                ["Bouvet Island", "BV", "Not Listed~None"],
                ["Brazil", "BR", "Acre~AC|Alagoas~AL|Amapá~AP|Amazonas~AM|Bahia~BA|Ceará~CE|Distrito Federal~DF|Espírito Santo~ES|Goiás~GO|Maranhão~MA|Mato Grosso~MT|Mato Grosso do Sul~MS|Minas Gerais~MG|Pará~PA|Paraíba~PB|Paraná~PR|Pernambuco~PE|Piauí~PI|Rio de Janeiro~RJ|Rio Grande do Norte~RN|Rio Grande do Sul~RS|Rondônia~RO|Roraima~RR|Santa Catarina~SC|São Paulo~SP|Sergipe~SE|Tocantins~TO"],
                ["British Indian Ocean Territory", "IO", "Not Listed~None"],
                ["Brunei Darussalam", "BN", "Not Listed~None"],
                ["Bulgaria", "BG", "Not Listed~None"],
                ["Burkina Faso", "BF", "Not Listed~None"],
                ["Burundi", "BI", "Not Listed~None"],
                ["Cambodia", "KH", "Not Listed~None"],
                ["Cameroon", "CM", "Not Listed~None"],
                ["Canada", "CA", "Alberta~AB|British Columbia~BC|Manitoba~MB|New Brunswick~NB|Newfoundland and Labrador~NL|Northwest Territories~NT|Nova Scotia~NS|Nunavut~NU|Ontario~ON|Prince Edward Island~PE|Quebec~QC|Saskatchewan~SK|Yukon Territories~YT"],
                ["Cape Verde", "CV", "Not Listed~None"],
                ["Cayman Islands", "KY", "Not Listed~None"],
                ["Central African Republic", "CF", "Not Listed~None"],
                ["Chad", "TD", "Not Listed~None"],
                ["Chile", "CL", "Not Listed~None"],
                ["China", "CN", "Anhui~34|Beijing~11|Chinese Taipei~71|Chongqing~50|Fujian~35|Gansu~62|Guangdong~44|Guangxi~45|Guizhou~52|Hainan~46|Hebei~13|Heilongjiang~23|Henan~41|Hong Kong~91|Hubei~42|Hunan~43|Jiangsu~32|Jiangxi~36|Jilin~22|Liaoning~21|Macao~92|Nei Mongol~15|Ningxia~64|Qinghai~63|Shaanxi~61|Shandong~37|Shanghai~31|Shanxi~14|Sichuan~51|Tianjin~12|Xinjiang~65|Xizang~54|Yunnan~53|Zhejiang~33"],
                ["Chinese Taipei", "TW", "Not Listed~None"],
                ["Christmas Island", "CX", "Not Listed~None"],
                ["Cocos (Keeling) Islands", "CC", "Not Listed~None"],
                ["Colombia", "CO", "Not Listed~None"],
                ["Comoros", "KM", "Not Listed~None"],
                ["Congo", "CG", "Not Listed~None"],
                ["Congo, the Democratic Republic of the", "CD", "Not Listed~None"],
                ["Cook Islands", "CK", "Not Listed~None"],
                ["Costa Rica", "CR", "Not Listed~None"],
                ["Cote d'Ivoire", "CI", "Not Listed~None"],
                ["Croatia", "HR", "Not Listed~None"],
                ["Cuba", "CU", "Not Listed~None"],
                ["Curaçao", "CW", "Not Listed~None"],
                ["Cyprus", "CY", "Not Listed~None"],
                ["Czech Republic", "CZ", "Not Listed~None"],
                ["Denmark", "DK", "Not Listed~None"],
                ["Djibouti", "DJ", "Not Listed~None"],
                ["Dominica", "DM", "Not Listed~None"],
                ["Dominican Republic", "DO", "Not Listed~None"],
                ["Ecuador", "EC", "Not Listed~None"],
                ["Egypt", "EG", "Not Listed~None"],
                ["El Salvador", "SV", "Not Listed~None"],
                ["Equatorial Guinea", "GQ", "Not Listed~None"],
                ["Eritrea", "ER", "Not Listed~None"],
                ["Estonia", "EE", "Not Listed~None"],
                ["Ethiopia", "ET", "Not Listed~None"],
                ["Falkland Islands (Malvinas)", "FK", "Not Listed~None"],
                ["Faroe Islands", "FO", "Not Listed~None"],
                ["Fiji", "FJ", "Not Listed~None"],
                ["Finland", "FI", "Not Listed~None"],
                ["France", "FR", "Alsace~A|Aquitaine~B|Auvergne~C|Burgundy~D|Brittany~E|Centre-Val de Loire~F|Champagne-Ardenne~G|Corsica~H|Franche-Comté~I|Ile-de-France~J|Languedoc-Roussillon~K|Limousin~L|Lorraine~M|Lower Normandy~P|Midi-Pyrénées~N|Nord-Pas-de-Calais~O|Pays de la Loire~R|Picardy~S|Poitou-Charentes~T|Provence-Alpes-Cote d'Azur~U|Rhône-Alpes~V|Upper Normandy~Q"],
                ["French Guiana", "GF", "Not Listed~None"],
                ["French Polynesia", "PF", "Not Listed~None"],
                ["French Southern Territories", "TF", "Not Listed~None"],
                ["Gabon", "GA", "Not Listed~None"],
                ["Gambia", "GM", "Not Listed~None"],
                ["Georgia", "GE", "Not Listed~None"],
                ["Germany", "DE", "Baden-Wurttemberg~BW|Bavaria~BY|Berlin~BE|Brandenburg~BB|Bremen~HB|Hamburg~HH|Hesse~HE|Lower Saxony~NI|Mecklenburg-Vorpommern~MV|North Rhine-Westphalia~NW|Rhineland-Palatinate~RP|Saarland~SL|Saxony~SN|Saxony-Anhalt~ST|Schleswig-Holstein~SH|Thuringia~TH"],
                ["Ghana", "GH", "Not Listed~None"],
                ["Gibraltar", "GI", "Not Listed~None"],
                ["Greece", "GR", "Not Listed~None"],
                ["Greenland", "GL", "Not Listed~None"],
                ["Grenada", "GD", "Not Listed~None"],
                ["Guadeloupe", "GP", "Not Listed~None"],
                ["Guam", "GU", "Not Listed~None"],
                ["Guatemala", "GT", "Not Listed~None"],
                ["Guernsey", "GG", "Not Listed~None"],
                ["Guinea", "GN", "Not Listed~None"],
                ["Guinea-Bissau", "GW", "Not Listed~None"],
                ["Guyana", "GY", "Not Listed~None"],
                ["Haiti", "HT", "Not Listed~None"],
                ["Heard Island and McDonald Islands", "HM", "Not Listed~None"],
                ["Holy See (Vatican City State)", "VA", "Not Listed~None"],
                ["Honduras", "HN", "Not Listed~None"],
                ["Hong Kong China", "HK", "Not Listed~None"],
                ["Hungary", "HU", "Not Listed~None"],
                ["Iceland", "IS", "Not Listed~None"],
                ["India", "IN", "Andaman and Nicobar Islands~AN|Andhra Pradesh~AP|Arunachal Pradesh~AR|Assam~AS|Bihar~BR|Chandigarh~CH|Chhattisgarh~CT|Dadra and Nagar Haveli~DN|Daman and Diu~DD|Delhi~DL|Goa~GA|Gujarat~GJ|Haryana~HR|Himachal Pradesh~HP|Jammu and Kashmir~JK|Jharkhand~JH|Karnataka~KA|Kerala~KL|Lakshadweep~LD|Madhya Pradesh~MP|Maharashtra~MH|Manipur~MN|Meghalaya~ML|Mizoram~MZ|Nagaland~NL|Odisha~OR|Puducherry~PY|Punjab~PB|Rajasthan~RJ|Sikkim~SK|Tamil Nadu~TN|Telangana~TS|Tripura~TR|Uttar Pradesh~UP|Uttarakhand~UT|West Bengal~WB"],
                ["Indonesia", "ID", "Not Listed~None"],
                ["Iran, Islamic Republic of", "IR", "Not Listed~None"],
                ["Iraq", "IQ", "Not Listed~None"],
                ["Ireland", "IE", "Carlow~CW|Cavan~CN|Clare~CE|Cork~CO|Donegal~DL|Dublin~D|Galway~GA|Kerry~KY|Kildare~KE|Kilkenny~KK|Laois~LS|Leitrim~LM|Limerick~LK|Longford~LD|Louth~LH|Mayo~MO|Meath~MH|Monaghan~MN|Offaly~OY|Roscommon~RN|Sligo~SO|Tipperary~TA|Waterford~WD|Westmeath~WH|Wexford~WX|Wicklow~WW"],
                ["Isle of Man", "IM", "Not Listed~None"],
                ["Israel", "IL", "Not Listed~None"],
                ["Italy", "IT", "Agrigento~AG|Alessandria~AL|Ancona~AN|Aosta~AO|Arezzo~AR|Ascoli Piceno~AP|Asti~AT|Avellino~AV|Bari~BA|Barletta-Andria-Trani~BT|Belluno~BL|Benevento~BN|Bergamo~BG|Biella~BI|Bologna~BO|Bolzano~BZ|Brescia~BS|Brindisi~BR|Cagliari~CA|Caltanissetta~CL|Campobasso~CB|Carbonia-Iglesias~CI|Caserta~CE|Catania~CT|Catanzaro~CZ|Chieti~CH|Como~CO|Cosenza~CS|Cremona~CR|Crotone~KR|Cuneo~CN|Enna~EN|Fermo~FM|Ferrara~FE|Florence~FI|Foggia~FG|Forlì-Cesena~FC|Frosinone~FR|Genoa~GE|Gorizia~GO|Grosseto~GR|Imperia~IM|Isernia~IS|L'Aquila~AQ|La Spezia~SP|Latina~LT|Lecce~LE|Lecco~LC|Livorno~LI|Lodi~LO|Lucca~LU|Macerata~MC|Mantua~MN|Massa and Carrara~MS|Matera~MT|Medio Campidano~VS|Messina~ME|Milan~MI|Modena~MO|Monza and Brianza~MB|Naples~NA|Novara~NO|Nuoro~NU|Ogliastra~OG|Olbia-Tempio~OT|Oristano~OR|Padua~PD|Palermo~PA|Parma~PR|Pavia~PV|Perugia~PG|Pesaro and Urbino~PU|Pescara~PE|Piacenza~PC|Pisa~PI|Pistoia~PT|Pordenone~PN|Potenza~PZ|Prato~PO|Ragusa~RG|Ravenna~RA|Reggio Calabria~RC|Reggio Emilia~RE|Rieti~RI|Rimini~RN|Rome~RM|Rovigo~RO|Salerno~SA|Sassari~SS|Savona~SV|Siena~SI|Sondrio~SO|Syracuse~SR|Taranto~TA|Teramo~TE|Terni~TR|Trapani~TP|Trento~TN|Treviso~TV|Trieste~TS|Turin~TO|Udine~UD|Varese~VA|Venice~VE|Verbano-Cusio-Ossola~VB|Vercelli~VC|Verona~VR|Vibo Valentia~VV|Vicenza~VI|Viterbo~VT"],
                ["Jamaica", "JM", "Not Listed~None"],
                ["Japan", "JP", "Aichi~23|Akita~05|Aomori~02|Chiba~12|Ehime~38|Fukui~18|Fukuoka~40|Fukushima~07|Gifu~21|Gunma~10|Hiroshima~34|Hokkaido~01|Hyogo~28|Ibaraki~08|Ishikawa~17|Iwate~03|Kagawa~37|Kagoshima~46|Kanagawa~14|Kochi~39|Kumamoto~43|Kyoto~26|Mie~24|Miyagi~04|Miyazaki~45|Nagano~20|Nagasaki~42|Nara~29|Niigata~15|Oita~44|Okayama~33|Okinawa~47|Osaka~27|Saga~41|Saitama~11|Shiga~25|Shimane~32|Shizuoka~22|Tochigi~09|Tokushima~36|Tokyo~13|Tottori~31|Toyama~16|Wakayama~30|Yamagata~06|Yamaguchi~35|Yamanashi~19"],
                ["Jersey", "JE", "Not Listed~None"],
                ["Jordan", "JO", "Not Listed~None"],
                ["Kazakhstan", "KZ", "Not Listed~None"],
                ["Kenya", "KE", "Not Listed~None"],
                ["Kiribati", "KI", "Not Listed~None"],
                ["Korea, Democratic People's Republic of", "KP", "Not Listed~None"],
                ["Korea, Republic of", "KR", "Not Listed~None"],
                ["Kuwait", "KW", "Not Listed~None"],
                ["Kyrgyzstan", "KG", "Not Listed~None"],
                ["Lao People's Democratic Republic", "LA", "Not Listed~None"],
                ["Latvia", "LV", "Not Listed~None"],
                ["Lebanon", "LB", "Not Listed~None"],
                ["Lesotho", "LS", "Not Listed~None"],
                ["Liberia", "LR", "Not Listed~None"],
                ["Libyan Arab Jamahiriya", "LY", "Not Listed~None"],
                ["Liechtenstein", "LI", "Not Listed~None"],
                ["Lithuania", "LT", "Not Listed~None"],
                ["Luxembourg", "LU", "Not Listed~None"],
                ["Macao China", "MO", "Not Listed~None"],
                ["Macedonia, the former Yugoslav Republic of", "MK", "Not Listed~None"],
                ["Madagascar", "MG", "Not Listed~None"],
                ["Malawi", "MW", "Not Listed~None"],
                ["Malaysia", "MY", "Not Listed~None"],
                ["Maldives", "MV", "Not Listed~None"],
                ["Mali", "ML", "Not Listed~None"],
                ["Malta", "MT", "Not Listed~None"],
                ["Marshall Islands", "MH", "Not Listed~None"],
                ["Martinique", "MQ", "Not Listed~None"],
                ["Mauritania", "MR", "Not Listed~None"],
                ["Mauritius", "MU", "Not Listed~None"],
                ["Mayotte", "YT", "Not Listed~None"],
                ["Mexico", "MX", "Aguascalientes~AG|Baja California~BC|Baja California Sur~BS|Campeche~CM|Chiapas~CS|Chihuahua~CH|Coahuila~CO|Colima~CL|Durango~DG|Federal District~DF|Guanajuato~GT|Guerrero~GR|Hidalgo~HG|Jalisco~JA|Mexico State~ME|Michoacán~MI|Morelos~MO|Nayarit~NA|Nuevo León~NL|Oaxaca~OA|Puebla~PB|Querétaro~QE|Quintana Roo~QR|San Luis Potosí~SL|Sinaloa~SI|Sonora~SO|Tabasco~TB|Tamaulipas~TM|Tlaxcala~TL|Veracruz~VE|Yucatán~YU|Zacatecas~ZA"],
                ["Micronesia", "FM", "Not Listed~None"],
                ["Moldova, Republic of", "MD", "Not Listed~None"],
                ["Monaco", "MC", "Not Listed~None"],
                ["Mongolia", "MN", "Not Listed~None"],
                ["Montenegro", "ME", "Not Listed~None"],
                ["Montserrat", "MS", "Not Listed~None"],
                ["Morocco", "MA", "Not Listed~None"],
                ["Mozambique", "MZ", "Not Listed~None"],
                ["Myanmar", "MM", "Not Listed~None"],
                ["Namibia", "NA", "Not Listed~None"],
                ["Nauru", "NR", "Not Listed~None"],
                ["Nepal", "NP", "Not Listed~None"],
                ["Netherlands", "NL", "Not Listed~None"],
                ["New Caledonia", "NC", "Not Listed~None"],
                ["New Zealand", "NZ", "Not Listed~None"],
                ["Nicaragua", "NI", "Not Listed~None"],
                ["Niger", "NE", "Not Listed~None"],
                ["Nigeria", "NG", "Not Listed~None"],
                ["Niue", "NU", "Not Listed~None"],
                ["Norfolk Island", "NF", "Not Listed~None"],
                ["Northern Mariana Islands", "MP", "Not Listed~None"],
                ["Norway", "NO", "Not Listed~None"],
                ["Oman", "OM", "Not Listed~None"],
                ["Pakistan", "PK", "Not Listed~None"],
                ["Palau", "PW", "Not Listed~None"],
                ["Palestinian Territory, Occupied", "PS", "Not Listed~None"],
                ["Panama", "PA", "Not Listed~None"],
                ["Papua New Guinea", "PG", "Not Listed~None"],
                ["Paraguay", "PY", "Not Listed~None"],
                ["Peru", "PE", "Not Listed~None"],
                ["Philippines", "PH", "Not Listed~None"],
                ["Pitcairn", "PN", "Not Listed~None"],
                ["Poland", "PL", "Not Listed~None"],
                ["Portugal", "PT", "Not Listed~None"],
                ["Puerto Rico", "PR", "Not Listed~None"],
                ["Qatar", "QA", "Not Listed~None"],
                ["Reunion", "RE", "Not Listed~None"],
                ["Romania", "RO", "Not Listed~None"],
                ["Russian Federation", "RU", "Not Listed~None"],
                ["Rwanda", "RW", "Not Listed~None"],
                ["Saint Barthélemy", "BL", "Not Listed~None"],
                ["Saint Helena, Ascension and Tristan da Cunha", "SH", "Not Listed~None"],
                ["Saint Kitts and Nevis", "KN", "Not Listed~None"],
                ["Saint Lucia", "LC", "Not Listed~None"],
                ["Saint Martin (French part)", "MF", "Not Listed~None"],
                ["Saint Pierre and Miquelon", "PM", "Not Listed~None"],
                ["Saint Vincent and the Grenadines", "VC", "Not Listed~None"],
                ["Samoa", "WS", "Not Listed~None"],
                ["San Marino", "SM", "Not Listed~None"],
                ["Sao Tome and Principe", "ST", "Not Listed~None"],
                ["Saudi Arabia", "SA", "Not Listed~None"],
                ["Senegal", "SN", "Not Listed~None"],
                ["Serbia", "RS", "Not Listed~None"],
                ["Seychelles", "SC", "Not Listed~None"],
                ["Sierra Leone", "SL", "Not Listed~None"],
                ["Singapore", "SG", "Not Listed~None"],
                ["Sint Maarten (Dutch part)", "SX", "Not Listed~None"],
                ["Slovakia", "SK", "Not Listed~None"],
                ["Slovenia", "SI", "Not Listed~None"],
                ["Solomon Islands", "SB", "Not Listed~None"],
                ["Somalia", "SO", "Not Listed~None"],
                ["South Africa", "ZA", "Not Listed~None"],
                ["South Georgia and the South Sandwich Islands", "GS", "Not Listed~None"],
                ["South Sudan", "SS", "Not Listed~None"],
                ["Spain", "ES", "Andalucía~AN|Aragon~AR|Asturias Principado de~AS|Canarias~CN|Cantabria~CB|Castilla y Leon~CL|Castilla-La Mancha~CM|Catalunya~CT|Ceuta~CE|Extremadura~EX|Galicia~GA|Illes Balears~IB|La Rioja~RI|Madrid, Comunidad de~MD|Melilla~ML|Murcia~MC|Navarra~NC|Pais Vasco~PV|Valenciana~VC"],
                ["Sri Lanka", "LK", "Not Listed~None"],
                ["Sudan", "SD", "Not Listed~None"],
                ["Suriname", "SR", "Not Listed~None"],
                ["Svalbard and Jan Mayen", "SJ", "Not Listed~None"],
                ["Swaziland", "SZ", "Not Listed~None"],
                ["Sweden", "SE", "Blekinge~K|Dalarna~W|Gavleborg~X|Gotland~I|Halland~N|Jamtland~Z|Jonkoping~F|Kalmar~H|Kronoberg~G|Norrbotten~BD|Orebro~T|Ostergötland~E|Skane~M|Sodermanland~D|Stockholm~AB|Uppsala~C|Varmland~S|Vasterbotten~AS|Vastmanland~U|Vastra Gotaland~O"],
                ["Switzerland", "CH", "Not Listed~None"],
                ["Syrian Arab Republic", "SY", "Not Listed~None"],
                ["Tajikistan", "TJ", "Not Listed~None"],
                ["Tanzania, United Republic of", "TZ", "Not Listed~None"],
                ["Thailand", "TH", "Not Listed~None"],
                ["Timor-Leste", "TL", "Not Listed~None"],
                ["Togo", "TG", "Not Listed~None"],
                ["Tokelau", "TK", "Not Listed~None"],
                ["Tonga", "TO", "Not Listed~None"],
                ["Trinidad and Tobago", "TT", "Not Listed~None"],
                ["Tunisia", "TN", "Not Listed~None"],
                ["Turkey", "TR", "Not Listed~None"],
                ["Turkmenistan", "TM", "Not Listed~None"],
                ["Turks and Caicos Islands", "TC", "Not Listed~None"],
                ["Tuvalu", "TV", "Not Listed~None"],
                ["Uganda", "UG", "Not Listed~None"],
                ["Ukraine", "UA", "Not Listed~None"],
                ["United Arab Emirates", "AE", "Not Listed~None"],
                ["United Kingdom", "GB", "England~ENG|Northern Ireland~NIR|Scotland~SCT|Wales~WL"],
                ["United States of America", "US", "Alabama~AL|Alaska~AK|American Samoa~AS|Arizona~AZ|Arkansas~AR|Armed Forces Americas~AA|Armed Forces Europe~AE|Armed Forces Pacific~AP|California~CA|Colorado~CO|Connecticut~CT|Delaware~DE|District of Columbia~DC|Federated Micronesia~FM|Florida~FL|Georgia~GA|Guam~GU|Hawaii~HI|Idaho~ID|Illinois~IL|Indiana~IN|Iowa~IA|Kansas~KS|Kentucky~KY|Louisiana~LA|Maine~ME|Marshall Islands~MH|Maryland~MD|Massachusetts~MA|Michigan~MI|Minnesota~MN|Mississippi~MS|Missouri~MO|Montana~MT|Nebraska~NE|Nevada~NV|New Hampshire~NH|New Jersey~NJ|New Mexico~NM|New York~NY|North Carolina~NC|North Dakota~ND|Northern Mariana Islands~MP|Ohio~OH|Oklahoma~OK|Oregon~OR|Palau~PW|Pennsylvania~PA|Puerto Rico~PR|Rhode Island~RI|South Carolina~SC|South Dakota~SD|Tennessee~TN|Texas~TX|United States Minor Outlying Islands~UM|US Virgin Islands~VI|Utah~UT|Vermont~VT|Virginia~VA|Washington~WA|West Virginia~WV|Wisconsin~WI|Wyoming~WY"],
                ["Uruguay", "UY", "Not Listed~None"],
                ["Uzbekistan", "UZ", "Not Listed~None"],
                ["Vanuatu", "VU", "Not Listed~None"],
                ["Venezuela, Bolivarian Republic of", "VE", "Not Listed~None"],
                ["Viet Nam", "VN", "Not Listed~None"],
                ["Virgin Islands, British", "VG", "Not Listed~None"],
                ["Virgin Islands, U.S.", "VI", "Not Listed~None"],
                ["Wallis and Futuna", "WF", "Not Listed~None"],
                ["Western Sahara", "EH", "Not Listed~None"],
                ["Yemen", "YE", "Not Listed~None"],
                ["Zambia", "ZM", "Not Listed~None"],
                ["Zimbabwe", "ZW", "Not Listed~None"]
            ],
            h = function() {
                jQuery("." + a).each(i)
            },
            i = function() {
                var a = this,
                    c = a.getAttribute("data-crs-loaded");
                if ("true" !== c) {
                    a.length = 0;
                    var e = jQuery(a).attr("data-default-option"),
                        g = e ? e : b,
                        h = a.getAttribute("data-show-default-option");
                    d = null === h ? !0 : "true" === h;
                    var i = jQuery(a).attr("data-default-value"),
                        l = jQuery(a).attr("data-value"),
                        o = 0;
                    d && (this.options[0] = new Option(g, "")), k({
                        whitelist: a.getAttribute("data-whitelist"),
                        blacklist: a.getAttribute("data-blacklist")
                    });
                    for (var p = 0; p < f.length; p++) {
                        var q = "shortcode" == l || "2-char" === l ? f[p][1] : f[p][0];
                        a.options[a.length] = new Option(f[p][0], q), null != i && i === q && (o = p, d && o++)
                    }
                    this.selectedIndex = o;
                    var r = jQuery(a).attr("data-region-id");
                    if (!r) return void console.error("Missing data-region-id on country-region-selector country field.");
                    var s = jQuery("." + r)[0];
                    if (s)
                        if (j(s), jQuery(this).on("change", function() {
                                n(a, s)
                            }), i && a.selectedIndex > 0) {
                            n(a, s);
                            var t = jQuery(s).attr("data-default-value"),
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
                var b = jQuery(a).attr("data-blank-option"),
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
                    h = jQuery(b).attr("data-default-option"),
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
        return jQuery(h), {
            init: h
        }
    });

});
</script>
