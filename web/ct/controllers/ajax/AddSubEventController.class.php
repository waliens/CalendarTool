<?php

/**
 * @file
* @brief Sub Event  AddingControllerClass
*/

use ct\models\notifiers\EventModificationNotifier;

namespace ct\controllers\ajax;

use util\mvc\AjaxController;
use util\superglobals\Superglobal;
use ct\models\events\SubEventModel;

use \DateTime;


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
		$keys = array("name", "details","limit", "start", "id_global_event", "where", "workload", "feedback", "practical_details", "type", "recurrence", "pathway", "teachingTeam");
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
				"id_GlobalEvent" => $this->sg_post->value('id_global_event'),
				"feedback" => $this->sg_post->value('feedback'),
				"workload" => $this->sg_post->value('workload'),
				"practical_details" => $this->sg_post->value('practical_details'));
			
		// get event date
		if($this->sg_post->value('limit') == "true")
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


		// check for recurrence
		$id_ret = array(); // new private event id

		if($this->sg_post->value('recurrence') != 6
				&& $this->sg_post->check("end-recurrence"))
		{
			$endrec = new DateTime($this->sg_post->value('end-recurrence'));
			$id_ret = $model->createEventWithRecurrence($data, $this->sg_post->value('recurrence'), $endrec);
		}
		else
			$id_ret[0] = $model->createEvent($data);
		
		if($this->sg_post->value('limit') == "true")
			new EventModificationNotifier(EventModificationNotifier::ADD_DL, $id_ret[0]);

		$this->add_output_data("id", $id_ret);
	
		$pathway = $this->json2array($this->sg_post->value('pathway'));
		$team = $this->json2array($this->sg_post->value('teachingTeam'));

		foreach($pathway as $key => $value){
			if(!$value['selected']){
				foreach($id_ret as $o => $id)
					$model->excludePathway($id, $value['id']);
			}
		}
		foreach($team as $key => $value){
			if(!$value['selected']){
				foreach($id_ret as $o => $id)
					$model->excludeMember($id, $value['id']);
			}
		}
		
		if($this->sg_post->check("attachments") < 0)
			return;
		
		$attach = $this->sg_post->value('attachments');
		foreach($attach as $key =>  $value){
			foreach($id_ret as $o => $id)
				$model->uploadFile($value["id"], $id);
		}
		
	}
}