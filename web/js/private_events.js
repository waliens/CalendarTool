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
	event_tag.setAttribute("id",item.id);
	event_tag.innerHTML = item.name;
	var delete_icon=document.createElement('a');
	var edit_icon=document.createElement('a');
	edit_icon.className="edit";
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

//send delete to server and update the GUI
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

