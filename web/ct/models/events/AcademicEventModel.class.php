<?php

namespace ct\models\events;


/**
 * @brief Describe the AcademicEvent
 * @author charybde
 *
 */
use ct\models\FileModel;

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
	
	
		$datas = array_intersect_key($datas, $this->fields_ac);
		$datas["Id_Event"] = $ret;
		$a = $this->sql->insert($this->table[1], $datas);

		if($a)
			return $ret;
		else
			$this->error .= "\n Error during Academic Event creation";
	}
	
	/**
	 * @brief upload a file to the server and link it to the envent
	 * @param string $file The key of the file entry in the $_FILES superglobal
	 * @param int $eventId
	 * @param int $userId optional the id of the owner of the file (default the current logged  user)
	 * @retval bool true if everything ok false if not
	 */
	public function upload_file($file, $eventId, $userId = null){
		if(!$this->is_academic_event($eventId))
			return false;
		
		$event = $this->sql->quote($eventId);
		
		$model = new FileModel();
		$path = "academic_event_files/".$eventId;
		$id = $model->add_file($path, $file);

		if($id == 0)
			return false;
		
		$insert = array("Id_File" => $id,
						"Id_Event" => $event);
		
		return $this->sql->insert("academic_event_file", $insert);
		
		
	}
	
	/**
	 * @brief delete a file from the server
	 * @param $fileId the id of the File
	 * @param $eventId the id of the event
	 * @retval bool true if everything were ok false otherwise
	 */
	public function delete_file($fileId, $eventId) {
		$model = new FileModel();
		
		if(!$model->delete_file($fileId))
			return false;
		
		$fid = $this->sql->quote($fileId);
		$event = $this->sql->quote($eventId);
		return $this->sql->delete("academic_event_file", "Id_Event = ".$event." AND Id_File = ".$fid);
	}

	/**
	 * @brief return the different pathway in which the event is involved
	 * @param int $eventId the id of the event
	 * @retval mixed the differents pathways or false if error 
	 */
	public function get_pathways($eventId) {
		$event = $this->sql->quote($eventId);
		$resp = $this->sql->select("academic_event_pathways", "Id_Academic_Event = ".$event, array("Id_Pathways)"));
		if(is_array($resp)){
			$pathways = array();
			foreach($resp AS $key => $value)
				array_push($pathways, $value);
			return $pathways;
		}
		else 
			return false;
	}
	
	
}



?>