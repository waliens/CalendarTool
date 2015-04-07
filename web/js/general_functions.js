// JavaScript Document

/**
*@brief Converts date formats	
*@param date string representing the date to be converted
*@param formatDestination string representing the output format
*@return String date in formatDestination format
*/
function convert_date(date,formatDestination){
		var dd;
		var mm;
		var yy;
		var chunks=date.split(" ");
		//date can be in the format "dd-mm-yyyy", "dddd DD MM YYY" or yyyy-mm-dd
		if(chunks.length>1){//we are in the case dddd DD MM YYY
			dd=chunks[1];

			if(chunks[2].length>2)
				mm=convert_month(chunks[2]);
			else mm=chunks[2];
			yy=chunks[3];
		}
		else {
			chunks=date.split("-");
			if(chunks[0].length==4){//we are in the case yyyy-mm-dd
				dd=chunks[2];
				mm=chunks[1];
				yy=chunks[0];
			}
			else{//we are in the case dd-mm-yyyy
				dd=chunks[0];
				mm=chunks[1];
				yy=chunks[2];

				}
		}
		date_standard=yy+"-"+mm+"-"+dd;
		var d = moment(date_standard);
		return d.format(formatDestination);
	}

/**
*@brief Convert month from abbreviated or full name notation to two digits notation
*@param month string of full/abbreviated month
*@retunr two digits month representation
*/
function convert_month(month){
	switch(month){
		case "janv.":
			return "01";
			break;
		case "janvier":
			return "01";
			break;
		case "févr.":
			return "02";
			break;
		case "février":
			return "02";
			break;
		case "mars":
			return "03";
			break;
		case "avr.":
			return "04";
			break;
		case "avril":
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
		case "juillet":
			return "07";
			break;
		case "août":
			return "08";
			break;
		case "sept.":
			return "09";
			break;
		case "septembre":
			return "09";
			break;
		case "octo.":
			return "10";
			break;
		case "octobre":
			return "10";
			break;
		case "nove.":
			return "11";
			break;
		case "novembre":
			return "11";
			break;
		case "dece.":
			return "12";
			break;
		case "decembre":
			return "12";
			break;
		
		}
	}
	
/**
*@brief converts recursion from number code to string
*@param recursion_id a code from 1 to 6
*@return type of recursion
*/
function get_recursion(recursion_id){
	switch(recursion_id){
		case "6":
			return "Jamais";
		case "1":
			return "Tous les jours";
		case "2":
			return "Toutes les semaines";
		case "3":
			return "Toutes les deux semaines";
		case "4":
			return "Tous les mois";
		case "5":
			return "Tous les ans"
		}
	}
	
/**
*@brief Change the value of the dropdown stating the event type
*@param tag the html element holding the category type
*/
function changeEventType(tag){
	$(tag).text(event.target.innerHTML);
	$(tag).attr("category-id",event.target.getAttribute("category-id"))
	}
	

/**
*@brief Populate event categories dropdown
*@param tag the dropdown to which we want to attach the categories 
*@param changeTypeTarget the dropdown entry from which we copy category name and id on click
*@param onlyAcademic a boolean stating wheather we want to populate the dropdown with both academic and student categories or not *
*/
function populate_event_categories_dropdown(tag,changeTypeTarget,onlyAcademic){
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
			var dropdown=document.getElementById(tag);
			dropdown.innerHTML="";
			for (i=0; i < academic_categories.length; i++){
				var a_tab="<a role='menuitem' tabindex='-1' href='#' onclick=\"changeEventType(\'"+changeTypeTarget+"\')\" category-id="+academic_categories[i].id+">"+academic_categories[i].name+"</a>";
				var li=document.createElement("li");
				li.innerHTML=a_tab;
				dropdown.appendChild(li);
			}
			if(!onlyAcademic){
				for(i=0;i<student_categories.length;i++){
					var a_tab="<a role='menuitem' tabindex='-1' href='#' onclick=\"changeEventType(\'"+changeTypeTarget+"\')\" category-id="+student_categories[i].id+">"+student_categories[i].name+"</a>";
					var li=document.createElement("li");
					li.innerHTML=a_tab;
					dropdown.appendChild(li);
				}
			}
		},
		error : function(xhr, status, error) {
			launch_error("Impossible de joindre le serveur (resp: '" + xhr.responseText + "')");
		}
	});
}


//setup timepickers of new event modal (CALENDAR.JS)
/*
*@briev Setup timepickers based on a given tag
*@param String tag used to build up the names of two HTML tags on which the time picker will be attached
*@param String tag of the btns to be enabled when the required fields are set in the form
*/
function setUpTimePickers(tag,btns){
	$(tag+" .time").timepicker({ 'forceRoundTime': true, 'step':1 });
	$(tag+"_endHour").on("changeTime",function(){
		//check if start and end day are the same and if so we set the maxTime of startHour
		if($(tag+"_startDate_datepicker").val()==$(tag+"_endDate_datepicker").val())
			$(tag+"_startHour").timepicker("option",{maxTime:$(tag+"_endHour").val()});
		else $(tag+"_startHour").timepicker("option",{maxTime:"24:00"});
		if($(tag+"_title").val().length>0&&$(tag+"_startHour").val().length>0)
				$('#edit_event_btns .btn-primary').prop("disabled", false);
		})
		$(tag+"_startHour").on("changeTime",function(){
		//check if start and end day are the same and if so we set the minTime of endHour
		if($(tag+"_startDate_datepicker").val()==$(tag+"_endDate_datepicker").val())
			$(tag+"_endHour").timepicker("option",{minTime:$(tag+"_startHour").val(), maxTime:"24:00"});
		else $("#private_event_endHour").timepicker("option",{minTime:"00:00", maxTime:"23:59"});
		//if it's a deadline we have to check if the required fields have been provided and if so enable the button to create the event
			if($(tag+"_title").val().length>0&&$(tag+"_startHour").val().length>0)
				$(btns+' .btn-primary').prop("disabled", false);
	})
}

/**
*@brief Set initial valid intervals for the time pickers
*@param String tag identifying the two time pickers
*/
function setTimePickersValidInterval(tag){
	//check if start and end day are the same and if so we set the minTime of endHour
	if($(tag+"_startDate_datepicker").val()==$(tag+"_endDate_datepicker").val()){
		$(tag+"_endHour").timepicker("option",{minTime:$(tag+"_startHour").val(), maxTime:"23:59"});
		$(tag+"_startHour").timepicker("option",{maxTime:$(tag+"_endHour").val()});
	}
}

// error management 
/** 
 * @brief Launch the error popup based on the error data from an ajax call
 * @param Object The error object : {error_code:int, error_msg:({"EN":string, "FR":string}|string)}
 * @param lang   The language in which the message must be displayed among "EN" or "FR" (optionnal, default : 'FR')
 */
function launch_error_ajax (error,lang) 
{
	// get proper language
	lang = (lang === "undefined" || (lang !== "FR" && lang !== "EN") ? "FR" : lang);
	
	// make title
	var title = (lang == "FR" ? "Une erreur s'est produite..." : "An error occurred...");

	// make content
	var body = "";

	if(typeof error.error_msg === "string") // msg is a string
		body = error.error_msg;
	else
	{
		var body_suffix = (lang == "FR" ? " (code d'erreur : " : " (error code : ") + error.error_code + ")";
		body = error.error_msg[lang] + body_suffix;
	}

	$("#error-ajax-modal-title").text(title);
	$("#error-ajax-modal-body").text(body);
	$("#error-ajax-modal").modal("show");
}

/**
 * @brief Launch an error with the given message
 * @param string The error message
 * @param lang  The language in which the message must be displayed among "EN" or "FR" (optionnal, default : 'FR')
 */
function launch_error (msg, lang)
{
	launch_error_ajax({error_code:-1, error_msg: msg}, lang);
}