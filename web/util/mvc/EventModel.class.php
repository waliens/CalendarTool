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
	protected $table;
	protected $translate;
	
	function __construct() {
		parent::__construct();
		$this->fields = array("id_event" => "int", "name" => "text", "description" => "text", "id_recurence" => "int", "place" => "text", "id_category" => "int");
		$this->table = "event";
		$this->translate = array("id_event" => "Id_Event", "name" => "Name", "description" => "Description", "id_recurence" => "Id_Recurrence", "place" => "Place", "id_category" => "Id_Category");
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
	private  function getData($table = null, array $infoData = null, array $requestData = null){
		$pdo = SQLAbstract_PDO::buildByPDO($db->get_handle());
		if($table == NULL)
			$table = "Event";

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
		return json_encode($data);
	}
	
	/**
	 * @brief Get an event from the bdd (this function check the args)
	 * @param $type type of event
	 * @param $requestedData what we want to obtain (nothing for *)
	 * @param $infoData what we know about the event
	 * @retval Json string of the event(s) -1 if error
	 */
	public function getEvent (array $infoData = null,  array $requestedData = null){	
		if($infoData == null)
			$infoData = array();
		
		
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
			
			}
			foreach($this->fields as $key => $value){ //Not sure this is useful
				if(!isset($arr[$key]))
					$arr[$key] = "";
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
		
		foreach($datas as $key => $value){
			echo $key." = ".$value."<br>";
				}
		return $this->sql->insert($this->table, $datas);
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
			$whereClause[i] = $key ." = `".$value."`";
		}
		
		return $this->sql->update($this->table, $data, implode(" AND ", $whereClause));
		
	}
	
}