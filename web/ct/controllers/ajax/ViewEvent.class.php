<?php


/**
 * @file
* @brief Private Event ControllerClass
*/

namespace ct\controllers\ajax;


use ct\models\UserModel;

use ct\models\events\StudentEventModel;
use util\mvc\AjaxController;
use util\superglobals\Superglobal;

/**
 * @class Event
 * @brief Class for handling the control of event
 */

class ViewEventController extends AjaxController
{


	public function __construct($type)
	{
		parent::__construct();
		$model;
		$sub = false;
		$priv = false;
		
		if($type == "SUB")
			$sub = true;
		if($type == "PRIVATE")
			$priv = true;

		// create models
		if($priv){
			$model = new StudentEventModel();
			$id = $this->connection->user_id();
		}
		if($sub)
			$model = new SubEventModel();
				
		$eventId = $this->sg_post->value("id");

		
		$req = $model->getEvent(array("id_event" => $this->sg_post->value()));

		if($priv && (!isset($req[0]) || intval($req[0]['Id_Owner']) != intval($id))){
			$this->set_error_predefined(self::ERROR_ACCESS_DENIED);
		}
		elseif($sub && (!isset($req[0])))
			$this->set_error_predefined(self::ERROR_MISSING_EVENT);
		else{
			$data = $req[0];
			$this->output_data['id'] = $data['Id_Event'];
			$this->output_data['name'] = $data['Name'];
			$this->output_data['description'] = $data['Descritpion'];
			$this->output_data['type'] = $data['EventType']; 
			
			if($data['DateType'] == "date_range"){
				$this->output_data['startDay'] = $data['Start'];
				$this->output_data['endDay'] = $data['End'];
			}
			elseif($data['DateType'] == "time_range"){	
				$this->output_data['startTime'] = $data['Start'];
				$this->output_data['endTime'] = $data['End'];
			}
			else	
				$this->output_data['deadline'] = $data['Start'];
			
			
			
			$this->output_data['category_id'] = $data['Id_Category'];

 			$this->output_data['category_name'] = ""; //TODO Lang distinction
			
			$this->output_data['recurrence'] = $data['Id_Recurrence'];
			
			$an = $model->get_annotation($eventId, $id);
			if($an)
				$this->output_data['annotation'] = $an;
		}
	}


}


