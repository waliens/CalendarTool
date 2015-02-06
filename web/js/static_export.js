// JavaScript Document
  
$('.modal').on('show.bs.modal', function (event) {
	//we clean the content of the alert
	$(this).find('.modal-body').html("");
	var checkbox = $(event.relatedTarget);
  //prevent the alert from being shown when a filter is unchecked
  if (!checkbox.prop('checked'))
	return event.preventDefault() // stops modal from being shown
  //populate the alert with the available filters
  else{
	  var trigger=checkbox.prop("id");
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
				// Blah
				break;
			case "session_filter":
				// Blah
				break;
			case "professor_filter":
				// Blah
				break;
			}
		}
})
//deal with the filter all_events which must disable all other when pressed and enable all when pressed again
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
	}
	else{
		$('input').removeAttr("disabled");
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
	//we insert the checkbox stating if the optional course is currently in the study plan or not
	var input=document.createElement('input');
	input.type='checkbox';
	input.id=course.code;
	var cell3=row.insertCell(2);
	cell3.className="text-center";
	cell3.appendChild(input);
	}