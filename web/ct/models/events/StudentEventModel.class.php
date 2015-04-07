<?php
namespace ct\models\events;

/**
 * @file
 * @brief Describe the Students Event
 * @author charybde
 *
 */

/**
 * @class StudentEventModel
 * @brief Class for making  operation on a student event
 */
class StudentEventModel extends EventModel{

	private $fields_st;
	
	function __construct(){
		parent::__construct();
		$this->fields_st = array("Id_Event" => "int", "Id_Owner" => "int");
	
		
		$this->table[1] = "student_event";

	}

	/**
	 *
	 * @brief Create an event and put it into the DB
	 * @param array $data The data provide by the user after being checked by the controller
	 * @retval mixed int the id of the created event if execute correctly error_info if not
	 */
	public function createEvent($data){
		$datas = $data;
		$ret = parent::createEvent($datas);
	
		if(!is_int($ret) || (is_bool($ret) && !$ret))
			return $ret;
		
		$datas = $this->checkParams($data, true, true);
		if($datas == -1)
			return false;
	
	
		$datas = array_intersect_key($datas, $this->fields_st);
		$datas["Id_Event"] = $ret;
		$a = $this->sql->insert($this->table[1], $datas);

		if($a)
			return $ret;
		else
			return $this->sql->error_info();
		
	}
	
	/**
	 * @brief create a range of identical events with the same recur number
	 * @param array $data the data (as you will insert if you only insert one) (erxept the date)
	 * @param int $n le nombre de fois que l'on veut inserer un event
	 * @return boolean|array false if error if ok an array containings ids newly inserted
	 */
	public function createBatchEvent($data, $n){
		$datas = $data;
		$ret = parent::createBatchEvent($data, $n);
	
		if(!$ret)
			return false;
	
		$datas = $this->checkParams($data, true, true);
		if($datas == -1)
			return false;
	
		if(isset($datas['Id_Event'])){
			return false;
		}
	
	
		$datas = array_intersect_key($datas, $this->fields_st);
		//Unquote stuff
		$datas = array_map("\ct\unquote", $datas);
		$datas['Id_Owner'] = \ct\get_numeric($datas['Id_Owner']);
		$datas["Id_Event"] = "";
		$key = array_keys($datas);
		$values = array();
	
		for($i = 0; $i < $n; ++$i) {
			$datas["Id_Event"] = $ret[$i];
			$d = \ct\array_flatten($datas);
			array_push($values, $d);
		}
			
	
	
		$a = $this->sql->insert_batch("student_event", $values, $key);
		if($a){
			return $ret;
		}
		else
			return false;
	
	}
}

?>