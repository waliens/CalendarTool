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
//get subevent info	
$("#subevent_info").on("show.bs.modal",function(){
	var subevent_id=subevent.getAttribute('id');
	$.ajax({
		dataType : "json",
		type : 'GET',
		//url : "json/subevent-info.json",
		url : "index.php?src=ajax&req=051&event="+subevent_id,
		success : function(data, status) {
			var subevent_id=data.id;
			var subevent_title=data.name;
			var subevent_description=data.description;
			var subevent_place=data.place;
			if(subevent_place==null)
				$("#subevent-place").parent().hide();
			else $("#subevent-place").parent().show();
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
		case "6":
			return "jamais";
		case "1":
			return "tous les jours";
		case "2":
			return "toutes les semaines";
		case "3":
			return "toutes les deux semaines";
		case "4":
			return "tous les mois";
		case "5":
			return "tous les ans"
		}
	}