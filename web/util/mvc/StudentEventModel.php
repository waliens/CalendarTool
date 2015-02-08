<?php
namespace util\mvc;

use util\mvc\EventModel;

/**
 * @brief Describe the AcademicEvent
 * @author charybde
 *
 */
class StudentEventModel extends EventModel{

	function __construct(){
		parent::__construct();
		$this->fields = array_merge($this->fields,  array("id_owner" => "int"));
		$this->translate = array_merge($this->translate,  array("id_owner" => "Id_Owner"));
		
		$this->table = $this->table." JOIN Student_Event";

	}

}

?>