<?php


/**
 * @file
* @brief Private Event ControllerClass
*/

namespace ct\controllers\ajax;


use ct\models\events\IndependentEventModel;

use ct\models\events\GlobalEventModel;
use ct\models\events\SubEventModel;
use util\mvc\AjaxController;
use util\superglobals\Superglobal;
use \DateTime;


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
		$indep = false;
		
		if($type == "SUB")
			$sub = true;
		if($type == "PRIVATE")
			$priv = true;
		if($type == "INDEP")
			$indep = true;
		
		$id = $this->connection->user_id();
		
		// create models
		if($priv)
			$model = new StudentEventModel();
		if($sub)
			$model = new SubEventModel();
		if($indep)
			$model = new IndependentEventModel();
				
		if($this->sg_get->check('event') > 0)
			$eventId = $this->sg_get->value("event");
		else{
			$this->set_error_predefined(self::ERROR_MISSING_INPUT_DATA);
			return;
		}
		
		$req = $model->getEvent(array("id_event" => $eventId));

		if($priv && (!isset($req[0]) || intval($req[0]['Id_Owner']) != intval($id))){
			$this->set_error_predefined(self::ERROR_ACCESS_DENIED);
		}
		elseif(($sub || $indep) && (!isset($req[0])))
			$this->set_error_predefined(self::ERROR_MISSING_EVENT);
		else{
			$data = $req[0];
			$ret = array();
			$ret['id'] = $data['Id_Event'];
			$ret['name'] = $data['Name'];
			$ret['description'] = $data['Description'];
			$ret['type'] = $data['EventType']; 
			$ret['place'] = $data['Place'];
			
			$start = new DateTime($data['Start']);
			
			$ret['startDay'] = $start->format("Y-m-d");
			$ret['startTime'] = $start->format("H:i:s");
			
			if($data['DateType'] == "deadline"){
				$ret['deadline'] = "true";
				$ret['endDay'] = '';
				$ret['endTime'] = '';
			}
			else{
				$ret['deadline'] = "false";
				$end = new DateTime($data['End']);
				$ret['endDay'] = $end->format("Y-m-d");
				$ret['endTime'] = $end->format("H:i:s");;
			}
			
			
			$ret['category_id'] = $data['Id_Category'];

			if($sub){
				$glob = new GlobalEventModel();
				$eng = $glob->get_language(array("id" => $data["Id_Global_Event"])) == GlobalEventModel::LANG_EN;
			}
			else
				$eng = false;
			
			if($eng)
 				$ret['category_name'] = $data['Categ_Name_EN'];
			else
				$ret['category_name'] = $data['Categ_Name_FR'];
				
			
			$ret['recurrence'] = $data['Id_Recurrence'];
			
			$an = $model->get_annotation($eventId, $id);
			if($an)
				$ret['annotation'] = $an;
			else 
				$ret['annotation'] = "";
			
			if($indep || $sub){
				$team = $model->getTeam($eventId);
				if($team){
					$prof = array();
					foreach($team as $key => $value){
						if($value["role"] == "Professor" || $value["role"] == "Professeur") // Attention hardcodé
							array_push($prof, $value["name"]." ".$value['surname']);
					}
					$ret['professor'] = implode(", ", $prof);
				}
			}
			
			if($ret['recurrence'] != intval(1)){
				$ret["start_recurrence"] = $model->getStartRecurrence($ret['recurrence']);
				$ret["end_recurrence"] = $model->getEndRecurrence($ret['recurrence']);
			}
			else {
				$ret["start_recurrence"] = "";
				$ret["end_recurrence"] = "";
			}
			$this->set_output_data($ret);
		}
	}


}


