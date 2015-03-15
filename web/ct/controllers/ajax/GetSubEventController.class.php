<?php


/**
 * @file
* @brief Get sub Event by prof ControllerClass
*/

namespace ct\controllers\ajax;



use ct\models\events\SubEventModel;

use ct\models\events\StudentEventModel;
use util\mvc\AjaxController;
use util\superglobals\Superglobal;

/**
 * @class Event
 * @brief Class for handling the control of event
 */

class GetSubEventController extends AjaxController
{


	public function __construct()
	{
		parent::__construct();

		$model = new SubEventModel();
		$profId = $this->sg_post->value("id");

		$req = $model->getEventByTeamMember($profId);
		if(!$req){
			$this->set_error_predefined(self::ERROR_ACTION_READ_DATA);
			return;
		}
		
		$this->add_output_data("subEvents", $req);
			
		
	}


}



