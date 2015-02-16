<?php

/**
 * @file
 * @brief Event ControllerClass
 */

namespace util\mvc;


/**
 * @class Event
 * @brief Class for getting event from D
 */
use nhitec\sql\SQLAbstract_PDO;

use util\database\Database;

class EventModel extends Model{
		
	protected $fields;
	protected $fields_event;
	protected $table;
	protected $translate;
	
	function __construct() {
		parent::__construct();
		
		$this->fields = array("id_event" => "int", "name" => "text", "description" => "text", "id_recurence" => "int", "place" => "text", "id_category" => "int", "limit" => "date", "start" => "date", "end" => "date");
		$this->fields_event = array("id_event" => "int", "name" => "text", "description" => "text", "id_recurence" => "int", "place" => "text", "id_category" => "int", "limit" => "date", "start" => "date", "end" => "date");
		$this->table = array();
		$this->table[0] = "event";
		$this->translate = array("id_event" => "Id_Event", "name" => "Name", "description" => "Description", "id_recurence" => "Id_Recurrence", "place" => "Place", "id_category" => "Id_Category", "limit" => "Limit", "start" =>"Start", "end" => "End");
	}
	
	/**
	 * @brief Get the Fields from the Model
	 * @retval array $this->fields
	 */
	public function getFields() { return $this->fields; }

	/**
	 * @brief Get the table from the Model
	 * @retval array $this->table
	 */
	public function getTable() {	return $this->table;	}
	
	
	/**
	 * @brief Get an event from the bdd
	 * @param String $tables tables to find the event (default Event)
	 * @param array String $infoData the data to identify the event
	 * @param array String $requestData the requested data for the event $id (empty = all)
	 * @retval string return the JSON representation of the event
	 */
	private  function getData($table, array $infoData = null, array $requestData = null){
		$pdo = SQLAbstract_PDO::buildByPDO($db->get_handle());

		
		//Build WHERE clause
		if(isset($infoData) && !empty($infoData)){
			$ar = array();
			$i = 0;
			
			foreach ($infoData as $key => $value){
				$ar[$i] = $key." = `".$value."`";
				$i++;
			}
			
			$where = implode(" AND ", $ar);
		}

		$data = $this->sql->select($table, $where, $requestData);
		return $data;
	}
	
	/**
	 * @brief Get an event from the bdd (this function check the args)
	 * @param $type type of event
	 * @param $requestedData what we want to obtain (nothing for *)
	 * @param $infoData what we know about the event
	 * @param $dateType the type of event concerning the date (Date|Deadline|TimeRange)
	 * @retval Json string of the event(s) -1 if error
	 */
	public function getEvent (array $infoData = null,  array $requestedData = null, $dateType = NULL){	
		if($infoData == null)
			$infoData = array();
		
		$table = implode(" JOIN ", $this->table);
		
		
		if(isset($dateType)){
			switch($dateType){
				case "Date":
					$table = $table ." JOIN date_range_event";
					break;
				case "Deadline":
					$table = $table ." JOIN deadline_event";
					break;
				case "TimeRange":
					$table = $table . " JOIN time_range_event";
					break;
					
			}
		}
		
		
		$info = $this->checkParams($infoData, false);
		if($requestedData ==  null)
			return $this->getData($this->table, $info);
		
		$request = $this->checkParams($requestedData, false);
		return getData($this->table, $info, $request);
	}
	
	/** 
	 * @brief check if the params given in input correspond to the type and translate into bdd rows name
	 * @param array $ar Parameters array
	 * @param boolean $ckey Check key (if not check values)
	 * @param boolean $cintegrity Check integrity
	 * @retval return the array without invalids params (-1 if prooblem during cintegrity)
	 */
	private function checkParams($ar, $ckey, $cintegrity = false){
		if($ckey)
			$intersect = "array_intersect_key";
		else
			$intersect = "$this->array_intersect_key_val";
		
		$arr = $intersect($ar, $this->fields);
		
		if($cintegrity){
			foreach($arr as $key => $value){
				if($this->fields[$key] == "int"  && !is_int($value) )
					return -1;
				elseif($this->fields[$key] == "bool" && !is_int($value)){
					if(abs($value) > 1)
						return -1;
				}
				elseif($this->fields[$key] == "text"){
					$arr[$key] = htmlEntities($value, ENT_QUOTES);
					$arr[$key] = nl2br($arr[$key]);
				}
				elseif($this->fields[$key] == "date"){
					//TODO
				}
			
			}
					
		}
		
		$ret = array();
		if($ckey){
			foreach ($arr as $key => $value){
				if(isset($this->translate[$key]))
					$ret[$this->translate[$key]] = $value;
			}
		}
		else{
			foreach($arr as $key => $value){
				if(isset($this->translate[$key]))
					$ret[$key] = $this->translate[$key];
			}
		}
		return $ret;
		
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
	 * @param array $data The data provide by the user after being checked by the controller
	 * @retval -1 if an error occurs
	 */
	public function createEvent($data){

		$datas = $this->checkParams($data, true, true);
		if($datas == -1)
			return -1;
		
		$datas = array_intersect_key($datas, $this->fields_event);

		return $this->sql->insert($this->table[0], $datas);
	}

	
	/**
	 * 
	 * @brief Update event(s) (specify by $from) data to the those specify by $to
	 * @param array $from array of elements that allow us to identy target event(s)
	 * @param array $to new data to put in the bdd 
	 * @retval -1 if an error occurs
	 */
	public function modifyEvent($from, $to){
		$data = $this->checkParams($to, true, true);
		if($data == -1)
			return -1;
		$where = checkParams($from, true);
		
		
		$whereClause = array();
		$i = 0;
		foreach($where as $key => $value){
			if($key = "Id_Event")
				$whereClause[i] = "event.". $key ." = `".$value."`"; //removing ambiguity
			else
				$whereClause[i] = $key ." = `".$value."`";
			$i++;
		}
		
		return $this->sql->update($this->table, $data, implode(" AND ", $whereClause));
		
	}
	/**
	 * @brief 
	 * @param int $id the id of the event
	 * @param string $type the type of event (Date|Deadline|TimeRange)
	 * @param DateTime $start the start of the event (or the deadline)
	 * @param DateTime $end the end of the event 
	 * @param boolean $update if it's already set to an other value
	 * @retval SQL_abstract return code
	 */
	public function setDate($id, $type, $start, $end = NULL, $update = false){
		switch($type){
			case "Date":
				$data = array();
				$data["Id_event"] = $id;
				$data["Start"] = $start->format("Y-m-d");
				$data["End"] = $start->format("Y-m-d");
				$table = "date_range_event";
				break;
			case "Deadline":
				$data = array();
				$data["Id_event"] = $id;
				$data["Limit"] = $start->format("Y-m-d H:i:s");
				$table = "deadline_event";
				break;
			case "TimeRange":
				$data = array();
				$data["Id_event"] = $id;
				$data["Start"] = $start->format("Y-m-d H:i:s");
				$data["End"] = $start->format("Y-m-d H:i:s");
				$table = "time_range_event";
				break;
			default:
				return -1;
				break;	
		}
		if($update)
			return $this->sql->update($table, $data, array("Id_event" => $id));
		else
			return $this->sql->insert($table, $data);
	}
	
	public function getEventFromIds($ids = null, $dateType = null){
		if($ids == null)
			$ids = array();
		
		if(empty($ids))
			return -1;
		
		$table = implode(" JOIN ", $this->table);
		
		
		$id = 'Id_Event = ';
		$id = $id.implode(" OR Id_Event = ", $ids);
		
		if(isset($dateType)){
			switch($dateType){
				case "Date":
					$table = $table ." JOIN date_range_event";
					break;
				case "Deadline":
					$table = $table ." JOIN deadline_event";
					break;
				case "TimeRange":
					$table = $table . " JOIN time_range_event";
					break;
						
			}
		}
		
		return $this->sql->select($table, $id);
		
	}
	
}