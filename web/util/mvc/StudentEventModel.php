<?php

use ct\util\mvc\EventModel;

/**
 * @brief Describe the AcademicEvent
 * @author charybde
 *
 */
class StudentEventModel extends EventModel{

	function __construct(){
		parent::__construct();
		$this->fields = array_merge($this->fields,  array("id_Owner" => "int"));
		$this->table = $this->table." JOIN Student_Event";

	}

}

?>