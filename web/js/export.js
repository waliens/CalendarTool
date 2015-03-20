// JavaScript Document
var today = new Date();
var year = today.getFullYear();
var month=today.getMonth();
var day=today.getDay();
if(month>1&&month<9)
	year=year-1;
else if(month==9){
	if(day<14)
		year=year-1;
	}
	
//update the navbar
$("#navbar li").removeClass("active");
$("#menu_nav").addClass("active");
//datepickers
var startDate;
var endDate;
//filters for export
var filters = {
          	allEvents: {isSet: 'false'},
			dateRange: {isSet: 'false', startDate: 'null', endDate: 'null'},
			courses: {isSet: 'false', id:[]},
			eventTypes: {isSet: 'false', id:[]},
			eventCategories: {isSet: 'false', id:[]},
			pathways: {isSet: 'false', id:[]},
			professors:	{isSet: 'false', id:[]}
          };
		  
//set dateRange by default
filters.dateRange.isSet=true;
filters.dateRange.endDate=(year+1)+"-09-14";
filters.dateRange.startDate=year+"-09-15";
 
$(document).ready(function(){
	//set moment locale to french
	moment.locale('fr');
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
	  $("#filter_alert .btn-primary").attr("id",trigger+"_btn");
	  $('#filter_alert .close').attr("id","close_"+trigger);
	  //populate the filter alert based on the triggered filter
	  switch (trigger) {
		  	case "date_filter":
				//enable ok button
				$("#date_filter_btn").attr("disabled",false);
				$(this).find('.modal-title').text("Filtrer par date");
				//build the datepicker elements
				$(this).find('.modal-body').html("<p><input id='startDateFilter' onclick=\"setSens(\'endDateFilter\',\'max\');\" readonly='true'><label class='common_text margin-left-10'>à partir</label></p><p><input id='endDateFilter' onclick=\"setSens(\'startDateFilter\',\'min\');\" readonly='true'><label class='common_text margin-left-10'>de</label></p>");
				buildDatepickerFilter();
				break;
			case "course_filter":
				$(this).find('.modal-title').text("Filtrer par cours");
				//get student courses
				$.ajax({
						dataType : "json",
						type : 'GET',
						//url : "json/student-courses.json",
						url : "index.php?src=ajax&req=031", 
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
							cell1.className="min-width-100"
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
						//url : "json/events_type.json",
						url : "index.php?src=ajax&req=041",
						async : true,
						success : function(data, status) {
							var date_types=data.date_type;
							var event_types=data.event_type;
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
							for (var i = 0; i < date_types.length; i++)
								addType(date_types[i]);
							for (var i = 0; i < event_types.length; i++)
								addType(event_types[i]);
						},
						error : function(xhr, status, error) {
						  var err = eval("(" + xhr.responseText + ")");
						  alert(err.Message);
						}
					});
				break;
			case "event_category_filter":
			$(this).find('.modal-title').text("Filtrer par categorie d'événement");
				//get events type
				$.ajax({
						dataType : "json",
						type : 'POST',
						//url : "json/event_categories.json",
						url : "index.php?src=ajax&req=047",
						data: {lang:"FR"},
						async : true,
						success : function(data, status) {
							var student_categories=data.student;
							var academic_categories=data.academic;
							//populate the filter list
							var filter_alert=$("#filter_alert .modal-body");
							var table=document.createElement("table");
							table.className="table";
							table.id="events_categories_filter_table";
							var row=table.insertRow(-1);
							row.className="text-bold";
							var cell1=row.insertCell(0);
							var cell2=row.insertCell(1);
							cell1.innerHTML="Événement Academique";
							cell2.innerHTML="Choisir";
							cell2.className="text-center"
							var cell3=row.insertCell(2);
							var cell4=row.insertCell(3);
							cell3.innerHTML="Événement Privé";
							cell4.innerHTML="Choisir";
							cell4.className="text-center"
							filter_alert.append(table);
							var i=0;
							for (i; i < academic_categories.length; i++){
								var acad_category_tag=document.createElement('p');
								acad_category_tag.innerHTML = academic_categories[i].name;
								var table=document.getElementById("events_categories_filter_table");
								var row=table.insertRow(-1);
								var cell1=row.insertCell(0);
								var cell2=row.insertCell(1);
								cell1.appendChild(acad_category_tag);
								var input=document.createElement('input');
								input.type='checkbox';
								input.id=academic_categories[i].name;
								cell2.className="text-center";
								cell2.appendChild(input);
								if(student_categories[i]!=null){
									var student_category_tag=document.createElement('p');
									student_category_tag.innerHTML = student_categories[i].name;
									var cell3=row.insertCell(2);
									var cell4=row.insertCell(3);
									cell3.appendChild(student_category_tag);
									var input=document.createElement('input');
									input.type='checkbox';
									input.id=student_categories[i].name;
									cell4.className="text-center";
									cell4.appendChild(input);
								}
							}
							for(var j=i;j<student_categories.length;j++){
								var table=document.getElementById("events_categories_filter_table");
								var row=table.insertRow(-1);
								var cell1=row.insertCell(0);
								var cell2=row.insertCell(1);
								var student_category_tag=document.createElement('p');
								student_category_tag.innerHTML = student_categories[i].name;
								var cell3=row.insertCell(2);
								var cell4=row.insertCell(3);
								cell3.appendChild(student_category_tag);
								var input=document.createElement('input');
								input.type='checkbox';
								input.id=student_categories[i].name;
								cell4.className="text-center";
								cell4.appendChild(input);
							}
						},
						error : function(xhr, status, error) {
						  var err = eval("(" + xhr.responseText + ")");
						  alert(err.Message);
						}
					});
			break;	
			case "pathway_filter":
				$(this).find('.modal-title').text("Filtrer par pathway");
				//get pathways
				$.ajax({
						dataType : "json",
						type : 'GET',
						//url : "json/pathways.json",
						url : "index.php?src=ajax&req=111",
						async : true,
						success : function(data, status) {
							var pathways=data.pathways;
							//populate the filter list
							var filter_alert=$("#filter_alert .modal-body");
							var table=document.createElement("table");
							table.className="table";
							table.id="pathways_filter_table";
							var row=table.insertRow(-1);
							row.className="text-bold";
							var cell1=row.insertCell(0);
							var cell2=row.insertCell(1);
							cell1.innerHTML="Pathway";
							cell2.innerHTML="Choisir";
							cell2.className="text-center"
							filter_alert.append(table);
							for (var i = 0; i < pathways.length; i++)
								addPathway(pathways[i]);
						},
						error : function(data, status, errors) {
							// Inserire un messagio di errore
						}
					});
				break;
			case "professor_filter":
				$(this).find('.modal-title').text("Filtrer par professeur");
				//get professors
				$.ajax({
						dataType : "json",
						type : 'GET',
						//url : "json/all_professors.json",
						url : "index.php?src=ajax&req=021",
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



//deals with the filter all_events which must disable all other (but the date filter) when pressed and enable all when pressed again
$("#all_events_filter").click(function(){
	if($(this).prop('checked')){
		//disable all other checkboxes but the date filter
		var checkboxes=$('input');
		for(var i=0;i<checkboxes.length;i++){
			var item=checkboxes.get(i);
				if(item.id!="all_events_filter"&&item.id!="date_filter"){
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
//builds the element datepicker in the alert called by the date range filter
function buildDatepickerFilter() {
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
	filterDates= new dhtmlXCalendarObject(["startDateFilter","endDateFilter"]);
	filterDates.hideTime();
	filterDates.setDateFormat("%Y-%m-%d");
	filterDates.setDate(td.format("YYYY-MM-DD"),td.add(1,"day").format("YYYY-MM-DD"));
	var t = new Date();
	byId("endDateFilter").value = td.format("dddd DD MMM YYYY");
	byId("startDateFilter").value = td.subtract(1,"day").format("dddd DD MMM YYYY");
	//convert the date returned from the datepicker to the format "dddd DD MMM YYYY"
	filterDates.attachEvent("onClick", function(date){
		$("#startDateFilter").val(convert_date($("#startDateFilter").val(),"dddd DD MMM YYYY"));
		$("#endDateFilter").val(convert_date($("#endDateFilter").val(),"dddd DD MMM YYYY"));
	});
}


function setSens(id, k) {
// update range
	if (k == "min") {
		filterDates.setSensitiveRange(convert_date(byId(id).value,"YYYY-MM-DD"), null);
	} else {
		filterDates.setSensitiveRange(null, convert_date(byId(id).value,"YYYY-MM-DD"));
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
	cell2.innerHTML=course.name;
	var input=document.createElement('input');
	input.type='checkbox';
	input.id=course.id;
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
	
//add the pathway to the list in the filter alert
function addPathway(pathway){
    var pathway_tag=document.createElement('p');
	pathway_tag.innerHTML = pathway.name;
	var table=document.getElementById("pathways_filter_table");
	var row=table.insertRow(-1);
	var cell1=row.insertCell(0);
	var cell2=row.insertCell(1);
	cell1.appendChild(pathway_tag);
	var input=document.createElement('input');
	input.type='checkbox';
	input.id=pathway.id;
	cell2.className="text-center";
	cell2.appendChild(input);
	}
	
//add the professor to the list in the filter alert
function addProfessor(professor){
    var professor_tag=document.createElement('p');
	professor_tag.innerHTML = professor.name+" "+professor.surname;
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
			filters.dateRange.isSet="true";
			filters.dateRange["startDate"]=convert_date($("#startDateFilter").val(),"YYYY-MM-DD");
			filters.dateRange["endDate"]=convert_date($("#endDateFilter").val(),"YYYY-MM-DD");
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
		case "event_category_filter":
			filters.eventCategories.isSet="true";
			var selectedCategories=$("#filter_alert input:checked");
			selectedCategories.each(function (){
				filters.eventCategories.id.push(this.id);
				});
			break;
		case "pathway_filter":
			filters.pathways.isSet="true";
				var selectedPathways=$("#filter_alert input:checked");
				selectedPathways.each(function (){
					filters.pathways.id.push(this.id);
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
			filters.dateRange.isSet="false";
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
		case "event_category_filter":
			filters.eventCategories.isSet="false";
			//empty the array of ids'
			filters.eventCategories.id.length=0;
		break;
		case "pathway_filter":
			filters.pathways.isSet="false";
			//empty the array of ids'
			filters.pathways.id.length=0;
		break;
		case "professor_filter":
			filters.professors.isSet="false";
			//empty the array of ids'
			filters.professors.id.length=0;
		break;
		}
	}

	
//set global var filters when a filter is selected
$("#filter_alert .btn-primary").click(function(){
		var filter=$('#filter_alert .btn-primary').attr("id").replace("_btn","");
		setFilter(filter);
});

//call the unSetFilter when the close button is clicked
$("#filter_alert .close").click(function(){
		var filter=$('#filter_alert .btn-primary').attr("id").replace("_btn","");
		unSetFilter(filter);
		$("#filter_alert").modal("hide");
		//eventually disable the form submission button if no other filter is set
		if($("input:checked").length==0)
			$("#static_export").attr("disabled",true);
});

$("input").click(function(){
	//enable the send form button only when at least one filter is selected
	if($("input:checked").length>0)
		$("#static_export").attr("disabled",false);
	else $("#static_export").attr("disabled",true);
	});

//send data to server after filters comple
$("#static_export").click(function(){
	//UNCOMMENT FOLLOWING LINE FOR TESTING WITHOUT SERVER
	//$("#dynamic_export_download_alert").modal("show");
	$.ajax({
			dataType : "json",
			type : 'POST',
			url : "index.php?src=ajax&req=091",
			data : filters,
			success : function(data, status) {
				$("#dynamic_export_download_alert").modal("show");
				$("#dynamic_export_file").attr("href",data.url);
			},
			error : function(xhr, status, error) {
					launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
				}
		});
});


//converts date formats	
//converts date formats	
function convert_date(date,formatDestination,formatOrigin){
		var dd;
		var mm;
		var yy;
		var chunks=date.split(" ");
		//date can be in the format "dd-mm-yyy", "dddd DD MM YYY" or yyyy-mm-dd
		if(chunks.length>1){
			dd=chunks[1];
			if(chunks[2].length<2)
				mm=convert_month(chunks[2]);
			else mm=chunks[2];
			yy=chunks[3];
		}
		else {
			chunks=date.split("-");
			if(chunks[0].length==4){
				dd=chunks[2];
				mm=chunks[1];
				yy=chunks[0];
			}
			else{
				dd=chunks[0];
				mm=chunks[1];
				yy=chunks[2];

				}
		}
		date_standard=yy+"-"+mm+"-"+dd;
		var d = moment(date_standard);
		return d.format(formatDestination);
	}
	
//enable filter ok button when at least one checkbox is selected
$("#filter_alert").on("click", $("#filter_alert input"),function(){
	//we make sure we are not in the date filter
		if($("#date_filter_btn").length==0){
			var checked=$("#filter_alert input:checked");
			if(checked.length>0)
				$("#filter_alert .btn-primary").attr("disabled",false);
			else $("#filter_alert .btn-primary").attr("disabled",true);
		}
	})
	
$("#filter_alert").on('hidden.bs.modal', function (e) {
	if($("#date_filter_btn").length==0)
		$("#filter_alert .btn-primary").attr("disabled",true);
});