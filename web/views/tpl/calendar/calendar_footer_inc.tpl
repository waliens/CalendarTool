<!-- FullCalendar stylesheet -->
<link rel='stylesheet' href='css/fullcalendar.css' />
<!-- Styles for time picker-->
<link rel='stylesheet' href='css/timepicker.css' />

<!-- FullCalendar Scripts --> 
<script src='js/moment.min.js'></script> 
<script src='js/moment-fr.js'></script> 
<script src='js/fullcalendar.js'></script> 
<script src='js/lang-all.js'></script> 
<!--date picker js and css-->
<link rel="stylesheet" media="screen" type="text/css" href="css/dhtmlxcalendar.css" />
<script type="text/javascript" src="js/dhtmlxcalendar.js"></script> 
<!-- timepicker js-->
<script src='js/timepicker.js'></script> 
<script>
//define french locale for date picker
dhtmlXCalendarObject.prototype.langData["fr"] = {
    dateformat: '%d.%m.%Y',
    monthesFNames: ["janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre"],
    monthesSNames: ["jan","fév","mar","avr","mai","jui","jui","aoû","sep","oct","nov","déc"],
    daysFNames: ["dimanche","lundi","mardi","mercredi","jeudi","vendredi","samedi"],
    daysSNames: ["di","lu","ma","me","je","ve","sa"],
    weekstart: 1,
    weekname: "s" 
};
//set franch locale for date picker objs
dhtmlXCalendarObject.prototype.lang = "fr";
</script> 
<!-- added js --> 
<script src="js/calendar.js"></script>
</body></html>