// JavaScript Document

//update the navbar
$("#navbar li").removeClass("active");
$("#profile_nav").addClass("active");
var subevent;

$(document).ready(function() {
	//populate user profile  info and courses, both optional and mandatory
	$.ajax({
		dataType : "json",
		type : 'GET',
		url : "json/professor-profile.json",
		url: "index.php?src=ajax&req=022",
		success : function(data, status) {
			var first_name=data.firstName;
			var last_name=data.lastName;
			var courses=data.courses;
			var indep_events=data.indep_events
			//populate the user profile info
			document.getElementById("user-name").innerHTML=first_name+" "+last_name;
			//populate the global courses and independent events tables
			for (var i = 0; i < courses.length; i++)
					addGlobalEvent(courses[i]);
			for (var i=0; i<indep_events.length; i++)
				addIndependentEvent(indep_events[i]);
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
	delete_icon.className="delete";
	//link the delete icon to the delete alert
	delete_icon.setAttribute("data-toggle","modal");
	delete_icon.setAttribute("data-target","#delete_global_event_alert");
	delete_icon.setAttribute("course-code",course.code);
	delete_icon.setAttribute("course-id",course.id);
	delete_icon.setAttribute("course-name",course.lib_cours_complet);
	var div_container2=document.createElement("div");
	div_container2.className="text-center";
	div_container2.appendChild(delete_icon);
	var row=global_events.insertRow(1);
	var cell1=row.insertCell(0);
	var cell2=row.insertCell(1);
	var cell3=row.insertCell(2);
	cell1.appendChild(course_id);
	cell2.innerHTML=course.lib_cours_complet;
	cell3.appendChild(div_container2);
	}

//populate independent events table
function addIndependentEvent(indep_event){
	var all_indep_events=document.getElementById("independent-events");
    var event_name=document.createElement('a');
	//link the event link to the event info pane
	event_name.setAttribute("data-toggle","modal");
	event_name.setAttribute("data-target","#subevent_info");
	event_name.setAttribute("id",indep_event.id);
	event_name.setAttribute("event-name",indep_event.lib_cours_complet);
	event_name.innerHTML = indep_event.lib_cours_complet;
	event_name.onclick=function(e){subevent=e.target}
	var delete_icon=document.createElement('a');
	var edit_icon=document.createElement('a');
	edit_icon.className="edit";
	delete_icon.className="delete";
	//link the delete icon to the delete alert
	delete_icon.setAttribute("data-toggle","modal");
	delete_icon.setAttribute("data-target","#delete_indep_event_alert");
	delete_icon.setAttribute("course-code",indep_event.code);
	delete_icon.setAttribute("course-id",indep_event.id);
	delete_icon.setAttribute("course-name",indep_event.lib_cours_complet);
	var div_container1=document.createElement("div");
	div_container1.className="text-center";
	var div_container2=document.createElement("div");
	div_container2.className="text-center";
	div_container1.appendChild(edit_icon);
	div_container2.appendChild(delete_icon);
	var row=all_indep_events.insertRow(1);
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
	$("#global_event_delete_confirm").attr("event-id",event.attr("course-id"));
});

//populate delete independent event alert
$('#delete_indep_event_alert').on('show.bs.modal', function (event) {
	var event = $(event.relatedTarget);
	$("span[name=indep_event_deleted]").text(event.attr("course-name"));
	$("#indep_event_delete_confirm").attr("event-id",event.attr("course-id"));
});

//confirm global event deletion
$("#delete_global_event_alert").on("click",".btn-primary",function(event){
	$("a[course-id='"+event.currentTarget.getAttribute("event-id")+"']").parent().parent().parent().remove();
	var event_id=event.currentTarget.getAttribute("event-id");
	//send deletion confirmation to server
	$.ajax({
			dataType : "json",
			type : 'POST',
			url : "index.php?src=ajax&req=o33",
			data : event_id,
			success : function(data, status) {
				// Inserire messaggio di successo
			},
			error : function(xhr, status, error) {
					  var err = eval("(" + xhr.responseText + ")");
					  alert(err.Message);
					}
		});
	
	})

//populate event info when modal appears
$("#event_info").on("show.bs.modal",function(event){
	var event_id=event.relatedTarget.getAttribute('event-id');
	$.ajax({
		dataType : "json",
		type : 'GET',
		//url : "json/globalevent-info.json",
		url : "index.php?src=ajax&req=032&event=2",// + event_id,
		success : function(data, status) {
			var global_event_id=data.id;
			var global_event_id_ulg=data.id_ulg;
			var global_event_name=data.name;
			var global_event_name_short=data.name_short
			var global_event_description=data.description;
			var global_event_feedback=data.feedback;
			var global_event_period=data.period;
			var global_event_lang=data.language;
			var global_event_acad_year=data.acad_year;
			var global_event_work_th=data.workload.th;
			var global_event_work_pr=data.workload.pr;
			var global_event_work_au=data.workload.au;
			var global_event_work_st=data.workload.st;
			var pathways=data.pathways;
			var subevents=data.subevents;
			var team=data.team;
			//populate alert with global event data
			$("#event-title").text(global_event_id_ulg+"\t"+global_event_name_short);
			$("#event-details").text(global_event_description);
			$("#event-feedback").text(global_event_feedback);
			$("#event-lang").text(global_event_lang);
			$("#event-period").text(global_event_period);
			$("#event-work").text("");
			if(global_event_work_th!="")
				$("#event-work").append(global_event_work_th+"h Th. ");
			if(global_event_work_pr!="")
				$("#event-work").append(global_event_work_pr+"h Proj. ");
			if(global_event_work_au!="")
				$("#event-work").append(global_event_work_au+"h Au. ");
			if(global_event_work_st!="")
				$("#event-work").append(global_event_work_st+"h St.");
			var pathways_table=document.createElement("table");
			pathways_table.id="pathways_table";
			$("#event-pathway").html(pathways_table);
			for(var i=0;i<pathways.length;i++)
				addPathway(pathways[i]);
			var subevents_table=document.createElement("table");
			subevents_table.className="table";
			subevents_table.id="subevents_table";
			$("#subevents_info").html(subevents_table);
			for(var i=0;i<subevents.length;i++)
				addSubevent(subevents[i]);
			var team_table=document.createElement("table");
			team_table.className="table";
			team_table.id="team_table";
			$("#event_team").html(team_table);	
			for(var i=0;i<team.length;i++)
				addTeamMember(team[i]);
		},
		error: function(xhr, status, error) {
		  var err = eval("(" + xhr.responseText + ")");
		  alert(err.Message);
		}
	});
	})

function addPathway(pathway){
	table=$("#pathways_table");
	var row=document.createElement("tr");
	var cell=document.createElement("td");
	cell.innerHTML=pathway.name;
	row.appendChild(cell);
	table.append(row);
	}
	
function addSubevent(item){
	table=$("#subevents_table");
	var row=document.createElement("tr");
	var cell=document.createElement("td");
	var a=document.createElement("a");
	a.setAttribute("data-dismiss","modal");
	a.setAttribute("data-target","#subevent_info");
	a.setAttribute("data-toggle","modal");
	a.innerHTML=item.name;
	a.id=item.id;
	a.onclick=function(e){showSubeventModal=true; subevent=e.target}
	cell.appendChild(a);
	row.appendChild(cell);
	table.append(row);
	}
	
function addTeamMember(member){
	table=$("#team_table");
	var row=document.createElement("tr");
	var cell1=document.createElement("td");
	cell1.innerHTML=member.name+" "+member.surname;
	cell1.id=member.id;
	row.appendChild(cell1);
	var cell2=document.createElement("td");
	cell2.innerHTML=member.role;
	row.appendChild(cell2);
	table.append(row);
	}

//displays info of subevents and independent events
$("#subevent_info").on("show.bs.modal",function(){
	//get subevent info
	var subevent_id=subevent.getAttribute('id');
	$.ajax({
		dataType : "json",
		type : 'GET',
		//url : "json/subevent-info.json",
		url : "index.php?src=ajax&req=051&event=" + subevent_id,
		success : function(data, status) {
			var subevent_id=data.id;
			var subevent_title=data.name;
			var subevent_description=data.description;
			var subevent_place=data.place;
			var subevent_type=data.type;
			var subevent_start=moment(data.startDay);
			if(data.startTime!=""){
				var chunks=data.startTime.split(":");
				subevent_start.set("hour",chunks[0]);
				subevent_start.set("minute",chunks[1]);
				$("#subevent_startDate").text(subevent_start.format("dddd, MMMM Do YYYY, h:mm a"));
			}
			else $("#subevent_startDate").text(subevent_start.format("dddd, MMMM Do YYYY"));
			var subevent_end;
			if(data.endDay!=""){
				$("#subevent_endDate").parent().removeClass("hidden");
				$("#subevent_startDate").prev().removeClass("hidden");
				subevent_end=moment(data.endDay);
				if(data.endTime!=""){
					var chunks=data.endTime.split(":");
					subevent_end.set("hour",chunks[0]);
					subevent_end.set("minute",chunks[1]);
					$("#subevent_endDate").text(subevent_end.format("dddd, MMMM Do YYYY, h:mm a"));
				}
				else $("#subevent_endDate").text(subevent_end.format("dddd, MMMM Do YYYY"));
			}
			else {
				$("#subevent_endDate").parent().addClass("hidden");
				$("#subevent_startDate").prev().addClass("hidden");
			}
			var deadline=data.deadline;
			var category_id=data.category_id;
			var category_name=data.category_name;
			var recurrence=get_recursion(data.recurrence);
			$("#recurrence").text(recurrence);
			if(recurrence=="jamais"){
				$("#start-recurrence").parent().addClass("hidden");
				$("#end-recurrence").parent().addClass("hidden");
			}
			else{
				$("#start-recurrence").parent().removeClass("hidden");
				$("#end-recurrence").parent().removeClass("hidden");
				var start_recurrence=moment(data.start_recurrence);
				var end_recurrence=moment(data.end_recurrence);
				$("#start-recurrence").text(start_recurrence.format("dddd, MMMM Do YYYY"));
				$("#end-recurrence").text(end_recurrence.format("dddd, MMMM Do YYYY"));
				}
			var favourite=data.favourite;
			//populate alert with global event data
			$("#subevent-title").text(subevent_title);
			$("#subevent-details").text(subevent_description);
			$("#subevent-category").text(category_name);
			$("#subevent-place").text(subevent_place);
		},
		error: function(xhr, status, error) {
		  var err = eval("(" + xhr.responseText + ")");
		  alert(err.Message);
		}
	});
})

function get_recursion(recursion_id){
	switch(recursion_id){
		case "1":
			return "jamais";
		case "2":
			return "tous les jours";
		case "3":
			return "toutes les semaines";
		case "4":
			return "toutes les deux semaines";
		case "5":
			return "tous les mois";
		case "6":
			return "tous les ans"
		}
	}