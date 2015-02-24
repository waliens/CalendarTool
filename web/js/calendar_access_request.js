// JavaScript Document
$(document).ready(function() {
	//populate pending requests of access to student calendar
	$.ajax({
		dataType : "json",
		type : 'GET',
		url : "calendar_access_request.json",
		async : true,
		success : function(data, status) {
			
		},
		error : function(data, status, errors) {
			// Inserire un messagio di errore
		}
	});
});