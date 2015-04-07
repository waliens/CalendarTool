<?php
/**
 * @file
* @brief  Event ControllerClass
*/

namespace ct\controllers\ajax;


use ct\models\notifiers\EventModificationNotifier;

use ct\models\events\IndependentEventModel;

use ct\models\events\SubEventModel;

use ct\models\events\StudentEventModel;
use util\mvc\AjaxController;
use util\superglobals\Superglobal;

	/**
	 * @class DeleteEventController
	 * @brief Request Nr : 55,63,83
	 * 		INPUT :	{id, applyRecursive}
  	 * 		OUTPUT : 
 	 * 		Method : POST
	 */

class DeleteEventController extends AjaxController
{


	public function __construct($type)
	{
		parent::__construct();
		$model;
		$sub = false;
		$priv = false;
		$indep = false;
		
		if($type == "SUB")
			$sub = true;
		if($type == "PRIVATE")
			$priv = true;
		if($type == "INDEP")
			$indep = true;	
		
		// create models
		if($priv){
			$model = new StudentEventModel();
		}
		if($sub)
			$model = new SubEventModel();
		if($indep)
			$model = new IndependentEventModel();
		
		
		$id = $this->connection->user_id();
		$eventId = $this->sg_post->value("id");
		$recur = $this->sg_post->value("applyRecursive");
		
		if($priv)
			$verif = $model->getEvent(array("id_event" => $eventId), array("id_owner", "id_recurrence"));
		else{
			$verif = $model->getEvent(array("id_event" => $eventId), array("id_recurrence"));
		}
				
		if(!isset($verif[0]))
			$this->set_error_predefined(self::ERROR_MISSING_EVENT);
		elseif($priv &&  intval($verif[0]['Id_Owner']) != intval($id)){
			$this->set_error_predefined(self::ERROR_ACCESS_DENIED);
		}
		elseif(($sub || $indep) && !$model->isInTeam($eventId, $id))
			$this->set_error_predefined(self::ERROR_ACCESS_DENIED);		
		else{  
			
			if($sub || $indep)
				new EventModificationNotifier(EventModificationNotifier::
					DELETE, $eventId);
			if($recur == "true"){
				$success = $model->deleteEventRecurrence($verif[0]['Id_Recurrence']);
			}
			else {
				$success = $model->delete_event(intval($eventId));
			}
			if(!$success){
				$this->set_error_predefined(self::ERROR_ACTION_DELETE_DATA);
			}
		}


	}


}


