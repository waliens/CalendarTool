// JavaScript Document
var filters={view:"month" ,all:"true",dateRange: {start: "01-03-2015", end: "31-03-2015"},courses: {isSet: 'false', id:[]},eventTypes: {isSet: 'false', id:[]},pathways: {isSet: 'false', id:[]},professors:{isSet: 'false', id:[]}};
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
var edit_existing_event=false;
//vars to handle the definition of event notes
var edit_existing_note=false;
var existing_note_content;
//private event vars
var event_id;
var event_date_start;
var event_date_end;
var event_all_day;
var event_place;
var event_details;
var event_recursive=false;
//dates picker
var datepicker = {"existing_event":0,"private_event":0,"recurrence_end":0};
var existing_event_datepicker;
var new_event_datepicker;
//holds the private event on click in case of update of its data
var private_event;
var modal_shown;
//holds displayed events ids in order not to duplicate them when new data is retrieved after changing the view mode 
//var displayed_events=[];

//update the navbar
$("#navbar li").removeClass("active");
$("#calendar_nav").addClass("active");

//when clicking on today, next, prev, dayview, monthview, weekview we need to load new events (potentially)
$("#calendar").on("click",[".fc-agendaDay-button",".fc-agendaWeek-button",".fc-month-button",".fc-today-button",".fc-icon-right-single-arrow",".fc-icon-left-single-arrow"],function(){
	addEvents();
	});
	
function getCurrentView(view){
	switch (view){
		case "month":
		return view;
		case "agendaWeek":
		return "week";
		case "agendaDay":
		return "day";
		}
	}

//add events to the calendar when changing the view
function addEvents(){
	$("#calendar").fullCalendar( 'removeEvents');
	var current_view=$("#calendar").fullCalendar( 'getView' ).name;
	filters.view=getCurrentView(current_view);
	filters.dateRange.start=$("#calendar").fullCalendar( 'getView' ).start.format("YYYY-MM-DD");
	filters.dateRange.end=$("#calendar").fullCalendar( 'getView' ).end.format("YYYY-MM-DD");
	//we have to take into account the fact that server side date ranges are inclusive and so for all views but the day view we have to subtract 1 to the right boundary
	if(filters.dateRange.end!=filters.dateRange.start)
		filters.dateRange.end=$("#calendar").fullCalendar( 'getView' ).end.subtract(1, 'days').format("YYYY-MM-DD");
	$('#calendar').fullCalendar('addEventSource', {
			events:function(start, end, timezone, callback){
			$.ajax({
				dataType : "json",
				type : 'POST',
				data: filters,
				url: "index.php?src=ajax&req=102",
				success : function(data, status) {
					/** error checking */
					if(data.error.error_code > 0)
					{	
						launch_error_ajax(data.error);
						return;
					}

					calendar_data=data;
					var events = [];
					//retireve all public events first
					for(var i=0;i<calendar_data.events.public.length;i++){
						var instance = calendar_data.events.public[i];
						//chech the event type to accordingly set the event color
						var color=getEventColor(instance);
						//if the event is not already displayed we add its id to the list of displayed events and we display it
						//if($.inArray(instance.id,displayed_events)==-1){
							events.push({
								id_server: instance.id,
								id: guid(),
								private: false,
								title: instance.name,
								start: instance.start,
								end: instance.end,
								recursive: instance.recursive,
								color: color,
								editable: false
							});
							//displayed_events.push(instance.id);
						//}
					}
					//then retrieve private events
					for(var i=0;i<calendar_data.events.private.length;i++){
						var instance=calendar_data.events.private[i];
						//if the event is not already displayed we add its id to the list of displayed events and we display it
						//if($.inArray(instance.id,displayed_events)==-1){
							events.push({
								id_server: instance.id,
								id: guid(),
								private: true,
								title: instance.name,
								start: instance.start,
								end: instance.end,
								recursive: instance.recursive,
								color: '#8AC007'
							});
							//displayed_events.push(instance.id);
						//}
					}
					callback(events);
				},
				error : function(data, status, errors) {
					launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
				}
			});
			}
			} 
		)
	}

$(document).ready(function() {
	//set moment locale to french
	moment.locale('fr');
	//initialize the calendar...
    $('#calendar').fullCalendar({
		lang: 'fr',
		nextDayThreshold : "00:00:00",
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
				type : 'POST',
				data: filters,
				url: "index.php?src=ajax&req=102",
				success : function(data, status) {
					/** error checking */
					if(data.error.error_code > 0)
					{	
						launch_error_ajax(data.error);
						return;
					}

					calendar_data=data;
					var events = [];
					//retireve all public events first
					for(var i=0;i<calendar_data.events.public.length;i++){
						var instance = calendar_data.events.public[i];
						//chech the event type to accordingly set the event color
						var color=getEventColor(instance);
						//if the event is not already displayed we add its id to the list of displayed events and we display it
						//if($.inArray(instance.id,displayed_events)==-1){
							events.push({
								id_server: instance.id,
								id: guid(),
								private: false,
								title: instance.name,
								start: instance.start,
								end: instance.end,
								recursive: instance.recursive,
								color: color,
								editable: false
							});
							//displayed_events.push(instance.id);
						//}
					}
					//then retrieve private events
					for(var i=0;i<calendar_data.events.private.length;i++){
						var instance=calendar_data.events.private[i];
						//if the event is not already displayed we add its id to the list of displayed events and we display it
						//if($.inArray(instance.id,displayed_events)==-1){
							events.push({
								id_server: instance.id,
								id: guid(),
								private: true,
								title: instance.name,
								start: instance.start,
								end: instance.end,
								recursive: instance.recursive,
								color: '#8AC007'
							});
							//displayed_events.push(instance.id);
						//}
					}
					callback(events);
				},
				error : function(data, status, errors) {
					launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
				}
			});
			},
			//handle click on event
		eventClick: function(calEvent, jsEvent, view) {
			var event_private=calEvent.private;
			event_id=calEvent.id;
			//check event type to call proper modal
			if(event_private){
				private_event=calEvent;
				//set recursion var
				if(calEvent.recursive)
					event_recursive=true;
				else event_recursive=false;
				populate_private_event(calEvent);
				$("#private_event").attr("event-id",calEvent.id_server);
				$("#private_event").modal("show");
				modal_shown="#private_event";
				}
			else{
				$("#event_info").attr("event-id",calEvent.id_server);
				$("#event_info").modal("show");
				modal_shown="#event_info";
				populate_public_event(calEvent);
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
				$("#event_owner").text(calEvent.owner);
				$("#event_owner").parent().parent().removeClass("hidden");
				$("#event_details").text(calEvent.details);
				//check if the event has notes or not
				if($("#notes_body")){
					$("#add_notes").addClass("hidden");
					$("#notes").removeClass("hidden");
					$("#notes_body").text(calEvent.notes);
				}
				else{
					$("#add_notes").removeClass('hidden');
					$("#notes").addClass("hidden");
					}
				}
			
		},
		
		//handle clicks within the calendar
		dayClick: function(date, jsEvent, view) {
			var target = date.format();
			buildDatePicker("private_event",target);
			$("#private_event_title").prop("readonly",false);
			$("#private_event_startDate_datepicker").prop("disabled",false);
			$("#private_event_startDate_datepicker").prop("readonly",true);
			$("#private_event_endDate_datepicker").prop("disabled",false);
			$("#private_event_endDate_datepicker").prop("readonly",true);
			$("#private_event_place").prop("readonly",false);
			$("#recurrence_btn").prop("disabled",false);
			$("#private_event_type_btn").prop("disabled",false);
			$("#private_event_details").prop("readonly",false);
			$("#deadline input").prop("disabled",false);
			//$("#private_event_startHour").prop("readonly",false);
			//$("#private_event_endHour").prop("readonly",false);
			$("#private_notes_body").prop("readonly",false);
			$("#edit_event_btns").removeClass("hidden");
			$("#private_event").modal("show");
			$("#edit_private_event").addClass('hidden');
			$("#delete_private_event").addClass('hidden');
			$("#private_event_title").focus();
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
	var id_event=$(modal_shown).attr("event-id");
	$("#add_notes").removeClass("hidden");
	$("#notes").addClass("hidden");
	$("#notes_body").text("");
	//Send delete confirmation to server
	$.ajax({
			dataType : "json",
			type : 'POST',
			url : "index.php?src=ajax&req=044",
			data : {"id_event":id_event},
			success : function(data, status) {
				/** error checking */
				if(data.error.error_code > 0)
				{	
					launch_error_ajax(data.error);
					return;
				}
				// TODO
			},
			error : function(data, status, errors) {
				launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
			}
		});
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
	var note=$("#notes_body").text();
		var id_event=$(modal_shown).attr("event-id");
		$("#mod_notes_btns").addClass("hidden");
		$("#notes_body").prop('contenteditable',"false");
		$("#notes_body").removeClass("box");
		$("#edit_note").removeClass("hidden");
		$("#delete_note").removeClass("hidden");
		//re-enable the backdrop of the modal (when clicking outside of the modal it closes)
		$(".modal-backdrop").on("click",function(){$("#event_info").modal("hide")});
	if(edit_existing_note){
		//send new data to server
		$.ajax({
				dataType : "json",
				type : 'POST',
				url : "index.php?src=ajax&req=043",
				data : {"id_event":id_event,"note":note},
				success : function(data, status) {
					/** error checking */
					if(data.error.error_code > 0)
					{	
						launch_error_ajax(data.error);
						return;
					}
					//TODO
				},
				error : function(data, status, errors) {
					launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
				}
			});
		}
	else{
		//send new data to server
		$.ajax({
				dataType : "json",
				type : 'POST',
				url : "index.php?src=ajax&req=042",
				data : {"id_event":id_event,"note":note},
				success : function(data, status) {
					/** error checking */
					if(data.error.error_code > 0)
					{	
						launch_error_ajax(data.error);
						return;
					}
					//TODO
				},
				error : function(data, status, errors) {
					launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
				}
			});
	}
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
		edit_existing_event=true;
		//prevent the modal to hide before we either confirm the new note or we abort
		$(".modal-backdrop").off("click");
		//prevent the button from being pressed again
		$("#edit_private_event .edit").attr("disabled",true);
		//make all event info editable
		$("#private_event_title").prop("readonly",false);
		$("#private_event_startDate_datepicker").prop("disabled",false);
		$("#deadline input").prop("disabled",false);
		$("#private_event_startHour").removeClass("hidden");
		$("#private_event_endDate").parent().removeClass("hidden");
		$("#private_event_endDate_datepicker").prop("disabled",false);
		$("#private_event_endDate_datepicker").removeClass("hidden");
		//$("#private_event_endHour").prop("readonly",false);
		$("#private_event_endHour").removeClass("hidden");
		$("#private_event_place").prop("readonly",false);
		$("#private_event_place").removeClass("hidden");
		$("#private_event_details").prop("readonly",false);
		$("#private_event_details").removeClass("hidden");
		$("#recurrence_btn").prop("disabled",false);
		$("#private_event_type_btn").prop("disabled",false);
		$("#private_notes_body").prop("readonly",false);
		$("#private_notes_body").parent().parent().removeClass("hidden");
		$("#edit_event_btns").removeClass("hidden");
		$("#edit_event_btns .btn-primary").prop("disabled",false);
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
	
//builds the object datepicker
function buildDatePicker(option,target) {
	//convert target date to format DD-MM-YYYY
	if(target)
		target=convert_date(target,"DD-MM-YYYY","YYYY-MM-DD");
	//prepare elements to which datepicker has to be attached
	var elements=[];
	//datepicker to be built for the existing event panel
	if(option=="existing_event"){
		elements.push($("#startDate_datepicker"),$("#endDate_datepicker"));
		//check how many date pickers we have to initialize, eg. for allday events there's only one to be initialized
		if(!$("#endDate").hasClass("hidden"))
			datepicker[option] = new dhtmlXCalendarObject([elements[0].attr("id"),elements[1].attr("id")]);
		else datepicker[option] = new dhtmlXCalendarObject(elements[0].attr("id"))
		//set date format
		datepicker[option].setDateFormat("%d-%m-%Y");
		byId("startDate_datepicker").value = convert_date(event_date_start,"dddd DD MMM YYYY");
		if($("#endDate_datepicker").length>0)
			byId("endDate_datepicker").value = convert_date(event_date_end,"dddd DD MMM YYYY");
		//convert the date returned from the datepicker to the format "dddd DD MMM YYYY"	
		datepicker[option].attachEvent("onClick", function(date){
			elements[0].val(convert_date(elements[0].val(),"dddd DD MMM YYYY"));
			elements[1].val(convert_date(elements[1].val(),"dddd DD MMM YYYY"));
		});
	}
	//datepicker to be built for the end recursion
	else if(option=="recurrence_end"){
		datepicker[option] = new dhtmlXCalendarObject("recurrence_end");
		datepicker[option].setDateFormat("%d-%m-%Y");
		setSens("private_event_endDate_datepicker","min","recurrence_end");
		//convert the date returned from the datepicker to the format "dddd DD MMM YYYY"	
		datepicker[option].attachEvent("onClick", function(date){
			$("#recurrence_end").val(convert_date($("#recurrence_end").val(),"dddd DD MMM YYYY"));
		});
		}
	
	//datepicker to be built for the new event panel
	else {
		elements.push($("#private_event_startDate_datepicker"),$("#private_event_endDate_datepicker"));
		datepicker[option] = new dhtmlXCalendarObject([elements[0].attr("id"),elements[1].attr("id")]);
		//set date format
		datepicker[option].setDateFormat("%d-%m-%Y");
		datepicker[option].setDate(target);	
		elements[0].val(convert_date(target,"dddd DD MMM YYYY"));
		elements[1].val(convert_date(target,"dddd DD MMM YYYY"));
		//convert the date returned from the datepicker to the format "dddd DD MMM YYYY"	
		datepicker[option].attachEvent("onClick", function(date){
			elements[0].val(convert_date(elements[0].val(),"dddd DD MMM YYYY"));
			elements[1].val(convert_date(elements[1].val(),"dddd DD MMM YYYY"));
		});
	}
	//hide the time in the datepicker tool
	datepicker[option].hideTime();
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
	
//sets the event recurrence
function updateRecurrence(){
	$("#recurrence").text(event.target.innerHTML);
	if(event.target.innerHTML!="jamais"){
		$("#recurrence_end_td").removeClass("hidden");
		//build date picker of the end recurrence input
		buildDatePicker("recurrence_end");
		}
	else $("#recurrence_end_td").addClass("hidden");
	}
	
//enable nev event confirm button only when requierd fields are inserted
$('#private_event_title').keyup(function () {
    if( $('#private_event_title').val().length > 0) {
        $('#edit_event_btns .btn-primary').prop("disabled", false);
    } else {
        $('#edit_event_btns .btn-primary').prop("disabled", true);
    }   
});
	
//reset new event modal content after display
$('#private_event').on('hidden.bs.modal', function (e) {
	edit_existing_event=false;
	$("#private_event_title").val("");
	$("#deadline input").prop("checked",false);
	$("#private_event_startHour").val("");
	$("#private_event_startHour").parent().parent().removeClass("hidden");
	$("#private_event_startHour").removeClass("hidden");
	$("#private_event_endHour").val("");
	$("#private_event_endHour").parent().parent().removeClass("hidden");
	$("#private_event_endHour").removeClass("hidden");
	$("#recurrence").text("jamais");
	$("#recurrence_end_td").addClass("hidden");
	$("#private_event_type").text("Travail");
	$("#private_event_place").val("");
	$("#private_event_place").parent().parent().removeClass("hidden");
	$("#private_event_details").val("");
	$("#private_event_details").parent().parent().removeClass("hidden");
	$("#private_notes_body").parent().parent().removeClass("hidden");
	$("#private_notes_body").val("");
	$("#edit_event_btns").addClass("hidden");
	$('#edit_event_btns .btn-primary').prop("disabled", true);
	$("#edit_private_event .edit").attr("disabled",false);
})

//setup timepickers of new event modal
$(".time").timepicker({ 'forceRoundTime': true });
$("#private_event_endHour").on("changeTime",function(){
	$("#private_event_startHour").timepicker("option",{maxTime:$("#private_event_endHour").val()});
	})
$("#private_event_startHour").on("changeTime",function(){
	$("#private_event_endHour").timepicker("option",{minTime:$("#private_event_startHour").val(), maxTime:"24:00"});
	})
//populate private event modal
function populate_private_event(event){
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
	
function populate_public_event(event){
	var event_id=event.id_server;
	$.ajax({
		dataType : "json",
		type : 'GET',
		url : "index.php?src=ajax&req=051&event="+event_id,
		async : true,
		success : function(data, status) {
			/** error checking */
			if(data.error.error_code > 0)
			{	
				launch_error_ajax(data.error);
				return;
			}

			//{id, name, description, place, type, startDay, endDay, startTime, endTime, deadline, category_id, category_name, recurrence, start_recurrence, end_recurrence, favourite}
			$("#event-title").text(data.name);
			$("#event_place").text(data.place);
			$("#event_owner").text(data.professor);
			$("#event_details").text(data.description);
			$("#event_category").text(data.category_name);
			$("#event_category").attr("category-id",data.category_id);
			$("#notes_body").text(data.annotation);
		},
		error : function(data, status, errors) {
			launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
		}
	});
	}
	
//update the calendar with the new event or confirm the edit of an existing event
function create_private_event(){
	var title=$("#private_event_title").val();
	var type=$("#private_event_type").text();
	var start=moment(convert_date($("#private_event_startDate_datepicker").val(), "YYYY-MM-DD"));
	var startHour=$("#private_event_startHour").val();
	if(startHour!=""){
		//divide minutes from hours
		startHour=startHour.split(":");
		start.minute(startHour[1]);
		start.hour(startHour[0]); 
	}
	var end=moment(convert_date($("#private_event_endDate_datepicker").val(), "YYYY-MM-DD"));
	var endHour=$("#private_event_endHour").val();
	if(endHour!=""){
		//divide minutes from hours
		endHour=endHour.split(":");
		end.minute(endHour[1]);
		end.hour(endHour[0]); 
	}
	//check if the event is an allDay event
	var allDay=false;
	if(!startHour && !endHour)
		allDay=true;
	var limit=false;
	if($("#deadline input").prop("checked"))
		limit=true;
	var recurrence=$("#recurrence").text();
	var recurrence_id=0;
	var end_recurrence;
	var place=$("#private_event_place").val();
	var details=$("#private_event_details").val();
	var notes=$("#private_notes_body").val();
	//check if we are adding a new private event
	if(!edit_existing_event){
		var id=guid();
		//check if the event is recursive
		if(recurrence!="jamais"){
			end_recurrence=$("#recurrence_end").val();
			if(end_recurrence!="")
				end_recurrence=moment(convert_date(end_recurrence, "YYYY-MM-DD"));
			var offset;
			switch(recurrence){
				case "tous les jours":
					offset=1;
					recurrence_id=1;
					//if user doesn't specify end of the recursion we set it to one year
					if(end_recurrence==""){
						end_recurrence=new moment(start);
						end_recurrence.add(1,"year");
					}
					//build start date in format required by fullcalendar.io
					var event_start;
					var event_end;
					var id_event=guid();
					while(start.isBefore(end_recurrence)){
						if(startHour!="")
							event_start=start.format("YYYY-MM-DD")+"T"+startHour[0]+":"+startHour[1];
						else event_start=start.format("YYYY-MM-DD")
						if(endHour!="")
							event_end=end.format("YYYY-MM-DD")+"T"+endHour[0]+":"+endHour[1];
						else event_end=end.format("YYYY-MM-DD")
						var new_private_event={"name":title, "start":event_start, "end":event_end, "recurrence": recurrence_id, "end-recurrence":end_recurrence.format("YYYY-MM-DD"), "place":place, "details":details, "note":notes, "type":"11"}
						$.ajax({
							dataType : "json",
							type : 'POST',
							url : "index.php?src=ajax&req=061",
							data : new_private_event,
							success : function(data, status) {
								/** error checking */
								if(data.error.error_code > 0)
								{	
									launch_error_ajax(data.error);
									return;
								}

								$('#calendar').fullCalendar('addEventSource', {
									events:[{
										id_server: id,
										id: id_event,
										private: true,
										title: title,
										start: event_start,
										end: event_end,
										allDay: allDay,
										place: place,
										details: details,
										notes: notes,
										color: "#8AC007",
										editable: true
										}]
									} 
								)
							},
							error : function(data, status, errors) {
								launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
							}
						});
						start.add(offset,"day");
						end.add(offset,"day");
					}
					break;
				case "tous les semaines":
					offset=7;
					recurrence_id=2;
					//if user doesn't specify end of the recursion we set it to one year
					if(end_recurrence==""){
						end_recurrence=new moment(start);
						end_recurrence.add(1,"year");
					}
					//build start date in format required by fullcalendar.io
					var event_start;
					var event_end;
					var id_event=guid();
					while(start.isBefore(end_recurrence)){
						if(startHour!="")
							event_start=start.format("YYYY-MM-DD")+"T"+startHour[0]+":"+startHour[1];
						else event_start=start.format("YYYY-MM-DD")
						if(endHour!="")
							event_end=end.format("YYYY-MM-DD")+"T"+endHour[0]+":"+endHour[1];
						else event_end=end.format("YYYY-MM-DD");
						$('#calendar').fullCalendar('addEventSource', {
							events:[{
								id_server: id,
								id: id_event,
								private: true,
								title: title,
								start: event_start,
								end: event_end,
								allDay: allDay,
								place: place,
								details: details,
								notes: notes,
								color: "#8AC007",
								editable: true
								}]
							} 
						)
						start.add(offset,"day");
						end.add(offset,"day");
					}
					break;
				case "tous les deux semaines":
					offset=14;
					recurrence_id=3
					//if user doesn't specify end of the recursion we set it to one year
					if(end_recurrence==""){
						end_recurrence=new moment(start);
						end_recurrence.add(1,"year");
					}
					//build start date in format required by fullcalendar.io
					var event_start;
					var event_end;
					var id_event=guid();
					while(start.isBefore(end_recurrence)){
						if(startHour!="")
							event_start=start.format("YYYY-MM-DD")+"T"+startHour[0]+":"+startHour[1];
						else event_start=start.format("YYYY-MM-DD")
						if(endHour!="")
							event_end=end.format("YYYY-MM-DD")+"T"+endHour[0]+":"+endHour[1];
						else event_end=end.format("YYYY-MM-DD");
						$('#calendar').fullCalendar('addEventSource', {
							events:[{
								id_server: id,
								id: id_event,
								private: true,
								title: title,
								start: event_start,
								end: event_end,
								allDay: allDay,
								place: place,
								details: details,
								notes: notes,
								color: "#8AC007",
								editable: true
								}]
							} 
						)
						start.add(offset,"day");
						end.add(offset,"day");
					}
					break;
				case "tous les mois":
					offset=1;
					recurrence_id=4;
					//if user doesn't specify end of the recursion we set it to one year
					if(end_recurrence==""){
						end_recurrence=new moment(start);
						end_recurrence.add(1,"year");
					}
					//build start date in format required by fullcalendar.io
					var event_start;
					var event_end;
					var id_event=guid();
					while(start.isBefore(end_recurrence)){
						if(startHour!="")
							event_start=start.format("YYYY-MM-DD")+"T"+startHour[0]+":"+startHour[1];
						else event_start=start.format("YYYY-MM-DD")
						if(endHour!="")
							event_end=end.format("YYYY-MM-DD")+"T"+endHour[0]+":"+endHour[1];
						else event_end=end.format("YYYY-MM-DD");
						$('#calendar').fullCalendar('addEventSource', {
							events:[{
								id_server: id,
								id: id_event,
								private: true,
								title: title,
								start: event_start,
								end: event_end,
								allDay: allDay,
								place: place,
								details: details,
								notes: notes,
								color: "#8AC007",
								editable: true
								}]
							} 
						)
						start.add(offset,"month");
						end.add(offset,"month");
					}
					break;
				case "tous les ans":
					offset=1;
					recurrence_id=5;
					//if user doesn't specify end of the recursion we set it to one year
					if(end_recurrence==""){
						end_recurrence=new moment(start);
						end_recurrence.add(10,"year");
					}
					//build start date in format required by fullcalendar.io
					var event_start;
					var event_end;
					var id_event=guid();
					while(start.isBefore(end_recurrence)){
						if(startHour!="")
							event_start=start.format("YYYY-MM-DD")+"T"+startHour[0]+":"+startHour[1];
						else event_start=start.format("YYYY-MM-DD")
						if(endHour!="")
							event_end=end.format("YYYY-MM-DD")+"T"+endHour[0]+":"+endHour[1];
						else event_end=end.format("YYYY-MM-DD");
						$('#calendar').fullCalendar('addEventSource', {
							events:[{
								id_server: id,
								id: id_event,
								private: true,
								title: title,
								start: event_start,
								end: event_end,
								allDay: allDay,
								place: place,
								details: details,
								notes: notes,
								color: "#8AC007",
								editable: true
								}]
							} 
						)
						start.add(offset,"year");
						end.add(offset,"year");
					}
					break;
			}
		}
		else{
		//send data to server event with no recursion
		var new_event={"name":title, "start":start.format("YYYY-MM-DD"), "end":end.format("YYYY-MM-DD"), "limit":limit, "recurrence":recurrence_id, "end-recurrence":"", "place":place, "details":details, "note":notes, "type":3}
		$.ajax({
						dataType : "json",
						type : 'POST',
						url : "index.php?src=ajax&req=61",
						data : new_event,
						success : function(data, status) {
							/** error checking */
							if(data.error.error_code > 0)
							{	
								launch_error_ajax(data.error);
								return;
							}

							$('#calendar').fullCalendar('addEventSource', {
								events:[{
									id_server: id,
									id: guid(),
									private: true,
									title: title,
									start: start,
									end: end,
									allDay: allDay,
									place: place,
									details: details,
									notes: notes,
									color: "#8AC007",
									editable: true
									}]
								} 
							)
						},
						error : function(xhr, status, error) {
							launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
						}
					});
		}
	}
	//otherwise we are editing an existing one
	else{
		private_event.title=title;
		private_event.start=start;
		private_event.end=end;
		private_event.place=place;
		private_event.details=details;
		private_event.notes=notes;
		private_event.allDay=allDay;
		private_event.recurrence=recurrence;
		$('#calendar').fullCalendar('updateEvent', private_event);
		//send update to server
		}
	//hide the modal
	$("#private_event").modal("hide");
	
}

//delete private event
function delete_private_event(){
	$('#calendar').fullCalendar('removeEvents', event_id);
	//hide the modal
	$("#private_event").modal("hide");
	//send delete to server
	}

//generate unique id for new private events
function guid() {
  function s4() {
    return Math.floor((1 + Math.random()) * 0x10000)
      .toString(16)
      .substring(1);
  }
  return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
    s4() + '-' + s4() + s4() + s4();
}

//change the value of the dropdown stating the private event type
function changePrivateEventType(){
	$("#private_event_type").text(event.target.innerHTML);
	}
	
function deadline(){
	$("#private_event_endDate").parent().toggleClass("hidden");
}
	
/*-----------------------------------------------------*/	
/*--------------------FILTERS--------------------------*/
/*-----------------------------------------------------*/	

//datepickers
var startDate;
var endDate;
 
  
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
				$(this).find('.modal-body').html("<p><input id='startDateFilter' onclick=\"setSensFilter(\'endDateFilter\',\'max\');\" readonly='true'><label class='common_text margin-left-10'>à partir</label></p><p><input id='endDateFilter' onclick=\"setSensFilter(\'startDateFilter\',\'min\');\" readonly='true'><label class='common_text margin-left-10'>de</label></p>");
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
							/** error checking */
							if(data.error.error_code > 0)
							{	
								launch_error_ajax(data.error);
								return;
							}

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
							launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
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
							/** error checking */
							if(data.error.error_code > 0)
							{	
								launch_error_ajax(data.error);
								return;
							}

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
							launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
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
							/** error checking */
							if(data.error.error_code > 0)
							{	
								launch_error_ajax(data.error);
								return;
							}

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
							launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
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
							/** error checking */
							if(data.error.error_code > 0)
							{	
								launch_error_ajax(data.error);
								return;
							}

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
							launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
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
							/** error checking */
							if(data.error.error_code > 0)
							{	
								launch_error_ajax(data.error);
								return;
							}

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
							launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
						}
					});
				break;
			}
		}
})



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
	/*startDate = new dhtmlXCalendarObject("startDateFilter");
	endDate = new dhtmlXCalendarObject("endDateFilter");
	startDate.setDateFormat("%Y-%m-%d");
	endDate.setDateFormat("%Y-%m-%d");
	startDate.setDate(td.format("YYYY-MM-DD"));
	byId("startDateFilter").value = td.format("dddd DD MM YYYY");
	endDate.setDate(td.add(1,"day").format("YYYY-MM-DD"));
	byId("endDateFilter").value = td.format("dddd DD MM YYYY");
	startDate.hideTime();
	endDate.hideTime();	
	startDate.setSensitiveRange(null,convert_date($("#endDateFilter").val(),"YYYY-MM-DD"));
	endDate.setSensitiveRange(convert_date($("#startDateFilter").val(),"YYYY-MM-DD"),null);
	//convert the date returned from the datepicker to the format "dddd DD MMM YYYY"	
	startDate.attachEvent("onClick", function(date){
		$("#startDateFilter").val(convert_date($("#startDateFilter").val(),"dddd DD MMM YYYY"));
	});
	endDate.attachEvent("onClick", function(date){
		$("#endDateFilter").val(convert_date($("#endDateFilter").val(),"dddd DD MMM YYYY"));
	});*/
}

function setSensFilter(id, k) {
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
		case "all_events_filter":
			$("#all_events_filter").prop('checked',true);
			$("#submit_filters").attr("disabled","disabled");
			//disable all other checkboxes
			var checkboxes=$('input');
			for(var i=0;i<checkboxes.length;i++){
				var item=checkboxes.get(i);
					if(item.id!="all_events_filter"){
						item.disabled = true;
						item.checked=false;
					}
			}
			//save filter info in the filter object
			filters.all="true";
			break;
		
		case "date_filter":
			//oldstart=moment(filters.dateRange["start"],"YYYY-MM-DD");
			newstart=moment(convert_date($("#startDateFilter").val(),"YYYY-MM-DD"));
			//oldend=moment(filters.dateRange["end"],"YYYY-MM-DD");
			newend=moment(convert_date($("#endDateFilter").val(),"YYYY-MM-DD"))
			//if(newstart.isAfter(oldstart)&&newstart.isBefore(oldend))
				filters.dateRange["start"]=newstart.format("YYYY-MM-DD");
			//if(newend.isBefore(oldend))
				filters.dateRange["end"]=newend.format("YYYY-MM-DD");
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
		case "all_events_filter":
			$('input').removeAttr("disabled");
			filters.all="false";
			break;
		case "date_filter":
			filters.dataRange.isSet="false";
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

//set and unset all events filter
$("#all_events_filter").click(function(){
	if($(this).prop('checked'))
		setFilter("all_events_filter");
	else unSetFilter("all_events_filter");
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

$("#filters input").click(function(){
	//enable the send form button only when at least one filter is selected
	if($("#filters input:checked").length>0)
		$("#submit_filters").attr("disabled",false);
	else $("#submit_filters").attr("disabled",true);
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
				/** error checking */
				if(data.error.error_code > 0)
				{	
					launch_error_ajax(data.error);
					return;
				}

				$("#dynamic_export_download_alert").modal("show");
				$("#dynamic_export_file").attr("href",data.url);
			},
			error : function(data, status, errors) {
				launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
			}
		});
});
	
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

//reset filters and retrieve all events belonging to the given view
function reset_filters(){
	//the reset is equivalent to select only the all events filter
	setFilter("all_events_filter");
	
	submit_filters();
	}

	
function submit_filters(){
	$("#calendar").fullCalendar( 'removeEvents');
	//displayed_events.length=0;
	$('#calendar').fullCalendar('addEventSource', {
			events:function(start, end, timezone, callback){
			$.ajax({
				dataType : "json",
				type : 'POST',
				data: filters,
				url: "index.php?src=ajax&req=102",
				success : function(data, status) {
					/** error checking */
					if(data.error.error_code > 0)
					{	
						launch_error_ajax(data.error);
						return;
					}

					calendar_data=data;
					var events = [];
					//retrieve all public events first
					for(var i=0;i<calendar_data.events.public.length;i++){
						var instance = calendar_data.events.public[i];
						//chech the event type to accordingly set the event color
						var color=getEventColor(instance);
						//if the event is not already displayed we add its id to the list of displayed events and we display it
						if(!$.inArray(instance.id,displayed_events)==-1){
							events.push({
								id_server: instance.id,
								id: guid(),
								private: false,
								title: instance.name,
								start: instance.start,
								end: instance.end,
								recursive: instance.recursive,
								color: color,
								editable: false
							});
							displayed_events.push(instance.id);
						}
					}
					//then retrieve private events
					for(var i=0;i<calendar_data.events.private.length;i++){
						var instance=calendar_data.events.private[i];
						//if the event is not already displayed we add its id to the list of displayed events and we display it
						if(!$.inArray(instance.id,displayed_events)==-1){
							events.push({
								id_server: instance.id,
								id: guid(),
								private: true,
								title: instance.name,
								start: instance.start,
								end: instance.end,
								recursive: instance.recursive,
								color: '#8AC007'
							});
							displayed_events.push(instance.id);
						}
					}
					callback(events);
				},
				error : function(xhr, status, error) {
					launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
				}
			});
			}
			} 
		)
	}