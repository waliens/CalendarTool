<?php

namespace ct\models\events;


/**
 * @file
 * @brief Describe the AcademicEvent
 * @author charybde
 *
 */
use ct\models\FileModel;
use ct\models\filters\PathwayFilter;
use ct\models\filters\DateTimeFilter;
use ct\models\FilterCollectionModel;
/**
 * @class AcademicEventModel
 * @brief Class for making generic operation on an  academic event
 */
class AcademicEventModel extends EventModel{

	private $fields_ac;
	
	function __construct(){
		parent::__construct();
		$this->fields_ac = array("Id_Event" => "int", "Feedback" => "text", "Workload" => "int", "Practical_Details" => "text");		
		$this->table[1] = "academic_event";

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
		$a = $this->sql->insert("academic_event", $datas);
		if($a)
			return $ret;
		else
			return false;
	}

	/**
	 * @copydoc EventModel::getEvent
	 */
	public function getEvent (array $infoData = null, array $requestedData = null)
	{
		$event = parent::getEvent($infoData, $requestedData);

		// check whether the getEvent has worked
		if(!$event)
			return false;
					
		if($requestedData != null || !isset($infoData['id_event']))
			return $event;
		
		// get the academic event data
		$acad_event = $this->sql->select_one("academic_event", "Id_Event = ".$this->sql->quote($infoData['id_event']));

		return !!$acad_event ? array(array_merge($event[0], $acad_event)) : $event;
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
	
	
		$datas = array_intersect_key($datas, $this->fields_ac);
		//Unquote stuff
		$datas = array_map("\ct\unquote", $datas);
		$datas["Id_Event"] = "";
		$key = array_keys($datas);
		$values = array();
		
		for($i = 0; $i < $n; ++$i) {	
			$datas["Id_Event"] = $ret[$i];
			$d = \ct\array_flatten($datas);
			array_push($values, $d);
		}
			
	
	
		$a = $this->sql->insert_batch("academic_event", $values, $key);
		if($a){
			return $ret;
		}
		else
			return false;
	
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
	
	//Those functions are particularized in sub classes
	public function getTeam($eventId, $lang = null) { return array(); }
	public function getPathways($eventId) { return array(); }

	/**
	 * @brief usefull to know if an user is part of a team
	 * @param unknown_type $eventId
	 * @param unknown_type $userId
	 */
	public function isInTeam($eventId, $userId){
		$team = $this->getTeam($eventId);
		if(!$team)
			return false;
		foreach($team as $key => $value){
			if($value['user'] == $userId)
				return true;
		}
		return false;
	}
	
	/**
	 * @brief Detect if there is some event for the give pathways happening in the give start end interval
	 * @param DateTime $start
	 * @param DateTime $end
	 * @param array $pathways
	 * @retval array  array of array("id", "name", "pathway_name") of the conflict false if no conflict
	 */
	public function conflictWarning($start, $end, $pathways){
		
		$filt = new PathwayFilter($pathways);
		$filt2 = new DateTimeFilter($start->format("d-m-Y H:i:s"), $end->format("d-m-Y H:i:s"));
		
		$col = new FilterCollectionModel();
		$col->add_filter($filt1);
		$col->add_filter($filt2);
		
		$ids = $col->get_filtered_events_ids();
		$ret = array();
		foreach($ids as $key => $value){
			$event = $this->getEvent(array("id_event" => $value), array("name", "start", "end"));
			$eventRet['name'] = $event['Name'];
			$eventRet['start'] = $event['Start'];
			$eventRet['end'] = $event['End'];
			
			$pathEvent = $this->getPathways($value);
			foreach($pathEvent as $o => $path){
				if(in_array($path['id'], $pathways)){
					$eventRet["pathway"] = $path['name_short'];
					
					array_push($ret, $event);
				}
			}
		}
		return empty($ret) ? false : $ret;
		
	}
	
	
}



?>