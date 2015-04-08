<?php

namespace ct\models\events;

/**
 * @file
 * @brief Describe the Independant events
 * @author charybde
 *
 */
use ct\models\PathwayModel;

use ct\models\UserModel;
use util\mvc\Model;

/**
 * @class IndependentEventModel
 * @brief Class for making  operation on an independent event
 */
class IndependentEventModel extends AcademicEventModel{
	
	private $fields_ind;
	
	function __construct(){
		parent::__construct();
		$this->fields_ind = array("Id_Event" => "int", "Id_Owner" => "int", "Public" => "bool");

		$this->table[2]= "independent_event";
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
		$a = $this->sql->insert("independent_event", $datas);
		if($a)
			return $ret;
		else
			return false;
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
	
	
		$datas = array_intersect_key($datas, $this->fields_ind);
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
			
	
	
		$a = $this->sql->insert_batch("independent_event", $values, $key);
		if($a){
			return $ret;
		}
		else
			return false;
	
	}
	
	/**
	 * @brief add members to a team 
	 * @param int $eventId the id of the event
	 * @param array $teamMembers a multidimentionnal array st array(array(id, role) , array(id,...
	 * @retval boolean true if ok
	 */
	public function setTeam($eventId, array $teamMembers){
	/*	if(!$this->event_exists($eventId, Model::LOCKMODE_LOCK) || !$this->is_independent_event($eventId)){
			//TODO SET ERROR
			return false;
		}*/
		
		$userM = new UserModel();
		$toinsert = array();
		foreach ($teamMembers as $key => $value){
			if(!$userM->user_id_exists($value['id'])){
		
				return false;	
			}
			$arr = array($eventId,  
						$value['id'], 
						$value['role']);
			array_push($toinsert, $arr);
		}
		
		$collumn = array("Id_Event", "Id_User", "Id_Role");
		$a = $this->sql->insert_batch("independent_event_manager", $toinsert, $collumn);
		return $a;
	}
	/**
	 * @brief return the team of an idep event
	 * @param int $eventId the id of the event
	 * @param null $lang used to have the same declaration in parent and in sub event
	 * @retval array|boolean
	 */
	public function getTeam($eventId , $lang = null){
		/*if(!$this->event_exists($eventId, Model::LOCKMODE_LOCK) || !$this->is_independent_event($eventId)){
			//TODO SET ERROR
			return false;
		}*/

		$query = "SELECT Id_User AS user, Name AS name, Surname AS surname, role, Id_Role AS id_role
		FROM  user NATURAL JOIN
			( SELECT * FROM independent_event_manager WHERE  Id_Event = ? )
				AS ttm
		NATURAL JOIN
			( SELECT Id_Role, Role_FR AS role FROM teaching_role ) AS roles
 ";
		
		$a = $this->sql->execute_query($query, array($eventId));
		return $a;
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
		if(!$userM->user_id_exists($value['id'])){
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
		return $this->sql->select("independent_event_pathway NATURAL JOIN pathway", 
								  "Id_Event=".$this->sql->quote($eventId), 
								  array("Id_Event", "Id_Pathway AS id", "Name_Long AS name_long", "Name_Short AS name_short"));
	}
	
	/**
	 * @brief link the event with a pathway (can be done several tiume to multiples pâthways)
	 * @param int $eventId
	 * @param int $pathwayId
	 * @retval boolean
	 */
	public function setPathway($eventId, $pathwayId){
		$data = array("Id_Pathway" => $this->sql->quote($pathwayId), "Id_Event" => $this->sql->quote($eventId));

		$a =  $this->sql->insert("independent_event_pathway", $data);
		
	}
	
	/**
	 * @brief remove a pathway form the list of path way of an event
	 * @param int $eventId
	 * @param int $pathwayId
	 * @retval boolean
	 */
	public function removePathway($eventId, $pathwayId){
		$data = "Id_Pathway='".$pathwayId."' AND Id_Event='". $this->sql->quote($eventId);
		
		$a =  $this->sql->delete("independent_event_pathway", $data);
		
	}

	/**
	 * @brief Return the list of users that can be added to the indep. event team
	 * @param[in] int $eventId The identifier of the event
	 * @retval array|bool An array containing the Id_User, Name and Surname of the people that can be added to the indep. event team
	 */
	public function getAddableUsers($eventId)
	{
		$query  =  "SELECT Id_User, Name, Surname FROM user
		 			WHERE Id_User NOT IN ( SELECT Id_User FROM independent_event_manager WHERE Id_Event = ? )
		 					AND Id_User != ?
		 			ORDER BY Name;";

		return $this->sql->execute_query($query, array($eventId, Connection::get_instance()->user_id()));
	}

	/**
	 * @brief Return the list of pathways that can still be associated with the event
	 * @param[in] int $eventId The identifier of the event
	 * @retval array|bool An array containing the Id_Pathway, Name_Long, Name_Short of the pathways that can be associated
	 */
	public function getAddablePathways($eventId)
	{
		$query  =  "SELECT Id_Pathway, Name_Long, Name_Short FROM pathway
		 			WHERE Id_Pathway NOT IN ( SELECT Id_Pathway FROM independent_event_pathway WHERE Id_Event = ? )
		 					AND Id_User != ?
		 			ORDER BY Name_Long;";

		return $this->sql->execute_query($query, array($eventId, Connection::get_instance()->user_id()));
	}
	
	public function reset_team($eventId){
		return $this->sql->delete("independent_event_manager", "Id_Event=".$this->sql->quote($eventId));
	}
}


?>