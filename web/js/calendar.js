// JavaScript Document
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

	
		},
		//handle clicks within the calendar
		dayClick: function(date, jsEvent, view) {

        var target = date.format();

    },
		
    })

});