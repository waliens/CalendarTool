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
		$this->fields = array_merge($this->fields,  array("id_owner" => "int"));
		$this->fields_st = array("Id_Event" => "int", "Id_Owner" => "int");
		
		$this->translate = array_merge($this->translate,  array("id_owner" => "Id_Owner"));
		
		$this->table[1] = "Student_Event";

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
	
		if(!is_bool($ret) || !$ret)
			return $ret;
		
		$datas = $this->checkParams($data, true, true);
		if($datas == -1)
			return false;
	
	
		$datas = array_intersect_key($datas, $this->fields_st);
		$a = $this->sql->insert($this->table[1], $datas);

		if($a)
			return true;
		else
			return $this->sql->error_info();
		
	}
}

?>