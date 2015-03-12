<?php

namespace ct\models\events;

/**
 * @brief Describe the SubEvents
 * @author charybde
 *
 */
use ct\models\PathwayModel;

use ct\models\UserModel;

class IndependentEventModel extends AcademicEventModel{
	
	private $fields_ind;
	
	function __construct(){
		parent::__construct();
		$this->fields_ind = array("Id_Event" => "int", "Id_Owner" => "int", "Public" => "bool");

		$this->table = $this->table[2]= "independent_event";
	
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
	
		$datas = array_intersect_key($datas, $this->fields_ind);
		$datas["Id_Event"] = $ret;
		$a = $this->sql->insert($this->table[2], $datas);

		if($a)
			return $ret;
		else
			return $this->sql->error_info();
	}
	
	/**
	 * @brief add members to a team 
	 * @param int $eventId the id of the event
	 * @param array $teamMembers a multidimentionnal array st array(array(Id_User, Id_Role) , array(Id_User,...
	 * @retval boolean true if ok
	 */
	public function setTeam($eventId, array $teamMembers){
		if(!$this->event_exists($eventId, Model::LOCKMODE_LOCK) || !$this->is_independent_event($eventId)){
			//TODO SET ERROR
			return false;
		}
		
		$userM = new UserModel();
		$toinsert = array();
		foreach ($teamMembers as $key => $value){
			if(empty($userM->get_user($value[0]))){
				//TODO SET ERROR
				return false;	
			}
			array_push($toinsert, array($this->sql->quote($eventId),  $this->sql->quote($value[0]), $this->sql->quote($value[1])));
		}
		$collumn = array("Id_Event", "Id_User", "Id_Role");
		$this->sql->insert_batch("independent_event_manager", $toinsert, $collumn);
		return true;
	}
	/**
	 * @brief return the team of an idep event
	 * @param int $eventId the id of the event
	 * @retval array|boolean
	 */
	public function getTeam($eventId){
		if(!$this->event_exists($eventId, Model::LOCKMODE_LOCK) || !$this->is_independent_event($eventId)){
			//TODO SET ERROR
			return false;
		}

		return $this->sql->select("independent_event_manager", "Id_Event =".$this->sql->quote($eventId), array("Id_User", "Id_Role"));
	}
	
	
	/**
	 * @brief remove an user to the team
	 * @param int $eventId
	 * @param int $userId the user id (not the ulg one)
	 * @retval boolean
	 */
	public function removeFromTeam($eventId, $userId){
		if(!$this->event_exists($eventId, Model::LOCKMODE_LOCK) || !$this->is_independent_event($eventId)){
			//TODO SET ERROR
			return false;
		}
		$userM = new UserModel();
		if(empty($userM->get_user($userId))){
			//TODO ERROR
			return false;
		}
		
		return  $this->sql->delete("independant_event_management", "Id_Event=". $this->sql->quote($eventId)." AND Id_User=".$this->sql->quote($userId));
	}
	
	/**
	 * @brief return an array containing the ids and the names of the dfferents pathway of the indep event
	 * @param int $eventId
	 * @retval array|boolean if error
	 */
	public function getPathways($eventId){
		if(!$this->event_exists($eventId, Model::LOCKMODE_LOCK) || !$this->is_independent_event($eventId)){
			//TODO SET ERROR
			return false;
		}
		return $this->sql->select("independent_event_pathway NATURAL JOIN pathway", "Id_Event=".$eventId, array("Id_Pathway", "Name_Long", "Name_Short"));
	}
	
	/**
	 * @brief link the event with a pathway (can be done several tiume to multiples pâthways)
	 * @param int $eventId
	 * @param int $pathwayId
	 * @retval boolean
	 */
	public function setPathway($eventId, $pathwayId){
		if(!$this->event_exists($eventId, Model::LOCKMODE_LOCK) || !$this->is_independent_event($eventId)){
			//TODO SET ERROR
			return false;
		}
		
		$pM = new PathwayModel();
		if(!$pM->pathway_exists($pathwayId)){
			return false;
		}
		$data = array("Id_Pathway" => $pathwayId, "Id_Event" => $eventId);
		return $this->sql->insert("independent_event_pathway", $data);
	}
	
	/**
	 * @brief remove a pathway form the list of path way of an event
	 * @param int $eventId
	 * @param int $pathwayId
	 * @retval boolean
	 */
	public function removePathway($eventId, $pathwayId){
		if(!$this->event_exists($eventId, Model::LOCKMODE_LOCK) || !$this->is_independent_event($eventId)){
			//TODO SET ERROR
			return false;
		}
		
		$pM = new PathwayModel();
		if(!$pM->pathway_exists($pathwayId)){
			return false;
		}
		$data = "Id_Pathway=".$pathwayId." AND Id_Event=". $eventId;
		
		return $this->sql->delete("independent_event_pathway", $data);
	}
}


?>