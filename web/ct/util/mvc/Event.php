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
	 * @param String $tables tables to find the event (default Event)
	 * @param array String $infoData the data to identify the event
	 * @param array String $requestData the requested data for the event $id (empty = all)
	 * @retval string return the JSON representation of the event
	 */
	private public function getData($table = "Event", array $infoData = array(), array $requestData = array()){
		$pdo = SQLAbstract_PDO::buildByPDO($db->get_handle());
		
		//Build WHERE clause
		if(!empty($infoData)){
			$flagFirst = true;
			$where = "";
			foreach ($infoData as $key => $value){
				if(!$flagFirst)
					$where .= " AND ";
				else 
					$flagFirst = false;
				
				$where .= $key." = ".$value;
			}
		}

		$data = $pdo->select($table, $where, $requestData);
		return json_encode($data);
	}
	
	/**
	 * @brief Get an event from the bdd (this function check the args)
	 * @param $type type of event
	 * @param $requestedData what we want to obtain (nothing for *)
	 * @param $infoData what we know about the event
	 * @retval Json string of the event(s) -1 if error
	 */
	function getData ($type, array $infoData = array(),  array $requestedData = array()){
		$eventField = array("id_event", "name", "description", "id_recurence", "place", "id_category");
		$academicEventFields = array("feedback", "workload", "practical_details");
		$subEventFields = array("id_GlobalEvent");
		$independantEventFields = array("id_Owner", "public");
		$studentEventFields = array("id_Owner");
		
		//Find the good Table
		if($type == "Event")
			$table = "Event";
		else if($type == "Academic_event")
			$table = "Event JOIN Academic_event";
		else if($type == "Student_event")
			$table = "Event JOIN Student_event";
		else if($type == "Sub_event")
			$table = "Event JOIN Academic_event JOIN Sub_event";
		else if($type == "Independant_Event")
			$table = "Event JOIN Academic_event JOIN Independant_Event";
		else
			return -1;
		
		if(empty($requestedData))
			return getData($table, $infoData);
		
		if($type == "Event")
			$request = array_intersect($requestedData, $eventFields);
		else if($type == "Academic_event")
			$request = array_intersect($requestedData, array_merge($eventFields, $academicEventFields));
		else if($type == "Student_event")
			$request = array_intersect($requestedData, array_merge($eventFields, $studentEventFields));
		else if($type == "Sub_event")
			$request = array_intersect($requestedData, array_merge($eventFields, $academicEventFields, $subEventFields));
		else if($type == "Independant_Event")
			$request = array_intersect($requestedData, array_merge($eventFields, $academicEventFields, $independantEventFields));
		
		return getData($table, $infoData, $request);
	}
	
}