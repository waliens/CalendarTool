<?php

namespace util\mvc;

use util\mvc\EventModel;

/**
 * @brief Describe the SubEvents
 * @author charybde
 *
 */
class SubEventModel extends AcademicEventModel{
	
	function __construct(){
		parent::__construct();
		$this->fields = array_merge($this->fields, array("id_GlobalEvent" => "int"));
		$this->table = $this->table." JOIN Sub_Event";
	
	}
	
	
}


?>