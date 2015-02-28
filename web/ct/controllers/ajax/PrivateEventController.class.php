<?php

/**
 * @file
 * @brief Private Event ControllerClass
 */

namespace ct\controllers\ajax;
use ct\models\events\StudentEventModel;

use util\mvc\AjaxController;

use ct;

/**
 * @class Event
 * @brief Class for handling the control of event
 */
use nhitec\sql\SQLAbstract_PDO;

use ct\util\database\Database;

class PrivateEventController extends AjaxController{
	
	
	public function __construct() {
		parent::__construct();
		
		if(isset($this->input_json)){
			$id_ret = array();
			$model = new StudentEventModel();
			$data = array("name" => $this->input_data['name'],
							"description" => $this->input_data['details'],
							"place" => $this->input_data['place'],
							"id_category" => $this->input_data['type'],
						);
			if(isset($this->input_data['limit']))
				$data['limit'] = $this->input_data['limit'];
			else{
				$data['start'] = $this->input_data['start'];
				$data['end'] = $this->input_data['end'];
			}
			
			$data['id_owner'] = $this->connection->user_id();
			
			if($this->input_data['recurrence'] != 0){
				$endrec = new DateTime($this->input_data['end-recurrence']);
				
				$id_ret = $model->createEventWithRecurrence($data, $this->input_data['recurrence'], $endrec);
			}
			else {
				$id_ret[0] = $model->createEvent($data);
			}
			
			if(isset($this->input_data['note'])){
				foreach($id_ret as $key => $value){
					$model->set_annotation($value, $data['id_owner'], $this->input_data['note']);
				}
			}
			
			$this->output_data['id'] = $id_ret;
			$this->output_data['error'] = false;//
		}
	}
	
	
}