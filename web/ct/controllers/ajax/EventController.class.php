<?php

/**
 * @file
 * @brief Event ControllerClass
 */

namespace ct\controller;
use ct;

/**
 * @class Event
 * @brief Class for handling the control of event
 */
use nhitec\sql\SQLAbstract_PDO;

use ct\util\database\Database;

class Event extends Controller{
	
	
	public function __construct() {
		parent::__construct();
	}
	

	
	/**
	 * @brief return one or several event according to the $which criteria
	 * @param array $which criteria to identify the event
	 * @param string $type type of the concern Event (event by default)
	 * @param array $data what you want to know about this event
	 */
	public function getEvent(array $which, array $data = null, $type = null){
		if($type == null)
			$type = "Event";
	
		return instantiateModel($type)->getEvent($which, $data);
	}
	

	/**
	 * @brief create an event from POST data
	 * @param string $type type of the concerne Event
	 * @retval if missing info return an array containing a list of bad info
	 */
	public function createPOSTEvent($type = null){
		if($type == null)
			$type = "Event";
		
		$model = instantiateEventModel($type);
		$fields = $model->getFields();
		
		$badInfo = array();
		
		foreach($fields as $key => $value){
			if(!($this->sg_post->check($key)) == ERR_OK){
				$badInfo[$key] = $key;
			}
		} 
		
		if(!isempty($badInfo))
			return $badInfo;
		
		return $model->createEvent($this->sg_post);
	}
	
	
	
	
	
	
}