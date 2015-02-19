<?php

namespace ct\model;

/**
 * @brief Describe the SubEvents
 * @author charybde
 *
 */
class IndependentEventModel extends AcademicEventModel{
	
	private $fields_ind;
	
	function __construct(){
		parent::__construct();
		$this->fields = array_merge($this->fields, array("id_owner" => "int", "public" => "bool"));
		$this->fields_ind = array("Id_Event" => "int", "Id_Owner" => "int", "Public" => "bool");
		$this->translate = array_merge($this->translate, array("id_owner" => "Id_Owner", "Public" => "Public"));
		$this->table = $this->table[2]= "independent_event";
	
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
	
	
		$datas = array_intersect_key($datas, $this->fields_ind);
		$this->sql->insert($this->table[2], $datas);
		$ret[2] = $this->sql->error_info();
		return $ret;
	}
	
}


?>