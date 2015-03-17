
// error management 
/** 
 * @brief Launch the error popup based on the error data from an ajax call
 * @param Object The error object : {error_code:int, error_msg:({"EN":string, "FR":string}|string)}
 * @param lang   The language in which the message must be displayed among "EN" or "FR" (optionnal, default : 'FR')
 */
function launch_error_ajax (error,lang) 
{
	// get proper language
	lang = (lang === "undefined" || (lang !== "FR" && lang !== "EN") ? : "FR" : lang);
	
	// make title
	var title = (lang == "FR" ? "Une erreur s\"est produite..." : "An error occurred...");

	// make content
	var body = "";

	if(typeof error.error_msg === "string") // msg is a string
		body = "Code " + error.error_code + " : " + error.error_msg;
	else
	{
		var body_prefix = (lang == "FR" ? "Erreur " : "Error ") + error.error_code + " : ";
		body = body_prefix + error.error_msg[lang];
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