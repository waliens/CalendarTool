<?php

/**
 * @file
* @brief Edit Private Event ControllerClass
*/

namespace ct\controllers\ajax;

use ct\models\events\StudentEventModel;
use util\mvc\AjaxController;
use util\superglobals\Superglobal;

/**
 * @class PrivateEventController
 * @brief Class for handling the create private event request
 */
class EditPrivateEventController extends AjaxController
{
	/**
	 * @brief Construct the PrivateEventController object and process the request
	 */
	public function __construct()
	{
		parent::__construct();

		// check if the expected keys are in the array
		$keys = array("id","name", "place","limit","start", "type", "recurrenceId", "details","applyRecursive");
	
		if($this->sg_post->check_keys($keys, Superglobal::CHK_ISSET) < 0)
		{
			$this->set_error_predefined(AjaxController::ERROR_MISSING_DATA);
			return;
		}

		// create private event
		$model = new StudentEventModel();

		$data = array("name" => $this->sg_post->value('name'),
				"description" => $this->sg_post->value('details'),
				"place" => $this->sg_post->value('place'),
				"id_category" => $this->sg_post->value('type'),
				"recurrence" => $this->sg_post->value('recurrenceId'));

		// get event date
		if($this->sg_post->value("limit") == "true"){
			$limit = new DateTime($this->sg_post->value("start"));
			$model->setDate($this->sg_post->value("id"), "Deadline", $limit,null, true);
		}
		elseif($this->sg_post->check_keys(array("start", "end")) > 0)
		{
			
			$start = new DateTime($this->sg_post->value('start'));
			$end = new DateTime($this->sg_post->value('end'));
			if($start->format("H:i:s") == "0:0:0")
				$model->setDate($this->sg_post->value("id"), "Date", $start, $end,true);
			else 
				$model->setDate($this->sg_post->value("id"), "TimeRange", $start, $end,true);				
			
		}


		// get owner id
		$data['id_owner'] = $this->connection->user_id();

		// check for recurrence
		$ret = false;
		if($this->sg_post->value('applyRecursive') == "true")
		{
				$ret = $model->modifyEvent(array("recurrence" => $this->sg_post->value('recurrenceId')), $data, true);
		}
		else
			$ret = $model->modifyEvent(array("id_event" => $this->sg_post->value('id')), $data);

		$this->add_output_data("ssucess", $ret);
	}
}


