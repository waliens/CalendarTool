// JavaScript Document

var today = new Date();
var day = today.getDate();
var month = today.getMonth()+1; //January is 0!
var year = today.getFullYear();
var calendar_data;
//vars to handle the definition of event notes
var edit_existing_note=false;
var existing_note_content;
//private event vars
var event_date;
var event_place;
var event_details;

$(document).ready(function() {
	//initialize the calendar...
    $('#calendar').fullCalendar({
		lang: 'fr',
		header: {
		left: 'prev,next today',
		center: 'title',
		right: 'month,agendaWeek,agendaDay'
		},
		editable: true,
		eventLimit: true, // allow "more" link when too many events
		fixedWeekCount: false, //each month only shows the weeks it contains (and not the default 6) 
		//handle click on event
		eventClick: function(calEvent, jsEvent, view) {
			var event_title=calEvent.title;
			//call the event info panel
			$("#event_info").modal("show");
			//populate title
			$("#event-title").text(event_title);
			//check if it's private and if so add an edit icon next to title
			if(calEvent.private){
				$("#edit_private_event").removeClass('hidden');
				$("#event-title").addClass("float-left-10padright");
			}
			else {
				$("#edit_private_event").addClass('hidden');
				$("#event-title").removeClass("float-left-10padright");
				}
			//check if it's an all day event
			if(calEvent.allDay)
				$("#event_time").text(calEvent.start.format('dddd Do MMM YYYY'));
			else{
				$("#event_time").text(calEvent.start.format("hh:mm")+" - "+calEvent.end.format("hh:mm")+" "+calEvent.start.format('dddd Do MMM YYYY'));
				}
			//populate place,prof and details
			$("#event_place").text(calEvent.place);
			//if the event is private there's no need to show the owner so we hide the corresponding table row
			if(calEvent.owner){
				$("#event_owner").text(calEvent.owner);
				$("#event_owner").parent().parent().removeClass("hidden");
			}
			else $("#event_owner").parent().parent().addClass("hidden");
			$("#event_details").text(calEvent.details);
			//check if the event has notes or not
			if(calEvent.notes){
				$("#add_notes").addClass("hidden");
				$("#notes").removeClass("hidden");
				$("#notes_body").text(calEvent.notes);
			}
			else{
				$("#add_notes").removeClass('hidden');
				$("#notes").addClass("hidden");
				}
		},
		
		//handle clicks within the calendar
		dayClick: function(date, jsEvent, view) {

			var target = date.format();
	
		},
		//populate events
		events:   function(start, end, timezone, callback){
			$.ajax({
				dataType : "json",
				type : 'GET',
				url : "calendar-month.json",
				//url: "calendar.html&src='ajax'&req=10&month="+month,
				success : function(data, status) {
					calendar_data=data;
					var events = [];
					//retireve all public events first
					for(var i=0;i<calendar_data.events.publicEvents.length;i++){
						var instance = calendar_data.events.publicEvents[i];
						//chech the event type to accordingly set the event color
						var color=getEventColor(instance);
						events.push({
							id: instance.id,
							private: false,
							title: instance.name,
							start: instance.start,
							end: instance.end,
							owner: instance.professor.name,
							place: instance.where,
							details: instance.details,
							notes: instance.notes,
							color: color
						});
					}
					//then retrieve private events
					for(var i=0;i<calendar_data.events.privateEvents.length;i++){
						var instance=calendar_data.events.privateEvents[i];
						events.push({
							id: instance.id,
							private: true,
							title: instance.name,
							start: instance.start,
							end: instance.end,
							place: instance.where,
							details: instance.details,
							notes: instance.notes,
							color: '#8AC007'
						});
					}
					callback(events);
				},
				error : function(data, status, errors) {
					// Inserire un messagio di errore
				}
			});
			},
    })
	//setup popover for delete button
	$("#delete_note .delete").popover({
		template: '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div><div class="modal-footer"><button type="button" class="btn btn-default">Annuler</button><button type="button" class="btn btn-primary id="confirm_delete_note" onclick="delete_note()">Confirmer</button></div></div>'
		});
});

//define the color of the event in the calendar based on the event type
function getEventColor(event){
	if(event.type=="deadline")
		return "#FF0000" //RED
	else if(event.type=="class")
		return "#2400FF" //BLUE
	else if(event.type=="exam")
		return "#FFAE00" //ORANGE
	}
	
//delete note when confirm deletion
function delete_note() {
	$("#add_notes").removeClass("hidden");
	$("#notes").addClass("hidden");
	$("#notes_body").text("");
	//Send delete confirmation to server
	}
	
//add note
function add_note(){
	edit_existing_note=false;
	//prevent the modal to hide before we either confirm the new note or we abort
	$(".modal-backdrop").off("click");
	//display and hide required elements
	$("#add_notes").addClass("hidden");
	$("#edit_note").addClass("hidden");
	$("#delete_note").addClass("hidden");
	$("#notes_body").prop('contenteditable',"true");
	$("#notes_body").addClass("box");
	$("#notes").removeClass("hidden");
	$("#mod_notes_btns").removeClass("hidden");
	$("#notes_body").focus();
	}
	
//save new note
function save_note(){
	$("#mod_notes_btns").addClass("hidden");
	$("#notes_body").prop('contenteditable',"false");
	$("#notes_body").removeClass("box");
	$("#edit_note").removeClass("hidden");
	$("#delete_note").removeClass("hidden");
	//re-enable the backdrop of the modal (when clicking outside of the modal it closes)
	$(".modal-backdrop").on("click",function(){$("#event_info").modal("hide")});
	//send new data to server
	}
	
function abort_note(){
	//abort the insertion of a new note
	if(!edit_existing_note){
		$("#add_notes").removeClass("hidden");
		$("#notes").addClass("hidden");
		$("#notes_body").prop('contenteditable',"false");
		$("#notes_body").text("");
		$("#notes_body").removeClass("box");
	}
	//abort the edit of an existing note
	else{
		$("#notes_body").prop('contenteditable',"false");
		$("#notes_body").removeClass("box");
		$("#mod_notes_btns").addClass("hidden");
		$("#notes_body").text(existing_note_content);
		}
	}
	
//edit current note
function edit_note(){
	edit_existing_note=true;
	existing_note_content=$("#notes_body").text();
	$("#notes_body").prop('contenteditable',"true");
	$("#notes_body").addClass("box");
	$("#mod_notes_btns").removeClass("hidden");
	$("#notes_body").focus();
	}

//edit event info	
function edit_private_event(){
	//prevent the button from being pressed again
	$("#edit_private_event a").off("click");
	//make all event info editable
	$("#event_time").prop('contenteditable',"true");
	$("#event_time").addClass("box");
	$("#event_place").prop('contenteditable',"true");
	$("#event_place").addClass("box");
	$("#event_details").prop('contenteditable',"true");
	$("#event_details").addClass("box");
	//save current event info
	event_time=$("#event_time").text();
	event_place=$("#event_place").text();
	event_details=$("#event_details").text();
	//display save, abort buttons
	$("#edit_event_btns").removeClass("hidden");
	}
	
//abort edit info
function abort_edit_event(){
	//bind edit button to handler
	$("#edit_private_event a").bind("click",edit_private_event());
	//rollback event info
	$("#event_time").text(event_time);
	$("#event_place").text(event_place);
	$("#event_details").text(event_details);
	//make all event info non editable
	$("#event_time").prop('contenteditable',"false");
	$("#event_time").removeClass("box");
	$("#event_place").prop('contenteditable',"false");
	$("#event_place").removeClass("box");
	$("#event_details").prop('contenteditable',"false");
	$("#event_details").removeClass("box");
	//hide save, abort buttons
	$("#edit_event_btns").addClass("hidden");
	}
	
