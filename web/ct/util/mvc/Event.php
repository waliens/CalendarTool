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

	
	/**
	 * @brief Get an event from the bdd
	 * @param String $table table to find the event (default Event)
	 * @param array String $infoData the data to identify the event
	 * @param array String $requestData the requested data for the event $id (empty = all)
	 * @retval string return the JSON representation of the event
	 */
	function getData($table = "Event", array $infoData = array(), array $requestData = array()){
		$pdo = SQLAbstract_PDO::buildByPDO($db->get_handle());
		
		$query = "SELECT ";
		if(empty($requestData))
			$query += "* ";
		else {
			$flagFirst = true;
			foreach ($requestData as $key => $value){
				if(!$flagFirst)
					$query += ", ";
				else
					$flagFirst = false;
				$query += $value;
			}
		}

		$query += "FROM ".$table;
		
		if(!empty($infoData)){
			$flagFirst = true;
			$query += " WHERE ";
			foreach ($infoData as $key => $value){
				if(!$flagFirst)
					$query += " AND ";
				else 
					$flagFirst = false;
				
				$query += $key." = ".$value;
			}
		}

		
		$data = $pdo->execute_query($query);
		return json_encode($data);
	}
	
}