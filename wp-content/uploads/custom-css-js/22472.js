<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
function getURLParam(name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
	var r = window.location.search.substr(1).match(reg);
	if (r != null) return unescape(r[2]); return null;
}

jQuery(document).ready(function () {  
	if (getURLParam('utm_source')) jQuery('.elqCampaignId input').val(getURLParam('utm_source'));
});

</script>
<!-- end Simple Custom CSS and JS -->
