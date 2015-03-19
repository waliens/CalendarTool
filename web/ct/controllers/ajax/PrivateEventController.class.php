<?php

/**
 * @file
 * @brief Creating Private Event ControllerClass
 */

namespace ct\controllers\ajax;

use ct\models\events\StudentEventModel;
use util\mvc\AjaxController;
use util\superglobals\Superglobal;

/**
 * @class PrivateEventController
 * @brief Class for handling the create private event request
 */
class PrivateEventController extends AjaxController
{
	/** 
	 * @brief Construct the PrivateEventController object and process the request
	 */
	public function __construct() 
	{
		parent::__construct();

		// check if the expected keys are in the array
		$keys = array("name","limit","start", "place", "type", "recurrence", "details", "end-recurrence", "type");

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
					  "id_category" => $this->sg_post->value('type'));

		// get event date 
		if($this->sg_post->value('limit') == "true"){
			$data['limit'] = $this->sg_post->value('start');
		}
		elseif($this->sg_post->check_keys(array("end")) > 0)
		{
			$data['start'] = $this->sg_post->value('start');
			$data['end'] = $this->sg_post->value('end');
		}
		else
		{
			$this->set_error_predefined(AjaxController::ERROR_MISSING_DATA);
			return;
		}
		
		// get owner id
		$data['id_owner'] = $this->connection->user_id();
		
		// check for recurrence
		$id_ret = array(); // new private event id

		if($this->sg_post->value('recurrence') != 0 
			&& $this->sg_post->check("end-recurrence"))
		{

			$endrec = new \DateTime($this->sg_post->value('end-recurrence'));
			$id_ret = $model->createEventWithRecurrence($data, $this->sg_post->value('recurrence'), $endrec);
		}
		else
			$id_ret[0] = $model->createEvent($data);
		
		// add annotation if necessary
		if($this->sg_post->check("note") > 0)
			foreach($id_ret as $key => $value)
				$model->set_annotation($value, $data['id_owner'], $this->sg_post->value('note'));
		if($id_ret == false)
			$this->set_error_predefined(self::ERROR_ACTION_ADD_DATA);
		$this->add_output_data("id", $id_ret);
	}
}


