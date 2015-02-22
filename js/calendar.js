// JavaScript Document

var today = new Date();
var day = today.getDate();
var month = today.getMonth()+1; //January is 0!
if(month<10)
	month="0"+month;
if (day<10)
	day="0"+day;
var year = today.getFullYear();
var minutes = today.getMinutes();
var hours = today.getHours();
var calendar_data;
//vars to handle the definition of event notes
var edit_existing_note=false;
var existing_note_content;
//private event vars
var event_date_start;
var event_date_end;
var event_all_day;
var event_place;
var event_details;
var event_recursive=false;
//dates picker
var datepicker = {"existing_event":0,"private_event":0};
var existing_event_datepicker;
var new_event_datepicker;

//update the navbar
$("#navbar li").removeClass("active");
$("#calendar_nav").addClass("active");

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
		//populate events
		events:   function(start, end, timezone, callback){
			$.ajax({
				dataType : "json",
				type : 'GET',
				url : "json/calendar-month.json",
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
							color: color,
							editable: false
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
							recursive: instance.recursive,
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
			//handle click on event
		eventClick: function(calEvent, jsEvent, view) {
			var event_private=calEvent.private;
			//check event type to call proper modal
			if(event_private){
				//set recursion var
				if(calEvent.recursive)
					event_recursive=true;
				else event_recursive=false;
				populate_private_event(calEvent);
				$("#private_event").modal("show");
				}
			else{
				$("#event_info").modal("show");
				populate_public_event(calEvent);
				}
			//check if it's an all day event
			if(calEvent.allDay){
				event_all_day=true;
				$("#startDate").text(calEvent.start.format('dddd DD MMM YYYY'));
				//check if there's an end date
				if(calEvent.end){
					$("#endDate").text(calEvent.end.format('dddd DD MMM YYYY'));
					$("#endDate").removeClass("hidden");
					$("#endDate_label").removeClass("hidden");
					$("#startDate_label").removeClass("hidden");
				}
				else {
					$("#endDate").addClass("hidden");
					$("#endDate_label").addClass("hidden");
					$("#startDate_label").addClass("hidden");
				}
			}
			else {
				event_all_day=false;
				$("#startDate").text(calEvent.start.format('dddd DD MMM YYYY')+" "+calEvent.start.format("HH:mm"));
				$("#endDate").text(calEvent.start.format('dddd DD MMM YYYY')+" "+calEvent.end.format("HH:mm"));
				$("#endDate").removeClass("hidden");
				$("#endDate_label").removeClass("hidden");
				$("#startDate_label").removeClass("hidden");
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
			buildDatePicker("private_event",target);
			$("#private_event_title").prop("disabled",false);
			$("#private_event_startDate_datepicker").prop("disabled",false);
			$("#private_event_endDate_datepicker").prop("disabled",false);
			$("#private_event_place").prop("disabled",false);
			$("#recurrence_btn").prop("disabled",false);
			$("#private_event_details").prop("disabled",false);
			$("#private_notes_body").prop("disabled",false);
			$("#new_event_btns").removeClass("hidden");
			$("#private_event").modal("show");
	
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
	//TODO
	}
	
//add note
function add_note(){
	edit_existing_note=false;
	$("#notes_body").text("");
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
	//TODO
	}
	
/*//checks if the event is recursive and eventually asks if we want to apply the modification to all instances or not; 
function recursive_check(){
	if(event_recursive){
		$("#recursive_event_popup").popover({
		template: '<div class="popover" role="tooltip"><div class="arrow"></div><div class="popover-content">Cet événement est récurrent.</div><div class="modal-footer"><div><button type="button" class="btn btn-primary" onclick="confirm_edit_event_norecurrence()">Seulement cet événement</button><button type="button" class="btn btn-default" onclick="confirm_edit_event_withrecurrence()">&Eacute;vénements à venir</button></div><button type="button" class="btn btn-default" onclick="abort_edit_event()">Annuler</button></div></div>',
		});
	}
}*/
	
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
	if(!$("#edit_private_event .edit").attr("disabled")){
		//prevent the modal to hide before we either confirm the new note or we abort
		$(".modal-backdrop").off("click");
		//prevent the button from being pressed again
		$("#edit_private_event .edit").attr("disabled",true);
		//make all event info editable
		$("#event_place").prop('contenteditable',"true");
		$("#event_place").addClass("box");
		$("#event_details").prop('contenteditable',"true");
		$("#event_details").addClass("box");
		//save current event info
		event_date_start=$("#startDate").text();
		event_date_end=$("#endDate").text();
		event_place=$("#event_place").text();
		event_details=$("#event_details").text();
		//build the date picker element
		$("#startDate").html('<p><input id="startDate_datepicker" onclick="setSens(\'endDate_datepicker\', \'max\', \'existing_event\');" readonly="true"><label class="common_text margin-left-10">Commence</label></p>');
		$("#endDate").html('<p><input id="endDate_datepicker" onclick="setSens(\'startDate_datepicker\', \'min\',\'existing_event\');" readonly=true"><label class="common_text margin-left-10">Se termine</label></p><p>Jour entier?<input type="checkbox" id="entire_day_checkbox" onclick="entire_day("existing_event_datepicker")"></p>');
		buildDatePicker("existing_event");
		//display save, abort buttons
		$("#edit_event_btns").removeClass("hidden");
	}
}
	
//abort edit info
function abort_edit_event(){
	//bind edit button to handler
	$("#edit_private_event .edit").attr("disabled",false);
	//rollback event info
	$("#startDate").html(event_date_start);
	$("#endDate").html(event_date_start);
	$("#event_place").text(event_place);
	$("#event_details").text(event_details);
	//make all event info non editable
	$("#event_place").prop('contenteditable',"false");
	$("#event_place").removeClass("box");
	$("#event_details").prop('contenteditable',"false");
	$("#event_details").removeClass("box");
	//hide save, abort buttons
	$("#edit_event_btns").addClass("hidden");
	//re-enable the backdrop of the modal (when clicking outside of the modal it closes)
	$(".modal-backdrop").on("click",function(){$("#event_info").modal("hide")});
	}
	
//confirm edit event
function confirm_edit_event(){
	//bind edit button to handler
	$("#edit_private_event .edit").attr("disabled",false);
	//make all event info non editable
	$("#event_place").prop('contenteditable',"false");
	$("#event_place").removeClass("box");
	$("#event_details").prop('contenteditable',"false");
	$("#event_details").removeClass("box");
	$("#startDate").html($("#startDate_datepicker").val());
	$("#endDate").html($("#endDate_datepicker").val());
	//hide save, abort buttons
	$("#edit_event_btns").addClass("hidden");
	//re-enable the backdrop of the modal (when clicking outside of the modal it closes)
	$(".modal-backdrop").on("click",function(){$("#event_info").modal("hide")});
	//send new data to server
	//TODO	
	}
	
//builds the object datepicker
function buildDatePicker(option,target) {
	//convert target date to format DD-MM-YYYY
	target=convert_date(target,"DD-MM-YYYY","YYYY-MM-DD");
	//prepare elements to which datepicker has to be attached
	var elements=[];
	//datepicker to be built for the existing event panel
	if(option=="existing_event"){
		elements.push($("#startDate_datepicker"),$("#endDate_datepicker"));
		//check how many date pickers we have to initialize, eg. for allday events there's only one to be initialized
		if(!$("#endDate").hasClass("hidden")){
			datepicker["existing_event"] = new dhtmlXCalendarObject([elements[0].attr("id"),elements[1].attr("id")]);
			//datepicker["existing_event"].setDate(convert_date(event_date_start,"DD-MM-YYYY"),convert_date(event_date_end,"DD-MM-YYYY"));
		}
		else {
			datepicker["existing_event"] = new dhtmlXCalendarObject(elements[0].attr("id"));
			//datepicker["existing_event"].setDate(convert_date(event_date_start,"DD-MM-YYYY"));
		}
		//set date format
		datepicker["existing_event"].setDateFormat("%d-%m-%Y");
		byId("startDate_datepicker").value = convert_date(event_date_start,"dddd DD MMM YYYY");
		if($("#endDate_datepicker").length>0)
			byId("endDate_datepicker").value = convert_date(event_date_end,"dddd DD MMM YYYY");
		//convert the date returned from the datepicker to the format "dddd DD MMM YYYY"	
		datepicker["existing_event"].attachEvent("onClick", function(date){
			elements[0].val(convert_date(elements[0].val(),"dddd DD MMM YYYY"));
			elements[1].val(convert_date(elements[1].val(),"dddd DD MMM YYYY"));
		});
	}
	//datepicker to be built for the new event panel
	else {
		elements.push($("#private_event_startDate_datepicker"),$("#private_event_endDate_datepicker"));
		datepicker["private_event"] = new dhtmlXCalendarObject([elements[0].attr("id"),elements[1].attr("id")]);
		//set date format
		datepicker["private_event"].setDateFormat("%d-%m-%Y");
		datepicker["private_event"].setDate(target);	
		elements[0].val(convert_date(target,"dddd DD MMM YYYY"));
		elements[1].val(convert_date(target,"dddd DD MMM YYYY"));
		//convert the date returned from the datepicker to the format "dddd DD MMM YYYY"	
		datepicker["private_event"].attachEvent("onClick", function(date){
			elements[0].val(convert_date(elements[0].val(),"dddd DD MMM YYYY"));
			elements[1].val(convert_date(elements[1].val(),"dddd DD MMM YYYY"));
		});
	}
}

//defines valid interval of dates for the date picker
function setSens(id, k, datepicker_instance) {
	// update range
	if (k == "min")
		datepicker[datepicker_instance].setSensitiveRange(convert_date(byId(id).value,"DD-MM-YYYY"), null);
	else datepicker[datepicker_instance].setSensitiveRange(null, convert_date(byId(id).value,"DD-MM-YYYY"));
}

//returns elements matching given id
function byId(id) {
	return document.getElementById(id);
}

function convert_month(month){
	switch(month){
		case "janv.":
			return "01";
			break;
		case "févr.":
			return "02";
			break;
		case "mars":
			return "03";
			break;
		case "avri":
			return "04";
			break;
		case "mai":
			return "05";
			break;
		case "juin":
			return "06";
			break;
		case "juil.":
			return "07";
			break;
		case "août":
			return "08";
			break;
		case "sept.":
			return "09";
			break;
		case "octo.":
			return "10";
			break;
		case "nove.":
			return "11";
			break;
		case "dece.":
			return "12";
			break;
		
		}
	}

//converts date formats	
function convert_date(date,formatDestination,formatOrigin){
		var dd;
		var mm;
		var yy;
		var chunks=date.split(" ");
		//date can be in the format "dd-mm-yyy", "dddd DD MM YYY" or yyyy-mm-dd
		if(chunks.length>1){
			dd=chunks[1];
			mm=convert_month(chunks[2]);
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
		moment.locale('fr'); 
		return d.format(formatDestination);
	}
	
//sets the event recurrence
function update_recurrence(recurrence){
	$("#recurrence").text(recurrence);
	}
	
//enable nev event confirm button only when requierd fields are inserted
$('#private_event_title').keyup(function () {
    if( $('#private_event_title').val().length > 0) {
        $('#new_event_btns .btn-primary').prop("disabled", false);
    } else {
        $('#new_event_btns .btn-primary').prop("disabled", true);
    }   
});
	
//reset new event modal content before display
$('#private_event').on('show.bs.modal', function (e) {
  $("#private_event_title").val("");
	$("#private_event_startHour").val("");
	$("#private_event_endHour").val("");
	$("#recurrence").text("jamais");
	$("#private_event_place").val("");
	$("#private_event_details").val("");
	$("#private_notes_body").val("");
	$("#new_event_btns").removeClass("hidden");
	$('#new_event_btns .btn-primary').prop("disabled", true);
})

//setup timepickers of new event modal
$("#private_event_startHour").timepicker();
$("#private_event_endHour").timepicker();

//populate private event modal
function populate_private_event(event){
	var title=event.title;
	var start=event.start;
	var end=event.end;
	var place=event.place;
	var details=event.details;
	var notes=event.notes;
	//populate modal title
	$("#private_event_modal_header").text(title);
	//adds an edit icon next to title
	$("#edit_private_event").removeClass('hidden');
	$("#private_event_modal_header").addClass("float-left-10padright");
	//populate modal fields
	$("#private_event_title").val(title);
	$("#private_event_title").prop("disabled",true);
	$("#private_event_startDate_datepicker").val(start);
	$("#private_event_startDate_datepicker").prop("disabled",true);
	$("#private_event_endDate_datepicker").val(end);
	$("#private_event_endDate_datepicker").prop("disabled",true);
	$("#private_event_place").val(place);
	$("#private_event_place").prop("disabled",true);
	$("#private_event_details").val(details);
	$("#private_event_details").prop("disabled",true);
	$("#private_notes_body").val(notes);
	$("#private_notes_body").prop("disabled",true);
	//hides button used when creating a new event
	$("#new_event_btns").addClass("hidden");
	}
	
function populate_public_event(event){
	var event_title=event.title;
	$("#event-title").text(event_title);
	}
	
//update the calendar with the new event
function create_private_event(){
	
	var title=$("#private_event_title").val();
	var start=convert_date($("#private_event_startDate_datepicker").val(), "YYYY-MM-DD");
	var startHour=$("#private_event_startHour").val();
	if(startHour)
		start=start+"T"+startHour;
	var end=convert_date($("#private_event_endDate_datepicker").val(), "YYYY-MM-DD");
	var endHour=$("#private_event_endHour").val();
	if(endHour)
		end=end+"T"+endHour;
	var place=$("#private_event_place").val();
	var details=$("#private_event_details").val();
	var notes=$("#private_notes_body").val();
	
	$('#calendar').fullCalendar('addEventSource', {
		events:[{
			id: 10000, //retrive unique ID from server
			private: true,
			title: title,
			start: start,
			end: end,
			place: place,
			details: details,
			notes: notes,
			color: "#8AC007",
			editable: true
			}]
		} 
	)
	
	//hide the modal
	$("#private_event").modal("hide");
}