<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">

function validateInput(input) {
        　　var re = /^[0-9]+[0-9]*]*$/;   //Validate the inputs 2017-12-01 Kenny
        　　var tzCount = jQuery("#row1 input").val();
			var nsaCount = jQuery("#row2 input").val();
			var eclassCount = jQuery("#row3 input").val();
			var eclassMidCount = jQuery("#row4 input").val();
			var eclassHighCount = jQuery("#row5 input").val();
			var userCount = jQuery("#row6 input").val();
			var hoursPerUser = jQuery("#row7 input").val();
			
			var mode = jQuery("input[name='mode']:radio:checked").val();
			
			if ( mode==1){ // Appliance Mode
				
		   　　 if ( !re.test(tzCount)) {
				　　　　alert("Please input number for TZ series Appliances");
				　　　　jQuery("#row1 input").val('');
				　　　　return false;
			　　}
				if ( !re.test(nsaCount)) {
				　　　　alert("Please input number for NSA series Appliances ");
				　　　　jQuery("#row2 input").val('');
				　　　　return false;
			　　}
				if ( !re.test(eclassCount)) {
				　　　　alert("Please input number for E-Class Entry Level Appliances");
				　　　　jQuery("#row3 input").val('');
				　　　　return false;
			　　}
				if ( !re.test(eclassMidCount)) {
				　　　　alert("Please input number for E-Class Mid-Range Appliances");
				　　　　jQuery("#row4 input").val('');
				　　　　return false;
			　　}
				if ( !re.test(eclassHighCount)) {
				　　　　alert("Please input number for E-Class High End Appliances");
				　　　　jQuery("#row5 input").val('');
				　　　　return false;
			　　}
				
			}

			
			if ( mode==0){ //User Mode
				if ( !re.test(userCount)){
					　alert("Please input number For Total Users");
					  jQuery("#row6 input").val('');
					  return false;
				}else{
					  if(parseInt(userCount) = 1 && parseInt(hoursPerUser)  1 ? '.' + x[1] : '';
  var rgx = /(\d+)(\d{3})/;
  while (rgx.test(x1)) {
    x1 = x1.replace(rgx, '$1' + ',' + '$2');
  }
  return x1 + x2;
}

function calculate() {

	var syslogs = parseFloat(document.calc.syslogs.value);
	var months = parseInt(document.calc.months.value);
	var backups = parseInt(document.calc.backups.value);
	var weeklyReps = document.calc.weeklyReps.checked;
	
	if (!syslogs) syslogs = 0;
	if (!months) months = 0;
	if (!backups) backups = 0;

	var caRole = "AIOP/CA";
	var numAgents = 0;
	var multipleAgents = false;
	var agentCalcSyslog = 0;
	
	if (document.calc.performance[0].checked) { // High
		syslogsPerDay = 350;
		if(syslogs > 300) {
			agentCalcSyslog = syslogs - 300;
			multipleAgents = true;
		}
	}
	else if (document.calc.performance[1].checked) { // Mid
		syslogsPerDay = 250;
		if(syslogs > 200) {
			agentCalcSyslog = syslogs - 200;
			multipleAgents = true;
		}
	}
	else if (document.calc.performance[2].checked) { // Low
		syslogsPerDay = 150;
		if(syslogs > 100) {
			agentCalcSyslog = syslogs - 100;
			multipleAgents = true;
		}
	}
	
	var dbSize = syslogs*uploadFactor*(months*30)*gbPerMil;
	var rawFileSizePerDay = syslogs * filesGbPerMil;
	var archivedSizePerDay = rawFileSizePerDay * archivedFilesGbPerMil;
	var filesSize = rawFileSizePerDay + (months * archivedSizePerDay * 30) + 50; //50 GB buffer
	var optimiztionCache = syslogs*uploadFactor*cacheGbPerMil;

	var cacheSize = weeklyReps? optimiztionCache + (optimiztionCache * 0.1): optimiztionCache;
	var value = agentCalcSyslog/syslogsPerDay;
	if (value < 1) value = 1;
	else value = parseInt(value) + 1;
	document.calc_output.summarizers.value = "1 AIOP/Console" + (multipleAgents? ", " + value + " Agents": "");
	
	if(multipleAgents) {
		dbSize = dbSize / (value + 1);
		filesSize = filesSize / (value + 1);
		cacheSize = cacheSize / (value + 1);
	}
	
	if(cacheSize < 20) cacheSize = 20;
	var backupSize = (filesSize + dbSize)*backups;
	document.calc_output.database.value = addCommas((dbSize+filesSize).toFixed(3));
	document.calc_output.cache.value = addCommas(cacheSize.toFixed(3));
	document.calc_output.totaldisk.value = addCommas((dbSize+filesSize+backupSize + cacheSize).toFixed(3));
}

//calculate(); // initial calculation
</script>
<!-- end Simple Custom CSS and JS -->
