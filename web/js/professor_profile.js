// JavaScript Document

//update the navbar
$("#navbar li").removeClass("active");
$("#profile_nav").addClass("active");
var subevent;
//var holding values to abort global event modification
var edit_global_event_old;

$(document).ready(function() {
	//populate user profile  info and courses, both optional and mandatory
	$.ajax({
		dataType : "json",
		type : 'GET',
		//url : "json/professor-profile.json",
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
		error: function(xhr, status, error) {
		  var err = eval("(" + xhr.responseText + ")");
		  alert(err.Message);
		}
	});
});

//populate global events table
function addGlobalEvent(course){
	var global_events=document.getElementById("global_events");
    var course_id=document.createElement('a');
	course_id.setAttribute("id",course.code);
	course_id.setAttribute("event-id",course.id);
	course_id.innerHTML = course.code;
	//link the event link to the event info pane
	course_id.setAttribute("data-toggle","modal");
	course_id.setAttribute("data-target","#event_info");
	course_id.setAttribute("event-name",course.lib_cours_complet);
	$("#edit_global_event").attr("event-id",course.id);
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
	event_name.setAttribute("event-name",indep_event.name);
	event_name.innerHTML = indep_event.name;
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
	delete_icon.setAttribute("course-name",indep_event.name);
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
	var event_id=event.currentTarget.getAttribute("event-id");
	//send deletion confirmation to server
	$.ajax({
			dataType : "json",
			type : 'POST',
			url : "index.php?src=ajax&req=033",
			data : {id:event_id},
			success : function(data, status) {
				$("a[course-id='"+event.currentTarget.getAttribute("event-id")+"']").parent().parent().parent().remove();
			},
			error : function(xhr, status, error) {
					  var err = eval("(" + xhr.responseText + ")");
					  alert(err.Message);
					}
		});
	
	})
	
//confirm independent event deletion
$("#delete_indep_event_alert").on("click",".btn-primary",function(event){
	var event_id=event.currentTarget.getAttribute("event-id");
	//send deletion confirmation to server
	$.ajax({
			dataType : "json",
			type : 'POST',
			url : "index.php?src=ajax&req=083",
			data : {id:event_id,applyRecursive:"false"},
			success : function(data, status) {
				$("#independent-events #"+event_id).parent().parent().remove()
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
		url : "index.php?src=ajax&req=032&event="+event_id,
		success : function(data, status) {
			var global_event_id=data.id;
			var global_event_id_ulg=data.id_ulg;
			var global_event_name=data.name;
			var global_event_name_short=data.name_short
			var global_event_description=data.description;
			var global_event_feedback=data.feedback;
			var global_event_period=data.period;
			var global_event_lang=convert_language(data.language);
			var global_event_acad_year=data.acad_year;
			var global_event_work_th=data.workload.th;
			var global_event_work_pr=data.workload.pr;
			var global_event_work_au=data.workload.au;
			var global_event_work_st=data.workload.st;
			var pathways=data.pathways;
			var subevents=data.subevents;
			var team=data.team;
			//populate alert with global event data
			$("#add-event-member-conf-abort-buttons").addClass("hidden");
			$("#add-event-member").attr("event-id",global_event_id);
			$("#add-subevent").attr("event-id",global_event_id);
			$("#add-event-member-abort").attr("event-id",global_event_id);
			$("#add-event-member-confirm").attr("event-id",global_event_id);
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
				populateSubevent(subevents[i]);
			var team_table=document.createElement("table");
			team_table.className="table";
			team_table.id="team_table";
			$("#event_team").html(team_table);	
			for(var i=0;i<team.length;i++)
				populateTeamMember(team[i]);
			$("#team_table tr:first .delete").addClass("delete-disabled");
			//hide confirm/abort edit buttons
			$("#edit-global-event-buttons").addClass("hidden");
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
	
//populate subevent to the table subevents of a global event
function populateSubevent(item){
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

//populate team members to the table of team members of a global event
function populateTeamMember(member){
	table=$("#team_table");
	var row=document.createElement("tr");
	var cell1=document.createElement("td");
	cell1.innerHTML=member.name+" "+member.surname;
	cell1.id=member.user;
	row.appendChild(cell1);
	var cell2=document.createElement("td");
	cell2.innerHTML=member.role;
	row.appendChild(cell2);
	table.append(row);
	var cell3=document.createElement("td");
	cell3.innerHTML='<div class="text-center"><a class="delete" member-id="'+member.user+'"></a></div>';
	row.appendChild(cell3);
	table.append(row);
	}

//displays info of subevents and independent events
$("#subevent_info").on("show.bs.modal",function(){
	//get subevent info
	var subevent_id=subevent.getAttribute('id');
	$.ajax({
		dataType : "json",
		type : 'GET',
		url : "index.php?src=ajax&req=051&event="+subevent_id,
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

function edit_global_event(){
	var event_id=event.currentTarget.parentNode.getAttribute("event-id");
	//initialize variables to hold old content of the event
	edit_global_event_old={details:$("#event-details").text(),feedback:$("#event-feedback").text(),language:$("#event-lang").text()};
	var language=edit_global_event_old.language.replace(/\s+/g, '');
	var language_code=revert_convert_language(language);
	//update modale view to make language, feedback and description editable
	$("#event-details").html('<input type="text" class="form-control" aria-describedby="sizing-addon1" id="edit_global_cours_details" value=\"'+edit_global_event_old.details+'">');
	$("#event-feedback").html('<input type="text" class="form-control" aria-describedby="sizing-addon1" id="edit_global_cours_feedback" value="'+edit_global_event_old.feedback+'">');
	$("#event-lang").html('<div class="dropdown"><button class="btn btn-default dropdown-toggle" type="button" id="edit_cours_language" data-toggle="dropdown" aria-expanded="true" language="'+language_code+'"> '+language+' <span class="caret"></span> </button><ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" id="edit_languages_list"><li role="presentation"><a role="menuitem" tabindex="-1" href="#" language="FR">Français</a></li><li role="presentation"><a role="menuitem" tabindex="-1" href="#" language="EN">Anglais</a></li></ul></div>');
	//display confirm and abort buttons and attach to the button the id of the event we are editing
	$("#edit-global-event-buttons").removeClass("hidden");
	$("#edit-global-event-buttons").attr("event-id",event_id);
	}
	
//confirm global event edit
function  edit_global_event_confirm(){
	var event_id=$("#edit-global-event-buttons").attr("event-id");
	var event_details=$("#edit_global_cours_details").val();
	var event_lan=convert_language($("#edit_cours_language").attr("language"));
	var event_feedback=$("#edit_global_cours_feedback").val();
	$.ajax({
		dataType : "json",
		type : 'POST',
		url : "index.php?src=ajax&req=034",
		data: {id:event_id,description:event_details,feedback:event_feedback,language:event_lan},
		success : function(data, status) {
			$("#event-details").html('');
			$("#event-details").text(event_details);
			$("#event-feedback").html('');
			$("#event-feedback").text(event_feedback);
			$("#event-lang").html('');
			$("#event-lang").text(event_lan);
			$("#edit-global-event-buttons").addClass("hidden");
		},
		error: function(xhr, status, error) {
		  var err = eval("(" + xhr.responseText + ")");
		  alert(err.Message);
		}
	});
	}

//abort the edit of a global event
function edit_global_event_abort(){
	$("#event-details").html('');
	$("#event-details").value(edit_global_event_old.details);
	$("#event-feedback").html('');
	$("#event-feedback").value(edit_global_event_old.feedback);
	$("#event-lang").html('');
	$("#event-lang").value(edit_global_event_old.language);
	$("#edit-global-event-buttons").addClass("hidden");
	}
	
//add global event panel - populate list of years
$("#add_global_event_alert").on("show.bs.modal",function(){
	var current_year=new Date().getFullYear(), current_month = new Date().getMonth();

	if(current_month > 0 && current_month <= 8)
		current_year--;
	$("#global_event_add_confirm").prop("disabled",true);
	$('#years_list').html("");
	$("#selected_year").html('Année <span class="caret"></span>');
	$("#cours_to_add").html('Sélectionnez cours <span class="caret"></span>');
	$("#global_course_list").html("");
	$("#new_global_cours_details").val("");
	$("#new_global_cours_feedback").val("");
	for(var i=0;i<3;i++){
    	$('#years_list').append($('<li role="presentation"><a role="menuitem" tabindex="-1" href="#">'+(current_year+i)+'</a></li>'));
	}
	})

//update global courses list of add new cours modal on selection of the year	
$("#years_list").on("click","a",function(event){
	var year=event.currentTarget.innerHTML;
	$("#selected_year").html(year+' <span class="caret"></span>');
	$.ajax({
		dataType : "json",
		type : 'POST',
		//url : "json/global-events-list.json",
		url : "index.php?src=ajax&req=036",
		data: {"year":year},
		success : function(data, status) {
			$("#global_course_list").html("");
			$("#cours_to_add").html('Sélectionner cours <span class="caret"></span>');
			$("#cours_language").html('Sélectionner langue <span class="caret"></span>');
			$("#new_global_cours_details").html("");
			$("#new_global_cours_feedback").html("");
			var courses=data.courses;
			for(var i=0;i<courses.length;i++){
				//var shortText=courses[i].nameShort;
				//shortText = jQuery.trim(shortText).substring(0, 40) + "...";
				$("#global_course_list").append($('<li role="presentation"><a cours-id='+courses[i].id_ulg+' role="menuitem" tabindex="-1" href="#">'+courses[i].id_ulg+"\t"+courses[i].nameShort+'</a></li>'));
			}
		},
		error: function(xhr, status, error) {
		  var err = eval("(" + xhr.responseText + ")");
		  alert(err.Message);
		}
	});
	})
	
//update the selected cours to be added in the add new cours modal
$("#global_course_list").on("click","a",function(event){
	var course=event.currentTarget.innerText;
	$("#cours_to_add").html(course+' <span class="caret"></span>');
	$("#cours_to_add").attr("cours-id",event.currentTarget.getAttribute("cours-id"));
	})
	
//update the selected cours language
$("#languages_list").on("click","a",function(event){
	var language=event.currentTarget.innerHTML;
	var language_code=event.currentTarget.getAttribute("language");
	$("#cours_language").html(language+' <span class="caret"></span>');
	$("#cours_language").attr("language",language_code);
	})
	
//update the selected cours language for edit global event
$("#event_info").on("click","#edit_languages_list a",function(event){
	var language=event.currentTarget.innerHTML;
	var language_code=event.currentTarget.getAttribute("language");
	$("#edit_cours_language").html(language+' <span class="caret"></span>');
	$("#edit_cours_language").attr("language",language_code);
	})
	
$("#global_event_add_confirm").click(function(event){
	var cours_id=$("#cours_to_add").attr("cours-id");
	if($("#cours_language").text()!="Sélectionner language")
		var language=$("#cours_language").attr("language")
	var feedback=$("#new_global_cours_feedback").val();
	var description=$("#new_global_cours_details").val();
	var new_course={"ulgId":cours_id, "description":description, "feedback":feedback, "language":language};
	//send cours to add to the server
	$.ajax({
		dataType : "json",
		type : 'POST',
		url : "index.php?src=ajax&req=035",
		data: new_course,
		success : function(data, status) {
			var cours_to_add={"id":data.id,"code":cours_id, "lib_cours_complet":$("#cours_to_add").text()}
			addGlobalEvent(cours_to_add);
			},
		error: function(xhr, status, error) {
		  var err = eval("(" + xhr.responseText + ")");
		  alert(err.Message);
		}
	});
	})	

//enable add global event button when course, language and year are selected
$("#add_global_event_alert").on("click","#global_course_list li,#languages_list li",function() {
	if($("#cours_language").text()!="Sélectionner langue "&&$("#cours_to_add").text()!="Sélectionnez cours ")
		$("#global_event_add_confirm").prop("disabled",false);
	else $("#global_event_add_confirm").prop("disabled",true);
});

//convert language from two letters code to full string
function convert_language(ln){
	switch(ln){
		case "FR":
		return "Français";
		case "EN":
		return "Anglais";
		}
	}

//convert language from full string to two letters code
function revert_convert_language(ln){
	switch(ln){
		case "Français":
		return "FR";
		case "Anglais":
		return "EN";
		}
	}
	
//add team member to global event
$("#add-event-member").click(function(event){
	var event_id=event.currentTarget.getAttribute("event-id");
	//retrieve list of team members that can be added
	$.ajax({
		dataType : "json",
		type : 'POST',
		url :"json/team-members.json",
		//url : "index.php?src=ajax&req=",
		data: {id_global_event:event_id},
		success : function(data, status) {
			$("#add-event-member-conf-abort-buttons").removeClass("hidden");
			$("#add-event-member").parent().addClass("hidden");
			$("#event_team").append('<div class="dropdown" style="margin-left: 10px;margin-bottom: 10px;"><button class="btn btn-default dropdown-toggle" type="button" id="add_team_member_dropdown" data-toggle="dropdown" aria-expanded="true" >Sélectionner un membre de l\'équipe <span class="caret"></span> </button><ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" id="new_team_members_list"></ul></div><div class="dropdown" style="margin-left: 10px;margin-bottom: 10px;"><button class="btn btn-default dropdown-toggle" type="button" id="add_team_member_role_dropdown" data-toggle="dropdown" aria-expanded="true" >Sélectionner un role <span class="caret"></span> </button><ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" id="new_team_members_role_list"></ul></div>');
			for(var i=0;i<data.team.length;i++)
				$("#new_team_members_list").append('<li role="presentation"><a role="menuitem" tabindex="-1" href="#" member-id="'+data.team[i].user_id+'">'+data.team[i].name+"\t"+data.team[i].surname+'</a></li>');
			},
		error: function(xhr, status, error) {
		  var err = eval("(" + xhr.responseText + ")");
		  alert(err.Message);
		}
	});
	//retrieve list of team roles
	$.ajax({
		dataType : "json",
		type : 'POST',
		//url :"json/team-members-roles.json",
		url : "index.php?src=ajax&req=074",
		data: {lang:"FR"},
		success : function(data, status) {
			//{roles:[{id, role}]}
			for(var i=0;i<data.roles.length;i++)
				$("#new_team_members_role_list").append('<li role="presentation"><a role="menuitem" tabindex="-1" href="#" member-role-id="'+data.roles[i].id+'">'+data.roles[i].role+'</a></li>');
			},
		error: function(xhr, status, error) {
		  var err = eval("(" + xhr.responseText + ")");
		  alert(err.Message);
		}
	});
	})

//update the team members dropdown with the selected value
$("#event_team").on("click","#new_team_members_list a",function(event){
	var team_member=event.currentTarget.innerText;
	$("#add_team_member_dropdown").html(team_member+' <span class="caret"></span>');
	$("#add_team_member_dropdown").attr("member-id",event.currentTarget.getAttribute("member-id"));
	$("#add-event-member-abort").attr("member-id",event.currentTarget.getAttribute("member-id"));
	$("#add-event-member-confirm").attr("member-id",event.currentTarget.getAttribute("member-id"));
	//enable add team member button when both team member and role have been selected
	if($("#add_team_member_dropdown").attr("member-id")&&$("#add_team_member_role_dropdown").attr("member-role-id"))
		$("#add-event-member-confirm").removeAttr("disabled");
	})
	
//update the team member role dropdown with the selected value
$("#event_team").on("click","#new_team_members_role_list a",function(event){
	var role=event.currentTarget.innerText;
	$("#add_team_member_role_dropdown").html(role+' <span class="caret"></span>');
	$("#add_team_member_role_dropdown").attr("member-role-id",event.currentTarget.getAttribute("member-role-id"));
	$("#add-event-member-abort").attr("member-role-id",event.currentTarget.getAttribute("member-role-id"));
	$("#add-event-member-confirm").attr("member-role-id",event.currentTarget.getAttribute("member-role-id"));
	//enable add team member button when both team member and role have been selected
	if($("#add_team_member_dropdown").attr("member-id")&&$("#add_team_member_role_dropdown").attr("member-role-id"))
		$("#add-event-member-confirm").removeAttr("disabled");
	})
	

	
//abort add team member
$("#add-event-member-abort").click(function(event){
	$("#add_team_member_dropdown").remove();
	$("#add_team_member_role_dropdown").remove();
	$("#add-event-member-conf-abort-buttons").addClass("hidden");
	$("#add-event-member").parent().removeClass("hidden");
	})

//confirm add team member
$("#add-event-member-confirm").click(function(event){
	var event_id=event.currentTarget.getAttribute("event-id");
	var member_id=event.currentTarget.getAttribute("member-id");
	var member_fullname=$("#add_team_member_dropdown").text();
	var member_role=$("#add_team_member_role_dropdown").text()
	$("#add-event-member-conf-abort-buttons").addClass("hidden");
	$("#add-event-member").parent().removeClass("hidden");
	$("#add_team_member_dropdown").remove();
	$("#add_team_member_role_dropdown").remove();
	$.ajax({
		dataType : "json",
		type : 'POST',
		url : "index.php?src=ajax&req=072",
		data: {id_user:member_id, id_global_event:event_id, id_role:"2"},
		success : function(data, status) {			
			$('#team_table tr:last').after('<tr><td>'+member_fullname+'</td><td>'+member_role+'</td><td><div class="text-center"><a class="delete" member-id="'+member_id+'"></a></div></td></tr>');
			},
		error: function(xhr, status, error) {
		  var err = eval("(" + xhr.responseText + ")");
		  alert(err.Message);
		}
	});
	})

//remove team member
$("#event_team").on("click",".delete",function(event){
	var event_id=$("#add-event-member").attr("event-id");
	var member_id=event.currentTarget.getAttribute("member-id");
	$.ajax({
		dataType : "json",
		type : 'POST',
		url : "index.php?src=ajax&req=073",
		data: {id_user:member_id, id_global_event:event_id},
		success : function(data, status) {			
			event.currentTarget.parentNode.parentNode.parentNode.remove();
			},
		error: function(xhr, status, error) {
		  var err = eval("(" + xhr.responseText + ")");
		  alert(err.Message);
		}
	});
	})

$("#add-subevent").click(function(){
	var global_event_id=this.getAttribute("event-id");
	$("#new_subevent_creation_confirm").attr("global_event_id",global_event_id);
	})