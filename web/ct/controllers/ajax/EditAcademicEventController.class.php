<?php
/**
 * @file
* @brief Edit Academic (sub & indep) Event ControllerClass
*/

namespace ct\controllers\ajax;

use ct\models\notifiers\EventModificationNotifier;

use util\mvc\AjaxController;
use util\superglobals\Superglobal;
use \DateTime;
use ct\models\events\SubEventModel;
use ct\models\events\IndependentEventModel;
/**
 * @class EditAcademicEventController
 * @brief Request Nr : 054, 085
 * 		INPUT : {id, name, details, where, entireDay, start, end, deadline, type, pract_details, feedback, workload, pathways:[{id,selected}], teachingTeam:[{id,selected}], applyRecursive}
 * 		OUTPUT :
 * 		Method : POST
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


		// get owner id
		if(!$sub)
			$data['id_owner'] = $id;

		// check for recurrence
		$ret = false;
		if($this->sg_post->value('applyRecursive') == "true")
		{
			$idRec = $model->getEvent(array("id_event" => $this->sg_post->value('id')), array("id_recurrence") );
			if(!$idRec)
				return;
			$idRec = $idRec[0]["Id_Recurrence"];
		
			$ret = $model->modifyEvent(array("id_recurrence" => $idRec), $data, true);
		}
		else
			$ret = $model->modifyEvent(array("id_event" => $this->sg_post->value('id')), $data);

		
		//Pathway & team
		$pathway = $this->json2array($this->sg_post->value('pathways'));
		$team = $this->json2array($this->sg_post->value('teachingTeam'));

		if($this->sg_post->value('applyRecursive') == "true"){
			$idRec = $model->getEvent(array("id_event" => $this->sg_post->value('id')), array("id_recurrence") );
			if(!$idRec)
				return;
			$idRec = $idRec[0]["Id_Recurrence"];
			$ids = $model->getEvent(array("id_recurrence" => $idRec), array("id_event") );
			$ids = \ct\array_flatten($ids);
			foreach($ids as $o => $id){
				foreach($pathway as $key => $value){
					if(!$sub)
						$model->setPathway($id, $value);
					else{
						if(!$value["selected"])
							$model->excludePathway($id, $value['id']);
					}
				}
				$model->reset_team($id);
				if(!$sub)
					$model->setTeam($id, $team);
				else{
					foreach($team as $key => $value){
						if(!$value["selected"])
							$model->excludeMember($id, $value['id']);
					}
				}
			}
			//date recurrent
			$previous_date = $model->getEvent(array("id_event" => $this->sg_post->value("id")), array("start"))[0]["Start"];
			$oldStart = new DateTime($previous_date);
			$start = new DateTime($this->sg_post->value('start'));
			
			$shift = $oldStart->diff($start, false);
			$shift2 = $shift->days;
			if($oldStart > $start){
				$shift2 *= -1;
			}
			$mins = $shift->h * 60 + $shift->i;
			$ret = $model->setDateRecur($idRec, $shift2, $mins);
			
				
			if(!$ret){
				$this->set_error_predefined(self::ERROR_ACTION_UPDATE_DATA);
				return;
			}
			
		}
		
		else {
			
			// get event date
			if($this->sg_post->check_keys(array("deadline", "start")) > 0 && $this->sg_post->value("deadline") == "true"){
				$limit = new DateTime($this->sg_post->value("start"));
				$model->setDate($this->sg_post->value("id"), "Deadline", $limit, null, true);
				new EventModificationNotifier(EventModificationNotifier::UPDATE_TIME, $this->sg_post->value("id"));
			}
			elseif($this->sg_post->check_keys(array("start", "end", "entireDay")) > 0)
			{
				$start = new DateTime($this->sg_post->value('start'));
				$end = new DateTime($this->sg_post->value('end'));
				if($this->sg_post->value("entireDay") == "true")
					$model->setDate($this->sg_post->value("id"), "Date", $start, $end,true);
				else
					$model->setDate($this->sg_post->value("id"), "TimeRange", $start, $end,true);
				new EventModificationNotifier(EventModificationNotifier::UPDATE_TIME, $this->sg_post->value("id"));
			
			}
			
			foreach($pathway as $key => $value){
				if(!$sub)
					$model->setPathway($this->sg_post->value("id"), $value);
				else{
					if(!$value["selected"])
						$model->excludePathway($this->sg_post->value("id"), $value['id']);
				}			
			}
			$model->reset_team($this->sg_post->value("id"));
			if(!$sub)
				$model->setTeam($this->sg_post->value("id"), $team);
			else{
				foreach($team as $key => $value){
					if(!$value["selected"]){
						$model->excludeMember($this->sg_post->value("id"), $value['id']);
					}
				}
			}
		}
	}
}


