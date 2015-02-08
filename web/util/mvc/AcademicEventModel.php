
<?php

namespace util\mvc;

use util\mvc\EventModel;

/**
 * @brief Describe the AcademicEvent
 * @author charybde
 *
 */
class AcademicEventModel extends EventModel{

	function __construct(){
		parent::__construct();
		$this->fields = array_merge($this->fields, array("feedback" => "text", "workload" => "int", "practical_details" => "text"));
		$this->table = $this->table." JOIN academic_event";
		$this->translate = array_merge($this->translate, array("feedback" => "Feedback", "workload" => "Workload", "practical_details" => "Practical_Details"));
	}

}


?>