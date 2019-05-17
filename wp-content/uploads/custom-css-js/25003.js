<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
    //pointer keeps a reference to the type node in product list
    var dataset = null, pricelistdescription = null, pointer = [];

    jQuery(document).on('gform_post_render',function() {

        //disableRegForm(); //disable registration form by default

        //make copies of textbox inputs with dropdowns
        var upgradeTypeName = jQuery('.upgrade-type').attr('name');
        jQuery('.upgrade-type').attr('name',"upgradeType").hide();;
        jQuery('<select class="newUpgradeType" name="'+upgradeTypeName+'"><option value="">Choose Your Type</option></select>').insertBefore('.upgrade-type');

        var upgradeBrandName = jQuery('.upgrade-brand').attr('name');
        jQuery('.upgrade-brand').attr('name','upgradeBrand').hide();;
        jQuery('<select class="newUpgradeBrand" name="'+upgradeBrandName+'"><option value="">Choose Your Brand & Model</option></select>').insertBefore('.upgrade-brand');

        var upgradeAvailabilityName = jQuery('.upgrade-availability').attr('name');
        jQuery('.upgrade-availability').attr('name',"upgradeAvailability").hide();;
        jQuery('<select class="newUpgradeAvailability" name="'+upgradeAvailabilityName+'"><option value="">Available SonicWall Upgrades</option></select>').insertBefore('.upgrade-availability');

        //disable all dropdowns initially until logic enables them
        jQuery('.newUpgradeBrand').attr("disabled", true);
        jQuery('.newUpgradeAvailability').attr("disabled", true);

        

</script>
<!-- end Simple Custom CSS and JS -->
