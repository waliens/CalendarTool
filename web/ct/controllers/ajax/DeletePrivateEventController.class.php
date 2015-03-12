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

class DeletePrivateEventController extends AjaxController
{


	public function __construct()
	{
		parent::__construct();


		// create private event
		$model = new StudentEventModel();

		// get owner id
		$id = $this->connection->user_id();
		$eventId = $this->sg_post->value("id");
		$recur = $this->sg_post->value("applyRecursive");
		

		$verif = $model->getEvent(array("id_event" => $eventId), array("id_owner", "id_recurrence"));

		if(!isset($verif[0]['Id_Owner']) || intval($verif[0]['Id_Owner']) != intval($id)){
			$this->set_error_predefined(self::ERROR_ACCESS_DENIED);
		}
		else{
			if($recur){
				$success = $model->deleteEventRecurrence($verif[0]['Id_Recurrence']);
			}
			else {
				$success = $model->deleteEvent(intval($eventId));
			}
			if(!$success){
				$this->set_error_predefined(self::ERROR);
			}
		}

		$this->output_data['id'] = $eventId;

	}


}


