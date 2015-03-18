<?php
namespace ct\models\events;

/**
 * @brief Describe the AcademicEvent
 * @author charybde
 *
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
}

?>