<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
jQuery(document).ready(function () {   
   jQuery('.remove-mm').mouseover(function(){
        jQuery('.reveal-overlay').removeClass('mm-page mm-slideout');
    });
});

/* Sonicwall generates about 3 million messages per day from about 200 users */
/* .251 summarizes about 100,000 per minute */

	var syslogsPerTz = 1; // million per day
	var syslogsPerNsa = 5; // million per day
	var syslogsPerEclass = 10; // million per day
	var syslogsPerEclassMid = 50; // million per day
	var syslogsPerEclassHigh = 100; // million per day

	/*
	Naveen Rajavasireddy - for 1 million messages, ~15 MB space is used in reporting database
	*/
	var cacheGbPerMil = 2; // gigabytes per million rows
	var gbPerMil = 0.020; // gigabytes per million rows
	var filesGbPerMil = 0.4; // gigabytes per million rows
	var archivedFilesGbPerMil = 0.1; // compression ration of files 10%
	var uploadFactor = 0.7; // % of syslog uploaded to DB
	var syslogsPerDay = 150; // syslogs per day - millions (VA 250 GB)
	var syslogsPerUserPerHour = .008; // syslogs per hour - millions
	var mode = 0; // user mode
	
	//set the input number cache and rollback after cancel 2017-12-01 Kenny
	var cache_tzCount = 0; 
	var cache_nsaCount = 0;
	var cache_eclassCount = 0;
	var cache_eclassMidCount = 0;
	var cache_eclassHighCount = 0;
	var cache_userCount = 0;
	var cache_hoursPerUser = 0;


function updateSyslogs() {
	
		var tzCount = parseInt(document.calculateSyslog.tzCount.value) || 0;
		var nsaCount = parseInt(document.calculateSyslog.nsaCount.value) || 0;
		var eclassCount = parseInt(document.calculateSyslog.eclassCount.value) || 0;
		var eclassMidCount = parseInt(document.calculateSyslog.eclassMidCount.value) || 0;
		var eclassHighCount = parseInt(document.calculateSyslog.eclassHighCount.value) || 0;
		var userCount = parseInt(document.calculateSyslog.userCount.value) || 0;
		var hoursPerUser = parseInt(document.calculateSyslog.hoursPerUser.value) || 0;
		
		//set the input number cache and rollback after cancel 2017-12-01 Kenny
		cache_tzCount = tzCount
		cache_nsaCount = nsaCount;
		cache_eclassCount = eclassCount;
	    cache_eclassMidCount = eclassMidCount;
		cache_eclassHighCount = eclassHighCount;
		cache_userCount = userCount;
		cache_hoursPerUser = hoursPerUser;
		
		if (mode == 0) document.calc.syslogs.value = (userCount * hoursPerUser * syslogsPerUserPerHour).toFixed(3);
		else document.calc.syslogs.value = (tzCount * syslogsPerTz + nsaCount * syslogsPerNsa + eclassCount * syslogsPerEclass + eclassMidCount * syslogsPerEclassMid + eclassHighCount * syslogsPerEclassHigh).toFixed(3);
		calculate();
}

function showCalculate()
{
	showOverlayDiv('bodyid', 'calculateSyslogDiv');
}

		
function changeMode(){

	if (document.calculateSyslog.mode[0].checked) {
		mode=0;
		document.getElementById("row1").style.display="none";
		document.getElementById("row2").style.display="none";
		document.getElementById("row3").style.display="none";
		document.getElementById("row4").style.display="none";
		document.getElementById("row5").style.display="none";
		document.getElementById("row6").style.display="";
		document.getElementById("row7").style.display="";
	}
	else {
		mode=1;
		document.getElementById("row1").style.display="";
		document.getElementById("row2").style.display="";
		document.getElementById("row3").style.display="";
		document.getElementById("row4").style.display="";
		document.getElementById("row5").style.display="";
		document.getElementById("row6").style.display="none";
		document.getElementById("row7").style.display="none";
	}
	
	jQuery("#row1 input").val('');
	jQuery("#row2 input").val('');
	jQuery("#row3 input").val('');
	jQuery("#row4 input").val('');
	jQuery("#row5 input").val('');
	jQuery("#row6 input").val('');
	jQuery("#row7 input").val('');
	document.calc.syslogs.value = '';
}

function addCommas(nStr)
{
  nStr += '';
  x = nStr.split('.');
  x1 = x[0];
  x2 = x.length > 1 ? '.' + x[1] : '';
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

//Validate Inputs
//Validate Inputs:
var re = /^[0-9]+[0-9]*]*$/;

function validateUser() {
    var userCount = jQuery("#row6 input").val();
	if ( !re.test(userCount)){
					　alert("Please input number For Total Users");
					  jQuery("#row6 input").val('');
					  return false;
	}else{
		  if(parseInt(userCount) < 1){
			 alert("Number of total users should be greater than 0");
			 jQuery("#row6 input").val('');
			 return false;
		  }
		
	}
}

function validateHoursPerUser() {
	var hoursPerUser = jQuery("#row7 input").val();
	if ( !re.test(hoursPerUser)){
		　alert("Please input number for Hours Browsing / User / Day");
		  jQuery("#row7 input").val('');
		  return false;
	}else{
		if(parseInt(hoursPerUser) >= 1 && parseInt(hoursPerUser) <= 24 ){
			return true
		}else{
			alert("The Hours should be between 1 and 24");
			jQuery("#row7 input").val('');
			return false;
			
		}
		
	}
}

function validateTZ() {
	var tzCount = jQuery("#row1 input").val();
	if ( !re.test(tzCount)) {
	　　　　alert("Please input number for TZ series Appliances");
	　　　　jQuery("#row1 input").val('');
	　　　　return false;
　　}
}
function validateNSA() {
	var nsaCount = jQuery("#row2 input").val();
	if ( !re.test(nsaCount)) {
	　　　　alert("Please input number for NSA series Appliances ");
	　　　　jQuery("#row2 input").val('');
	　　　　return false;
　　}
}
function validateEclass() {
	var eclassCount = jQuery("#row3 input").val();
	if ( !re.test(eclassCount)) {
	　　　　alert("Please input number for E-Class Entry Level Appliances");
	　　　　jQuery("#row3 input").val('');
	　　　　return false;
　　}
}
function validateEclassMid() {
	var eclassMidCount = jQuery("#row4 input").val();
	if ( !re.test(eclassMidCount)) {
	　　　　alert("Please input number for E-Class Mid-Range Appliances");
	　　　　jQuery("#row4 input").val('');
	　　　　return false;
　　}
}
function validateEclassHigh() {
	var eclassHighCount = jQuery("#row5 input").val();
	if ( !re.test(eclassHighCount)) {
	　　　　alert("Please input number for E-Class High End Appliances");
	　　　　jQuery("#row5 input").val('');
	　　　　return false;
　　}
}

</script>
<!-- end Simple Custom CSS and JS -->
