<?php

/**
 * @file
 * @brief Event ControllerClass
 */

namespace ct\util\mvc;


/**
 * @class Event
 * @brief Class for handling the control of event
 */
use nhitec\sql\SQLAbstract_PDO;

use ct\util\database\Database;

class Event extends AjaxController{
	
	private $db;
	
	function __construct() {
		$this->db = Database::get_instance();
	}

	
	
	
	
}