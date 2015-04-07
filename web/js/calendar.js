// JavaScript Document
var filters={view:"month" ,
			 allEvent:{isSet:"true"},
			 dateRange: {start: "", end: ""},
			 courses: {isSet: 'false', id:[]},
			 eventCategories: {isSet: 'false', id:[]},
			 eventTypes: {isSet: 'false', timeType:[], eventType:[]},
			 pathways: {isSet: 'false', id:[]},
			 professors:{isSet: 'false', id:[]}
			 };

var today = new Date();
var day = today.getDate();
var month = today.getMonth()+1; //January is 0!
var year = today.getFullYear();

//setup vars for semester view
var startSemester;
var endSemester;
if(month==1){//we are in January so we want to retrieve first semester
	startSemester=moment((year-1)+"-09-15");
	endSemester=moment(year+"-02-01");
}
else if(month>1&&month<=9){
	if(month==9&day>15){
		startSemester=moment(year+"-09-15");
		endSemester=moment((year+1)+"-02-01");
		}
		
	else {//we are in period between January and September 14 so we want to retrieve the second semester
		startSemester=moment(year+"-02-01");
		endSemester=moment(year+"-09-15");
		}
	}
else {//we are in the period between 15 Sep and 31 Dec so we want to retrieve the first semester
	startSemester=moment(year+"-09-15");
	endSemester=moment((year+1)+"-02-01");
	}	

//transform the month in two digits notation
if(month<10)
	month="0"+month;
if (day<10)
	day="0"+day;
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
var events_source=[];
var semester=false;
var nextSemester=false;
var prevSemester=false;


//update the navbar
$("#navbar li").removeClass("active");
$("#calendar_nav").addClass("active");
	
//get calendar current view name	
function getCurrentView(view){
	switch (view){
		case "month":
			return view;
		case "agendaWeek":
			return "week";
		case "agendaDay":
			return "day";
		case "agendaSixMonth":
			return "semester";
		}
	}

//add events to the calendar when changing the view
function addEvents(){
	
	//setting up filters daterange
	filters.dateRange.start=$("#calendar").fullCalendar( 'getView' ).start.format("YYYY-MM-DD");
	filters.dateRange.end= $("#calendar").fullCalendar( 'getView' ).end.format("YYYY-MM-DD");
	$(".fc-event-container").remove();
	$("#calendar").fullCalendar( 'removeEvents');
	var current_view=$("#calendar").fullCalendar( 'getView' ).name;
	if(current_view=="agendaSixMonth"&&!semester){	
		semester=true;
		$("#calendar").fullCalendar('gotoDate', startSemester);
		
	}
	else{
		var current_view=$("#calendar").fullCalendar( 'getView' ).name;
		if(current_view=="agendaSixMonth"){
			var date=$("#calendar").fullCalendar( 'getView' ).start;
			//hide dates that do not belong to the semester
			while(date.isBefore(moment(startSemester))){
				$("td .fc-day-number[data-date='"+date.format("YYYY-MM-DD")+"']").addClass("fc-other-month");
				date.add(1,"day");
			}
			date=$("#calendar").fullCalendar( 'getView' ).end;
			while(date.isAfter(moment(endSemester).subtract(1,"day"))){
				$("td .fc-day-number[data-date='"+date.format("YYYY-MM-DD")+"']").addClass("fc-other-month");
				date.subtract(1,"day");
			}
			semester=false;
		}
	filters.view=getCurrentView(current_view);
	filters.dateRange.start=$("#calendar").fullCalendar( 'getView' ).start.format("YYYY-MM-DD");
	filters.dateRange.end=$("#calendar").fullCalendar( 'getView' ).end.format("YYYY-MM-DD");
	//we have to take into account the fact that server side date ranges are inclusive and so for all views but the day view we have to subtract 1 to the right boundary
	if(filters.dateRange.end!=filters.dateRange.start)
		filters.dateRange.end=$("#calendar").fullCalendar( 'getView' ).end.subtract(1, 'days').format("YYYY-MM-DD");
	
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
						var color=instance.color;
						//strip off the T00:00:00 for date range events
						var start=instance.start;
						var end=instance.end;
						var recurrent=false;
						var title=instance.name;
						if(instance.timeType=="date_range"){
							start=instance.start.replace("T00:00:00","");
							end=instance.end.replace("T00:00:00","");
								if(end!=start){
									endmoment=moment(end).add(1,"day");
									end=endmoment.format("YYYY-MM-DD");	
								}
									
							}
						else if(instance.timeType=="deadline"){
							end="";
							var chunks=start.split("T");
							var time=chunks[1];
							var hour=time.split(":")[0];
							start=start.split("T")[0];
							title=hour+" "+title;
							}
						var id;
						if(instance.recursive!=1){//the event is recurrent
							id=instance.recursive;
							recurrent=true;
						}
						else {
							id=guid();
							recurrent=false;
						}
						var newEvent={
							id_server: instance.id,
							id: id,
							private: false,
							timeType:instance.timeType,
							title: title,
							start: start,
							end: end,
							recursive: recurrent,
							color: color,
							editable: false
						}
						$('#calendar').fullCalendar( 'renderEvent', newEvent);
					}
					//then retrieve private events
					for(var i=0;i<calendar_data.events.private.length;i++){
						var instance=calendar_data.events.private[i];
						var title=instance.name;
						//strip off the T00:00:00 for date range events
						var start=instance.start;
						var end=instance.end;
						var color=instance.color
						if(instance.timeType=="date_range"){
							start=instance.start.replace("T00:00:00","");
							end=instance.end.replace("T00:00:00","");
							if(end!=start){
									endmoment=moment(end).add(1,"day");
									end=endmoment.format("YYYY-MM-DD");	
								}
							}
						else if(instance.timeType=="deadline"){
							end="";
							var chunks=start.split("T");
							var time=chunks[1];
							var hour=time.split(":")[0];
							start=start.split("T")[0];
							title=hour+" "+title;
							}
						var id;
						if(instance.recursive!=1){//the event is recurrent
							id=instance.recursive;
							recurrent=true;
						}
						else {
							id=guid();
							recurrent=false;
						}
						var newEvent={
							id_server: instance.id,
							id: id,
							private: true,
							timeType:instance.timeType,
							title: title,
							start: start,
							end: end,
							recursive: recurrent,
							color: color
						}
						$('#calendar').fullCalendar( 'renderEvent', newEvent);
					}
					},
					error : function(xhr, status, error) {
						launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
					}
				});
	}
	}

//load calendar on document ready with events of the current month
$(document).ready(function() {
	//if the logged in user is a prof we hide the upcoming deadlines field
	if(!student){
		$("#upcoming_deadlines").hide();
		}
	//set moment locale to french
	moment.locale('fr');
	var start=moment(filters.dateRange.start);
	var end=moment(filters.dateRange.end);
	timezone="local";
	//initialize the calendar...
    $('#calendar').fullCalendar({
		lang: 'fr',
		nextDayThreshold : "00:00:00",
		header: {
		left: 'prev,next today',
		center: 'title',
		//right: 'month,agendaWeek,agendaDay'
		right: 'agendaSixMonth,month,agendaWeek,agendaDay'
		},
		views: {
			agendaSixMonth: {
				type: 'month',
				duration: { weeks: 34 },
				buttonText: 'Semestre',
			}
		},
		height: 550,
		editable: true,
		viewRender: function(view,element){
			var current_view=getCurrentView(view.name);
			if(current_view=="semester")
				$(".fc-left").hide();
			else $(".fc-left").show();
			addEvents();	
			},
		eventLimit: true, // allow "more" link when too many events
		fixedWeekCount: false, //each month only shows the weeks it contains (and not the default 6) 
			//handle click on event
		eventClick: function(calEvent, jsEvent, view) {
			var event_private=calEvent.private;
			event_id=calEvent.id;
			//check event type to call proper modal
			if(event_private){	//private event
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
			else{	//public event
				$("#academic_event_info_modal").attr("event-id",calEvent.id_server);
				$("#academic_event_info_modal").modal("show");
				modal_shown="#academic_event_info_modal";
				populate_public_event(calEvent);
				}
			
		},
		
		//handle clicks within the calendar
		dayClick: function(date, jsEvent, view) {
			if(student){
				edit_existing_event=false;
				var target = date.format();
				buildDatePicker("private_event",target);
				$("#private_event_modal_header").text("Nouvel événement privé");
				$("#private_event_modal_header").removeClass("float-left-10padright");
				$("#private_event_title").prop("readonly",false);
				$("#private_event_startDate_datepicker").prop("disabled",false);
				$("#private_event_startDate_datepicker").prop("readonly",false);
				$("#private_event_endDate_datepicker").prop("disabled",false);
				$("#private_event_endDate_datepicker").prop("readonly",false);
				$("#private_event_startHour").prop("disabled",false);
				$("#private_event_endHour").prop("disabled",false);
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
				setTimeInterval(date,view);
				setTimePickersValidInterval("#private_event");
			}
		},
		//function to be called when private event is dragged and dropped
		eventDrop:
			function(event, delta, revertFunc) {
				//$("#event_recursive_dragdrop_alert").modal("show");
				var revert=false;
				if(event.recursive){//the event is recurrent
					if (!confirm("Cet événement est récurrent. Etes-vous sûr de ce changement?")){
						revertFunc();
						revert=true;	
					}
				}
				if(!revert){
					var start=event.start.format("YYYY-MM-DD")+"T"+event.start.format("HH:mm:ss");
					var end;
					var limit=false;
					if(event.end)
						end=event.end.format("YYYY-MM-DD")+"T"+event.end.format("HH:mm:ss");
					if(event.timeType=="deadline")
						limit=true;
					$.ajax({
						dataType : "json",
						type : 'POST',
						url : "index.php?src=ajax&req=131",
						data : {id:event.id_server,start:start,end:end,allDay:event.allDay,limit:limit},
						success : function(data, status) {
							/** error checking */
							if(data.error.error_code > 0)
							{	
								launch_error_ajax(data.error);
								return;
							}
						},
						error : function(xhr, status, error) {
							launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
						}
					});
				}
			}
    })
	
	//load deadlines, favourites and upcoming events
	$.ajax({
			dataType : "json",
			type : 'GET',
			url : "index.php?src=ajax&req=101",
			success : function(data, status) {
				/** error checking */
				if(data.error.error_code > 0)
				{	
					launch_error_ajax(data.error);
					return;
				}
					var deadlines=data.upcomingDeadlines;
					document.getElementById("deadlines").innerHTML="";
					for(i=0;i<deadlines.length;i++)
						addDeadline(deadlines[i]);
					//add the table headers if there's at least a deadline
					var deadlines_table=document.getElementById("deadlines");
					var row=deadlines_table.insertRow(0);
					var cell1=row.insertCell(0);
					if(deadlines.length>0){
						var cell2=row.insertCell(1);
						var cell3=row.insertCell(2);
						var titleHeader=document.createElement('p');
						titleHeader.className="text-bold";
						var whenHeader=document.createElement('p');
						whenHeader.className="text-bold";
						var recurrenceHeader=document.createElement('p');
						recurrenceHeader.className="text-bold";
						titleHeader.innerHTML="Titre"
						cell1.appendChild(titleHeader);
						whenHeader.innerHTML="Quand";
						cell2.appendChild(whenHeader);
						recurrenceHeader.innerHTML="Récurrence";
						cell3.appendChild(recurrenceHeader);
					}
					else{
						var noDeadlines=document.createElement("p");
						noDeadlines.innerHTML="Vous n'avez pas deadlines à venir dans les deux prochaines semaines"
						cell1.appendChild(noDeadlines);
						}
			},
			error : function(xhr, status, error) {
				launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
			}
		});	
	
	/*--------------------------SETTING UP NOTE POPOVER-----------------------------*/
	
	//setup popover for delete note button
	$("#delete_note .delete").popover({
		template: '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div><div class="modal-footer"><button type="button" class="btn btn-default">Annuler</button><button type="button" class="btn btn-primary id="confirm_delete_note" onclick="delete_note()">Confirmer</button></div></div>'
		});

	/*--------------------------END SETTING UP POPOVER-----------------------------*/	
});


/*$("#calendar").on("click",".fc-next-button",function(){
	var current_view=getCurrentView($("#calendar").fullCalendar( 'getView' ).name);
	if(current_view=="semester"){
		if(semester){
			if(startSemester.month()==1){//1 is february - we are in the second semester and we clicked next
				startSemester.month(8);//next semester starts in september
				startSemester.day(15)
				endSemester.month(1);//and finishes the 31 of january - but we set the 1st of Feb because it's exclusive
				endSemester.day(1);
				endSemester.add(1,"year");
			}
			else {//we are in the second semester and we clicked next
				startSemester.month(1);//next semester starts in february
				startSemester.day(1);
				startSemester.add(1,"year");
				endSemester.month(8);//and finishes it the 14th of September - we set it to 15th since it's exclusive 
				endSemester.day(15);
				endSemester.add(1,"year");
			}
		}
	addEvents();
	}
})

$("#calendar").on("click",".fc-prev-button",function(){
	nextSemester=false;
	prevSemester=true;
})*/



//populate event categories of private event modal when creating a new private event
$("#private_event").on("show.bs.modal",function(){
	//populate event categories
	populate_event_categories_dropdown("private_event_categories_dropdown","#private_event_type");
	//setup timepickers of new event modal
	setUpTimePickers("#private_event","#edit_event_btns");
	})
	
//set time intervals of new private event
function setTimeInterval(date,view){
	//get current calendar view
	var current_view=getCurrentView(view.name);
	//if the view is the day or week view we load in the time pickers the start and end hour where the user clicked
	//otherwise we select the current hour
	var startHour;
	var minutes;
	var endHour;
	if(current_view=="day"||current_view=="week"){
		startHour=date.hours();
		minutes=date.minutes();
		if(minutes=="0")
			minutes="00";
		endHour=date.add(1,"hour").hours();
		}
	else{
		var currentTime=new Date();
		currentTime=moment(currentTime);
		startHour=currentTime.hours();
		endHour=currentTime.add(1,"hour").hours();
		minutes="00";
		}
	$("#private_event_startHour").val(startHour+":"+minutes);
	$("#private_event_endHour").val(endHour+":"+minutes)
	}

//add deadlines to the calendar upperview
function addDeadline(item){
	var deadlines_table=document.getElementById("deadlines");
    var event_tag=document.createElement('a');
	event_tag.setAttribute("event-id",item.id);
	event_tag.innerHTML = item.name;
	event_tag.setAttribute("data-toggle","modal");
	var modal;
	if(item.academic_event=="true")
		modal="#academic_event_info_modal";
	else modal="#private_event";
	event_tag.setAttribute("data-target",modal);
	var recurrence=get_recursion(item.recurrence_id);
	var event_recurrence=document.createElement("p");
	event_recurrence.innerText=recurrence;
	var limit=buildMoment(item.limit);	
	var event_limit=document.createElement('p');
	event_limit.innerText=limit;
	var row=deadlines_table.insertRow(0);
	var cell1=row.insertCell(0);
	var cell2=row.insertCell(1);
	var cell3=row.insertCell(2);
	cell1.appendChild(event_tag);
	cell2.appendChild(event_limit);
	cell3.appendChild(event_recurrence);
	}

//populate modal when a deadline is clicked from the deadline panel of the calendar view
$("#deadlines").on("click","a",function(){
	var modal=event.target.getAttribute("data-target");
	var event_id=event.target.getAttribute("event-id");
	if(modal=="#private_event")
		populate_private_event({id_server:event_id,id:""});
	else populate_public_event({id_server:event_id});
	})

/*--------------------------------------------------------------------------*/
/*--------------------------- MANAGE NOTE ----------------------------------*/
/*--------------------------------------------------------------------------*/
	
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
			error : function(xhr, status, error) {
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
		$(".modal-backdrop").on("click",function(){$("#academic_event_info_modal").modal("hide")});
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
				error : function(xhr, status, error) {
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
				error : function(xhr, status, error) {
					launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
				}
			});
	}
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


/*------------------------------------------------------------------------------*/
/*--------------------------- MANAGE NOTE END ----------------------------------*/
/*------------------------------------------------------------------------------*/

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
		$("#private_event_startDate_datepicker").prop("readonly",false);
		$("#deadline input").prop("disabled",false);
		$("#private_event_startHour").removeClass("hidden");
		$("#private_event_startHour").prop("disabled",false);
		if(!$("#deadline input").prop("checked")){
			$("#private_event_endDate").parent().removeClass("hidden");
			$("#private_event_endDate").prop("disabled",false);
			$("#private_event_endDate_datepicker").prop("disabled",false);
			$("#private_event_endDate_datepicker").prop("readonly",false);
			$("#private_event_endDate_datepicker").removeClass("hidden");
			$("#private_event_endHour").removeClass("hidden");
			$("#private_event_endHour").prop("disabled",false);
		}
		$("#private_event_place").prop("readonly",false);
		$("#private_event_place").removeClass("hidden");
		$("#private_event_details").prop("readonly",false);
		$("#private_event_details").removeClass("hidden");
		$("#private_event_type_btn").prop("disabled",false);
		$("#private_notes_body").prop("readonly",false);
		$("#private_notes_body").parent().parent().removeClass("hidden");
		$("#edit_event_btns").removeClass("hidden");
		$("#edit_event_btns .btn-primary").prop("disabled",false);
		//populate event category list
		populate_event_categories_dropdown("private_event_categories_dropdown","#private_event_type");
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
	$(".modal-backdrop").on("click",function(){$("#academic_event_info_modal").modal("hide")});
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
		datepicker[option].setDateFormat("%l %d %F %Y");
		$("#startDate_datepicker").val(convert_date(event_date_start,fullcalendarDateFormat));
		if($("#endDate_datepicker").length>0)
			$("#endDate_datepicker").val(convert_date(event_date_end,fullcalendarDateFormat));
		//convert the date returned from the datepicker to the format "dddd DD MMM YYYY"	
		datepicker[option].attachEvent("onClick", function(date){
			elements[0].val(convert_date(elements[0].val(),fullcalendarDateFormat));
			elements[1].val(convert_date(elements[1].val(),fullcalendarDateFormat));
		});
	}
	//datepicker to be built for the end recursion
	else if(option=="recurrence_end"){
		datepicker[option] = new dhtmlXCalendarObject("recurrence_end");
		datepicker[option].setDateFormat("%l %d %F %Y");
		setSens("private_event_endDate_datepicker","min","recurrence_end");
		//convert the date returned from the datepicker to the format "dddd DD MMM YYYY"	
		datepicker[option].attachEvent("onClick", function(date){
			$("#recurrence_end").val(convert_date($("#recurrence_end").val(),fullcalendarDateFormat));
		});
		}
	
	//datepicker to be built for the new event panel
	else {
		elements.push($("#private_event_startDate_datepicker"),$("#private_event_endDate_datepicker"));
		datepicker[option] = new dhtmlXCalendarObject([elements[0].attr("id"),elements[1].attr("id")]);
		//set date format
		datepicker[option].setDateFormat("%d-%m-%Y");
		datepicker[option].setDate(target);	
		elements[0].val(convert_date(target,fullcalendarDateFormat));
		elements[1].val(convert_date(target,fullcalendarDateFormat));
		//convert the date returned from the datepicker to the format "dddd DD MMM YYYY"	
		datepicker[option].attachEvent("onClick", function(date){
			elements[0].val(convert_date(elements[0].val(),fullcalendarDateFormat));
			elements[1].val(convert_date(elements[1].val(),fullcalendarDateFormat));
		});
	}
	//hide the time in the datepicker tool
	datepicker[option].hideTime();
}

//defines valid interval of dates for the date picker
function setSens(id, k, datepicker_instance) {
	if($("#deadline input:checked"))
		return;
	else{
		// update range
		if (k == "min")
			datepicker[datepicker_instance].setSensitiveRange(convert_date(byId(id).value,"DD-MM-YYYY"), null);
		else datepicker[datepicker_instance].setSensitiveRange(null, convert_date(byId(id).value,"DD-MM-YYYY"));
	}
}

//returns elements matching given id
function byId(id) {
	return document.getElementById(id);
}
	
//sets the event recurrence
function updateRecurrence(){
	$("#recurrence").text(event.target.innerText);
	$("#recurrence").attr("recurrence-id",event.target.getAttribute("recurrence-id"));
	if(event.target.innerHTML!="jamais"){
		$("#recurrence_end_td").removeClass("hidden");
		//build date picker of the end recurrence input
		buildDatePicker("recurrence_end");
		}
	else $("#recurrence_end_td").addClass("hidden");
	}
	
//enable new/edit event confirm button only when requierd fields are inserted
$('#private_event_title, #private_event_startHour').keyup(function () {
	//when the title has been defined
    if( $('#private_event_title').val().length > 0) {
		//enable if deadline is not selected or is selected and also an hour has been provided
		if($("#deadline input:checked").length==1&&$("#private_event_startHour").val().length>0||$("#deadline input:checked").length==0)
			$('#edit_event_btns .btn-primary').prop("disabled", false);
		else $('#edit_event_btns .btn-primary').prop("disabled", true);
    } 
	else $('#edit_event_btns .btn-primary').prop("disabled", true);
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
	$("#recurrence").attr("recurrence-id",6);
	$("#recurrence_end_td").addClass("hidden");
	$("#private_event_type").text("Travail");
	$("private_event_type").attr("category-id",11);
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

//populate private event modal
function populate_private_event(event){
	var event_id=event.id_server;
	var event_id_fc=event.id
	$("#delete_private_event .delete").attr("event-id",event_id);
	$("#delete_private_event .delete").attr("event-id-fc",event_id_fc);
	$.ajax({
		dataType : "json",
		type : 'GET',
		url : "index.php?src=ajax&req=066&event="+event_id,
		async : true,
		success : function(data, status) {
			/** error checking */
			if(data.error.error_code > 0)
			{	
				launch_error_ajax(data.error);
				return;
			}
			//{id, name, description, place, professor, type, startDay, endDay, startTime, endTime, deadline, category_id, category_name, recurrence, recurrence_type, favourite, annotation}
			$("#recurrence").text(get_recursion(data.recurrence_type));
			$("#recurrence").attr("recurrence-id",data.recurrence_type);	
			$("#private_event_place").val(data.place);
			$("#private_event_type").text(data.category_name);
			$("#private_event_type").attr("category-id",data.category_id);
			$("#private_event_details").val(data.description);
			$("#private_notes_body").val(data.annotation);
			var event_type=data.type;
			var title=data.name;
			var type=data.type;
			var start=data.startDay;
			var end=data.endDay;
			var place=data.place;
			var details=data.description;
			var notes=data.annotation;
			buildDatePicker("private_event",start);
			//check if event has start hour
			var startHour;
			var endHour;
			if(data.type!="date_range"){
				$("#private_event_startHour").removeClass("hidden");
				startHour=data.startTime.split(":");
				$("#private_event_startHour").val(startHour[0]+":"+startHour[1]); 
				$("#private_event_startHour").prop("disabled",true);
				if(type!="deadline"){
					$("#private_event_endHour").removeClass("hidden");
					$("#private_event_endHour").prop("disabled",true);
					endHour=data.endTime.split(":");
					$("#private_event_endHour").val(endHour[0]+":"+endHour[1]);
				}
				//else $("#new_event_startDate").prev().addClass("hidden");
			}
			else{
				 $("#private_event_startHour").addClass("hidden");
				 $("#private_event_endHour").addClass("hidden");
				}

			//check if the event as an end date (excluding case in which it's a deadline
			if(end!=""&&type!="deadline"){
				var end=new moment(end);
				end=end.format(fullcalendarDateFormat);
				$("#private_event_endDate_datepicker").val(end);
			}
			else 	$("#private_event_endDate_datepicker").parent().parent().addClass("hidden"); 
			//populate modal title
			$("#private_event_modal_header").text(title);
			//adds edit/delete icons next to title
			$("#edit_private_event").removeClass("hidden");
			$("#delete_private_event").removeClass("hidden");
			//define delete popup alert based on whether the event is recurrent or not
			if(data.recurrence_type!="6"){//the event is recurrent
				$("#delete_private_event .delete").popover({
					template: '<div class="popover" role="tooltip"><div class="arrow" style="top: 50%;"></div><h3 class="popover-title">Supprimer événement récurrent</h3><div class="popover-content">Cet événement est récurrent.</div><div class="modal-footer text-center"><div style="margin-bottom:5px;"><button type="button" class="btn btn-primary" onclick="delete_private_event(false)">Seulement cet événement</button></div><div style="margin-bottom:5px;"><button type="button" class="btn btn-default" onclick="delete_private_event(true)">&Eacute;vénements à venir</button></div><div><button type="button" class="btn btn-default">Annuler</button></div></div></div>',
					});				
			}
			else{//event is not recurrent
				//setup popover for delete private event button
				$("#delete_private_event .delete").popover({
					template: '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div><div class="modal-footer"><button type="button" class="btn btn-default">Annuler</button><button type="button" class="btn btn-primary id="confirm_delete_private_event" onclick="delete_private_event(false)">Confirmer</button></div></div>'
					});
				}
			$("#private_event_modal_header").addClass("float-left-10padright");
			//populate modal fields
			$("#private_event_title").val(title);
			$("#private_event_title").prop("readonly",true);
			$("#deadline input").prop("disabled",true);
			if(data.deadline=="true")
				$("#deadline input").prop("checked",true);
			$("#new_event_startDate").prev().removeClass("hidden");
			start=new moment(start);
			start=start.format(fullcalendarDateFormat);
			$("#private_event_startDate_datepicker").val(start);
			$("#private_event_startDate_datepicker").prop("readonly",true);
			$("#private_event_startDate_datepicker").prop("disabled",true);
			//$("#private_event_startHour").prop("readonly",true);
			$("#private_event_endDate_datepicker").prop("readonly",true);
			$("#private_event_endDate_datepicker").prop("disabled",true);
			//$("#private_event_endHour").prop("readonly",true);
			$("#private_event_place").prop("readonly",true);
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
				},
				error : function(xhr, status, error) {
					launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
				}
		});
	}
	
function populate_public_event(event){
	var event_id=event.id_server;
	var req="056";//subevent by default
	$.ajax({
		dataType : "json",
		type : 'GET',
		url : "index.php?src=ajax&req="+req+"&event="+event_id,
		async: true,
		success : function(data, status) {
			/** error checking */
			if(data.error.error_code > 0)
			{	
				launch_error_ajax(data.error);
				return;
			}	
			//{name, details, pract_details, where, limit, start, end, type, recursiveID, pathways[{}], teachingTeam: [{id, role}], attachments:[{id, url,name}], softAdd}
			var academic_event_id=data.id;
			var academic_event_title=data.name;
			var academic_event_description=data.description;
			var academic_event_place=data.place;
			if(academic_event_place==null)
				$("#academic_event_place").parent().hide();
			else $("#academic_event_place").parent().show();
			var academic_event_type=data.type;
			var academic_event_start=moment(data.startDay);
			if(data.startTime!=""){
				var chunks=data.startTime.split(":");
				academic_event_start.set("hour",chunks[0]);
				academic_event_start.set("minute",chunks[1]);
				$("#academic_event_start").html(academic_event_start.format(fullcalendarDateFormat+" , h:mm a"));
			}
			else $("#academic_event_start").html(academic_event_start.format(fullcalendarDateFormat));
			var academic_event_end;
			if(data.endDay!=""){
				$("#academic_event_end").parent().removeClass("hidden");
				academic_event_end=moment(data.endDay);
				if(data.endTime!=""){
					var chunks=data.endTime.split(":");
					academic_event_end.set("hour",chunks[0]);
					academic_event_end.set("minute",chunks[1]);
					$("#academic_event_end").html(academic_event_end.format(fullcalendarDateFormat+" , h:mm a"));
				}
				else $("#academic_event_end").html(academic_event_end.format(fullcalendarDateFormat));
			}
			else {
				$("#academic_event_end").parent().addClass("hidden");
			}
			var deadline=data.deadline;
			if(deadline=="false")
				$("#academic_event_deadline").hide();
			else {
				$("#academic_event_deadline").show();
				$("#academic_event_deadline input").prop("checked",true);
			}
			var category_id=data.category_id;
			var category_name=data.category_name;
			var recurrence=get_recursion(data.recurrence_type);
			//var academic_event_pract_details=data.pract_details;
			$("#academic_event_recurrence").html(recurrence);

			//recurrence=1 means the event is not recursive, otherwise is the instance of a recursion
			if(recurrence=="Jamais")
				$("#academic_event_recurrence_end").parent().addClass("hidden");
			else{
				$("#academic_event_recurrence_end").parent().removeClass("hidden");
				var end_recurrence=moment(data.end_recurrence);
				$("#academic_event_recurrence_end").html(end_recurrence.format(fullcalendarDateFormat));
				}
			var pract_details=data.pract_details;
			var feedback=data.feedback;
			var workload=data.workload;
			var favourite=data.favourite;
			var team=data.team;
			var pathways=data.pathways;
			//populate alert with global event data
			$("#academic_event_title").html(academic_event_title);
			$("#academic_event_details").html(academic_event_description);
			$("#academic_event_category").html(category_name);
			$("#academic_event_place").html(academic_event_place);
			$("#academic_event_pract_details_body").html(pract_details);
			$("#academic_event_feedback_body").html(feedback);
			$("#academic_event_workload").html(workload);
			$("#academic_event_team_table").html("");
			for(var i=0;i<team.length;i++)
				$("#academic_event_team_table").append("<p team-id="+team[i].id+">"+team[i].surname+" "+team[i].name+"\t - <span role-id="+team[i].role_id+">"+team[i].role+"</span></p>")
			$("#academic_event_pathways_table").html("");
			for(var i=0;i<pathways.length;i++)
				$("#academic_event_pathways_table").append("<p pathway-id="+pathways[i].id+">"+pathways[i].name+"</p>");
			//check if the event has notes or not
			$("#notes_body").text(data.annotation);
			if($("#notes_body").text()!=""){
				$("#add_notes").addClass("hidden");
				$("#notes").removeClass("hidden");
				$("#notes_body").text(data.notes);
			}
			else{
				$("#add_notes").removeClass('hidden');
				$("#notes").addClass("hidden");
				}
		},
		error: function(xhr, status, error) {
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
	var startHourSet=false;
	var endHourSet=false;
	var recurrent=false;
	if(startHour!=""){
		startHourSet=true;
		//divide minutes from hours
		startHour=startHour.split(":");
		start.minute(startHour[1]);
		start.hour(startHour[0]); 
	}
	var limit=false;
	if($("#deadline input").prop("checked")){
		limit=true;
		end="";
	}
	else{
		var end=moment(convert_date($("#private_event_endDate_datepicker").val(), "YYYY-MM-DD"));
		var endHour=$("#private_event_endHour").val();
		
		if(endHour!=""){
			endHourSet=true;
			//divide minutes from hours
			endHour=endHour.split(":");
			end.minute(endHour[1]);
			end.hour(endHour[0]); 
		}
	}
	//check if the event is an allDay event
	var allDay=false;
	if(startHour=="" && endHour=="")
		allDay=true;
	var recurrence=$("#recurrence").text();
	var recurrence_id=$("#recurrence").attr("recurrence-id");
	var end_recurrence;
	var end_recurrence_json;
	var lastday=$('#calendar').fullCalendar('getView').end;
	var place=$("#private_event_place").val();
	var type=$("#private_event_type").attr("category-id")
	var details=$("#private_event_details").val();
	var notes=$("#private_notes_body").val();
	//check if we are adding a new private event
	if(!edit_existing_event){
		if(startHourSet)
			startstring=start.format("YYYY-MM-DDTHH:mm:ss");
		else startstring=start.format("YYYY-MM-DD");
		if(!limit){
			if(endHourSet)
				endstring=end.format("YYYY-MM-DDTHH:mm:ss");
			else endstring=end.format("YYYY-MM-DD");
		}
		else {
			end=new moment(start);
			end=end.add(1,"minute");
			endstring=end.format("YYYY-MM-DDTHH:mm:ss");	
		}
		endjson=endstring;
		startjson=startstring;
		if(recurrence_id!=6){
			//if user doesn't specify end of the recursion we set it to one year for all cases, 10 years for "tous les ans" recurrence
			if($("#recurrence_end").val()==""){
				end_recurrence=new moment(start);
				if(recurrence=="tous les ans")
					end_recurrence.add(10,"year");
				else end_recurrence.add(1,"year");
			}
			else end_recurrence=moment(convert_date($("#recurrence_end").val(),"YYYY-MM-DD"));
			end_recurrence_json=end_recurrence.format("YYYY-MM-DD");
			recurrent=true;	
		}
		else {
			end_recurrence_json=""
			recurrent=false;
		}
		//send data to server event with no recursion
		var new_event={"name":title, "start":startjson, "end":endjson, entireDay:allDay, "limit":limit, "recurrence":recurrence_id, "end-recurrence":end_recurrence_json, "place":place, "details":details, "note":notes, "type":type}
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
					var id=guid();
					//check if the event is recursive
					if(recurrence!="jamais"){
						var offset;
						var offset_type;
						switch(recurrence){
							case "tous les jours":
								offset=1;
								offset_type="day";
								recurrence_id=1;
								break;
							case "toutes les semaines":
								offset=7;
								offset_type="day";
								recurrence_id=2;
								break;
							case "toutes les deux semaines":
								offset=14;
								offset_type="day";
								recurrence_id=3
								break;
							case "tous les mois":
								offset=1;
								offset_type="month";
								recurrence_id=4;
								break;
							case "tous les ans":
								offset=1;
								offset_type="year";
								recurrence_id=5;
								break;
						}
						var id_event=guid();
						var i=0;
						while(end.isBefore(end_recurrence)&&end.isBefore(lastday)){
							$('#calendar').fullCalendar('addEventSource', {
									events:[{
										id_server: data.id[i],
										id: id_event,
										private: true,
										title: title,
										start: start,
										end: end,
										allDay: allDay,
										place: place,
										details: details,
										notes: notes,
										recurrence: recurrent,
										color: getColor(type),
										editable: true
										}]
									} 
								)
							i++;
							start.add(offset,offset_type);
							end.add(offset,offset_type);
						}
					}
					//event is not recursive
					else{
						$('#calendar').fullCalendar('addEventSource', {
								events:[{
									id_server: data.id[0],
									id: guid(),
									private: true,
									title: title,
									start: start,
									end: end,
									allDay: allDay,
									place: place,
									details: details,
									notes: notes,
									color: getColor(type),
									editable: true
									}]
								} 
							)
						}
				},
				error : function(xhr, status, error) {
					launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
				}
			});
	}
	//otherwise we are editing an existing one
	else{
		private_event.title=title;
		private_event.start=start;
		if($("#deadline input").prop("checked")){
			end=moment(start);
			end=end.add(1,"minute");
			private_event.end=end;
			end=""
			}
		else private_event.end=end;
		private_event.place=place;
		private_event.details=details;
		private_event.notes=notes;
		private_event.allDay=allDay;
		private_event.recurrence=recurrence;
		private_event.color=getColor($("#private_event_type").attr("category-id"));
		$('#calendar').fullCalendar('updateEvent', private_event);
		//send update to server
		if(startHourSet)
			start=start.format("YYYY-MM-DDTHH:mm:ss");
		else start=start.format("YYYY-MM-DD");
		if(end!=""){
			if(endHourSet)
				end=end.format("YYYY-MM-DDTHH:mm:ss");
			else end=end.format("YYYY-MM-DD");
			}
		var edit_event={id:private_event.id_server, name:title, details:details, where:place, limit:$("#deadline input").prop("checked"), start:start, end:end, entireDay:allDay, type:$("#private_event_type").attr("category-id"), recursiveID:recurrence_id, applyRecursive:false}
		$.ajax({
				dataType : "json",
				type : 'POST',
				url : "index.php?src=ajax&req=065",
				data : edit_event,
				success : function(data, status) {
					/** error checking */
					if(data.error.error_code > 0)
					{	
						launch_error_ajax(data.error);
						return;
					}
					
				},
				error : function(xhr, status, error) {
					launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
				}
			});
		}
		
	//hide the modal
	$("#private_event").modal("hide");
	
}

//delete private event
function delete_private_event(applyRecursive){
	var event_id=$("#delete_private_event .delete").attr("event-id");
	var event_id_fc=$("#delete_private_event .delete").attr("event-id-fc");
	$.ajax({
		dataType : "json",
		type : 'POST',
		url : "index.php?src=ajax&req=063",
		data : {id:event_id,applyRecursive:applyRecursive},
		success : function(data, status) {
			/** error checking */
			if(data.error.error_code > 0)
			{	
				launch_error_ajax(data.error);
				return;
			}
			if(applyRecursive)
				$('#calendar').fullCalendar('removeEvents', function(element){if(element.id==event_id_fc)return true});
			else $('#calendar').fullCalendar('removeEvents', function(element){if(element.id_server==event_id)return true});
			//hide the modal
			$("#private_event").modal("hide");
		},
		error : function(xhr, status, error) {
			launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
		}
	});
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
	
function deadline(){
	if($("#deadline input").prop("checked")){
		$("#private_event_endDate").parent().addClass("hidden");
		datepicker["private_event"].setSensitiveRange(null, null);
		if($("#private_event_startHour").val().length==0)
			$('#edit_event_btns .btn-primary').prop("disabled", true);
		else {
			if($("#private_event_title").val().length>0)
				$('#edit_event_btns .btn-primary').prop("disabled", false);
			}
	}
	else{ 
		$("#private_event_endDate").prop("disabled",false);
		$("#private_event_endDate_datepicker").prop("disabled",false);
		$("#private_event_endDate_datepicker").prop("readonly",false);
		$("#private_event_endDate_datepicker").removeClass("hidden");	
		$("#private_event_endDate").parent().removeClass("hidden");
		}
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
						error : function(xhr, status, error) {
							launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
						}
					});
				break;
			case "event_type_filter":
				$(this).find('.modal-title').text("Filtrer par type d'événement");
				//get events types
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
							cell1.innerHTML="Catégorie Temporelle";
							cell2.innerHTML="Choisir";
							cell2.className="text-center"
							var cell3=row.insertCell(2);
							var cell4=row.insertCell(3);
							cell3.innerHTML="Type d'événement";
							cell4.innerHTML="Choisir";
							cell4.className="text-center"
							filter_alert.append(table);
							var i=0;
							for (i; i < date_types.length; i++){
								var date_type_tag=document.createElement('p');
								date_type_tag.innerHTML = date_types[i].name;
								var table=document.getElementById("events_types_filter_table");
								var row=table.insertRow(-1);
								var cell1=row.insertCell(0);
								var cell2=row.insertCell(1);
								cell1.appendChild(date_type_tag);
								var input=document.createElement('input');
								input.type='checkbox';
								input.id=date_types[i].id;
								cell2.className="text-center";
								cell2.appendChild(input);
								if(event_types[i]!=null){
									var event_type_tag=document.createElement('p');
									event_type_tag.innerHTML = event_types[i].name;
									var cell3=row.insertCell(2);
									var cell4=row.insertCell(3);
									cell3.appendChild(event_type_tag);
									var input=document.createElement('input');
									input.type='checkbox';
									input.id=event_types[i].id;
									cell4.className="text-center";
									cell4.appendChild(input);
								}
							}
							var j=i;
							for(j;j<event_types.length;j++){
								var table=document.getElementById("events_types_filter_table");
								var row=table.insertRow(-1);
								var cell1=row.insertCell(0);
								var cell2=row.insertCell(1);
								var event_type_tag=document.createElement('p');
								event_type_tag.innerHTML = event_types[j].name;
								var cell3=row.insertCell(2);
								var cell4=row.insertCell(3);
								cell3.appendChild(event_type_tag);
								var input=document.createElement('input');
								input.type='checkbox';
								input.id=event_types[j].id;
								cell4.className="text-center";
								cell4.appendChild(input);
							}
						},
						error : function(xhr, status, error) {
							launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
						}
					});
				break;
			case "event_category_filter":
			$(this).find('.modal-title').text("Filtrer par categorie d'événement");
				//get events categories
				$.ajax({
						dataType : "json",
						type : 'POST',
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
								input.id=academic_categories[i].id;
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
							var j=i;
							for(j;j<student_categories.length;j++){
								var table=document.getElementById("events_categories_filter_table");
								var row=table.insertRow(-1);
								var cell1=row.insertCell(0);
								var cell2=row.insertCell(1);
								var student_category_tag=document.createElement('p');
								student_category_tag.innerHTML = student_categories[j].name;
								var cell3=row.insertCell(2);
								var cell4=row.insertCell(3);
								cell3.appendChild(student_category_tag);
								var input=document.createElement('input');
								input.type='checkbox';
								input.id=student_categories[j].id;
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
						error : function(xhr, status, error) {
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
						error : function(xhr, status, error) {
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
	$("#endDateFilter").val(td.format(fullcalendarDateFormat));
	$("#startDateFilter").val(td.subtract(1,"day").format(fullcalendarDateFormat));
	//convert the date returned from the datepicker to the format "dddd DD MMM YYYY"
	filterDates.attachEvent("onClick", function(date){
		$("#startDateFilter").val(convert_date($("#startDateFilter").val(),fullcalendarDateFormat));
		$("#endDateFilter").val(convert_date($("#endDateFilter").val(),fullcalendarDateFormat));
	});
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

			filters.allEvent.isSet="true";
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
			var selectedEventTypes=$('#events_types_filter_table tbody td:nth-child(4) input:checked');
			var selectedTimeTyps=$('#events_types_filter_table tbody td:nth-child(2) input:checked')
			selectedEventTypes.each(function (){
				filters.eventTypes.eventType.push(this.id);
				});
			selectedTimeTyps.each(function (){
				filters.eventTypes.timeType.push(this.id);
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
			filters.allEvent.isSet="false";
			break;
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
			filters.eventTypes.eventType.length=0;
			filters.eventTypes.timeType.length=0;
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
			error : function(xhr, status, error) {
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
	//update displayed events based on the selected filters
	addEvents();
	}
	
//returns category color
function getColor(category){
	switch(parseInt(category)){
		case 1:
			return "#00a3c7";
			break;
		case 2:
			return "#066d18";
			break;
		case 3:
			return "#545454";
			break;
		case 4:
			return "#0010a5";
			break;
		case 5:
			return "#6300a5";
			break;
		case 6:
			return "#fec500";
			break;
		case 7:
			return "#7279db";
			break;
		case 8:
			return "#0064b5";
			break;
		case 9:
			return "#00AAFF";
			break;
		case 10:
			return "#ab699b";
			break;
		case 11:
			return "#ff9400";
			break;
		case 12:
			return "#48cfd2";
			break;
		case 13:
			return "#0fad00";
			break;
		case 14:
			return "#8cc700";
			break;
		case 15:
			return "#3f5643";
			break;
		case 16:
			return "#553300";
			break;
		case 17:
			return "#540055";
			break;
		case 18:
			return "#b6b9c0";
			break;
		case 19:
			return "#ff0000";
			break;
		case 20:
			return "#ff6600";
			break;
		case 21:
			return "#c5007c";
			break;
		}
	}
	
//hide field if not set
function isSet(field){
	if(field.text()=="")
		field.parent().parent().addClass("hidden");
	else field.parent().parent().removeClass("hidden");
	}

function buildMoment(date){
	var dateMoment;
	var dateString;
	if(date!=""){
		var dateChunks=date.split("T");//split date and time
		dateMoment=moment(dateChunks[0]);
		if(dateChunks.length==2){//if there is also a time
			var hourChunks=dateChunks[1].split(":")//split hour and minute
			dateMoment=moment(dateChunks[0]+" "+hourChunks[0]+":"+hourChunks[1]);
		}
	}
	if(dateMoment._f=="YYYY-MM-DD HH:mm")
		dateString=dateMoment.format("ddd DD MMM YYYY HH:mm");
	else dateString=dateMoment.format("ddd DD MMM YYYY");
	return dateString;
}