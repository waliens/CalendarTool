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
		$this->fields = array("id_event" => "int", "name" => "text", "description" => "text", "id_recurence" => "int", "place" => "text", "id_category" => "int");
		$this->table = "Event";
	}
	/**
	 * @brief Get an event from the bdd
	 * @param String $tables tables to find the event (default Event)
	 * @param array String $infoData the data to identify the event
	 * @param array String $requestData the requested data for the event $id (empty = all)
	 * @retval string return the JSON representation of the event
	 */
	private public function getData($table = null, array $infoData = null, array $requestData = null){
		$pdo = SQLAbstract_PDO::buildByPDO($db->get_handle());
		if($table == NULL)
			$table = "Event";

		//Build WHERE clause
		//TODO ensure translation between key in php and rows in DB
		if(isset($infoData) && !empty($infoData)){
			$ar = array();
			$i = 0;
			
			foreach ($infoData as $key => $value){
				$ar[$i] = $key." = `".$value."`";
				$i++;
			}
			
			$where = implode(" AND ", $ar);
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
	public function getData (array $infoData = null,  array $requestedData = null){	
		if($infoData == null)
			$infoData = array();
		
		if($requestedData ==  null)
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
			$intersect = "array_intersect_key";
		else
			$intersect = "array_intersect_key_val";
		
		return $intersect($ar, $this->fields);
		
	}
	private function array_intersect_key_val($array, $keyArray){ //$array est un tableau dont on cherche a savoir quelles sont les valeurs en commun avec les clés de $keyarray
		$retval = array();
		foreach($array as $key => $value){
			if(array_key_exists($value, $keyArray))
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
	public function createEvent($data){

		$datas = checkParams($type, $data, true);
		if(!checkIntegrity($datas))
			return -1;
	}
	
	/**
	 * 
	 * @brief Check the data integrity and format the texts area to suitables ones
	 * @param array $data the data provide by the user (note the array is suppose to be complete after a check by the  controller)
	 * @retval false if there is a problem with the data integrity true otherwise
	 */
	private function checkIntegrity($data){
		foreach($data as $key => $value){
			if($this->fields[$key] == "int"  && !is_int($value) )
				return false;
			elseif($this->fields[$key] == "bool" && !is_int($value)){
				if(abs($value) > 1)
					return false;
			}
			elseif($this->fields[$key] == "text"){
				$data[$key] = htmlEntities($value, ENT_QUOTES);
				$data[$key] = nl2br($data[$key]);
			}
				
		}
		return true;
	}
}