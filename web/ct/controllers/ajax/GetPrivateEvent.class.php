<?php

/**
 * @file
 * @brief Private Event ControllerClass
 */

namespace ct\controllers\ajax;


use ct\models\events\StudentEventModel;
use util\mvc\AjaxController;
use util\superglobals\Superglobal;

/**
 * @class Event
 * @brief Class for handling the control of event
 */

class GetPrivateEventController extends AjaxController
{

		
	public function __construct() 
	{
		parent::__construct();


		// create private event
		$model = new StudentEventModel();

		// get owner id
		$id = $this->connection->user_id();
		
		$ret = array(); 
		
		$ret_sql = $model->getEvent(array("id_owner" => $id), array("id_event", "name", "id_recurrence"));
		
		foreach($ret as $key => $value){
			array_push($ret, array("name" => $value["Name"], "id" => $value['Id_Event'], "recurrence" => $value['Id_Recurrence']));
		}

		$this->output_data['event'] = $ret;
		$this->set_error($model->get_error());
		
	}
	

}


