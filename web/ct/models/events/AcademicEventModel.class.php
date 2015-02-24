<?php

namespace ct\models\events;


/**
 * @brief Describe the AcademicEvent
 * @author charybde
 *
 */
class AcademicEventModel extends EventModel{

	private $fields_ac;
	
	function __construct(){
		parent::__construct();
		$this->fields = array_merge($this->fields, array("feedback" => "text", "workload" => "int", "practical_details" => "text"));
		$this->fields_ac = array("Id_Event" => "int", "Feedback" => "text", "Workload" => "int", "Practical_Details" => "text");
		
		$this->table[1] = "academic_event";
		$this->translate = array_merge($this->translate, array("feedback" => "Feedback", "workload" => "Workload", "practical_details" => "Practical_Details"));
	}

	/**
	 *
	 * @brief Create an event and put it into the DB
	 * @param array $data The data provide by the user after being checked by the controller
	 * @retval -1 if an error occurs
	 */
	public function createEvent($data){
		$datas = $data;
		$ret = array();
		$ret[0] = parent::createEvent($datas);
	
		$datas = $this->checkParams($data, true, true);
		if($datas == -1)
			return -1;
	
	
		$datas = array_intersect_key($datas, $this->fields_ac);
	
		$this->sql->insert($this->table[1], $datas);
		$ret[1] = $this->sql->error_info();
		return $ret;
	}
}


?>