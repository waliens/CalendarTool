<?php

/**
 * @file
 * @brief Event ControllerClass
 */

namespace ct\util\mvc;


/**
 * @class Event
 * @brief Class for getting event from D
 */
use nhitec\sql\SQLAbstract_PDO;

use ct\util\database\Database;

class EventModel extends Model{
		
	protected $fields;
	protected $table;
	
	function __construct() {
		parent::__construct();
		$this->fields = array("id_event", "name", "description", "id_recurence", "place", "id_category");
		$this->table = "Event";
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
	function getData (array $infoData = array(),  array $requestedData = array()){		
		if(empty($requestedData))
			return getData($this->table, $infoData);
		
		$request = checkParams($requestedData, false);
		return getData($this->table, $infoData, $request);
	}
	
	/** 
	 * @brief check if the params given in input correspond to the type
	 * @param array $ar Parameters array
	 * @param boolean $key Check key (if not check values)
	 * @retval return the array without invalids params
	 */
	function checkParams($ar, $key){
		if($key)
			$intersect = "array_intersect_key_val";
		else
			$intersect = "array_intersect";
		
		return $intersect($ar, $this->fields);
		
	}
	private function array_intersect_key_val($array, $keyArray){
		$retval = array();
		foreach($array as $key => $value){
			if(array_key_exists($key, $keyArray))
				$retval[$key] = $value;
		}
		return $retval;
	}
	
	
	/**
	 * 
	 * @brief Create an event and put it into the DB
	 * @param array $data The data provide by the user
	 * @retval -1 if an error occurs
	 */
	function createEvent($data){
		//Check input type
		$datas = checkParams($type, $data, true);
		foreach($datas as $key => $value){ 
			//TODO
			
		}
		
	}
}