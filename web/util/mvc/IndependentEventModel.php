<?php

namespace util\mvc;

use util\mvc\EventModel;

/**
 * @brief Describe the SubEvents
 * @author charybde
 *
 */
class IndependentEventModel extends AcademicEventModel{
	
	function __construct(){
		parent::__construct();
		$this->fields = array_merge($this->fields, array("id_Owner" => "int", "public" => "bool"));
		$this->table = $this->table." JOIN Independent_Event";
	
	}
	
	
}


?>