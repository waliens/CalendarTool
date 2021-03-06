// JavaScript Document
var today = new Date();
//update the navbar
$("#navbar li").removeClass("active");
$("#profile_nav").addClass("active");
var subevent;
//var holding values to abort global event modification
var edit_global_event_old;
//dates picker
var datepicker={"new_subevent_dates":0,"edit_subevent_dates":0,"new_indepevent_dates":0,"edit_academic_event":0,"new_subevent_recurrence_end":0,"new_indepevent_recurrence_end":0}

$(document).ready(function() {
	//populate user profile  info and courses, both optional and mandatory
	$.ajax({
		dataType : "json",
		type : 'GET',
		//url : "json/professor-profile.json",
		url: "index.php?src=ajax&req=022",
		success : function(data, status) {
			/** error checking */
			if(data.error.error_code > 0)
			{	
				launch_error_ajax(data.error);
				return;
			}	

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
			launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
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
	event_name.setAttribute("data-target","#academic_event_info_modal");
	event_name.setAttribute("id",indep_event.id);
	event_name.setAttribute("event-name",indep_event.name);
	event_name.innerHTML = indep_event.name;
	event_name.onclick=function(e){subevent=e.target};
	var delete_icon=document.createElement('a');
	var edit_icon=document.createElement('a');
	edit_icon.className="edit";
	edit_icon.setAttribute("data-toggle","modal");
	edit_icon.setAttribute("data-target","#edit_academic_event");
	edit_icon.setAttribute("event-id",indep_event.id);
	delete_icon.className="delete";
	//link the delete icon to the delete alert
	delete_icon.setAttribute("data-toggle","modal");
	delete_icon.setAttribute("data-target","#delete_academic_event_alert");
	//delete_icon.setAttribute("course-code",indep_event.code);
	delete_icon.setAttribute("event-id",indep_event.id);
	delete_icon.setAttribute("event-name",indep_event.name);
	delete_icon.setAttribute("recurrence-type",indep_event.recurrence_type);
	var div_container1=document.createElement("div");
	div_container1.className="text-center";
	div_container1.appendChild(edit_icon);
	div_container1.appendChild(delete_icon);
	var row=all_indep_events.insertRow(1);
	var cell1=row.insertCell(0);
	var cell2=row.insertCell(1);
	var cell3=row.insertCell(2);
	var cell4=row.insertCell(3);
	var when=document.createElement("p");
	when.setAttribute("event-id",indep_event.id);
	when.id="start_time_independent_event";
	when.innerHTML=indep_event.start.replace("T"," ");
	var recurrence=document.createElement("p");
	recurrence.innerHTML=get_recursion(indep_event.recurrence_type);
	cell1.appendChild(event_name);
	cell2.appendChild(when);
	cell3.appendChild(recurrence);
	cell4.appendChild(div_container1);
	}
	
//populate delete global event alert
$('#delete_global_event_alert').on('show.bs.modal', function (event) {
	var event = $(event.relatedTarget);
	$("span[name=global_course_deleted]").html(event.attr("course-name"));
	$("#global_event_delete_confirm").attr("event-id",event.attr("course-id"));
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
				/** error checking */
				if(data.error.error_code > 0)
				{	
					launch_error_ajax(data.error);
					return;
				}	

				$("a[course-id='"+event.currentTarget.getAttribute("event-id")+"']").parent().parent().parent().remove();
			},
			error : function(xhr, status, error) {
				launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
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
				/** error checking */
				if(data.error.error_code > 0)
				{	
					launch_error_ajax(data.error);
					return;
				}	

				$("#independent-events #"+event_id).parent().parent().remove()
			},
			error : function(xhr, status, error) {
				launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
			}
		});
	
	})

//populate event info when modal appears
$("#event_info").on("show.bs.modal",function(event){
	$("#edit_global_event .edit").removeClass("edit-disabled");
	var event_id=event.relatedTarget.getAttribute('event-id');
	$.ajax({
		dataType : "json",
		type : 'GET',
		url : "index.php?src=ajax&req=032&event="+event_id,
		success : function(data, status) {
			/** error checking */
			if(data.error.error_code > 0)
			{	
				launch_error_ajax(data.error);
				return;
			}	

			var global_event_id=data.id;
			var owner_id=data.owner_id;
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
			$("#add_member_conf_abort_buttons").addClass("hidden");
			$("#add_member").attr("event-id",global_event_id);
			$("#add-subevent").attr("event-id",global_event_id);
			$("#add_member_abort").attr("event-id",global_event_id);
			$("#add_member_confirm").attr("event-id",global_event_id);
			$("#event-title").html(global_event_id_ulg+"\t"+global_event_name_short);
			$("#event-details").html(global_event_description);
			$("#event-feedback").html(global_event_feedback);
			$("#event-lang").html(global_event_lang);
			$("#event-period").html(global_event_period);
			$("#event-work").html("");
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
			//add the table headers if there's at least a subevent
			var row=subevents_table.insertRow(0);
			var cell1=row.insertCell(0);
			if(subevents.length>0){
				var cell2=row.insertCell(1);
				var cell3=row.insertCell(2);
				var cell4=row.insertCell(3);
				var titleHeader=document.createElement('p');
				titleHeader.className="text-bold";
				var whenHeader=document.createElement('p');
				whenHeader.className="text-bold";
				var recurrenceHeader=document.createElement('p');
				recurrenceHeader.className="text-bold";
				titleHeader.innerHTML="Titre"
				cell1.appendChild(titleHeader);
				whenHeader.innerHTML="Quand";
				cell2.appendChild(whenHeader);
				recurrenceHeader.innerHTML="Récurrence";
				cell3.appendChild(recurrenceHeader);
			}
			var team_table=document.createElement("table");
			team_table.className="table";
			team_table.id="team_table";
			$("#event_team").html(team_table);	
			for(var i=0;i<team.length;i++)
				populateTeamMember(team[i]);
			$("#team_table .delete[member-id="+owner_id+"]").addClass("delete-disabled");
			//hide confirm/abort edit buttons
			$("#edit-global-event-buttons").addClass("hidden");
		},
		error: function(xhr, status, error) {
			launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
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
	var cell1=document.createElement("td");
	var cell2=document.createElement("td");
	var cell3=document.createElement("td");
	var a=document.createElement("a");
	a.setAttribute("data-dismiss","modal");
	a.setAttribute("data-target","#academic_event_info_modal");
	a.setAttribute("data-toggle","modal");
	a.innerHTML=item.name;
	a.id=item.id;
	a.onclick=function(e){showSubeventModal=true; subevent=e.target};
	cell1.appendChild(a);
	row.appendChild(cell1);
	cell2.innerHTML=item.start.replace("T"," ");
	cell2.setAttribute("event-id",item.id);
	cell2.id="start_time_subevent";
	row.appendChild(cell2);
	cell3.innerHTML=get_recursion(item.recurrence_type);
	row.appendChild(cell3);
	var cell4=document.createElement("td");
	cell4.innerHTML='<div class="text-center"><a class="edit" event-id="'+item.id+'"></a><a class="delete" data-toggle="modal" data-dismiss="modal" data-target="#delete_academic_event_alert" global-event-id="'+$("#add-subevent").attr("event-id")+'" event-id="'+item.id+'" recurrence-type='+item.recurrence_type+' event-name="'+item.name+'"></a></div>';
	row.appendChild(cell4);
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
	var cell3=document.createElement("td");
	cell3.innerHTML='<div class="text-center"><a class="delete" member-id="'+member.user+'"></a></div>';
	row.appendChild(cell3);
	table.append(row);
	}

//populate add new indep event modal
$("#new_indepevent").on("show.bs.modal",function(){
	//clean data from the new indep event modal on show	
	$("#new_indepevent_title").val("");
	$("#new_indepevent_recurrence").html("Jamais");
	$("#new_indepevent_recurrence").attr("recurrence-id",6);
	$("#new_indepevent_recurrence_end_td").addClass("hidden");
	$("#new_indepevent_type").html("Cours théorique");
	$("#new_indepevent_type").attr("category-id",1);
	$("#new_indepevent_workload").val(30);
	$("#new_indepevent_place").val("");
	$("#new_indepevent_details").val("");
	$("#new_indepevent_feedback_body").val("");
	$("#new_indepevent_pract_details_body").val("");
	$("#new_indepevent_pathways_table").html("");
	$("#new_indepevent_team_table").html("");
	$("#new_indepevent_team_members_list").html("");
	$("#new_indepevent_team_members_role_list").html("");
	//populate categories dropdown
	populate_event_categories_dropdown("new_indepevent_categories","#new_indepevent_type",true);
	buildDatePicker("new_indepevent");
	//setup timepickers of new subevent modal
	setUpTimePickers("#new_indepevent","#new_indepevent_btns");
	//populate time pickers
	var currentTime=new Date();
	currentTime=moment(currentTime);
	var	startHour=currentTime.hours();
	var	endHour=currentTime.add(1,"hour").hours();
	var	minutes="00";
	$("#new_indepevent_startHour").val(startHour+":"+minutes);
	$("#new_indepevent_endHour").val(endHour+":"+minutes);
	setTimePickersValidInterval("#new_indepevent");
	//retrieve list of team members, roles and pathways
	$.ajax({
		dataType : "json",
		type : 'GET',
		url : "index.php?src=ajax&req=087",
		success : function(data, status) {
			/** error checking */
			if(data.error.error_code > 0)
			{	
				launch_error_ajax(data.error);
				return;
			}	
			
			//{pathways:[{id, name}], users:[{id, name, surname}, roles:{id, role}]}
			var team_members=data.users;
			var roles=data.roles;
			var pathways=data.pathways;
			
			//populate team members dropdown
			$("#new_indepevent_team_table").html("");			
			for(var i=0;i<team_members.length;i++)
				$("#new_indepevent_team_members_list").append('<li role="presentation"><a role="menuitem" tabindex="-1" href="#" member-id="'+team_members[i].id+'">'+team_members[i].name+"\t"+team_members[i].surname+'</a></li>');
			
			for(var i=0;i<roles.length;i++)
				$("#new_indepevent_team_members_role_list").append('<li role="presentation"><a role="menuitem" tabindex="-1" href="#" member-role-id="'+roles[i].id+'">'+roles[i].role+'</a></li>');
			
			//populate pathways dropdown
			$("#new_indepevent_pathways_list").html("");
			for(var i=0;i<pathways.length;i++)
				$("#new_indepevent_pathways_list").append('<li role="presentation"><a role="menuitem" tabindex="-1" href="#" pathway-id="'+pathways[i].id+'">'+pathways[i].name+'</a></li>');
		}
		,
		error : function(xhr, status, error) {
		  var err = eval("(" + xhr.responseText + ")");
		  alert(err.Message);
		}
	});
		
})
	
//add pathway to indep event
$("#new_indepevents_pathways").on("click","#new_indepevent_pathways_list a",function(event){
	var pathway_id=event.currentTarget.getAttribute("pathway-id");
	$("#new_indepevent_pathways_table").append("<tr><td pathway-id="+pathway_id+">"+event.currentTarget.innerHTML+"</td><td><div class='text-center'><a class='delete' pathway-id="+pathway_id+"></a></div></td></tr>");
	//hide the selected pathway from the dropdown
	$("#new_indepevent_pathways_list a[pathway-id='"+pathway_id+"']").parent().hide();
	})	
	
//delete pathway from indep event
$("#new_indepevents_pathways").on("click","#new_indepevent_pathways_table .delete",function(event){
	var pathway_id=event.currentTarget.getAttribute("pathway-id");
	$("#new_indepevent_pathways_table td[pathway-id='"+pathway_id+"']").parent().remove();
	//add the deleted pathway to the list of the available to add back in
	$("#new_indepevent_pathways_list a[pathway-id='"+pathway_id+"']").parent().show();
	});
	
//add team member to indep event
$("#new_indepevent_team_members_role_list").on("click","a",function(event){
	var member_id=$("#add_indepevent_team_member_dropdown").attr("member-id");
	var role_id=event.currentTarget.getAttribute("member-role-id");
	//if, when selecting a team member role, we've also already selected a team member, we add it to the list
	if(member_id!=""){
		var team_member=$("#add_indepevent_team_member_dropdown").text();
		var team_role=event.currentTarget.innerText;
		$("#new_indepevent_team_table").append("<tr><td member-id="+member_id+">"+team_member+"</td><td member-role-id="+role_id+">"+team_role+"</td><td><div class='text-center'><a class='delete' member-id="+member_id+"></a></div></td></td></tr>");
		$("#new_indepevent_team_members_list a[member-id='"+member_id+"']").parent().hide();
		
		//reset the ids of member and role
		$("#add_indepevent_team_member_role_dropdown").attr("role_id","");
		$("#add_indepevent_team_member_dropdown").attr("member-id","");
		$("#add_indepevent_team_member_role_dropdown").html("Sélectionner un role <span class='caret'></span> ");
		$("#add_indepevent_team_member_dropdown").html("Ajouter un membre de l'équipe <span class='caret'></span> ");
		
		}
	else update_team_member_role_dropdown("new_indepevent_team",event);
	})
	
$("#new_indepevent_team_members_list").on("click","a",function(event){
	var role_id=$("#add_indepevent_team_member_role_dropdown").attr("member-role-id");
	var member_id=event.currentTarget.getAttribute("member-id");
	if(role_id!=""){
		var team_member=event.currentTarget.innerText;
		var team_role=$("#add_indepevent_team_member_role_dropdown").text();
		$("#new_indepevent_team_table").append("<tr><td>"+team_member+"</td><td>"+team_role+"</td><td><div class='text-center'><a class='delete' member-id="+member_id+"></a></div></td></td></tr>");
		$("#new_indepevent_team_members_list a[member-id='"+member_id+"']").parent().hide();
		//reset the ids of member and role
		$("#add_indepevent_team_member_role_dropdown").attr("role_id","");
		$("#add_indepevent_team_member_dropdown").attr("member-id","");
		$("#add_indepevent_team_member_role_dropdown").html("Sélectionner un role <span class='caret'></span> ");
		$("#add_indepevent_team_member_dropdown").html("Ajouter un membre de l'équipe <span class='caret'></span> ");
		}
	else update_team_member_dropdown("new_indepevent_team",event);
	})


//delete indepevent team member
$("#new_indepevent_team_table").on("click",".delete",function(event){
	var member_id=event.currentTarget.getAttribute("member-id");
	$("#new_indepevent_team_table .delete[member-id="+member_id+"]").parent().parent().parent().remove();
	$("#new_indepevent_team_members_list a[member-id='"+member_id+"']").parent().show();
	})


//displays info of subevents and independent events
$("#academic_event_info_modal").on("show.bs.modal",function(){
	//get subevent info
	var subevent_id=subevent.id;
	var req="051";//subevent by default
	
	if($("#independent-events #"+subevent_id+"").length>0)
		req="084";
	$.ajax({
		dataType : "json",
		type : 'GET',
		url : "index.php?src=ajax&req="+req+"&event="+subevent_id,
		async: true,
		success : function(data, status) {
			/** error checking */
			if(data.error.error_code > 0)
			{	
				launch_error_ajax(data.error);
				return;
			}	
			//{name, details, pract_details, where, limit, start, end, type, recursiveID, pathways[{}], teachingTeam: [{id, role}], attachments:[{id, url,name}], softAdd}
			var academic_event_id=data.id;
			var academic_event_title=data.name;
			var academic_event_description=data.description;
			var academic_event_place=data.place;
			if(academic_event_place==null)
				$("#academic_event_place").parent().hide();
			else $("#academic_event_place").parent().show();
			var academic_event_type=data.type;
			var academic_event_start=moment(data.startDay);
			if(data.startTime!=""){
				var chunks=data.startTime.split(":");
				academic_event_start.set("hour",chunks[0]);
				academic_event_start.set("minute",chunks[1]);
				$("#academic_event_start").html(academic_event_start.format("dddd DD MMMM YYYY, h:mm a"));
			}
			else $("#academic_event_start").html(academic_event_start.format(fullcalendarDateFormat));
			var academic_event_end;
			if(data.endDay!=""){
				$("#academic_event_end").parent().removeClass("hidden");
				academic_event_end=moment(data.endDay);
				if(data.endTime!=""){
					var chunks=data.endTime.split(":");
					academic_event_end.set("hour",chunks[0]);
					academic_event_end.set("minute",chunks[1]);
					$("#academic_event_end").html(academic_event_end.format("dddd DD MMMM YYYY, h:mm a"));
				}
				else $("#academic_event_end").html(academic_event_end.format(fullcalendarDateFormat));
			}
			else {
				$("#academic_event_end").parent().addClass("hidden");
			}
			var deadline=data.deadline;
			if(deadline=="false")
				$("#academic_event_deadline").hide();
			else {
				$("#academic_event_deadline").show();
				$("#academic_event_deadline input").prop("checked",true);
			}
			var category_id=data.category_id;
			var category_name=data.category_name;
			var recurrence=get_recursion(data.recurrence);
			//var academic_event_pract_details=data.pract_details;
			$("#academic_event_recurrence").html(recurrence);

			//recurrence=1 means the event is not recursive, otherwise is the instance of a recursion
			if(recurrence=="Jamais")
				$("#academic_event_recurrence_end").parent().addClass("hidden");
			else{
				$("#academic_event_recurrence_end").parent().removeClass("hidden");
				var end_recurrence=moment(data.end_recurrence);
				$("#academic_event_recurrence_end").html(end_recurrence.format(fullcalendarDateFormat));
				}
			var pract_details=data.pract_details;
			var feedback=data.feedback;
			var workload=data.workload;
			var favourite=data.favourite;
			var team=data.team;
			var pathways=data.pathways;
			//populate alert with global event data
			$("#academic_event_title").html(academic_event_title);
			$("#academic_event_details").html(academic_event_description);
			$("#academic_event_category").html(category_name);
			$("#academic_event_place").html(academic_event_place);
			$("#academic_event_pract_details_body").html(pract_details);
			$("#academic_event_feedback_body").html(feedback);
			$("#academic_event_workload").html(workload);
			$("#academic_event_team_table").html("");
			for(var i=0;i<team.length;i++)
				$("#academic_event_team_table").append("<p team-id="+team[i].id+">"+team[i].surname+" "+team[i].name+"\t - <span role-id="+team[i].role_id+">"+team[i].role+"</span></p>")
			$("#academic_event_pathways_table").html("");
			for(var i=0;i<pathways.length;i++)
				$("#academic_event_pathways_table").append("<p pathway-id="+pathways[i].id+">"+pathways[i].name+"</p>")
			
		},
		error: function(xhr, status, error) {
			launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
		}
	});
})

$("#subevents_info_accordion").on("click",".edit",function(){
	$("#event_info").modal("hide");
	$("#edit_academic_event").modal("show");
	})

//edit academic event
$("#edit_academic_event").on("show.bs.modal",function(){
	//get subevent info
	var event_id=event.target.getAttribute("event-id");
	var req="051";//subevent by default
	if($("#independent-events #"+event_id+"").length>0)
		req="084";
	$("#edit_academic_event_btns").attr("event-id",event_id);
	$.ajax({
		dataType : "json",
		type : 'GET',
		url : "index.php?src=ajax&req="+req+"&event="+event_id,
		async: true,
		success : function(data, status) {
			/** error checking */
			if(data.error.error_code > 0)
			{	
				launch_error_ajax(data.error);
				return;
			}	
			//{name, details, pract_details, where, limit, start, end, type, recursiveID, pathways[{}], teachingTeam: [{id, role}], attachments:[{id, url,name}], softAdd}
			var academic_event_id=data.id;
			var academic_event_title=data.name;
			$("#edit_academic_modal_header").html(academic_event_title);
			var academic_event_description=data.description;
			var academic_event_place=data.place;
			var academic_event_type=data.type;
			var academic_event_start=moment(data.startDay);
			//build datepicker
			buildDatePicker("edit_academic_event");
			$("#edit_academic_event_startDate_datepicker").val(academic_event_start.format(fullcalendarDateFormat));
			if(data.startTime!=""){
				var chunks=data.startTime.split(":");
				$("#edit_academic_event_startHour").val(chunks[0]+":"+chunks[1]);
			}
			var academic_event_end;
			if(data.endDay!=""){
				academic_event_end=moment(data.endDay);
				$("#edit_academic_event_endDate_datepicker").val(academic_event_end.format(fullcalendarDateFormat))
				if(data.endTime!=""){
					var chunks=data.endTime.split(":");
					$("#edit_academic_event_endHour").val(chunks[0]+":"+chunks[1]);
				}
			}
			//setup timepickers of new subevent modal
			setUpTimePickers("#edit_academic_event","#edit_academic_event_btns");
			setTimePickersValidInterval("#edit_academic_event");
			var deadline=data.deadline;
			if(deadline=="true"){
				$("#edit_academic_event_deadline input").prop("checked",true);
				$("#edit_academic_event_endDate").parent().addClass("hidden");
				}
			else {
				$("#edit_academic_event_deadline input").prop("checked",false);
				$("#edit_academic_event_endDate").parent().removeClass("hidden");	
			}
			var category_id=data.category_id;
			var category_name=data.category_name;
			var recurrence=get_recursion(data.recurrence);
			var academic_event_pract_details=data.pract_details;
			var feedback=data.feedback;
			$("#edit_academic_event_recurrence").val(recurrence);

			//recurrence=1 means the event is not recursive, otherwise is the instance of a recursion
			if(recurrence=="Jamais"){
				$("#edit_academic_event_recurrence_end").parent().hide()
				$("#edit_academic_event_recurrence").html(recurrence);
				$("#edit_academic_event_recurrence").attr("recurrence-id",data.recurrence);
				$('#edit_academic_event_creation_confirm_recursion').addClass('hidden');
				$('#edit_academic_event_creation_confirm_norecursion').removeClass('hidden');
				}
			else{
				$('#edit_academic_event_creation_confirm_recursion').removeClass('hidden');
				$('#edit_academic_event_creation_confirm_norecursion').addClass('hidden');
				$("#edit_academic_event_recurrence_end").parent().show();
				$("#edit_academic_event_recurrence").html(recurrence);
				$("#edit_academic_event_recurrence").attr("recurrence-id",data.recurrence);
				var end_recurrence=moment(data.end_recurrence);
				$("#edit_academic_event_recurrence_end").html(end_recurrence.format(fullcalendarDateFormat));
				//add popup to confirm button
				$("#edit_academic_event_creation_confirm_recursion").popover({
					template: '<div class="popover" role="tooltip"><div class="arrow" style="top: 50%;"></div><h3 class="popover-title">Mis à jour événement récurrent</h3><div class="popover-content">Cet événement est récurrent.</div><div class="modal-footer text-center"><div style="margin-bottom:5px;"><button type="button" class="btn btn-primary" onclick="edit_academic_event(false)">Seulement cet événement</button></div><div style="margin-bottom:5px;"><button type="button" class="btn btn-default" onclick="edit_academic_event(true)">Tous les événements</button></div><div><button type="button" class="btn btn-default">Annuler</button></div></div></div>',
					});	
				}
			var favourite=data.favourite;
			var team=data.team;
			var pathways=data.pathways;
			//populate alert with global event data
			$("#edit_academic_event_title").val(academic_event_title);
			$("#edit_academic_event_details").val(academic_event_description);
			$("#edit_academic_event_category").val(category_name);
			$("#edit_academic_event_place").val(academic_event_place);
			$("#edit_academic_event_pract_details_body").val(academic_event_pract_details);
			$("#edit_academic_event_feedback_body").val(feedback);
			//populate event categories
			populate_event_categories_dropdown("edit_academic_event_categories","#edit_academic_event_type",true);
			$("#edit_academic_event_team_table").html("");
			for(var i=0;i<team.length;i++)
				$("#edit_academic_event_team_table").append("<tr><td team-id="+team[i].id+">"+team[i].surname+" "+team[i].name+"\t - <span role-id="+team[i].id_role+">"+team[i].role+"</span></td><td><input type='checkbox' team-id="+team[i].id+" checked></td></tr>")
			//disable owner checkbox
			$("#edit_academic_event_team_table input[team-id="+data.owner_id+"]").prop("disabled",true);
			$("#edit_academic_event_pathways_table").html("");
			for(var i=0;i<pathways.length;i++)
				$("#edit_academic_event_pathways_table").append("<tr><td pathway-id="+pathways[i].id+">"+pathways[i].name+"</td><td><input type='checkbox' pathway-id="+pathways[i].id+" checked></td></tr>")
			
		},
		error: function(xhr, status, error) {
			launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
		}
	});
})

//confirm academic event edit
function edit_academic_event(applyRecursive){
	var event_id=$("#edit_academic_event_btns").attr("event-id");
	var req="054";//subevent by default
	if($("#independent-events #"+event_id+"").length>0)
		req="085";
	var name=$("#edit_academic_event_title").val();
	var details=$("#edit_academic_event_details").val();
	var where=$("#edit_academic_event_place").val();
	var startHour=$("#edit_academic_event_startHour").val();
	var endHour=$("#edit_academic_event_endHour").val();
	var entireDay=false;
	if(startHour==""&&endHour=="")
		entireDay=true;
	var start=moment(convert_date($("#edit_academic_event_startDate_datepicker").val(),"YYYY-MM-DD")).format("YYYY-MM-DD");
	var end;
	if(startHour!="")
		start=start+"T"+startHour+":00";
	if($("#edit_academic_event_endDate_datepicker").val()!="")
		end=moment(convert_date($("#edit_academic_event_endDate_datepicker").val(),"YYYY-MM-DD")).format("YYYY-MM-DD");
	if(endHour!="")
		end=end+"T"+endHour+":00";
	var deadline=$("#edit_academic_event_deadline input").prop("checked");
	if(deadline)
		end="";
	var category=$("#edit_academic_event_type").attr("category-id");
	var pract_details=$("#edit_academic_event_pract_details_body").val();
	var feedback=$("#edit_academic_event_feedback_body").val();
	var workload=$("#edit_academic_event_workload").val();
	var pathways=$("#edit_academic_event_pathways_table input");
	var pathways_json=[];
	var team=$("#edit_academic_event_team_table input");
	var team_json=[];
	for(var i=0;i<pathways.length;i++){
		if(pathways.get(i).checked){
			if(req=="054")
				pathways_json.push({id:pathways.get(i).getAttribute("pathway-id"),selected:true});
			else pathways_json.push(pathways.get(i).getAttribute("pathway-id"));
		}
		else if (req=="054") pathways_json.push({id:pathways.get(i).getAttribute("pathway-id"),selected:false});
	}
	pathways_json=JSON.stringify(pathways_json);
	for(var i=0;i<team.length;i++){
		if(team.get(i).checked){
			if(req=="054")
				team_json.push({id:team.get(i).getAttribute("team-id"),selected:true});
			else team_json.push({id:team.get(i).getAttribute("team-id"),role:$("#edit_academic_event_team_table td[team-id='"+team.get(i).getAttribute("team-id")+"'] span").attr("role-id")});	
		}
		else if(req=="054") team_json.push({id:team.get(i).getAttribute("team-id"),selected:false});
	}
	team_json=JSON.stringify(team_json);
	//SUBEVENT: {id, name, details, where, entireDay, start, end, deadline, type, pract_details, feedback, workload, pathways:[{id,selected}], teachingTeam:[{id,selected}], applyRecursive}
	//INDEP EVENT: {id, name, details, where, entireDay, start, end, deadline, type, pract_details, feedback, workload, pathways:[], teachingTeam:[], applyRecursive}
	var data={id:event_id,name:name,details:details,where:where,entireDay:entireDay,start:start,end:end,deadline:deadline,type:category,pract_details:pract_details,feedback:feedback,workload:workload, pathways:pathways_json,teachingTeam:team_json,applyRecursive:applyRecursive};
	$.ajax({
		dataType : "json",
		type : 'POST',
		url : "index.php?src=ajax&req="+req,
		data: data,
		success : function(data, status) {
			/** error checking */
			if(data.error.error_code > 0)
			{	
				launch_error_ajax(data.error);
				return;
			}	
			$("#edit_academic_event").modal("hide");
			//update the title in case it has changed
			if(req=="085"){
				$("#independent-events #"+event_id).html(name);
				$("#start_time_independent_event [event-id="+event_id+"]").html(start.replace("T"," "));
			}
			else{
				$("#subevents_table #"+event_id).html(name);
				$("#start_time_subevent [event-id="+event_id+"]").html(start.replace("T"," "));
				}
		},
		error: function(xhr, status, error) {
			launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
		}
	});
}

function edit_global_event(){
	if(!$("#edit_global_event .edit").hasClass("edit-disabled")){
		//disable edit button
		$("#edit_global_event .edit").addClass("edit-disabled");
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
}
	
//confirm global event edit
function  edit_global_event_confirm(){
	//enable edit button
	$("#edit_global_event .edit").removeClass("edit-disabled");
	var event_id=$("#edit-global-event-buttons").attr("event-id");
	var event_details=$("#edit_global_cours_details").val();
	var event_lan=$("#edit_cours_language").attr("language");
	var event_feedback=$("#edit_global_cours_feedback").val();
	$.ajax({
		dataType : "json",
		type : 'POST',
		url : "index.php?src=ajax&req=034",
		data: {id:event_id,description:event_details,feedback:event_feedback,language:event_lan},
		success : function(data, status) {
			/** error checking */
			if(data.error.error_code > 0)
			{	
				launch_error_ajax(data.error);
				return;
			}	

			$("#event-details").html('');
			$("#event-details").html(event_details);
			$("#event-feedback").html('');
			$("#event-feedback").html(event_feedback);
			$("#event-lang").html('');
			$("#event-lang").html(convert_language(event_lan));
			$("#edit-global-event-buttons").addClass("hidden");
		},
		error: function(xhr, status, error) {
			launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
		}
	});
	}

//abort the edit of a global event
function edit_global_event_abort(){
	$("#edit_global_event .edit").removeClass("edit-disabled");
	$("#event-details").html('');
	$("#event-details").html(edit_global_event_old.details);
	$("#event-feedback").html('');
	$("#event-feedback").html(edit_global_event_old.feedback);
	$("#event-lang").html('');
	$("#event-lang").html(edit_global_event_old.language);
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
	$("#cours_to_add").html('Sélectionner cours <span class="caret"></span>');
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
			/** error checking */
			if(data.error.error_code > 0)
			{	
				launch_error_ajax(data.error);
				return;
			}	

			$("#global_course_list").html("");
			$("#cours_to_add").html('Sélectionner cours <span class="caret"></span>');
			$("#cours_language").html('Sélectionner langue <span class="caret"></span>');
			$("#new_global_cours_details").html("");
			$("#new_global_cours_feedback").html("");
			var courses=data.courses;
			$("#global_course_list").html("");
			for(var i=0;i<courses.length;i++){
				//var shortText=courses[i].nameShort;
				//shortText = jQuery.trim(shortText).substring(0, 40) + "...";
				$("#global_course_list").append($('<li role="presentation"><a cours-id='+courses[i].id_ulg+' role="menuitem" tabindex="-1" href="#">'+courses[i].id_ulg+"\t"+courses[i].nameShort+'</a></li>'));
			}
		},
		error: function(xhr, status, error) {
			launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
		}
	});
	})

$("#cours_to_add").click(function(){
	if($("#global_course_list li").length==0)
		$("#global_course_list").append("<li><p style='padding:0 5px;'>Pas de cours disponible pour l'année sélectionnée</p></li>")
	})
	
//update the selected cours to be added in the add new cours modal
$("#global_course_list").on("click","a",function(event){
	var course=event.currentTarget.innerText;
	$("#cours_to_add").html(course+' <span class="caret"></span>');
	$("#cours_to_add").attr("cours-id",event.currentTarget.getAttribute("cours-id"));
	})
	
//update the selected cours language
$("#languages_list").on("click","a",function(event){
	updateCourseLan("#cours_language");
	})
	
//update the selected cours language for edit global event
$("#event_info").on("click","#edit_languages_list a",function(event){
	updateCourseLan("#edit_cours_language");
	})
	
function updateCourseLan(tag){
	var language=event.currentTarget.innerHTML;
	var language_code=event.currentTarget.getAttribute("language");
	$(tag).html(language+' <span class="caret"></span>');
	$(tag).attr("language",language_code);
	}
	
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
			/** error checking */
			if(data.error.error_code > 0)
			{	
				launch_error_ajax(data.error);
				return;
			}	

			var cours_to_add={"id":data.id,"code":cours_id, "lib_cours_complet":$("#cours_to_add").text()}
			addGlobalEvent(cours_to_add);
			},
		error: function(xhr, status, error) {
			launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
		}
	});
	})	

//enable add global event button when course, language and year are selected
$("#add_global_event_alert").on("click","#global_course_list li,#languages_list li",function() {
	if($("#cours_language").text()!="Sélectionner langue "&&$("#cours_to_add").text()!="Sélectionner cours ")
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
$("#add_member").click(function(event){
	var event_id=event.currentTarget.getAttribute("event-id");
	//retrieve list of team members that can be added
	$.ajax({
		dataType : "json",
		type : 'POST',
		url : "index.php?src=ajax&req=075",
		data: {id_global_event:event_id},
		success : function(data, status) {
			/** error checking */
			if(data.error.error_code > 0)
			{	
				launch_error_ajax(data.error);
				return;
			}	

			$("#add_member_conf_abort_buttons").removeClass("hidden");
			$("#add_member").parent().addClass("hidden");
			$("#event_team").append('<div class="dropdown" style="margin-left: 10px;margin-bottom: 10px;"><button class="btn btn-default dropdown-toggle" type="button" id="add_team_member_dropdown" data-toggle="dropdown" aria-expanded="true" >Sélectionner un membre de l\'équipe <span class="caret"></span> </button><ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" id="new_team_members_list"></ul></div><div class="dropdown" style="margin-left: 10px;margin-bottom: 10px;"><button class="btn btn-default dropdown-toggle" type="button" id="add_team_member_role_dropdown" data-toggle="dropdown" aria-expanded="true" >Sélectionner un role <span class="caret"></span> </button><ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1" id="new_team_members_role_list"></ul></div>');
			for(var i=0;i<data.users.length;i++)
				$("#new_team_members_list").append('<li role="presentation"><a role="menuitem" tabindex="-1" href="#" member-id="'+data.users[i].id_user+'">'+data.users[i].name+"\t"+data.users[i].surname+'</a></li>');
			},
		error: function(xhr, status, error) {
			launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
		}
	});
	//retrieve list of team roles
	$.ajax({
		dataType : "json",
		type : 'POST',
		url : "index.php?src=ajax&req=074",
		data: {lang:"FR"},
		success : function(data, status) {
			/** error checking */
			if(data.error.error_code > 0)
			{	
				launch_error_ajax(data.error);
				return;
			}	

			for(var i=0;i<data.roles.length;i++)
				$("#new_team_members_role_list").append('<li role="presentation"><a role="menuitem" tabindex="-1" href="#" member-role-id="'+data.roles[i].id+'">'+data.roles[i].role+'</a></li>');
			},
		error: function(xhr, status, error) {
			launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
		}
	});
	})

//update the team members dropdown with the selected value for subevent and independent event
$("#event_team").on("click","#new_team_members_list a",function(event){update_team_member_dropdown("event_team",event)})

function update_team_member_dropdown(dropdown,event){
	var team_member=event.currentTarget.innerText;
	var text="";
	if(dropdown=="new_indepevent_team")
		text="indepevent_";	
	$("#add_"+text+"team_member_dropdown").html(team_member+' <span class="caret"></span>');
	$("#add_"+text+"team_member_dropdown").attr("member-id",event.currentTarget.getAttribute("member-id"));
	$("#add_"+text+"event_member_abort").attr("member-id",event.currentTarget.getAttribute("member-id"));
	$("#add_"+text+"member_confirm").attr("member-id",event.currentTarget.getAttribute("member-id"));
	//enable add team member button when both team member and role have been selected
	if($("#add_"+text+"team_member_dropdown").attr("member-id")&&$("#add_"+text+"team_member_role_dropdown").attr("member-role-id"))
		$("#add_"+text+"_member_confirm").removeAttr("disabled");
	}
	
//update the team member role dropdown with the selected value
$("#event_team").on("click","#new_team_members_role_list a",function(event){update_team_member_role_dropdown("event_team",event)});	

function update_team_member_role_dropdown(option,event){
	var role=event.currentTarget.innerText;
	var text="";
	if(option=="new_indepevent_team")
		text="indepevent_"
	$("#add_"+text+"team_member_role_dropdown").html(role+' <span class="caret"></span>');
	$("#add_"+text+"team_member_role_dropdown").attr("member-role-id",event.currentTarget.getAttribute("member-role-id"));
	$("#add_"+text+"member_abort").attr("member-role-id",event.currentTarget.getAttribute("member-role-id"));
	$("#add_"+text+"member_confirm").attr("member-role-id",event.currentTarget.getAttribute("member-role-id"));
	//enable add team member button when both team member and role have been selected
	if($("#add_"+text+"team_member_dropdown").attr("member-id")&&$("#add_"+text+"team_member_role_dropdown").attr("member-role-id"))
		$("#add_"+text+"member_confirm").removeAttr("disabled");
	}
	
//abort add team member
$("#add_member_abort").click(function(event){
	$("#add_team_member_dropdown").parent().html("");
	$("#add_team_member_role_dropdown").parent().html("");
	$("#add_member_conf_abort_buttons").addClass("hidden");
	$("#add_member").parent().removeClass("hidden");
	})

//confirm add team member to global event
$("#add_member_confirm").click(function(event){
	add_team_member_confirm("globalevent",event);
	})
//confirm add team member to indep event
$("#add_indepevent_member_confirm").click(function(event){
	add_team_member_confirm("indepevent",event);
	})

function add_team_member_confirm(option,event){
	var reqId;
	var text="";
	var global_event_id="";
	var indep_event_id="";
	var member_id=event.currentTarget.getAttribute("member-id");
	var member_fullname=$("#add_"+text+"team_member_dropdown").text();
	var member_role=$("#add_"+text+"team_member_role_dropdown").attr("member-role-id");
	var member_role_name=$("#add_"+text+"team_member_role_dropdown").text();
	var data;
	$("#add_"+text+"member_conf_abort_buttons").addClass("hidden");
	$("#add_member").parent().removeClass("hidden");
	$("#add_"+text+"team_member_dropdown").parent().html("");
	$("#add_"+text+"team_member_role_dropdown").parent().html("");
	
	if(option=="indepevent"){
		text="indepevent_";
		reqId="088";
		indep_event_id=event.currentTarget.getAttribute("event-id");
		data={id_event:indep_event_id,id_user:member_id, id_role:member_role};
	}
	else {
		reqId="072";
		global_event_id=event.currentTarget.getAttribute("event-id");
		data= {id_user:member_id, id_global_event:global_event_id, id_role:member_role};	
	}
	
	$.ajax({
		dataType : "json",
		type : 'POST',
		url : "index.php?src=ajax&req="+reqId,
		data: data,
		success : function(data, status) {	
			/** error checking */
			if(data.error.error_code > 0)
			{	
				launch_error_ajax(data.error);
				return;
			}

			$("#"+text+"team_table tr:last").after('<tr><td>'+member_fullname+'</td><td>'+member_role_name+'</td><td><div class="text-center"><a class="delete" member-id="'+member_id+'"></a></div></td></tr>');
		},
		error: function(xhr, status, error) {
			launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
		}
	});
	}
//remove team member
$("#event_team").on("click",".delete",function(event){
	if(!$(this).hasClass("delete-disabled")){
		var event_id=$("#add_member").attr("event-id");
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
	}
})

$("#add-subevent").click(function(){
	var global_event_id=this.getAttribute("event-id");
	$("#new_subevent_creation_confirm").attr("global_event_id",global_event_id);
	})

//populate new subevent modal	
$("#new_subevent").on('show.bs.modal', function (event) {
	var global_event_id=$("#new_subevent_creation_confirm").attr("global_event_id");

	//clean eventual old data
	$("#new_subevent_categories").html("");
	$("#new_subevent_pathways_table").html("");
	$("#new_subevent_team_table").html("");
	//build datepicker
	buildDatePicker("new_subevent");
	//setup timepickers of new subevent modal
	setUpTimePickers("#new_subevent","#new_subevent_btns");
	setTimePickersValidInterval("#new_subevent");
	//populate time pickers
	var currentTime=new Date();
	currentTime=moment(currentTime);
	var	startHour=currentTime.hours();
	var	endHour=currentTime.add(1,"hour").hours();
	var	minutes="00";
	$("#new_subevent_startHour").val(startHour+":"+minutes);
	$("#new_subevent_endHour").val(endHour+":"+minutes)
	setTimePickersValidInterval("#new_subevent");
	//populate event categories
	$.ajax({
			dataType : "json",
			type : 'POST',
			url : "index.php?src=ajax&req=047",
			data: {lang:"FR"},
			async : true,
			success : function(data, status) {
				var categories=data.academic;
				$("#new_subevent_categories").html("");
				for (var i=0; i < categories.length; i++)
					$("#new_subevent_categories").append("<li role='presentation'><a role='menuitem' tabindex='-1' href='#' onclick=\"changeEventType(\'#new_subevent_type\')\" category-id="+categories[i].id+">"+categories[i].name+"</a></li>");
			},
			error : function(xhr, status, error) {
			  var err = eval("(" + xhr.responseText + ")");
			  alert(err.Message);
			}
		});
	//populate event pathways and team
	$.ajax({
			dataType : "json",
			type : 'GET',
			url : "index.php?src=ajax&req=032&event="+global_event_id,
			async : true,
			success : function(data, status) {
				var pathways=data.pathways;
				var team=data.team;
				for (var i=0; i < pathways.length; i++)
					addPathwayWithCheckbox(pathways[i]);
				for (var i=0; i < team.length; i++)
					addTeamWithCheckbox(team[i]);
				//disable checkbox of the global event owner
				var owner_id=data.owner_id;
				team_checkboxes=$("#new_subevent_team_table input");
				for(var i=0;i<team_checkboxes.length;i++){
					if(team_checkboxes[i].getAttribute("id")==owner_id)
						team_checkboxes[i].setAttribute("disabled","disabled")
				}
			},
			error : function(xhr, status, error) {
			  var err = eval("(" + xhr.responseText + ")");
			  alert(err.Message);
			}
		});
	})
	
//builds the object datepicker
function buildDatePicker(option,target) {
	if(option=="new_subevent"||option=="new_indepevent"||option=="edit_academic_event"){
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
		
		datepicker[option+"_dates"]= new dhtmlXCalendarObject([option+"_startDate_datepicker",option+"_endDate_datepicker"]);
		datepicker[option+"_dates"].hideTime();
		datepicker[option+"_dates"].setDateFormat("%l %d %F %Y");
		datepicker[option+"_dates"].setDate(td.format(fullcalendarDateFormat),td.format(fullcalendarDateFormat));

		$("#"+option+"_endDate_datepicker").val(td.format(fullcalendarDateFormat));
		$("#"+option+"_startDate_datepicker").val(td.format(fullcalendarDateFormat));
		
		datepicker[option+"_dates"].attachEvent("onShow",function(date){
			var checked=$("#"+option+"_deadline input").prop('checked');
			if(checked)
				datepicker[option+"_dates"].clearSensitiveRange();
			});
		datepicker[option+"_dates"].attachEvent("onClick",function(date){
			//if start day and end day are different we have to enable all 24hours range for time pickers
			if($("#"+option+"_endDate_datepicker").val()!=$("#"+option+"_startDate_datepicker").val()){
				$("#"+option+"_endHour").timepicker("option",{minTime:"00:00", maxTime:"23:59"});
				$("#"+option+"_startHour").timepicker("option",{maxTime:"24:00"});
				}
			})

	}
	else if(option=="new_subevent_recurrence_end"||option=="new_indepevent_recurrence_end"){
		var tag;
		if(option=="new_subevent_recurrence_end")
			tag="new_subevent";
		else tag="new_indepevent";
		datepicker[option] = new dhtmlXCalendarObject(tag+"_recurrence_end");
		datepicker[option].hideTime();
		datepicker[option].setDateFormat("%l %d %F %Y");
		setSens(tag+"_endDate_datepicker","min",tag+"_recurrence_end");
		}
}
	
//defines valid interval of dates for the date picker
function setSens(id, k, datepicker_instance) {
	// update range
	if (k == "min")
		datepicker[datepicker_instance].setSensitiveRange($("#"+id).val(), null);
	else datepicker[datepicker_instance].setSensitiveRange(null, $("#"+id).val());
}
	
//sets the new subevent recurrence
function updateRecurrence(tag){
	$("#"+tag+"_recurrence").html(event.target.innerText);
	$("#"+tag+"_recurrence").attr("recurrence-id",event.target.getAttribute("recurrence-id"));
	if(event.target.innerHTML!="Jamais"){
		$("#"+tag+"_recurrence_end_td").removeClass("hidden");
		//build date picker of the end recurrence input
		buildDatePicker(tag+"_recurrence_end");
		}
	else $("#"+tag+"_recurrence_end_td").addClass("hidden");
	}
	
//change the value of the dropdown stating the event type
function changeEventType(tag){
	$(tag).html(event.target.innerHTML);
	$(tag).attr("category-id",event.target.getAttribute("category-id"));
	}
	
//confirm creation new subevent
$("#new_subevent_creation_confirm").on("click",function(){
	var id_global=$("#new_subevent_creation_confirm").attr("global_event_id");
	var title=$("#new_subevent_title").val();
	var deadline=$("#new_subevent_deadline input").prop("checked");
	var start=convert_date($("#new_subevent_startDate_datepicker").val(),"YYYY-MM-DD");
	var end;
	if($("#new_subevent_startHour").val().length!=0)
		start=start+"T"+$("#new_subevent_startHour").val();
	if(!deadline){
		end=convert_date($("#new_subevent_endDate_datepicker").val(),"YYYY-MM-DD");
		if($("#new_subevent_endHour").val().length!=0)
			end=end+"T"+$("#new_subevent_endHour").val();
		}

	var entireDay=false;
	if($("#new_subevent_startHour").val().length==0&&$("#new_subevent_endHour").val().length==0)
		entireDay=true;
	var recurrence=$("#new_subevent_recurrence").attr("recurrence-id");
	var end_recurrence;
	if(recurrence!=6){
		end_recurrence=convert_date($("#new_subevent_recurrence_end").val(),"YYYY-MM-DD");
		}
	var place=$("#new_subevent_place").val();
	var category=$("#new_subevent_type").attr("category-id")

	var details=$("#new_subevent_details").val();
	var feedback=$("#new_subevent_feedback_body").val();
	var workload=$("#new_subevent_workload").val();
	var pract_details=$("#new_soubevent_pract_details_body").val();
	var pathways=$("#new_subevent_pathways_table input");
	var pathways_json=[];
	var team=$("#new_subevent_team_table input");
	var team_json=[];
	for(var i=0;i<pathways.length;i++)
		pathways_json.push({id:pathways.get(i).id,selected:pathways.get(i).checked});
	pathways_json=JSON.stringify(pathways_json);
	for(var i=0;i<team.length;i++)
		team_json.push({id:team.get(i).id,selected:team.get(i).checked});
	team_json=JSON.stringify(team_json);
	//populate the server with the new subevent data
	var new_event={name:title, id_global_event:id_global, feedback:feedback, workload:workload, practical_details:pract_details, details:details, where:place, limit:deadline, entireDay:entireDay, start:start, end:end, type:category, recurrence:recurrence, "end-recurrence":end_recurrence, pathway:pathways_json, teachingTeam: team_json, attachments:""}
	$.ajax({
			dataType : "json",
			type : 'POST',
			url : "index.php?src=ajax&req=053",
			data: new_event,
			async : true,
			success : function(data, status) {
				$('#new_subevent').modal('hide');
				$("#global_events [event-id="+id_global+"]").click();
			},
			error : function(xhr, status, error) {
			  var err = eval("(" + xhr.responseText + ")");
			  alert(err.Message);
			}
		});
	})
	
//confirm creation new indep event
$("#new_indepevent_creation_confirm").on("click",function(){
	var title=$("#new_indepevent_title").val();
	var deadline=$("#new_indepevent_deadline input").prop("checked");
	var start=convert_date($("#new_indepevent_startDate_datepicker").val(),"YYYY-MM-DD");
	var end;
	if($("#new_indepevent_startHour").val().length!=0)
		start=start+"T"+$("#new_indepevent_startHour").val();
	if(!deadline){
		end=convert_date($("#new_indepevent_endDate_datepicker").val(),"YYYY-MM-DD");
		if($("#new_indepevent_endHour").val().length!=0)
			end=end+"T"+$("#new_indepevent_endHour").val();
		}
	var entireDay=false;
	if($("#new_indepevent_startHour").val().length==0&&$("#new_indepevent_endHour").val().length==0)
		entireDay=true;
	var recurrence=$("#new_indepevent_recurrence").attr("recurrence-id");
	var end_recurrence;
	if(recurrence!=6){
		end_recurrence=convert_date($("#new_indepevent_recurrence_end").val(),"YYYY-MM-DD");
		//if user doesn't specify end of the recursion we set it to one year for all cases, 10 years for "tous les ans" recurrence
		if(end_recurrence==""){
			end_recurrence=new moment(start);
			if(recurrence=="tous les ans")
				end_recurrence.add(10,"year");
			else end_recurrence.add(1,"year");
		end_recurrence=end_recurrence.format("YYYY-MM-DD");
		}
		//recurrent=true;	
	}
	var place=$("#new_indepevent_place").val();
	var category=$("#new_indepevent_type").attr("category-id");
	var feedback=$("#new_indepevent_feedback_body").val();
	var workload=$("#new_indepevent_workload").val();
	var details=$("#new_indepevent_details").val();
	var pract_details=$("#new_indepevent_pract_details_body").val();
	var pathways=$("#new_indepevent_pathways_table td[pathway-id]");
	var pathways_json=[];
	var team=$("#new_indepevent_team_table tr");
	var team_json=[];
	for(var i=0;i<pathways.length;i++)
		pathways_json.push(pathways.get(i).getAttribute("pathway-id"));
	pathways_json=JSON.stringify(pathways_json);
	for(var i=0;i<team.length;i++){
		var member_id=team.get(i).querySelector('[member-id]').getAttribute("member-id");
		var role_id=team.get(i).querySelector('[member-role-id]').getAttribute("member-role-id");
		team_json.push({id:member_id,role:role_id});
	}
	team_json=JSON.stringify(team_json);
	//populate the server with the new indepevent data
	//"name", "details", "limit","where", "start", "workload", "feedback", "practical_details", "type", "recurrence", "pathways", "teaching_team" et "end" si limit = false
	var new_event={name:title, details:details, practical_details:pract_details, where:place, limit:deadline, start:start, end:end, type:category, workload:workload, feedback:feedback, recurrence:recurrence, "end-recurrence":end_recurrence, pathways:pathways_json, teaching_team: team_json, attachments:"", softAdd:0};
	$.ajax({
			dataType : "json",
			type : 'POST',
			url : "index.php?src=ajax&req=081",
			data: new_event,
			async : true,
			success : function(data, status) {
				$('#new_indepevent').modal('hide');
				var indep_event={id:data.id, name:title, start:start, recurrence_type:recurrence};
				addIndependentEvent(indep_event);
			},
			error : function(xhr, status, error) {
			  var err = eval("(" + xhr.responseText + ")");
			  alert(err.Message);
			}
		});
	})
	
//enable create new subevent confirm button
$("#new_subevent input").on("keyup", function(){
if($("#new_subevent_title").val()!="")
	$("#new_subevent_creation_confirm").attr("disabled",false)
else $("#new_subevent_creation_confirm").attr("disabled",true)
})

//enable create new indep event confirm button
$("#new_indepevent input").on("keyup", function(){
if($("#new_indepevent_title").val()!="")
	$("#new_indepevent_creation_confirm").attr("disabled",false)
else $("#new_indepevent_creation_confirm").attr("disabled",true)
})

//add the pathway to the list in the subevent alert
function addPathwayWithCheckbox(pathway){
    var pathway_tag=document.createElement('p');
	pathway_tag.innerHTML = pathway.name;
	var table=document.getElementById("new_subevent_pathways_table");
	var row=table.insertRow(-1);
	var cell1=row.insertCell(0);
	var cell2=row.insertCell(1);
	cell1.appendChild(pathway_tag);
	var input=document.createElement('input');
	input.type='checkbox';
	input.checked=true;
	input.id=pathway.id;
	cell2.className="text-center";
	cell2.appendChild(input);
	}
	
//add the team member to the list in the subevent alert
function addTeamWithCheckbox(team){
    var team_tag=document.createElement('p');
	team_tag.innerHTML = team.name+"\t"+team.role;
	var table=document.getElementById("new_subevent_team_table");
	var row=table.insertRow(-1);
	var cell1=row.insertCell(0);
	var cell2=row.insertCell(1);
	cell1.appendChild(team_tag);
	var input=document.createElement('input');
	input.type='checkbox';
	input.checked=true;
	input.id=team.user;
	cell2.className="text-center";
	cell2.appendChild(input);
	}

$("#delete_academic_event_alert").on("show.bs.modal",function(event){
	var trigger=$(event.relatedTarget);
	var event_id=trigger.attr("event-id");
	var global_event_id=trigger.attr("global-event-id");//this is empty if we are deleting an indep event
	var event_name=trigger.attr("event-name");
	if(trigger.attr("recurrence-type")==6){//deleting non recurrent subevent
		$("#delete_academic_event_norecurr_btns").show();
		$("#delete_academic_event_recurr_btns").hide();
		$("#delete_subevent_alert .modal-title").text("Supprimer l'événement")
		}
	else{//deleting recurrent subevent
		$("#delete_academic_event_norecurr_btns").hide();
		$("#delete_academic_event_recurr_btns").show();
		$("#delete_academic_event_alert .modal-title").text("Supprimer l'événement récurrent");
		}
	$("span[name='academic_event_deleted']").text(event_name);
	$("#delete_academic_event_alert .btn-primary").attr("event-id",event_id);
	$("#delete_academic_event_alert .btn-primary").attr("global-event-id",global_event_id);
		
	})
	
//delete subevent function	
function confirm_delete_academic_event(applyRecursive){
	var event_id=event.currentTarget.getAttribute("event-id");
	var global_event_id=event.currentTarget.getAttribute("global-event-id");
	var req="055"//delete subevent;
	if(global_event_id==null)
		req="083";
	$.ajax({
			dataType : "json",
			type : 'POST',
			url : "index.php?src=ajax&req="+req,
			data: {id:event_id,applyRecursive:applyRecursive},
			async : true,
			success : function(data, status) {
				if(global_event_id!=null){
					$("#subevents_table #"+event_id).parent().parent().remove();
					$("#global_events [event-id="+global_event_id+"]").click();
				}
				else {
					$("#independent-events #"+event_id).parent().parent().remove();
					}
			},
			error : function(xhr, status, error) {
			  var err = eval("(" + xhr.responseText + ")");
			  alert(err.Message);
			}
		});
	}