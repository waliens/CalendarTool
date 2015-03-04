<?php

/**
 * @file
* @brief Deleting note ControllerClass
*/

namespace ct\controllers\ajax;


use ct\models\events\EventModel;
use util\mvc\AjaxController;
use util\superglobals\Superglobal;

/**
 * @class Event
 * @brief Class for handling the control of event
 */

class DeleteController extends AjaxController
{


	public function __construct($update = false)
	{
		parent::__construct();

		// check if the expected keys are in the array
		$keys = array("id_event");

		if($this->sg_post->check_keys($keys, Superglobal::CHK_ISSET) < 0)
		{
			$this->set_error("Missing data");
			return;
		}

		// create model
		$model = new EventModel();

		// get data
		$userId = $this->connection->user_id();
		$eventId = $this->sg_post->value("id_event");
		
		$model->delete_annotation($eventId, $userId);
		
		$this->set_error($model->get_error());

	}


}


