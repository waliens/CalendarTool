// JavaScript Document

//converts date formats	
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

//convert month from abbreviated or full name notation to two digits notation
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
	
//converts recursion from number code to string
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
	
//change the value of the dropdown stating the event type
function changeEventType(tag){
	$(tag).text(event.target.innerHTML);
	$(tag).attr("category-id",event.target.getAttribute("category-id"))
	}
	
//populate event categories dropdown
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