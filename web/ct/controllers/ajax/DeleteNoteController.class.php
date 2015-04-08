<?php

/**
 * @file
* @brief Deleting note ControllerClass
*/

namespace ct\controllers\ajax;


use ct\models\events\EventModel;
use util\mvc\AjaxController;
use util\superglobals\Superglobal;

	/**
	 * @class DeleteNoteController
	 * @brief Request Nr : 044
	 * 		INPUT :	{id_event}
  	 * 		OUTPUT : 
 	 * 		Method : POST
	 */

class DeleteNoteController extends AjaxController
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
		
		$a = $model->delete_annotation($eventId, $userId);
		if(!$a)
			$this->set_error_predefined(self::ERROR_ACTION_DELETE_DATA);
		
	}


}


