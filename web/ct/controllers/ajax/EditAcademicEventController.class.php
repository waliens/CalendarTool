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
				//$model->setTeam($id, $team);
			}
		}
		
		else {
			
			
			
			foreach($pathway as $key => $value){
				if(!$sub)
					$model->setPathway($id, $value);
				else{
					if(!$value["selected"])
						$model->excludePathway($id, $value['id']);
				}			
			}
					
		//	$model->setTeam($id, $team);
		}
	}
}


