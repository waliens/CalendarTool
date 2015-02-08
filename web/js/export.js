// JavaScript Document

var filters = {
          	allEvents: {isSet: 'false'},
			dataRange: {isSet: 'false', startDate: 'null', endDate: 'null'},
			courses: {isSet: 'false', id:[]},
			eventTypes: {isSet: 'false', id:[]},
			sessions: {isSet: 'false', id:[]},
			professors:	{isSet: 'false', id:[]}
          };
 
$(document).ready(function(){
	//bind alert popup to filters submission button
	$("#dynamic_export").popover();
	//bind alert popup to ok button of each filter pane
	$("#filter_alert .btn-primary").popover();
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
	  $("#filter_alert .btn-primary").attr("id",trigger);
	  $('#filter_alert .close').attr("id","close_"+trigger);
	  //populate the filter alert based on the triggered filter
	  switch (trigger) {
		  	case "date_filter":
				$(this).find('.modal-title').text("Filtrer par date");
				//build the datepicker elements
				$(this).find('.modal-body').html("<p><input id='startDate' onclick='setSens('endDate', 'max');' readonly='true'><label class='common_text margin-left-10'>à partir</label></p><p><input id='endDate' onclick='setSens('startDate', 'min');' readonly='true'><label class='common_text margin-left-10'>de</label></p>");
				buildCalendar();
				break;
			case "course_filter":
				$(this).find('.modal-title').text("Filtrer par cours");
				//get student courses
				$.ajax({
						dataType : "json",
						type : 'GET',
						url : "student-courses.json",
						async : true,
						success : function(data, status) {
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
							cell1.innerHTML="ID";
							cell2.innerHTML="Title";
							cell3.innerHTML="Choisir";
							filter_alert.append(table);
							for (var i = 0; i < courses.length; i++)
								addCourse(courses[i]);
						},
						error : function(data, status, errors) {
							// Inserire un messagio di errore
						}
					});
				break;
			case "event_type_filter":
				$(this).find('.modal-title').text("Filtrer par type d'événement");
				//get events type
				$.ajax({
						dataType : "json",
						type : 'GET',
						url : "events_type.json",
						async : true,
						success : function(data, status) {
							var types=data.types;
							//populate the filter list
							var filter_alert=$("#filter_alert .modal-body");
							var table=document.createElement("table");
							table.className="table";
							table.id="events_types_filter_table";
							var row=table.insertRow(-1);
							row.className="text-bold";
							var cell1=row.insertCell(0);
							var cell2=row.insertCell(1);
							cell1.innerHTML="Type";
							cell2.innerHTML="Choisir";
							cell2.className="text-center"
							filter_alert.append(table);
							for (var i = 0; i < types.length; i++)
								addType(types[i]);
						},
						error : function(data, status, errors) {
							// Inserire un messagio di errore
						}
					});
				break;
			case "session_filter":
				$(this).find('.modal-title').text("Filtrer par session");
				//get sessions
				$.ajax({
						dataType : "json",
						type : 'GET',
						url : "sessions.json",
						async : true,
						success : function(data, status) {
							var sessions=data.sessions;
							//populate the filter list
							var filter_alert=$("#filter_alert .modal-body");
							var table=document.createElement("table");
							table.className="table";
							table.id="sessions_filter_table";
							var row=table.insertRow(-1);
							row.className="text-bold";
							var cell1=row.insertCell(0);
							var cell2=row.insertCell(1);
							cell1.innerHTML="Session";
							cell2.innerHTML="Choisir";
							cell2.className="text-center"
							filter_alert.append(table);
							for (var i = 0; i < sessions.length; i++)
								addSession(sessions[i]);
						},
						error : function(data, status, errors) {
							// Inserire un messagio di errore
						}
					});
				break;
			case "professor_filter":
				$(this).find('.modal-title').text("Filtrer par professeur");
				//get sessions
				$.ajax({
						dataType : "json",
						type : 'GET',
						url : "all_professors.json",
						async : true,
						success : function(data, status) {
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
							// Inserire un messagio di errore
						}
					});
				break;
			}
		}
})

//deals with the filter all_events which must disable all other when pressed and enable all when pressed again
$("#all_events_filter").click(function(){
	if($(this).prop('checked')){
		//disable all other checkboxes
		var checkboxes=$('input');
		for(var i=0;i<checkboxes.length;i++){
			var item=checkboxes.get(i);
				if(item.id!="all_events_filter"){
					item.disabled = true;
					item.checked=false;
				}
		}
		//save filter info in the filter object
		filters.allEvents["isSet"]="true";
	}
	else{
		$('input').removeAttr("disabled");
		filters.allEvents["isSet"]="false";
		}
	})

//builds the element datepicker in the alert called by the date range filter
function buildCalendar() {
	var myCalendar;
	//build current date
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1; //January is 0!
	var yyyy = today.getFullYear();
	
	if(dd<10) {
		dd='0'+dd
	} 
	
	if(mm<10) {
		mm='0'+mm
	} 
	
	today = yyyy+'-'+mm+'-'+dd;
	myCalendar = new dhtmlXCalendarObject(["startDate","endDate"]);
	myCalendar.setDate(today);
	myCalendar.hideTime();
	// init values
	var t = new Date();
	byId("startDate").value = today;
	byId("endDate").value = today;
}

function setSens(id, k) {
	// update range
	if (k == "min") {
		myCalendar.setSensitiveRange(byId(id).value, null);
	} else {
		myCalendar.setSensitiveRange(null, byId(id).value);
	}
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
	cell2.innerHTML=course.lib_cours_complet
	var input=document.createElement('input');
	input.type='checkbox';
	input.id=course.code;
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
	
//add the session to the list in the filter alert
function addSession(session){
    var session_tag=document.createElement('p');
	session_tag.innerHTML = session.name;
	var table=document.getElementById("sessions_filter_table");
	var row=table.insertRow(-1);
	var cell1=row.insertCell(0);
	var cell2=row.insertCell(1);
	cell1.appendChild(session_tag);
	var input=document.createElement('input');
	input.type='checkbox';
	input.id=session.id;
	cell2.className="text-center";
	cell2.appendChild(input);
	}
	
//add the professor to the list in the filter alert
function addProfessor(professor){
    var professor_tag=document.createElement('p');
	professor_tag.innerHTML = professor.name;
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
			filters.dataRange.isSet="true";
			filters.dataRange["startDate"]=$("#startDate").val();
			filters.dataRange["endDate"]=$("#endDate").val();
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
			var selectedTypes=$("#filter_alert input:checked");
			selectedTypes.each(function (){
				filters.eventTypes.id.push(this.id);
				});
			break;
		case "session_filter":
			filters.sessions.isSet="true";
				var selectedSessions=$("#filter_alert input:checked");
				selectedSessions.each(function (){
					filters.sessions.id.push(this.id);
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
			filters.dataRange.isSet="false";
		break;
		case "course_filter":
			filters.courses.isSet="false";
			//empty the array of ids'
			filters.courses.id.length=0;
		break;
		case "event_type_filter":
			filters.eventTypes.isSet="false";
			//empty the array of ids'
			filters.eventTypes.id.length=0;
		break;
		case "session_filter":
			filters.sessions.isSet="false";
			//empty the array of ids'
			filters.sessions.id.length=0;
		break;
		case "professor_filter":
			filters.professors.isSet="false";
			//empty the array of ids'
			filters.professors.id.length=0;
		break;
		}
	}

	
//prevent modal from hiding if the ok button is pressed and no input box has been selected
$("#filter_alert .btn-primary").click(function(){
	$('.modal').on('hide.bs.modal', function (event) {
				var filter=$('#filter_alert .btn-primary').attr("id");
				//we make sure the form has at least an input field
				if($("#filter_alert input:checkbox").length!=0){
					var selectedCourses=$("#filter_alert input:checked");
					//make sure there's at least one box checked otherwise show an error and prevent the alert from closing
					if(selectedCourses.length==0)
						return event.preventDefault() // stops modal from closing	
					else {
						setFilter(filter);
						$('#filter_alert .btn-primary').popover("destroy");}	
				}
				else setFilter(filter);
		});
});

//call the unSetFilter when the close button is clicked
$("#filter_alert .close").click(function(){
		var filter=$('#filter_alert .btn-primary').attr("id");
		unSetFilter(filter);
});

//send data to server after filters comple
$("#dynamic_export").click(function(){
	//check that at least a choice has been made
	if($("#filters input:checked").length==0){
		$("#dynamic_export").data("title","Erreur");
		$("#dynamic_export").data("content","Sélectionner au moins une option");
		$("#dynamic_export").popover("show");
	}
	//send data to server
	else{
		//UNCOMMENT FOLLOWING LINE FOR TESTING WITHOUT SERVER
		//$("#dynamic_export_download_alert").modal("show");
		$.ajax({
						dataType : "json",
						type : 'POST',
						url : "dynamic_export.html",
						data : filters,
						success : function(data, status) {
							$("#dynamic_export_download_alert").modal("show");
							$("#dynamic_export_file").attr("href",data.url);
						},
						error : function(data, status, errors) {
							// Inserire un messagio di errore
						}
					});
		}
});
