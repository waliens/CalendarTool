// JavaScript Document
$(document).ready(function() {
	//populate user profile  info and courses, both optional and mandatory
	$.ajax({
		dataType : "json",
		type : 'GET',
		url : "student-profile.json",
		async : true,
		success : function(data, status) {
			/** error checking */
			if(data.error.error_code > 0)
			{	
				launch_error_ajax(data.error);
				return;
			}
							
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
			launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
		}
	});
});

//populate mandatory courses table
function addMandatoryCourse(course){
	var allMandatoryCourses=document.getElementById("user-mandatory-courses");
	temp = document.createElement('a');
	temp.className="list-group-item";
    temp.innerHTML = course.name;
	allMandatoryCourses.appendChild(temp);
	}
	
//populate mandatory courses table
function addOptionalCourse(course){
	var allOptionalCourses=document.getElementById("user-optional-courses");
	temp = document.createElement('a');
	temp.className="list-group-item";
    temp.innerHTML = course.name;
	allOptionalCourses.appendChild(temp);
	}