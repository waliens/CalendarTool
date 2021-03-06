// JavaScript Document
var today = new Date();
var year = today.getFullYear();
var month=today.getMonth()+1; //January is 0
var day=today.getDay();
	
//update the navbar
$("#navbar li").removeClass("active");
$("#static_export_page").addClass("active");
//datepickers
var startDate;
var endDate;
//filters for export
var filters = {
          	allEvent: {isSet: 'false'},
			dateRange: {start: 'null', end: 'null'},
			courses: {isSet: 'false', id:[]},
			eventTypes: {isSet: 'false', timeType:[], eventType:[]},
			eventCategories: {isSet: 'false', id:[]},
			pathways: {isSet: 'false', id:[]},
			professors:	{isSet: 'false', id:[]}
          };
		  
//set dateRange by default to current semester
if(month==1){//we are in January so we want to retrieve first semester
	filters.dateRange.start=(year-1)+"-09-15";
	filters.dateRange.end=year+"-01-31";
}
else if(month>1&&month<=9){
	if(month==9&day>15){
		filters.dateRange.start=year+"-09-15";
		filters.dateRange.end=(year+1)+"-01-31";
		}
		
	else {//we are in period between January and September 14 so we want to retrieve the second semester
		filters.dateRange.start=year+"-02-01";
		filters.dateRange.end=year+"-09-14";
		}
	}
else {//we are in the period between 15 Sep and 31 Dec so we want to retrieve the first semester
	filters.dateRange.start=year+"-09-15";
	filters.dateRange.end=(year+1)+"-01-31";
	}
 
$(document).ready(function(){
	//set moment locale to french
	moment.locale('fr');
	}); 
 
  
$('#filter_alert').on('show.bs.modal', function (event) {
	//we clean the content of the alert
	$(this).find('.modal-body').html("");
	var checkbox = $(event.relatedTarget);
  //prevent the alert from being shown when a filter is unchecked and remove the filter from the var filters
  if (!checkbox.prop('checked')){
	unSetFilter(checkbox.prop('id'));
	return event.preventDefault() // stops modal from being shown
    }
  //populate the alert with the available filters
  else{
	  var trigger=checkbox.prop("id");
	  $("#filter_alert .btn-primary").attr("id",trigger+"_btn");
	  $('#filter_alert .close').attr("id","close_"+trigger);
	  //populate the filter alert based on the triggered filter
	  switch (trigger) {
		  	case "date_filter":
				//enable ok button
				$("#date_filter_btn").attr("disabled",false);
				$(this).find('.modal-title').text("Filtrer par date");
				//build the datepicker elements
				$(this).find('.modal-body').html("<p><input id='startDateFilter' onclick=\"setSens(\'endDateFilter\',\'max\');\" readonly='true'><label class='common_text margin-left-10'>à partir</label></p><p><input id='endDateFilter' onclick=\"setSens(\'startDateFilter\',\'min\');\" readonly='true'><label class='common_text margin-left-10'>de</label></p>");
				buildDatepickerFilter();
				break;
			case "course_filter":
				$(this).find('.modal-title').text("Filtrer par cours");
				//get student courses
				$.ajax({
						dataType : "json",
						type : 'GET',
						//url : "json/student-courses.json",
						url : "index.php?src=ajax&req=031", 
						async : true,
						success : function(data, status) {
							/** error checking */
							if(data.error.error_code > 0)
							{	
								launch_error_ajax(data.error);
								return;
							}

							var courses=data.courses;
							//populate the filter list
							var filter_alert=$("#filter_alert .modal-body");
							var table=document.createElement("table");
							table.className="table";
							table.id="course_filter_table";
							var row=table.insertRow(-1);
							row.className="text-bold";
							var cell1=row.insertCell(0);
							var cell2=row.insertCell(1);
							var cell3=row.insertCell(2);
							cell1.innerHTML="Code";
							cell1.className="min-width-110"
							cell2.innerHTML="Titre";
							cell3.innerHTML="Choisir";
							filter_alert.append(table);
							for (var i = 0; i < courses.length; i++)
								addCourse(courses[i]);
						},
						error : function(data, status, errors) {
							launch_error("Impossible de joindre le serveur (resp: '" + data.responseText + "')");
						}
					});
				break;
			case "event_type_filter":
				$(this).find('.modal-title').text("Filtrer par type d'événement");
				//get events type
				$.ajax({
						dataType : "json",
						type : 'GET',
						//url : "json/events_type.json",
						url : "index.php?src=ajax&req=041",
						async : true,
						success : function(data, status) {
							/** error checking */
							if(data.error.error_code > 0)
							{	
								launch_error_ajax(data.error);
								return;
							}
							var date_types=data.date_type;
							var event_types=data.event_type;
							//populate the filter list
							var filter_alert=$("#filter_alert .modal-body");
							var table=document.createElement("table");
							table.className="table";
							table.id="events_types_filter_table";
							var row=table.insertRow(-1);
							row.className="text-bold";
							var cell1=row.insertCell(0);
							var cell2=row.insertCell(1);
							cell1.innerHTML="Catégorie Temporelle";
							cell2.innerHTML="Choisir";
							cell2.className="text-center"
							var cell3=row.insertCell(2);
							var cell4=row.insertCell(3);
							cell3.innerHTML="Type d'événement";
							cell4.innerHTML="Choisir";
							cell4.className="text-center"
							filter_alert.append(table);
							var i=0;
							for (i; i < date_types.length; i++){
								var date_type_tag=document.createElement('p');
								date_type_tag.innerHTML = date_types[i].name;
								var table=document.getElementById("events_types_filter_table");
								var row=table.insertRow(-1);
								var cell1=row.insertCell(0);
								var cell2=row.insertCell(1);
								cell1.appendChild(date_type_tag);
								var input=document.createElement('input');
								input.type='checkbox';
								input.id=date_types[i].id;
								cell2.className="text-center";
								cell2.appendChild(input);
								if(event_types[i]!=null){
									var event_type_tag=document.createElement('p');
									event_type_tag.innerHTML = event_types[i].name;
									var cell3=row.insertCell(2);
									var cell4=row.insertCell(3);
									cell3.appendChild(event_type_tag);
									var input=document.createElement('input');
									input.type='checkbox';
									input.id=event_types[i].id;
									cell4.className="text-center";
									cell4.appendChild(input);
								}
							}
							var j=i;
							for(j;j<event_types.length;j++){
								var table=document.getElementById("events_types_filter_table");
								var row=table.insertRow(-1);
								var cell1=row.insertCell(0);
								var cell2=row.insertCell(1);
								var event_type_tag=document.createElement('p');
								event_type_tag.innerHTML = event_types[j].name;
								var cell3=row.insertCell(2);
								var cell4=row.insertCell(3);
								cell3.appendChild(event_type_tag);
								var input=document.createElement('input');
								input.type='checkbox';
								input.id=event_types[j].id;
								cell4.className="text-center";
								cell4.appendChild(input);
							}
						},
						error : function(xhr, status, error) {
							launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
						}
					});
				break;
			case "event_category_filter":
			$(this).find('.modal-title').text("Filtrer par categorie d'événement");
				//get events type
				$.ajax({
						dataType : "json",
						type : 'POST',
						//url : "json/event_categories.json",
						url : "index.php?src=ajax&req=047",
						data: {lang:"FR"},
						async : true,
						success : function(data, status) {
							/** error checking */
							if(data.error.error_code > 0)
							{	
								launch_error_ajax(data.error);
								return;
							}

							var student_categories=data.student;
							var academic_categories=data.academic;
							//populate the filter list
							var filter_alert=$("#filter_alert .modal-body");
							var table=document.createElement("table");
							table.className="table";
							table.id="events_categories_filter_table";
							var row=table.insertRow(-1);
							row.className="text-bold";
							var cell1=row.insertCell(0);
							var cell2=row.insertCell(1);
							cell1.innerHTML="Événement Academique";
							cell2.innerHTML="Choisir";
							cell2.className="text-center"
							var cell3=row.insertCell(2);
							var cell4=row.insertCell(3);
							cell3.innerHTML="Événement Privé";
							cell4.innerHTML="Choisir";
							cell4.className="text-center"
							filter_alert.append(table);
							var i=0;
							for (i; i < academic_categories.length; i++){
								var acad_category_tag=document.createElement('p');
								acad_category_tag.innerHTML = academic_categories[i].name;
								var table=document.getElementById("events_categories_filter_table");
								var row=table.insertRow(-1);
								var cell1=row.insertCell(0);
								var cell2=row.insertCell(1);
								cell1.appendChild(acad_category_tag);
								var input=document.createElement('input');
								input.type='checkbox';
								input.id=academic_categories[i].id;
								cell2.className="text-center";
								cell2.appendChild(input);
								if(student_categories[i]!=null){
									var student_category_tag=document.createElement('p');
									student_category_tag.innerHTML = student_categories[i].name;
									var cell3=row.insertCell(2);
									var cell4=row.insertCell(3);
									cell3.appendChild(student_category_tag);
									var input=document.createElement('input');
									input.type='checkbox';
									input.id=student_categories[i].id;
									cell4.className="text-center";
									cell4.appendChild(input);
								}
							}
							var j=i;
							for(j;j<student_categories.length;j++){
								var table=document.getElementById("events_categories_filter_table");
								var row=table.insertRow(-1);
								var cell1=row.insertCell(0);
								var cell2=row.insertCell(1);
								var student_category_tag=document.createElement('p');
								student_category_tag.innerHTML = student_categories[j].name;
								var cell3=row.insertCell(2);
								var cell4=row.insertCell(3);
								cell3.appendChild(student_category_tag);
								var input=document.createElement('input');
								input.type='checkbox';
								input.id=student_categories[j].id;
								cell4.className="text-center";
								cell4.appendChild(input);
							}
						},
						error : function(xhr, status, error) {
							launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");					
						}
					});
			break;	
			case "pathway_filter":
				$(this).find('.modal-title').text("Filtrer par pathway");
				//get pathways
				$.ajax({
						dataType : "json",
						type : 'GET',
						//url : "json/pathways.json",
						url : "index.php?src=ajax&req=111",
						async : true,
						success : function(data, status) {
							/** error checking */
							if(data.error.error_code > 0)
							{	
								launch_error_ajax(data.error);
								return;
							}

							var pathways=data.pathways;
							//populate the filter list
							var filter_alert=$("#filter_alert .modal-body");
							var table=document.createElement("table");
							table.className="table";
							table.id="pathways_filter_table";
							var row=table.insertRow(-1);
							row.className="text-bold";
							var cell1=row.insertCell(0);
							var cell2=row.insertCell(1);
							cell1.innerHTML="Pathway";
							cell2.innerHTML="Choisir";
							cell2.className="text-center"
							filter_alert.append(table);
							for (var i = 0; i < pathways.length; i++)
								addPathway(pathways[i]);
						},
						error : function(data, status, errors) {
							launch_error("Impossible de joindre le serveur (resp: '" + data.responseText + "')");
						}
					});
				break;
			case "professor_filter":
				$(this).find('.modal-title').text("Filtrer par professeur");
				//get professors
				$.ajax({
						dataType : "json",
						type : 'GET',
						//url : "json/all_professors.json",
						url : "index.php?src=ajax&req=021",
						async : true,
						success : function(data, status) {
							/** error checking */
							if(data.error.error_code > 0)
							{	
								launch_error_ajax(data.error);
								return;
							}

							var professors=data.professors;
							//populate the filter list
							var filter_alert=$("#filter_alert .modal-body");
							var table=document.createElement("table");
							table.className="table";
							table.id="professors_filter_table";
							var row=table.insertRow(-1);
							row.className="text-bold";
							var cell1=row.insertCell(0);
							var cell2=row.insertCell(1);
							cell1.innerHTML="Professor";
							cell2.innerHTML="Choisir";
							cell2.className="text-center"
							filter_alert.append(table);
							for (var i = 0; i < professors.length; i++)
								addProfessor(professors[i]);
						},
						error : function(data, status, errors) {
							launch_error("Impossible de joindre le serveur (resp: '" + data.responseText + "')");
						}
					});
				break;
			}
		}
})



//deals with the filter all_events which must disable all other (but the date filter) when pressed and enable all when pressed again
$("#all_events_filter").click(function(){
	if($(this).prop('checked')){
		//disable all other checkboxes but the date filter
		var checkboxes=$('input');
		for(var i=0;i<checkboxes.length;i++){
			var item=checkboxes.get(i);
				if(item.id!="all_events_filter"&&item.id!="date_filter"){
					item.disabled = true;
					item.checked=false;
				}
		}
		//save filter info in the filter object
		filters.allEvent["isSet"]="true";
	}
	else{
		$('input').removeAttr("disabled");
		filters.allEvent["isSet"]="false";
		}
	})

//builds the element datepicker in the alert called by the date range filter
//builds the element datepicker in the alert called by the date range filter
function buildDatepickerFilter() {
	//build current date
	var td = new Date();
	var dd = td.getDate();
	var mm = td.getMonth()+1; //January is 0!
	var yyyy = td.getFullYear();
	
	if(dd<10) {
		dd='0'+dd
	} 
	
	if(mm<10) {
		mm='0'+mm
	} 
	
	td = yyyy+'-'+mm+'-'+dd;
	td = moment(today);
	filterDates= new dhtmlXCalendarObject(["startDateFilter","endDateFilter"]);
	filterDates.hideTime();
	filterDates.setDateFormat("%l %d %F %Y");
	filterDates.setDate(td.format(fullcalendarDateFormat),td.format(fullcalendarDateFormat));
	var t = new Date();
	byId("endDateFilter").value = td.format(fullcalendarDateFormat);
	byId("startDateFilter").value = td.format(fullcalendarDateFormat);
}


function setSens(id, k) {
// update range
	if (k == "min")
		filterDates.setSensitiveRange(byId(id).value, null);
	else filterDates.setSensitiveRange(null, byId(id).value);
}
function byId(id) {
	return document.getElementById(id);
}

//add the course to the list in the filter alert
function addCourse(course){
    var course_tag=document.createElement('p');
	course_tag.innerHTML = course.code;
	var table=document.getElementById("course_filter_table");
	var row=table.insertRow(-1);
	var cell1=row.insertCell(0);
	var cell2=row.insertCell(1);
	cell1.appendChild(course_tag);
	cell2.className="margin-left-10";
	cell2.innerHTML=course.name;
	var input=document.createElement('input');
	input.type='checkbox';
	input.id=course.id;
	var cell3=row.insertCell(2);
	cell3.className="text-center";
	cell3.appendChild(input);
	}
	
//add the event type to the list in the filter alert
function addType(type){
    var type_tag=document.createElement('p');
	type_tag.innerHTML = type.name;
	var table=document.getElementById("events_types_filter_table");
	var row=table.insertRow(-1);
	var cell1=row.insertCell(0);
	var cell2=row.insertCell(1);
	cell1.appendChild(type_tag);
	var input=document.createElement('input');
	input.type='checkbox';
	input.id=type.name;
	cell2.className="text-center";
	cell2.appendChild(input);
	}
	
//add the pathway to the list in the filter alert
function addPathway(pathway){
    var pathway_tag=document.createElement('p');
	pathway_tag.innerHTML = pathway.name;
	var table=document.getElementById("pathways_filter_table");
	var row=table.insertRow(-1);
	var cell1=row.insertCell(0);
	var cell2=row.insertCell(1);
	cell1.appendChild(pathway_tag);
	var input=document.createElement('input');
	input.type='checkbox';
	input.id=pathway.id;
	cell2.className="text-center";
	cell2.appendChild(input);
	}
	
//add the professor to the list in the filter alert
function addProfessor(professor){
    var professor_tag=document.createElement('p');
	professor_tag.innerHTML = professor.name+" "+professor.surname;
	var table=document.getElementById("professors_filter_table");
	var row=table.insertRow(-1);
	var cell1=row.insertCell(0);
	var cell2=row.insertCell(1);
	cell1.appendChild(professor_tag);
	var input=document.createElement('input');
	input.type='checkbox';
	input.id=professor.id;
	cell2.className="text-center";
	cell2.appendChild(input);
	}
	
//set the global filter variable to be sent to the server on form submission
function setFilter(filter){
	switch(filter){
		case "date_filter":
			filters.dateRange["start"]=convert_date($("#startDateFilter").val(),"YYYY-MM-DD");
			filters.dateRange["end"]=convert_date($("#endDateFilter").val(),"YYYY-MM-DD");
			break;
		case "course_filter":
			filters.courses.isSet="true";
			var selectedCourses=$("#filter_alert input:checked");
			selectedCourses.each(function (){
				filters.courses.id.push(this.id);
				});
			break;
		case "event_type_filter":
			filters.eventTypes.isSet="true";
			var selectedEventTypes=$('#events_types_filter_table tbody td:nth-child(4) input:checked');
			var selectedTimeTyps=$('#events_types_filter_table tbody td:nth-child(2) input:checked')
			selectedEventTypes.each(function (){
				filters.eventTypes.eventType.push(this.id);
				});
			selectedTimeTyps.each(function (){
				filters.eventTypes.timeType.push(this.id);
				});
			break;
		case "event_category_filter":
			filters.eventCategories.isSet="true";
			var selectedCategories=$("#filter_alert input:checked");
			selectedCategories.each(function (){
				filters.eventCategories.id.push(this.id);
				});
			break;
		case "pathway_filter":
			filters.pathways.isSet="true";
				var selectedPathways=$("#filter_alert input:checked");
				selectedPathways.each(function (){
					filters.pathways.id.push(this.id);
					});
			break;
		case "professor_filter":
			filters.professors.isSet="true";
				var selectedProf=$("#filter_alert input:checked");
				selectedProf.each(function (){
					filters.professors.id.push(this.id);
					});
			break;
		}
	}
	
function unSetFilter(filter){
	//we uncheck the checkbox in the main view
	$("#filters #"+filter).attr("checked",false);
	switch(filter){
		case "date_filter":
			filters.dateRange.end=(year+1)+"-09-14";
			filters.dateRange.start=year+"-09-15";
		break;
		case "course_filter":
			filters.courses.isSet="false";
			//empty the array of ids'
			filters.courses.id.length=0;
		break;
		case "event_type_filter":
			filters.eventTypes.isSet="false";
			//empty the array of ids'
			filters.eventTypes.timeType.length=0;
			filters.eventTypes.eventType.length=0;
		break;
		case "event_category_filter":
			filters.eventCategories.isSet="false";
			//empty the array of ids'
			filters.eventCategories.id.length=0;
		break;
		case "pathway_filter":
			filters.pathways.isSet="false";
			//empty the array of ids'
			filters.pathways.id.length=0;
		break;
		case "professor_filter":
			filters.professors.isSet="false";
			//empty the array of ids'
			filters.professors.id.length=0;
		break;
		}
	}

	
//set global var filters when a filter is selected
$("#filter_alert .btn-primary").click(function(){
		var filter=$('#filter_alert .btn-primary').attr("id").replace("_btn","");
		setFilter(filter);
});

//call the unSetFilter when the close button is clicked
$("#filter_alert .close").click(function(){
		var filter=$('#filter_alert .btn-primary').attr("id").replace("_btn","");
		unSetFilter(filter);
		$("#filter_alert").modal("hide");
		//eventually disable the form submission button if no other filter is set
		if($("input:checked").length==0)
			$("#static_export").attr("disabled",true);
});

$("input").click(function(){
	//enable the send form button only when at least one filter is selected
	if($("input:checked").length>0)
		$("#static_export").attr("disabled",false);
	else $("#static_export").attr("disabled",true);
	});

//send data to server after filters completion
$("#static_export").click(function(){
	//UNCOMMENT FOLLOWING LINE FOR TESTING WITHOUT SERVER
	//$("#dynamic_export_download_alert").modal("show");
	$.ajax({
			dataType : "json",
			type : 'POST',
			url : "index.php?src=ajax&req=091",
			data : filters,
			success : function(data, status) {
				/** error checking */
				if(data.error.error_code > 0)
				{	
					launch_error_ajax(data.error);
					return;
				}

				$("#static_export_download_alert").modal("show");
				$("#static_export_file").attr("href",data.url);
			},
			error : function(data, status, errors) {
				launch_error("Impossible de joindre le serveur (resp: '" + data.responseText + "')");
			}
		});
});
	
//enable filter ok button when at least one checkbox is selected
$("#filter_alert").on("click", $("#filter_alert input"),function(){
	//we make sure we are not in the date filter
		if($("#date_filter_btn").length==0){
			var checked=$("#filter_alert input:checked");
			if(checked.length>0)
				$("#filter_alert .btn-primary").attr("disabled",false);
			else $("#filter_alert .btn-primary").attr("disabled",true);
		}
	})
	
$("#filter_alert").on('hidden.bs.modal', function (e) {
	if($("#date_filter_btn").length==0)
		$("#filter_alert .btn-primary").attr("disabled",true);
});