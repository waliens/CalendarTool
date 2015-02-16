<?php
namespace util\mvc;

use util\mvc\EventModel;

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
		$this->fields_st = array("id_event" => "int", "id_owner" => "int");
		
		$this->translate = array_merge($this->translate,  array("id_owner" => "Id_Owner"));
		
		$this->table[1] = "Student_Event";

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
	
	
		$datas = array_intersect_key($datas, $this->fields_st);
	
		return $ret && $this->sql->insert($this->table[1], $datas);
	}
}

?>