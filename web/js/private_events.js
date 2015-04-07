// JavaScript Document

//update the navbar
$("#navbar li").removeClass("active");
$("#private_events_page").addClass("active");

//dates picker
var datepicker = {"private_event":0,"recurrence_end":0};

$(document).ready(function() {
	//retrieve private events from server
	$.ajax({
		dataType : "json",
		type : 'GET',
		url: "index.php?src=ajax&req=062",
		async : true,
		success : function(data, status) {
			/** error checking */
			if(data.error.error_code > 0)
			{	
				launch_error_ajax(data.error);
				return;
			}
			var private_events=data.events;
			//populate the private events table
			for (var i = 0; i < private_events.length; i++)
				addEvent(private_events[i]);
		},
		error : function(xhr, status, error) {
						launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
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
	var recurrence=get_recursion(item.recurrence_type);
	var event_recurrence=document.createElement("p");
	event_recurrence.innerText=recurrence;
	var start=buildMoment(item.start);	
	var event_start=document.createElement('p');
	event_start.innerText=start;
	var end="";
	if(item.end!="")
		end=buildMoment(item.end);
	var event_end=document.createElement('p');
	event_end.innerText=end;
	var row=private_events_table.insertRow(-1);
	var cell1=row.insertCell(0);
	var cell2=row.insertCell(1);
	var cell3=row.insertCell(2);
	var cell4=row.insertCell(3);
	cell1.appendChild(event_tag);
	cell2.appendChild(event_start);
	cell3.appendChild(event_end);
	cell4.appendChild(event_recurrence);
	}


//delete private event
function delete_private_event(recurrence){
	var event_id=$("#delete_private_event .delete").attr("event-id");
	$.ajax({
		dataType : "json",
		type : 'POST',
		url : "index.php?src=ajax&req=063",
		data:{id:event_id,applyRecursive:recurrence},
		async : true,
		success : function(data, status) {
			/** error checking */
			if(data.error.error_code > 0)
			{	
				launch_error_ajax(data.error);
				return;
			}
			$("#private_event").modal("hide");
			$("#private_events a[event-id='"+event_id+"']").parent().parent().remove();
		},
		error : function(xhr, status, error) {
						launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
		}
	});
}


//populate private event modal on modal display
$("#private_event").on('show.bs.modal', function (event) {
	var event_id=event.relatedTarget.getAttribute("id");
	$("#edit_event_btns .btn-primary").attr("event-id",event_id);
	populate_private_event(event);
	})
	
//populate private event modal
function populate_private_event(event){
	var event_id=event.relatedTarget.getAttribute("event-id");
	$("#delete_private_event .delete").attr("event-id",event_id);
	$("#edit_event_btns button").attr("event-id",event_id);
	$.ajax({
		dataType : "json",
		type : 'GET',
		url : "index.php?src=ajax&req=064&event="+event_id,
		async : true,
		success : function(data, status) {
			/** error checking */
			if(data.error.error_code > 0)
			{	
				launch_error_ajax(data.error);
				return;
			}
			$("#recurrence").text(get_recursion(data.recurrence));
			$("#recurrence").attr("recurrence-id",data.recurrence);	
			$("#private_event_place").val(data.place);
			$("#private_event_category").text(data.category_name);
			$("#private_event_category").attr("category-id",data.category_id);
			$("#private_event_details").val(data.description);
			$("#private_notes_body").val(data.annotation);
			var event_type=data.type;
			var title=data.name;
			var type=data.type;
			var start=moment(data.startDay).format("dddd DD MMM YYYY");
			var place=data.place;
			var details=data.description;
			var notes=data.annotation;
			buildDatePicker("private_event",start);
			//check if event has start hour
			var startHour;
			var endHour;
			if(type!="date_range"){
				$("#private_event_startHour").removeClass("hidden");
				startHourChunks=data.startTime.split(":");
				startHour=moment(data.startDay);
				startHour.hours(startHourChunks[0]);
				startHour.minutes(startHourChunks[1]);
				startHour=startHour.format("HH:mm");
				$("#private_event_startHour").val(startHour);
				$("#private_event_startHour").prop("disabled",true);
				if(type!="deadline"){
					$("#private_event_endHour").removeClass("hidden");
					$("#private_event_endHour").prop("disabled",true);
					endHourChunks=data.endTime.split(":");
					endHour=moment(data.endDay);
					endHour.hours(endHourChunks[0]);
					endHour.minutes(endHourChunks[1]);
					endHour=endHour.format("HH:mm");
					$("#private_event_endHour").val(endHour);
				}
				//else $("#new_event_startDate").prev().addClass("hidden");
			}
			else{
				 $("#private_event_startHour").addClass("hidden");
				 $("#private_event_endHour").addClass("hidden");
				}

			//check if the event as an end date (excluding case in which it's a deadline
			if(data.endDay&&type!="deadline"){
				var end=moment(data.endDay)
				if(type=="date_range"&&!moment(data.endDay).isSame(moment(data.startDay)))
					end.subtract(1,"day");
				var end=end.format("dddd DD MMM YYYY");
				$("#private_event_endDate_datepicker").val(end);
			}
			else 	$("#private_event_endDate_datepicker").parent().parent().addClass("hidden"); 
			//populate modal title
			$("#private_event_modal_header").text(title);
			//adds edit/delete icons next to title
			$("#edit_private_event").removeClass("hidden");
			$("#edit_private_event .edit").attr("disabled",false);
			$("#delete_private_event").removeClass("hidden");
			//define delete popup alert based on whether the event is private or not
			if(data.recurrence!="6"){//the event is recurrent
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
			$("#private_event_category_btn").prop("disabled",true);
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
		$("#private_event_category_btn").prop("disabled",false);
		$("#private_notes_body").prop("readonly",false);
		$("#private_notes_body").parent().parent().removeClass("hidden");
		$("#edit_event_btns").removeClass("hidden");
		$("#edit_event_btns .btn-primary").prop("disabled",false);
		//populate event category list
		populate_event_categories_dropdown("private_event_categories_dropdown","#private_event_type");
		//setup timepickers of new event modal
		setUpTimePickers("#private_event","#edit_event_btns");
		setTimePickersValidInterval("#private_event");
	}
}

//confirm the edit of an existing event
function confirm_edit_private_event(){
	var event_id=$("#edit_event_btns .btn-primary").attr("event-id");
	var title=$("#private_event_title").val();
	var type=$("#private_event_category").text();
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
	if(!startHour && !endHour)
		allDay=true;
	
	var recurrence=$("#recurrence").text();
	var recurrence_id=$("#recurrence").attr("recurrence-id");
	var end_recurrence;
	var place=$("#private_event_place").val();
	var type=$("#private_event_category").attr("category-id")
	var details=$("#private_event_details").val();
	var notes=$("#private_notes_body").val();
	if($("#deadline input").prop("checked")){
		end=moment(start);
		end=end.add(1,"minute");
		end=""
		}
	//send update to server
	if(end!="")
		end=end.format("YYYY-MM-DDTHH:mm:ss");
	var edit_event={id:event_id, name:title, details:details, where:place, limit:$("#deadline input").prop("checked"), start:start.format("YYYY-MM-DDTHH:mm:ss"), end:end, type:$("#private_event_category").attr("category-id"), recursiveID:recurrence_id, applyRecursive:false}
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
				
	//hide the modal
	$("#private_event").modal("hide");
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
})