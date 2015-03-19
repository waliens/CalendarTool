// JavaScript Document

//update the navbar
$("#navbar li").removeClass("active");
$("#menu_nav").addClass("active");

$(document).ready(function() {
	//retrieve private events from server
	$.ajax({
		dataType : "json",
		type : 'GET',
		//url : "json/all_private_events.json",
		url: "index.php?src=ajax&req=062",
		async : true,
		success : function(data, status) {
			var private_events=data.events;
			//populate the private events table
			for (var i = 0; i < private_events.length; i++)
				addEvent(private_events[i]);
		},
		error : function(data, status, errors) {
			// Inserire un messagio di errore
		}
	});
});

//add private event to the table of private events
function addEvent(item){
	var private_events_table=document.getElementById("private_events");
    var event_tag=document.createElement('a');
	event_tag.setAttribute("event-id",item.id);
	event_tag.innerHTML = item.name;
	event_tag.setAttribute("data-toggle","modal");
	event_tag.setAttribute("data-target","#private_event");
	var delete_icon=document.createElement('a');
	var edit_icon=document.createElement('a');
	edit_icon.className="edit";
	edit_icon.setAttribute("event-id",item.id);
	delete_icon.className="delete";
	//link the delete icon to the delete alert
	delete_icon.setAttribute("data-toggle","modal");
	delete_icon.setAttribute("data-target","#delete_alert");
	delete_icon.setAttribute("event-id",item.id);
	delete_icon.setAttribute("event-name",item.name);
	delete_icon.setAttribute("recurrence",item.recurrence);
	var div_container1=document.createElement("div");
	div_container1.className="text-center";
	var div_container2=document.createElement("div");
	div_container2.className="text-center";
	div_container1.appendChild(edit_icon);
	div_container2.appendChild(delete_icon);
	var row=private_events_table.insertRow(-1);
	var cell1=row.insertCell(0);
	var cell2=row.insertCell(1);
	var cell3=row.insertCell(2);
	cell1.appendChild(event_tag);
	cell2.appendChild(div_container1);
	cell3.appendChild(div_container2);
	}
	
//populate delete private event alert
$('#delete_alert').on('show.bs.modal', function (event) {
	var private_event = $(event.relatedTarget);
	var recurrence = private_event.attr("recurrence");
	$("#delete_alert .modal-body").html("Êtes-vous sûr de vouloir supprimer l'événement <span class='text-bold'>"+private_event.attr("event-name")+"</span>");
	$("#delete_confirm").attr("event-id",private_event.prop("id"));
});



//delete private event
$("#delete_confirm").click(function(){
	$.ajax({
		dataType : "json",
		type : 'POST',
		url : "index.php?src=ajax&req=102",
		data:$("#delete_confirm").attr("event-id"),
		async : true,
		success : function(data, status) {
			var event_id=$("#delete_confirm").attr("event-id");
			$("a[id="+event_id+"]").parent().parent().remove();
		},
		error : function(data, status, errors) {
			// Inserire un messagio di errore
		}
	});
	});


//add event-id to the confirm button of the private event modal on display
$("#private_event").on('show.bs.modal', function (event) {
	var event_id=event.relatedTarget.getAttribute("id");
	$("#edit_event_btns .btn-primary").attr("event-id",event-id);
	})
	
//populate private event modal
function populate_private_event(event){
	$("#delete_private_event .delete").attr("event-id",event.id_server);
	var title=event.title;
	var allDay=event.allDay;
	var start=event.start.format("dddd DD MMM YYYY");
	buildDatePicker("private_event",start);
	//check if event has start hour
	var startHour;
	if(!allDay){
		startHour=event.start.format("HH:mm");
		$("#private_event_startHour").val(startHour);
		endHour=event.end.format("HH:mm");
		$("#private_event_endHour").val(endHour);
	}
	else $("#private_event_startHour").addClass("hidden");
	//check if the event as an end date
	if(event.end){
		var end=event.end.format("dddd DD MMM YYYY");
		$("#private_event_endDate_datepicker").val(end);
	}
	else 	$("#private_event_endDate_datepicker").parent().parent().addClass("hidden"); 
	var place=event.place;
	var details=event.details;
	var notes=event.notes;
	//populate modal title
	$("#private_event_modal_header").text(title);
	//adds edit/delete icons next to title
	$("#edit_private_event").removeClass('hidden');
	$("#delete_private_event").removeClass('hidden');
	$("#private_event_modal_header").addClass("float-left-10padright");
	//populate modal fields
	$("#private_event_title").val(title);
	$("#private_event_title").prop("readonly",true);
	$("#deadline input").prop("disabled",true);
	$("#private_event_startDate_datepicker").val(start);
	$("#private_event_startDate_datepicker").prop("readonly",true);
	$("#private_event_startDate_datepicker").prop("disabled",true);
	//$("#private_event_startHour").prop("readonly",true);
	$("#private_event_endDate_datepicker").prop("readonly",true);
	$("#private_event_endDate_datepicker").prop("disabled",true);
	//$("#private_event_endHour").prop("readonly",true);
	$("#private_event_place").val(place);
	$("#private_event_place").prop("readonly",true);
	$("#private_event_details").val(details);
	$("#private_event_details").prop("readonly",true);
	$("#recurrence_btn").prop("disabled",true);
	$("#private_event_type_btn").prop("disabled",true);
	if(notes!=""){
		$("#private_notes_body").val(notes);
		$("#private_notes_body").prop("readonly",true);
	}
	else $("#private_notes_body").parent().parent().addClass("hidden");
	//hides button used when creating a new event
	$("#edit_event_btns").addClass("hidden");
	}

