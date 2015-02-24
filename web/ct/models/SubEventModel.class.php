<?php

namespace ct\model;


/**
 * @brief Describe the SubEvents
 * @author charybde
 *
 */
class SubEventModel extends AcademicEventModel{
	
	private $fields_sb;
	
	function __construct(){
		parent::__construct();
		$this->fields = array_merge($this->fields, array("id_GlobalEvent" => "int"));
		$this->fields_sb = array("Id_Event" => "int", "Id_Global_Event" => "int");
		
		$this->translate = array_merge($this->translate, array("id_GlobalEvent" => "Id_Global_Event"));
		
		$this->table[2] ="sub_event";
	
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
		
		
		$datas = array_intersect_key($datas, $this->fields_sb);
		$this->sql->insert($this->table[2], $datas);
		$ret[2] = $this->sql->error_info();
		return $ret;
	}
}


?>