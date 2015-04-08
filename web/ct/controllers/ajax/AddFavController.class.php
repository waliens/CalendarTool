<?php 
/**
 * @file
* @brief Adding favorite event ControllerClass
*/

namespace ct\controllers\ajax;


use ct\models\events\EventModel;
use util\mvc\AjaxController;
use util\superglobals\Superglobal;

/**
 * @class AddFavController
 * @brief Request Nr : 045
 * 		INPUT : {id_event}
 * 		OUTPUT : none
 * 		Method : POST
 */

class AddFavController extends AjaxController
{


	public function __construct($update = false)
	{
		parent::__construct();

		// check if the expected keys are in the array
		$keys = array("id_event");

		if($this->sg_post->check_keys($keys, Superglobal::CHK_ISSET) < 0)
		{
			$this->set_error_predefined(self::ERROR_MISSING_DATA);
			return;
		}

		// create model
		$model = new EventModel();
		
		// get data
		$userId = $this->connection->user_id();
		$eventId = $this->sg_post->value("id_event");

		$a = $model->addAsFavorite($eventId, $userId);
		if(!$a)
			$this->set_error_predefined(self::ERROR_MISSING_EVENT); //It's problably a missing event 
	}


}


