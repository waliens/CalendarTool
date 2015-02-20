// JavaScript Document
$(document).ready(function() {
	//populate user profile  info and courses, both optional and mandatory
	$.ajax({
		dataType : "json",
		type : 'GET',
		url : "professor-profile.json",
		//url: "professor-profile.html&src='ajax'&req=8",
		success : function(data, status) {
			var first_name=data.firstName;
			var last_name=data.lastName;
			var courses=data.courses;
			//populate the user profile info
			document.getElementById("user-name").innerHTML=first_name+" "+last_name;
			//populate the global courses and independent events tables
			for (var i = 0; i < courses.length; i++){
				if(courses[i].global=="true")
					addGlobalEvent(courses[i]);
				else addIndependentEvent(courses[i]);
			}
		},
		error : function(data, status, errors) {
			// Inserire un messagio di errore
		}
	});
});

//populate global events table
function addGlobalEvent(course){
	var global_events=document.getElementById("global_events");
    var course_id=document.createElement('a');
	course_id.setAttribute("id",course.code);
	course_id.innerHTML = course.code;
	//link the event link to the event info pane
	course_id.setAttribute("data-toggle","modal");
	course_id.setAttribute("data-target","#event_info");
	course_id.setAttribute("event-name",course.lib_cours_complet);
	var delete_icon=document.createElement('a');
	var edit_icon=document.createElement('a');
	edit_icon.className="edit";
	delete_icon.className="delete";
	//link the delete icon to the delete alert
	delete_icon.setAttribute("data-toggle","modal");
	delete_icon.setAttribute("data-target","#delete_global_event_alert");
	delete_icon.setAttribute("course-id",course.code);
	delete_icon.setAttribute("course-name",course.lib_cours_complet);
	var div_container1=document.createElement("div");
	div_container1.className="text-center";
	var div_container2=document.createElement("div");
	div_container2.className="text-center";
	div_container1.appendChild(edit_icon);
	div_container2.appendChild(delete_icon);
	var row=global_events.insertRow(-1);
	var cell1=row.insertCell(0);
	var cell2=row.insertCell(1);
	var cell3=row.insertCell(2);
	var cell4=row.insertCell(3);
	cell1.appendChild(course_id);
	cell2.innerHTML=course.lib_cours_complet;
	cell3.appendChild(div_container1);
	cell4.appendChild(div_container2);
	}

//populate independent events table
function addIndependentEvent(indep_event){
	var all_indep_events=document.getElementById("independent-events");
    var event_name=document.createElement('a');
	//link the event link to the event info pane
	event_name.setAttribute("data-toggle","modal");
	event_name.setAttribute("data-target","#event_info");
	event_name.setAttribute("id",indep_event.code);
	event_name.setAttribute("event-name",indep_event.lib_cours_complet);
	event_name.innerHTML = indep_event.lib_cours_complet;
	var delete_icon=document.createElement('a');
	var edit_icon=document.createElement('a');
	edit_icon.className="edit";
	delete_icon.className="delete";
	//link the delete icon to the delete alert
	delete_icon.setAttribute("data-toggle","modal");
	delete_icon.setAttribute("data-target","#delete_indep_event_alert");
	delete_icon.setAttribute("course-id",indep_event.code);
	delete_icon.setAttribute("course-name",indep_event.lib_cours_complet);
	var div_container1=document.createElement("div");
	div_container1.className="text-center";
	var div_container2=document.createElement("div");
	div_container2.className="text-center";
	div_container1.appendChild(edit_icon);
	div_container2.appendChild(delete_icon);
	var row=all_indep_events.insertRow(-1);
	var cell1=row.insertCell(0);
	var cell2=row.insertCell(1);
	var cell3=row.insertCell(2);
	cell1.appendChild(event_name);
	cell2.appendChild(div_container1);
	cell3.appendChild(div_container2);
	}
	
//populate delete global event alert
$('#delete_global_event_alert').on('show.bs.modal', function (event) {
	var event = $(event.relatedTarget);
	$("span[name=global_course_deleted]").text(event.attr("course-name"));
	$("#delete_confirm").attr("event-id",event.prop("id"));
});

//populate delete independent event alert
$('#delete_indep_event_alert').on('show.bs.modal', function (event) {
	var event = $(event.relatedTarget);
	$("span[name=indep_event_deleted]").text(event.attr("course-name"));
	$("#delete_confirm").attr("event-id",event.prop("id"));
});

//display event info on click
$('#event_info').on('show.bs.modal', function (event) {
	var target = $(event.relatedTarget);
	$("#event-title").text(target.attr("event-name"));
	//get event info from server
	$.ajax({
		dataType : "json",
		type : 'GET',
		url : "globalevent-info.json",
		//url: "professor-profile.html&src='ajax'&req=9&id=target.id",
		success : function(data, status) {
			//store the info retrieved from the server in ad hoc vars
			var eventId=data.id;
			var eventName=data.name;
			var eventDescription=data.description;
			var eventLanguage=data.language;
			var eventWork=data.work;
			var eventStart=data.start;
			var eventEnd=data.end;
			var eventPathway=data.pathway;
			var subevents=data.subevents;
			var team=data.team;
			//populate global event info
			$("#event-title").text(eventName);
			$("#event-details").text(eventDescription);
			$("#event-lang").text(eventLanguage);
			$("#event-work").text(eventWork);
			$("#event-period").text(eventStart+" - "+eventEnd);
			var table=document.createElement("table");
			for(var i=0;i<eventPathway.length;i++){
				var row=table.insertRow(-1);
				var cell1=row.insertCell(0);
				cell1.innerHTML=eventPathway[i].name;
				}
			$("#event-pathway").html(table);
			//populate subevents info
			var subeventsTable=document.createElement("table");
			subeventsTable.className="table";
			var row=subeventsTable.insertRow(-1);
			row.className="text-bold";
			var cell0=row.insertCell(0);
			var cell00=row.insertCell(1);
			cell0.innerHTML="Code";
			cell00.innerHTML="Nom";
			for(var i=0;i<subevents.length;i++){
				var row=subeventsTable.insertRow(-1);
				var cell1=row.insertCell(0);
				var cell2=row.insertCell(1);
				var subevent_tag=document.createElement('a');
				subevent_tag.innerHTML = subevents[i].code;
				subevent_tag.setAttribute("subevent-id",subevents[i].code);
				//link the event link to the event info pane
				subevent_tag.setAttribute("data-toggle","modal");
				subevent_tag.setAttribute("data-target","#subevent_panel");
				cell1.appendChild(subevent_tag);
				cell2.innerHTML=subevents[i].lib_cours_complet;
				}
			$("#subevents_info").html(subeventsTable);
			//populate team info
			var teamTable=document.createElement("table");
			teamTable.className="table";
			for(var i=0;i<team.length;i++){
				var row=teamTable.insertRow(-1);
				var cell1=row.insertCell(0);
				cell1.innerHTML=team[i].name;
				cell1.setAttribute("team-id",team[i].id)
				}
			$("#event_team").html(teamTable);
		},
		error : function(data, status, errors) {
			// Inserire un messagio di errore
		}
	});
	});
	
	
/*var event_id=data.id;
			var event_name=data.name;
			var event_description=data.description;
			var event_place=data.place;
			var event_type=data.type;
			var start_day=data.startDay;
			var end_day=data.endDay;
			var start_time=data.startTime;
			var end_time=data.endTime;
			var deadline=data.deadline;
			var category_id=data.category_id;
			var category_name=data.category_name;
			var recurrence=data.recurrence;
			var annotation=data.annotation;
			var favourite=data.favourite;*/