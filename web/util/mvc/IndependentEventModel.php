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
		$this->fields = array_merge($this->fields, array("id_owner" => "int", "public" => "bool"));
		$this->translate = array_merge($this->translate, array("id_owner" => "Id_Owner", "Public" => "Public"));
		
		$this->table = $this->table." JOIN independent_event";
	
	}
	
	
}


?>