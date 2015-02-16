
<?php

namespace util\mvc;

use util\mvc\EventModel;

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
		$this->fields_ac = array("id_event" => "int", "feedback" => "text", "workload" => "int", "practical_details" => "text");
		
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
		$ret = parent::createEvent($datas);
	
		$datas = $this->checkParams($data, true, true);
		if($datas == -1)
			return -1;
	
	
		$datas = array_intersect_key($datas, $this->fields_ac);
	
		return $ret && $this->sql->insert($this->table[1], $datas);
	}
}


?>