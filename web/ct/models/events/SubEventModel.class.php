<?php

namespace ct\models\events;


/**
 * @brief Describe the SubEvents
 * @author charybde
 *
 */
use ct\models\PathwayModel;

use util\mvc\Model;
use ct\models\UserModel;

class SubEventModel extends AcademicEventModel{
	
	private $fields_sb;
	
	function __construct(){
		parent::__construct();
		$this->fields_sb = array("Id_Event" => "int", "Id_Global_Event" => "int");
		
		
		$this->table[2] ="sub_event";
	
	}
	
	public function getIdGlobal($eventId){
		$mod = new GlobalEventModel();
		$idGlob = $this->getEvent(array("id_event" => $eventId, array("id_globalEvent")));
		if(!empty($idGlob) && isset($idGlob[0]['Id_Global_Event'])){
			return $idGlob[0]['Id_Global_Event'];
		}
		else{
			return false;
		}
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
		
		$datas = array_intersect_key($datas, $this->fields_sb);
		$datas["Id_Event"] = $ret;
		$a = $this->sql->insert($this->table[2], $datas);
		
		
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
	
	
		$datas = array_intersect_key($datas, $this->fields_sb);
		//Unquote stuff
		$datas = array_map("\ct\unquote", $datas);
		$datas['Id_Global_Event'] = \ct\get_numeric($datas['Id_Global_Event']);
		$datas["Id_Event"] = "";
		$key = array_keys($datas);
		$values = array();
	
		for($i = 0; $i < $n; ++$i) {
			$datas["Id_Event"] = $ret[$i];
			$d = \ct\array_flatten($datas);
			array_push($values, $d);
		}
			
	
	
		$a = $this->sql->insert_batch("sub_event", $values, $key);
		if($a){
			return $ret;
		}
		else
			return false;
	
	}
	/**
	 * @brief return the team members of a subevent
	 * @param int $eventId
	 */
	public function getTeam($eventId, $lang = GlobalEventModel::LANG_FR){
		/*if(!$this->event_exists($eventId, Model::LOCKMODE_LOCK) || !$this->is_sub_event($eventId)){
			//TODO SET ERROR
			return false;
		}*/

		$idGlob = $this->getIdGlobal($eventId);
		
		if(!$idGlob)
				return false;
		
		
		
		if($lang === GlobalEventModel::LANG_FR)
			$lang_col = "Role_FR AS role";
		else
			$lang_col = "Role_EN AS role";
		
		$query = "SELECT Id_User AS user, Name AS name, Surname AS surname, role, Id_Role AS id_role
						FROM  user NATURAL JOIN
						( SELECT * FROM teaching_team_member WHERE Id_Global_Event = ".$idGlob." AND Id_User NOT in 
							(SELECT Id_User FROM sub_event_excluded_team_member WHERE Id_Event = ".$this->sql->quote($eventId)." AND Id_Global_Event = ".$idGlob."))
							 AS ttm
						NATURAL JOIN 
						( SELECT Id_Role, ".$lang_col." FROM teaching_role ) AS roles";
		return $this->sql->execute_query($query);
	}
	
	/**
	 * @brief exclude a Team Member for a specific sub event
	 * @param int $eventId
	 * @param int $userId
	 * @retval Boolean
	 */
	public function excludeMember($eventId, $userId){	
		$idGlob = $this->getIdGlobal($eventId);

		
		return $this->sql->insert("sub_event_excluded_member", array("Id_Event" => $eventId, "Id_User" => $userId, "Id_Global_Event" => $idGlob));
	}
	
	/**
	 * @brief get the different pathways a sub event is link to
	 * @param int $eventId
	 * @retval array (id,name_long, name_short)
	 */
	public function getPathways($eventId)
	{	
		$idGlob = $this->getIdGlobal($eventId);

		if(!$idGlob)
			return false;
		
		$query  =  "SELECT Id_Pathway AS id, Name_Long AS name_long, Name_Short AS name_short 
					FROM pathway NATURAL JOIN
					( SELECT Id_Pathway FROM 
					  ( SELECT * FROM global_event_pathway WHERE Id_Global_Event = ? ) AS glbs_path
					  WHERE Id_Pathway NOT IN 
					  	( SELECT Id_Pathway 
					  	  FROM sub_event_excluded_pathway 
					  	  WHERE Id_Global_Event = ? AND Id_Event = ? ) 
					) AS sub_paths;";

		return $this->sql->execute_query($query, array($idGlob, $idGlob, $eventId));
	}
	
	/**
	 * @brief exclude a pathway for a specific sub event
	 * @param int $eventId
	 * @param int $pathwayId
	 * @retval Boolean
	 */
	public function excludePathway($eventId, $pathwayId){
		$pM = new PathwayModel();
		if(!$pM->pathway_exists($pathwayId)){
			return false;
		}
		
		$idG = $this->getIdGlobal($eventId);
		return $this->sql->insert("sub_event_excluded_pathway", array("Id_Event" => $this->sql->quote($eventId), "Id_Pathway" => $this->sql->quote($pathwayId), "Id_Global_Event" => $idG));
	}
	
	/**
	 * @brief return an array containing the different id and name of the events where userId is part of the teaching team
	 * @param unknown_type $userId
	 */
	public function getEventByTeamMember($userId) {
		
		$userId = $this->sql->quote($userId);
		
		$query = "SELECT Id_Event AS id, name FROM event NATURAL JOIN sub_event
					NATURAL JOIN teaching_team_member WHERE Id_User =".$userId." AND Id_Event NOT in 
							(SELECT Id_Event FROM sub_event_excluded_team_member WHERE Id_User=".$userId.")";
		return $this->sql->execute_query($query);
	}
	
	

}


?>