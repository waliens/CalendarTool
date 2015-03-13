<?php

/**
 * @file
* @brief Sub Event  AddingControllerClass
*/

namespace ct\controllers\ajax;

use util\mvc\AjaxController;
use util\superglobals\Superglobal;
use ct\models\SubEventModel;

/**
 * @class PrivateEventController
 * @brief Class for handling the create private event request
 */
class AddSubEventController extends AjaxController
{
	/**
	 * @brief Construct the PrivateEventController object and process the request
	 */
	public function __construct()
	{
		parent::__construct();

		// check if the expected keys are in the array
		$keys = array("name", "details","id_global_event", "where", "start", "end", "type", "recurrence", "pathway", "teaching_team");
		if($this->sg_post->check_keys($keys, Superglobal::CHK_ISSET) < 0)
		{
			$this->set_error_predefined(AjaxController::ERROR_MISSING_DATA);
			return;
		}

		// create private event
		$model = new SubEventModel();

		$data = array("name" => $this->sg_post->value('name'),
				"description" => $this->sg_post->value('details'),
				"place" => $this->sg_post->value('where'),
				"id_category" => $this->sg_post->value('type'),
				"id_global_event" => $this->sg_post->value('id__global_event'));
			
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
			$this->set_error_predefined(AjaxController::ERROR_MISSING_DATA);
			return;
		}

		
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


		$this->add_output_data("id", $id_ret);
	
		$pathway = $this->sg_post->value('pathway');
		$team = $this->sg_post->value('teaching_team');
		
		foreach($pathway as $key => $value){
			if($value['selected'] == "false"){
				foreach($id_ret as $o => $id)
					$model->excludePathway($id, $value['id']);
			}
		}
		foreach($team as $key => $value){
			if($value['selected'] == "false"){
				foreach($id_ret as $o => $id)
					$model->excludeMember($id, $value['id']);
			}
		}
		
		if(!$this->sg_post->check("attachments"))
			return;
		
		$attach = $this->sg_post->value('attachments');
		foreach($attach as $key =>  $value){
			foreach($id_ret as $o => $id)
				$model->uploadFile($value["id"], $id);
		}
		
	}
}



