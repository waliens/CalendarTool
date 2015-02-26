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
	 * @retval mixed true if execute correctly error_info if not
	 */
	public function createEvent($data){
		$datas = $data;
		$ret = parent::createEvent($datas);
	
		if(!is_int($ret) || (is_bool($ret) && !$ret))
			return $ret;
			
		$datas = $this->checkParams($data, true, true);
		if($datas == -1)
			return false;
	
	
		$datas = array_intersect_key($datas, $this->fields_ac);
		$datas["Id_Event"] = $ret;
		$a = $this->sql->insert($this->table[1], $datas);

		if($a)
			return $ret;
		else
			return $this->sql->error_info();
	}
	
	/**
	 * @brief upload a file to the server and link it to the envent
	 * @param FILE $file
	 * @retval bool true if everything ok
	 */
	public function upload_file($file){}
	
	/**
	 * @brief delete a file from the server
	 * @param int $id
	 * @retval bool true if everything ok
	 */
	public function delete_file($id) {}

	/**
	 * @brief return the different pathway in which the event is involved
	 * @param int $eventId the id of the event
	 * @retval mixed the differents pathways or false if not
	 */
	public function get_pathways($eventId) {}
	
	
}



?>