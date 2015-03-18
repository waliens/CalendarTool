<?php

/**
 * @file
* @brief Indep Event  AddingControllerClass
*/

namespace ct\controllers\ajax;

use ct\models\events\IndependentEventModel;

use util\mvc\AjaxController;
use util\superglobals\Superglobal;
use \DateTime;


/**
 * @class PrivateEventController
 * @brief Class for handling the create private event request
 */
class AddIndepEventController extends AjaxController
{
	/**
	 * @brief Construct the PrivateEventController object and process the request
	 */
	public function __construct()
	{
		parent::__construct();

		// check if the expected keys are in the array
		$keys = array("name", "details", "limit","where", "start", "workload", "feedback", "practical_details", "type", "recurrence", "pathways", "teaching_team");
		if($this->sg_post->check_keys($keys, Superglobal::CHK_ISSET) < 0)
		{
			$this->set_error_predefined(AjaxController::ERROR_MISSING_DATA);
			return;
		}

		// create private event
		$model = new IndependentEventModel();
		
		$data = array("name" => $this->sg_post->value('name'),
				"description" => $this->sg_post->value('details'),
				"place" => $this->sg_post->value('where'),
				"id_category" => $this->sg_post->value('type'),
				"feedback" => $this->sg_post->value('feedback'),
				"workload" => $this->sg_post->value('workload'),
				"practical_details" => $this->sg_post->value('practical_details'));
			
		// get event date
		if($this->sg_post->value("limit") == "true")
			$data['limit'] = $this->sg_post->value('start');
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


		$this->add_output_data("id", $id_ret);

		$pathway = $this->sg_post->value('pathways');
		$team = $this->sg_post->value('teaching_team');

		foreach($pathway as $key => $value){
			foreach($id_ret as $o => $id)
				$model->setPathway($id, $value['id']);			
		}
		
		foreach($id_ret as $o => $id)
			$model->setTeam($id, $team);
		

		if($this->sg_post->check("attachments") < 0)
			return;

		$attach = $this->sg_post->value('attachments');
		foreach($attach as $key =>  $value){
			foreach($id_ret as $o => $id)
				$model->uploadFile($value["id"], $id);
		}

	}
}



