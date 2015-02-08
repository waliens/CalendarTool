// JavaScript Document
$(document).ready(function() {
	//retrieve private events from server
	$.ajax({
		dataType : "json",
		type : 'GET',
		url : "all_private_events.json",
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
	
//when the bean is clicked a deletion alert is invoked
$(".delete").click(function(){
	
	});