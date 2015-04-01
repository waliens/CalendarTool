<?php
/**
* @file
* @brief Edit  Event  drag and drop ControllerClass
*/

namespace ct\controllers\ajax;

use \DateTime;
use ct\models\events\StudentEventModel;
use util\mvc\AjaxController;
use util\superglobals\SG_Post;
use util\superglobals\Superglobal;



class EditDaD extends AjaxController
{
	/**
	 * @brief Construct the PrivateEventController object and process the request
	 */
	public function __construct()
	{
		parent::__construct();

		// check if the expected keys are in the array
		$keys = array("id", "start");

		if($this->sg_post->check_keys($keys, Superglobal::CHK_ISSET) < 0)
		{
			$this->set_error_predefined(AjaxController::ERROR_MISSING_DATA);
			return;
		}

		// create private event
		$model = new StudentEventModel();


		// get event date
		if($this->sg_post->check_keys(array("limit")) > 0 && $this->sg_post->value("limit") == "true"){
			$limit = new DateTime($this->sg_post->value("start"));
			$model->setDate($this->sg_post->value("id"), "Deadline", $limit, null, true);
		}
		elseif($this->sg_post->check_keys(array("start", "end")) > 0)
		{
				
			$start = new DateTime($this->sg_post->value('start'));
			$end = new DateTime($this->sg_post->value('end'));
			if($start->format("H:i:s") == "0:0:0" && $end->format("H:i:s") == "0:0:0")
				$model->setDate($this->sg_post->value("id"), "Date", $start, $end,true);
			else
				$model->setDate($this->sg_post->value("id"), "TimeRange", $start, $end,true);
				
		}
		else {
			$this->set_error_predefined(self::ERROR_MISSING_DATA);
			return;			
		}
		
		$this->set_error_predefined(self::ERROR_OK);
	}
}


