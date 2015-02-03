
<?php


use ct\util\mvc\EventModel;

/**
 * @brief Describe the AcademicEvent
 * @author charybde
 *
 */
class AcademicEventModel extends EventModel{

	function __construct(){
		parent::__construct();
		$this->fields = array_merge($this->fields, array("feedback", "workload", "practical_details"));
		$this->table = $this->table." JOIN Academic_Event";
		
	}

}


?>