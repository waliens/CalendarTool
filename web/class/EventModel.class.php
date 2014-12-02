<?php

//Maybe this class should be abstract
class EventModel extends Model{
	protected $_name, 
			  $_description,
			  $_id,
			  $_place,
			  $_category; // @type : EventCategoryModel
	

	public function __construct($id){
		
		$datas = $bdd->//Get datas from BDD (through SQLAbstract_PDO class heritence) 
		$this->hydrate($datas);
	}
	
	protected function hydrate(array $datas){
		foreach ($datas as $key => $value){
			$method = 'set'.ucfirst($key); //Ucfirst put the first hract uppercase
			// If setter exist use the method
			if (method_exists($this, $method)){
				$this->$method($value);
			}
		}
	}

	protected function setId($id){
		$this->_id = $id;
	}
	
	protected function setName($name){
		$this->_name = $name;
	}
	
	protected function setDescription($desc){
		$this->_description = $desc;
	}
	
	protected function setPlace($place){
		$this->_place = $place;
	}
	
	protected function setCategory($id){
		$this->_category = new EventCategoryModel($id);
	}
}
?>