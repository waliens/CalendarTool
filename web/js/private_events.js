// JavaScript Document

//update the navbar
$("#navbar li").removeClass("active");
$("#menu_nav").addClass("active");

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
	$("#delete_confirm").attr("event-id",private_event.attr("event-id"));
});



//delete private event
$("#delete_confirm").click(function(){
	var event_id=$("#delete_confirm").attr("event-id");
	$.ajax({
		dataType : "json",
		type : 'POST',
		url : "index.php?src=ajax&req=063",
		data:{id:event_id,applyRecursive:false},
		async : true,
		success : function(data, status) {
			/** error checking */
			if(data.error.error_code > 0)
			{	
				launch_error_ajax(data.error);
				return;
			}
			$(".delete[event-id='"+event_id+"']").parent().parent().parent().remove();
		},
		error : function(xhr, status, error) {
						launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
		}
	});
});


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
			$("#private_event_type").text(data.category_name);
			$("#private_event_type").attr("category-id",data.category_id);
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
				startHour=moment(data.startHour).format("HH:mm");
				$("#private_event_startHour").val(startHour);
				$("#private_event_startHour").prop("disabled",true);
				if(type!="deadline"){
					$("#private_event_endHour").removeClass("hidden");
					endHour=moment(data.endHour).format("HH:mm");
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
			$("#delete_private_event").removeClass("hidden");
			//define delete popup alert based on whether the event is private or not
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
		case "avr.":
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
		case "oct.":
			return "10";
			break;
		case "nov.":
			return "11";
			break;
		case "déc.":
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

			if(chunks[2].length>2)
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
	
	
//translates recursion id
function get_recursion(recursion_id){
	switch(recursion_id){
		case "6":
			return "jamais";
		case "1":
			return "tous les jours";
		case "2":
			return "toutes les semaines";
		case "3":
			return "toutes les deux semaines";
		case "4":
			return "tous les mois";
		case "5":
			return "tous les ans"
		}
	}