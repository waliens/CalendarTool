<?php

namespace ct\models\events;


/**
 * @brief Describe the SubEvents
 * @author charybde
 *
 */
use ct\models\UserModel;

class SubEventModel extends AcademicEventModel{
	
	private $fields_sb;
	
	function __construct(){
		parent::__construct();
		$this->fields_sb = array("Id_Event" => "int", "Id_Global_Event" => "int");
		
		
		$this->table[2] ="sub_event";
	
	}
	
	private function getIdGlobal($eventId){
		$mod = new GlobalEventModel();
		$idGlob = $this->getEvent(array("id_event" => $this->sql->quote($eventId), array("id_globalEvent"));
		if(!empty($idGlob) && isset($idGlob[0]['Id_Global_Event'])){
			return $idGlob[0]['Id_Global_Event'];
		}
		else{
			//Todo SEt etrror
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
			return $this->sql->error_info();
	}

	/**
	 * @brief return the team members of a subevent
	 * @param int $eventId
	 */
	public function getTeam($eventId, $lang = GlobalEventModel::LANG_FR){
		if(!$this->event_exists($eventId, Model::LOCKMODE_LOCK) || !$this->is_sub_event($eventId)){
			//TODO SET ERROR
			return false;
		}

		$idGlob = $this->getIdGlobal($eventId);
		if(!$idGlob)
				return false;
		
		
		if($lang === self::LANG_FR)
			$lang_col = "Role_FR AS role";
		else
			$lang_col = "Role_EN AS role";
		
		$query = "SELECT Id_User AS user, Name AS name, Surname AS surname, role, Description as `desc`
						FROM  user NATURAL JOIN
						( SELECT * FROM teaching_team_member WHERE Id_Global_Event = ".$idGlob." AND Id_User NOT in 
							(SELECT Id_User FROM sub_event_excluded_team_member WHERE Id_Event = ".$this->sql->quote($eventId)." AND Id_Global_Event = ".$idGlob."))
							 AS ttm
						NATURAL JOIN 
						( SELECT Id_Role, ".$lang_col." FROM teaching_role ) AS roles";
		$this->sql->execute_query($query);
	}
	
	/**
	 * @brief exclude a Team Member for a specific sub event
	 * @param int $eventId
	 * @param int $userId
	 * @retval Boolean
	 */
	public function excludeMember($eventId, $userId){
		if(!$this->event_exists($eventId, Model::LOCKMODE_LOCK) || !$this->is_sub_event($eventId)){
			//TODO SET ERROR
			return false;
		}
		$uM = new UserModel();
		if(empty($uM->get_user($userId))){
			return false;
		}
		
		$idGlob = $this->getIdGlobal($eventId);
		if(!$idGlob)
			return false;
		
		return $this->sql->insert("sub_event_excluded_member", array("Id_Event" => $eventId, "Id_User" => $userId, "Id_Global_Event" => $idGlob));
	}
	
	

}


?>