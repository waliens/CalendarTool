<?php
/**
 * @file
* @brief  Event ControllerClass
*/

namespace ct\controllers\ajax;


use ct\models\events\SubEventModel;

use ct\models\events\StudentEventModel;
use util\mvc\AjaxController;
use util\superglobals\Superglobal;

/**
 * @class Event
 * @brief Class for handling the control of event
 */

class DeleteEventController extends AjaxController
{


	public function __construct($type)
	{
		parent::__construct();
		$model;
		$sub = false;
		$priv = false;
		
		if($type == "SUB")
			$sub = true;
		if($type == "PRIVATE")
			$priv = true;
		
		// create models
		if($priv){
			$model = new StudentEventModel();
			$id = $this->connection->user_id();
		}
		if($sub)
			$model = new SubEventModel();
		
		
		$eventId = $this->sg_post->value("id");
		$recur = $this->sg_post->value("applyRecursive");
		
		if($priv)
			$verif = $model->getEvent(array("id_event" => $eventId), array("id_owner", "id_recurrence"));
		else
			$verif = $model->getEvent(array("id_event" => $eventId), array("id_recurrence"));
				
		
		if($priv && (!isset($verif[0]) || intval($verif[0]['Id_Owner']) != intval($id))){
			$this->set_error_predefined(self::ERROR_ACCESS_DENIED);
		}
		else{
			if($recur == "true"){
				$success = $model->deleteEventRecurrence($verif[0]['Id_Recurrence']);
			}
			else {
				$success = $model->delete_event(intval($eventId));
			}
			if(!$success){
				$this->set_error_predefined(self::ERROR_ACTION_DELETE_DATA);
			}
		}

		$this->output_data['id'] = $eventId;

	}


}


