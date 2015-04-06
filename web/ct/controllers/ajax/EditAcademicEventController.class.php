<?php
/**
 * @file
* @brief Edit Academic (sub & indep) Event ControllerClass
*/

namespace ct\controllers\ajax;

use util\mvc\AjaxController;
use util\superglobals\Superglobal;
use \DateTime;
use ct\models\events\SubEventModel;
use ct\models\events\IndependentEventModel;

/**
 * @class PrivateEventController
 * @brief Class for handling the create private event request
 */
class EditAcademicEventController extends AjaxController
{
	
	public function __construct($sub)
	{
		parent::__construct();
		
		// check if the expected keys are in the array
		$keys = array("id","name","where", "type", "details","applyRecursive", "pathways", "teachingTeam", "pract_details", "feedback");

		if($this->sg_post->check_keys($keys, Superglobal::CHK_ISSET) < 0)
		{
			$this->set_error_predefined(AjaxController::ERROR_MISSING_DATA);
			return;
		}

		$model;
		// create model event
		if($sub)
			$model = new SubEventModel();
		else
			$model = new IndependentEventModel();
		
		$id = $this->connection->user_id();
		if(!$model->isInTeam($this->sg_post->value("id"), $id)){
			$this->set_error_predefined(self::ERROR_ACCESS_DENIED);
			return;
		} 
		$data = array("name" => $this->sg_post->value('name'),
				"description" => $this->sg_post->value('details'),
				"place" => $this->sg_post->value('where'),
				"id_category" => $this->sg_post->value('type'),
				"practical_details" => $this->sg_post->value('pract_details'),
				"workload" => $this->sg_post->value("workload"),
				"feedback" => $this->sg_post->value("feedback"));

		// get event date
		if($this->sg_post->check("limit") > 0){
			$limit = new DateTime($this->sg_post->value("limit"));
			$model->setDate($this->sg_post->value("id"), "Deadline", $limit, null, true);
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
		$data['id_owner'] = $id;

		// check for recurrence
		$ret = false;
		if($this->sg_post->value('applyRecursive') == "true")
		{
			$ret = $model->modifyEvent(array("recurrence" => $this->sg_post->value('recurrenceId')), $data, true);
		}
		else
			$ret = $model->modifyEvent(array("id_event" => $this->sg_post->value('id')), $data);

		$this->add_output_data("sucess", $ret);
	}
}


