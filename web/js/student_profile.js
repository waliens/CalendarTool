// JavaScript Document

//update the navbar
$("#navbar li").removeClass("active");
$("#profile_nav").addClass("active");

//holds the checkbox of an  optional cours
var checkbox;
var subevent;
var showSubeventModal=false;

$(document).ready(function() {
	moment().locale('fr');
	//populate user profile  info and courses, both optional and mandatory
	$.ajax({
		dataType : "json",
		type : 'GET',
		//url : "json/student-profile.json",
		url : "index.php?src=ajax&req=011",
		async : true,
		success : function(data, status) {
			var first_name=data.firstName;
			var last_name=data.lastName;
			var pathway=data.pathway;
			var mandatory_courses=data.courses.mandatory;
			var optional_courses=data.courses.optional
			//populate the user profile info
			document.getElementById("user-name").innerHTML=first_name+" "+last_name;
			document.getElementById("user-pathway").innerHTML=pathway.nameLong;
			//populate the mandatory courses table
			for (var i = 0; i < mandatory_courses.length; i++)
				addMandatoryCourse( mandatory_courses[i]);
			//populate the optional courses table
			for (var i = 0; i < optional_courses.length; i++)
				addOptionalCourse( optional_courses[i]);
		},
		error : function(data, status, errors) {
			// Inserire un messagio di errore
		}
	});
});

//populate mandatory courses table
function addMandatoryCourse(course){
	var allMandatoryCourses=document.getElementById("user-mandatory-courses");
    var course_tag=document.createElement('a');
	course_tag.setAttribute("data-toggle","modal");
	course_tag.setAttribute("data-target","#event_info");
	course_tag.setAttribute("event-id",course.id)
	course_tag.innerHTML = course.code;
	var row=allMandatoryCourses.insertRow(-1);
	var cell1=row.insertCell(0);
	var cell2=row.insertCell(1);
	cell1.appendChild(course_tag);
	cell2.innerHTML=course.lib_cours_complet
	}
	
//populate optional courses table
function addOptionalCourse(course){
	var allOptionalCourses=document.getElementById("user-optional-courses");
    var course_tag=document.createElement('a');
	course_tag.setAttribute("data-toggle","modal");
	course_tag.setAttribute("data-target","#event_info");
	course_tag.innerHTML = course.code;
	course_tag.setAttribute("event-id",course.id)
	var row=allOptionalCourses.insertRow(-1);
	var cell1=row.insertCell(0);
	var cell2=row.insertCell(1);
	cell1.appendChild(course_tag);
	cell2.innerHTML=course.lib_cours_complet
	//we insert the checkbox stating if the optional course is currently in the study plan or not
	var input=document.createElement('input');
	input.type='checkbox';
	input.id=course.code;
	input.setAttribute("data-toggle","modal");
	input.setAttribute("data-target","#optional_course_alert");
	var cell3=row.insertCell(2);
	cell3.className="text-center";
	cell3.appendChild(input);
	//check the checkbox if the selected parameter is true
	if(course.selected=="true")
		$("#"+course.code).prop('checked', true);
	}
	
//populate optional course alert
$('#optional_course_alert').on('show.bs.modal', function (event) {
  checkbox = $(event.relatedTarget) // checkbox that triggered the modal
  var course_id = checkbox.prop('id') // Extract info from id attributes
  var modal = $(this);  
  var choice="";
  if(checkbox.prop('checked'))
  	choice="ajouter";
  else choice="supprimer";
  modal.find('.modal-title').text('Cours ' + course_id);
  modal.find('.modal-body p').text('Êtes-vous sûr que vous voulez '+choice+' le cours '+course_id+' de votre calendrier?');
})

//revert the selection of the optional course
$('#optional_course_alert .close').click(function(){
	if(checkbox.attr("checked"))
		checkbox.attr("checked",false)
	else checkbox.attr("checkbox",true);
	});

$('#optional_course_alert .btn-default').click(function(){
	// revert the action on the checkbox
	if(checkbox.attr("checked"))
		checkbox.attr("checked",false)
	else checkbox.attr("checkbox",true);
	});	
	
$('#optional_course_alert .btn-primary').click(function(){
	// send to server the modified info
	var optional_course = {"id":checkbox.attr("id"),"selected":checkbox.prop("checked")}
	$.ajax({
			dataType : "json",
			type : 'POST',
			url : "index.php?src=ajax&req=300",
			data : optional_course,
			success : function(data, status) {
				// insert success msg
			},
			error : function(data, status, errors) {
				// insert error msg
			}
		});
	});
	
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
			if(global_event_work_th!="0")
				$("#event-work").append(global_event_work_th+"h Th. ");
			if(global_event_work_pr!="0")
				$("#event-work").append(global_event_work_pr+"h Proj. ");
			if(global_event_work_au!="0")
				$("#event-work").append(global_event_work_au+"h Au. ");
			if(global_event_work_st!="0")
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
			if(subevents.length>0){
				var row=subevents_table.insertRow(0);
				var cell1=row.insertCell(0);
				var cell2=row.insertCell(1);
				var cell3=row.insertCell(2);
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
	var cell1=document.createElement("td");
	var cell2=document.createElement("td");
	var cell3=document.createElement("td");
	var a=document.createElement("a");
	a.setAttribute("data-dismiss","modal");
	a.setAttribute("data-target","#academic_event_info_modal");
	a.setAttribute("data-toggle","modal");
	a.innerHTML=item.name;
	a.id=item.id;
	a.onclick=function(e){showSubeventModal=true; subevent=e.target}
	cell1.appendChild(a);
	cell2.innerHTML=item.start;
	cell2.setAttribute("event-id",item.id);
	cell2.id="start_time_subevent";
	row.appendChild(cell1);
	cell3.innerHTML=get_recursion(item.recurrence_type);
	row.appendChild(cell2);
	row.appendChild(cell3);
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
//get subevent info	
$("#academic_event_info_modal").on("show.bs.modal",function(){
	var subevent_id=subevent.getAttribute('id');
	var req="051";//subevent by default
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
				$("#academic_event_start").html(academic_event_start.format("dddd Do MMMM YYYY, h:mm a"));
			}
			else $("#academic_event_start").html(academic_event_start.format("dddd Do MMMM YYYY"));
			var academic_event_end;
			if(data.endDay!=""){
				$("#academic_event_end").parent().removeClass("hidden");
				academic_event_end=moment(data.endDay);
				if(data.endTime!=""){
					var chunks=data.endTime.split(":");
					academic_event_end.set("hour",chunks[0]);
					academic_event_end.set("minute",chunks[1]);
					$("#academic_event_end").html(academic_event_end.format("dddd Do MMMM YYYY, h:mm a"));
				}
				else $("#academic_event_end").html(academic_event_end.format("dddd Do MMMM YYYY"));
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
				$("#academic_event_recurrence_end").html(end_recurrence.format("dddd Do MMMM YYYY"));
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
				$("#academic_event_pathways_table").append("<p pathway-id="+pathways[i].id+">"+pathways[i].name+"</p>");
			//check if the event has notes or not
			$("#notes_body").text(data.annotation);
		},
		error: function(xhr, status, error) {
			launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
		}
	});
})

function get_recursion(recursion_id){
	switch(recursion_id){
		case "6":
			return "Jamais";
		case "1":
			return "Tous les jours";
		case "2":
			return "Toutes les semaines";
		case "3":
			return "Toutes les deux semaines";
		case "4":
			return "Tous les mois";
		case "5":
			return "Tous les ans"
		}
	}