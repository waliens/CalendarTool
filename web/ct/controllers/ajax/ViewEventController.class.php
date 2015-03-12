<?php


/**
 * @file
* @brief Private Event ControllerClass
*/

namespace ct\controllers\ajax;


use ct\models\UserModel;

use ct\models\events\StudentEventModel;
use ct\models\events\SubEventModel;
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

		$id = $this->connection->user_id();
		
		// create models
		if($priv)
			$model = new StudentEventModel();
		if($sub)
			$model = new SubEventModel();
				
		
		$eventId = $this->sg_get->value("event");

		
		$req = $model->getEvent(array("id_event" => $eventId));

		if($priv && (!isset($req[0]) || intval($req[0]['Id_Owner']) != intval($id))){
			$this->set_error_predefined(self::ERROR_ACCESS_DENIED);
		}
		elseif($sub && (!isset($req[0])))
			$this->set_error_predefined(self::ERROR_MISSING_EVENT);
		else{
			$data = $req[0];
			$ret = array();
			$ret['id'] = $data['Id_Event'];
			$ret['name'] = $data['Name'];
			$ret['description'] = $data['Description'];
			$ret['type'] = $data['EventType']; 
			
			if($data['DateType'] == "date_range"){
				$ret['startDay'] = $data['Start'];
				$ret['endDay'] = $data['End'];
			}
			elseif($data['DateType'] == "time_range"){	
				$ret['startTime'] = $data['Start'];
				$ret['endTime'] = $data['End'];
			}
			else	
				$ret['deadline'] = $data['Start'];
			
			
			
			$ret['category_id'] = $data['Id_Category'];

 			$ret['category_name'] = ""; //TODO Lang distinction
			
			$ret['recurrence'] = $data['Id_Recurrence'];
			
			$an = $model->get_annotation($eventId, $id);
			if($an)
				$ret['annotation'] = $an;
			
			$this->set_output_data($ret);
		}
	}


}


