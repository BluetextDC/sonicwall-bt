<!-- start Simple Custom CSS and JS -->
<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
jQuery(document).ready(function () {

    //console.log("country selector version 1.0");
    set_options("US");//display US states by default
    var country_selector = ".crs-country &gt; div &gt; select";

    jQuery(country_selector).change(function () {
        var x = jQuery(this).attr("id");
        var x_jq = "#" + x + " :selected";
        var id = jQuery(x_jq).val();
        set_options(id);
    });

    function set_options(selected_index) {
        
        switch (selected_index) {

            case 'US': //USA

                country_array = new Array("Alabama",
                        "ALASKA",
                        "ARIZONA",
                        "ARKANSAS",
                        "CALIFORNIA",
                        "COLORADO",
                        "CONNECTICUT",
                        "DELAWARE",
                        "FLORIDA",
                        "GEORGIA",
                        "HAWAII",
                        "IDAHO",
                        "ILLINOIS",
                        "INDIANA",
                        "IOWA",
                        "KANSAS",
                        "KENTUCKY",
                        "LOUISIANA",
                        "MAINE",
                        "MARYLAND",
                        "MASSACHUSETTS",
                        "MICHIGAN",
                        "MINNESOTA",
                        "MISSISSIPPI",
                        "MISSOURI",
                        "MONTANA",
                        "NEBRASKA",
                        "NEVADA",
                        "NEW HAMPSHIRE",
                        "NEW JERSEY",
                        "NEW MEXICO",
                        "NEW YORK",
                        "NORTH CAROLINA",
                        "NORTH DAKOTA",
                        "OHIO",
                        "OKLAHOMA",
                        "OREGON",
                        "PENNSYLVANIA",
                        "RHODE ISLAND",
                        "SOUTH CAROLINA",
                        "SOUTH DAKOTA",
                        "TENNESSEE",
                        "TEXAS",
                        "UTAH",
                        "VERMONT",
                        "VIRGINIA",
                        "WASHINGTON",
                        "WEST VIRGINIA",
                        "WISCONSIN",
                        "WYOMING");
                break;


            case "UK": //UK
                console.log("UK Selected");
                country_array = new Array("ENGLAND", "NORTHERN IRELAND", "SCOTLAND", "WALES");
                break;

            case "BR": //Brazil
                country_array = new Array("ACRE", "ALAGOAS", "AMAPA", "AMAZONAS", "BAHIA", "CEARA", "DISTRITO FEDERAL", "ESPIRITO SANTO", "GOIAS", "MARANHAO", "MATO GROSSO", "MATO GROSSO DO SUL", "MINAS GERAIS", "PARA", "PARAIBA", "PARANA", "PERNAMBUCO", "PIAUI", "RIO DE JANEIRO", "RIO GRANDE DO NORTE", "RIO GRANDE DO SUL", "RONDONIA", "RORAIMA", "SANTA CATARINA", "SAO PAULO", "SERGIPE", "TOCANTINS");
                break;

            case "CA": //Canada
                country_array = new Array("ALBERTA", "BRITISH COLUMBIA", "MANITOBA", "NEW BRUNSWICK", "NEWFOUNDLAND", "NORTHWEST TERRITORIES", "NOVA SCOTIA", "NUNAVUT", "ONTARIO", "PRINCE EDWARD ISLAND", "QUEBEC", "SASKATCHEWAN", "YUKON TERRITORY");
                break;


            case 'FR': //France
                country_array = new Array(
                        "ALSACE", "AQUITAINE", "AUVERGNE", "BURGUNDY", "BRITTANY", "CENTRE-VAL DE LOIRE", "CHAMPAGNE", "ARDENNE", "CORSICA", "FRANCHE-COMTE", "ILE-DE-FRANCE", "LANGUEDOC-ROUSSILLON", "LIMOUSIN", "LORRAINE", "LOWER NORMANDY", "MIDI-PYRENEES", "NORD-PAS-DE-CALAIS", "PAYS DE LA LOIRE", "PICARDY", "POITOU-CHARENTES", "PROVENCE-ALPES-COTE D'AZUR", "RHONE-ALPES", "UPPER NORMANDY"
                        );
                break;

            case "DE": //Germany
                country_array = new Array("BADEN-WURTTEMBERG", "BAVARIA", "BERLIN", "BRANDENBURG", "BREMEN", "HAMBURG", "HESSE", "LOWER SAXONY", "MECKLENBURG-VORPOMMERN", "NORTH RHINE-WESTPHALIA", "RHINELAND-PALATINATE", "SAARLAND", "SAXONY", "SAXONY-ANHALT", "SCHLESWIG-HOLSTEIN", "THURINGIA");

                break;
            case "IN": //India
                country_array = new Array("ANDAMAN AND NICOBAR ISLANDS", "ANDHRA PRADESH", "ARUNACHAL PRADESH", "ASSAM", "BIHAR", "CHANDIGARH", "CHHATTISGARH", "DADRA AND NAGAR HAVELI", "DAMAN AND DIU", "DELHI", "GOA", "GUJARAT", "HARYANA", "HIMACHAL PRADESH", "JAMMU AND KASHMIR", "JHARKHAND", "KARNATAKA", "KERALA", "LAKSHADWEEP", "MADHYA PRADESH", "MAHARASHTRA", "MANIPUR", "MEGHALAYA", "MIZORAM", "NAGALAND", "ODISHA", "PUDUCHERRY", "PUNJAB", "RAJASTHAN", "SIKKIM", "TAMIL NADU", "TELANGANA", "TRIPURA", "UTTAR PRADESH", "UTTARAKHAND", "WEST BENGAL");
                break;


            case "IT": //Italy
                country_array = new Array("AGRIGENTO", "ALESSANDRIA", "ANCONA", "AOSTA", "AREZZO", "ASCOLI PICENO", "ASTI", "AVELLINO", "BARI", "BARLETTA-ANDRIA-TRANI", "BELLUNO", "BENEVENTO", "BERGAMO", "BIELLA", "BOLOGNA", "BOLZANO", "BRESCIA", "BRINDISI", "CAGLIARI", "CALTANISSETTA", "CAMPOBASSO", "CARBONIA-IGLESIAS", "CASERTA", "CATANIA", "CATANZARO", "CHIETI", "COMO", "COSENZA", "CREMONA", "CROTONE", "CUNEO", "ENNA", "FERMO", "FERRARA", "FLORENCE", "FOGGIA", "FORLÃƒÂ¬-CESENA", "FROSINONE", "GENOA", "GORIZIA", "GROSSETO", "IMPERIA", "ISERNIA", "L'AQUILA", "LA SPEZIA", "LATINA", "LECCE", "LECCO", "LIVORNO", "LODI", "LUCCA", "MACERATA", "MANTUA", "MASSA AND CARRARA", "MATERA", "MEDIO CAMPIDANO", "MESSINA", "MILAN", "MODENA", "MONZA AND BRIANZA", "NAPLES", "NOVARA", "NUORO", "OGLIASTRA", "OLBIA-TEMPIO", "ORISTANO", "PADUA", "PALERMO", "PARMA", "PAVIA", "PERUGIA", "PESARO AND URBINO", "PESCARA", "PIACENZA", "PISA", "PISTOIA", "PORDENONE", "POTENZA", "PRATO", "RAGUSA", "RAVENNA", "REGGIO CALABRIA", "REGGIO EMILIA", "RIETI", "RIMINI", "ROME", "ROVIGO", "SALERNO", "SASSARI", "SAVONA", "SIENA", "SONDRIO", "SYRACUSE", "TARANTO", "TERAMO", "TERNI", "TRAPANI", "TRENTO", "TREVISO", "TRIESTE", "TURIN", "UDINE", "VARESE", "VENICE", "VERBANO-CUSIO-OSSOLA", "VERCELLI", "VERONA", "VIBO VALENTIA", "VICENZA", "VITERBO");

                break;
            case "ES": //Spain
                console.log("SPAIN");
                country_array = new Array(
                        "ANDALUCIA", "ARAGON", "ASTURIAS PRINCIPADO DE", "CANARIAS", "CANTABRIA", "CASTILLA Y LEON", "CASTILLA-LA MANCHA", "CATALUNYA", "CEUTA", "EXTREMADURA", "GALICIA", "ILLES BALEARS", "LA RIOJA", "MADRID, COMUNIDAD DE", "MELILLA", "MURCIA", "NAVARRA", "PAIS VASCO", "VALENCIANA");
                break;

        }

        jQuery(".crs-state &gt; div &gt; select option").each(function () {
            if (jQuery.inArray(this.text, country_array) != '-1') {
                jQuery(this).show();
            } else {
                jQuery(this).hide();
            }
        });

    }
});
</script>
<!-- end Simple Custom CSS and JS --><!-- end Simple Custom CSS and JS -->
