<?php

/**
 * @file
* @brief Note Adding ControllerClass
*/

namespace ct\controllers\ajax;


use ct\models\events\EventModel;
use util\mvc\AjaxController;
use util\superglobals\Superglobal;

/**
 * @class AddNoteController
 * @brief Request Nr : 042
 * 		INPUT : {id_event, note}
 * 		OUTPUT : none
 * 		Method : POST
 */
class AddNoteController extends AjaxController
{


	public function __construct($update = false)
	{
		parent::__construct();

		// check if the expected keys are in the array
		$keys = array("id_event", "note");

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
		$note = $this->sg_post->value("note");
		
		$a = $model->set_annotation($eventId, $userId, $note, $update);
		if(!$a && !$update)
			$this->set_error_predefined(self::ERROR_ACTION_ADD_DATA); //Problably a duplicate key
		elseif(!$a && $update)
			$this->set_error_predefined(self::ERROR_ACTION_UPDATE_DATA); 
	}


}


