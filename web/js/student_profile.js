// JavaScript Document

//update the navbar
$("#navbar li").removeClass("active");
$("#profile_nav").addClass("active");

$(document).ready(function() {
	//populate user profile  info and courses, both optional and mandatory
	$.ajax({
		dataType : "json",
		type : 'GET',
		url : "json/student-profile.json",
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
	course_tag.innerHTML = course.code;
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
  var checkbox = $(event.relatedTarget) // checkbox that triggered the modal
  var course_id = checkbox.prop('id') // Extract info from id attributes
  var modal = $(this);  
  var choice="";
  if(checkbox.prop('checked'))
  	choice="ajouter";
  else choice="supprimer";
  modal.find('.modal-title').text('Cours ' + course_id);
  modal.find('.modal-body p').text('Êtes-vous sûr que vous voulez '+choice+' le cours '+course_id+' de votre calendrier?');
})

//apply the result of the button pressed on the optional course alert
$('#optional_course_alert .close').click(function(){
	// revert the action on the checkbox
	});

$('#optional_course_alert .btn-default').click(function(){
	// revert the action on the checkbox
	});	
	
$('#optional_course_alert .btn-primary').click(function(){
	// send to server the modified info
	});
