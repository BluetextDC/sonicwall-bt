<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
function getURLParam(name) {
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
	var r = window.location.search.substr(1).match(reg);
	if (r != null) return unescape(r[2]); return null;
}

jQuery(document).ready(function () {  
	if (getURLParam('utm_campaign')) jQuery('.utm_campaign input').val(getURLParam('utm_campaign'));
	if (getURLParam('utm_medium')) jQuery('.utm_medium input').val(getURLParam('utm_medium'));
	if (getURLParam('utm_source')) jQuery('.elqCampaignId input').val(getURLParam('utm_source'));
	if (getURLParam('elqCampaignId')) jQuery('.elqCampaignId input').val(getURLParam('elqCampaignId'));
	if (getURLParam('elqTrackId')) jQuery('.elqTrackId input').val(getURLParam('elqTrackId '));
	if (getURLParam('sfc')) jQuery('.sfc input').val(getURLParam('sfc'));
});
</script>
<!-- end Simple Custom CSS and JS -->
