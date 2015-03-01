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

class PrivateEventController extends AjaxController
{

		
	public function __construct() 
	{
		parent::__construct();

		// check if the expected keys are in the array
		$keys = array("name", "place", "type", "recurrence", "details", "end-recurrence");

		if($this->sg_post->check_keys($keys, Superglobal::CHK_ISSET) < 0)
		{
			$this->set_error("Missing data");
			return;
		}

		// create private event
		$model = new StudentEventModel();

		$data = array("name" => $this->sg_post->value('name'),
					  "description" => $this->sg_post->value('details'),
					  "place" => $this->sg_post->value('place'),
					  "id_category" => $this->sg_post->value('type'));

		// get event date 
		if($this->sg_post->check("limit") > 0)
			$data['limit'] = $this->sg_post->value('limit');
		elseif($this->sg_post->check_keys(array("start", "end")) > 0)
		{
			$data['start'] = $this->sg_post->value('start');
			$data['end'] = $this->sg_post->value('end');
		}
		else
		{
			$this->set_error("Missing time data");
			return;
		}
		
		// get owner id
		$data['id_owner'] = $this->connection->user_id();
		
		// check for recurrence
		$id_ret = array(); // new private event id

		if($this->sg_post->value('recurrence') != 0 
			&& $this->sg_post->check("end-recurrence"))
		{

			$endrec = new DateTime($this->sg_post->value('end-recurrence'));
			$id_ret = $model->createEventWithRecurrence($data, $this->sg_post->value('recurrence'), $endrec);
		}
		else
			$id_ret[0] = $model->createEvent($data);
		
		// add annotation if necessary
		if($this->sg_post->check("note") > 0)
			foreach($id_ret as $key => $value)
				$model->set_annotation($value, $data['id_owner'], $this->sg_post->value('note'));
		
		$this->output_data['id'] = $id_ret;
		$this->set_error($model->get_error());
		
	}
	

}


