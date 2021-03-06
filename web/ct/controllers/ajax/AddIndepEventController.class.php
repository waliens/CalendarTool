<?php

/**
 * @file
* @brief Indep Event  AddingControllerClass
*/

namespace ct\controllers\ajax;

use ct\models\notifiers\EventModificationNotifier;

use ct\models\events\IndependentEventModel;

use util\mvc\AjaxController;
use util\superglobals\Superglobal;
use \DateTime;


/**
 * @class AddIndepEventController
 * @brief Request Nr : 081
 * 		INPUT : {name, feedback, workload, practical_details, details, where, entireDay, limit, start, end, type, recurrence, end-recurrence, pathways:[], teaching_team: [{id, role}], attachments:[{id, url, name}], softAdd}
 * 		OUTPUT : {id, error{.... conflict[{pathway, name, start, end}]}
 * 		Method : POST
 */


class AddIndepEventController extends AjaxController
{
	
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
		if($this->sg_post->value("limit") == "true"){
			$data['limit'] = $this->sg_post->value('start');
		}	
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

		if(empty($pathway))
			$data['public'] = 1;

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

		//if($this->sg_post->value("limit") == "true")
		//	new EventModificationNotifier(EventModificationNotifier::ADD_DL, $id_ret[0]);
		
		$this->add_output_data("id", $id_ret);

		
		$pathway = $this->json2array($this->sg_post->value('pathways'));
		$team = $this->json2array($this->sg_post->value('teaching_team'));

		$you = array("id" =>  $this->connection->user_id(), "role" => 1);

		
		foreach($id_ret as $o => $id){
			foreach($pathway as $key => $value){
				$model->setPathway($id, $value);
			}
			if(!empty($team))
				$model->setTeam($id, $team);
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



