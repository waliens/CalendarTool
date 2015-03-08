<?php

/**
 * @file
* @brief Event ControllerClass
*/

namespace ct\controllers\ajax;
use util\mvc\AjaxController;
use util\superglobals\Superglobal;

/**
 * @class Event
 * @brief Class for handling the control of event
 */

class GetEventTypeController extends AjaxController
{


	public function __construct()
	{
		parent::__construct();


		// create models
		$model = new EventModel();

		// get owner id
		$id = $this->connection->user_id();
		$eventId = $this->sg_post->value("id");


		$req = $model->getEvent(array("id_event" => $this->sg_post->value()), array("EventType", "DateType"));

		if(!isset($req[0])){
			$this->set_error_predefined(self::ERROR_MISSING_EVENT);
		}
		else{
			$data = $req[0];
			$this->output_data['event_type'] = $data['EventType'];
			$this->output_data['date_type'] = $data['DateType'];

		}


	}
}

