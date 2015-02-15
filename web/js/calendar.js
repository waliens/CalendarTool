// JavaScript Document

var today = new Date();
var day = today.getDate();
var month = today.getMonth()+1; //January is 0!
var year = today.getFullYear();
var calendar_data;

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
			//check if it's an all day event
			if(calEvent.allDay)
				$("#event_time").text(calEvent.start.format('dddd Do MMM YYYY'))
			else{
				$("#event_time").text(calEvent.start.format("hh:mm")+" - "+calEvent.end.format("hh:mm")+" "+calEvent.start.format('dddd Do MMM YYYY'))
				}
			//populate place,prof and details
			$("#event_place").text(calEvent.place);
			//if the event is private there's no need to show the owner so we hide the corresponding table row
			if(calEvent.owner){
				$("#event_owner").text(calEvent.owner);
				$("#event_owner").parent().css('display','table-row');
			}
			else $("#event_owner").parent().css('display','none');
			$("#event_details").text(calEvent.details);
		},
		//populate events
		
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
					for(var i=0;i<data.events.publicEvents.length;i++){
						var instance = data.events.publicEvents[i];
						//chech the event type to accordingly set the event color
						var color=getEventColor(instance);
						events.push({
							id: instance.id,
							title: instance.name,
							start: instance.start,
							end: instance.end,
							owner: instance.professor.name,
							place: instance.where,
							details: instance.details,
							color: color
						});
					}
					//then retrieve private events
					for(var i=0;i<data.events.privateEvents.length;i++){
						var instance=data.events.privateEvents[i];
						events.push({
							id: instance.id,
							title: instance.name,
							start: instance.start,
							end: instance.end,
							place: instance.where,
							details: instance.details,
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