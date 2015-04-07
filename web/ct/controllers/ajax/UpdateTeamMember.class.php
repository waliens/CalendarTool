<?php
namespace ct\controllers\ajax;

use util\mvc\AjaxController;
use util\superglobals\Superglobal;
use ct\models\events\IndependentEventModel;


	/**
	 * @class UpdateTeamMember
	 * @brief Request Nr : 088, 089
	 * 		INPUT :{id_event, id_user, id_role}
	 * 		OUTPUT : 
	 * 		Method : POST
	 */
class UpdateTeamMember extends AjaxController
{

	public function __construct($add)
	{
		parent::__construct();

		// check if the expected keys are in the array
		$keys;
		if($add)
			$keys = array("id_event","id_user", "id_role");
		else
			$keys = array("id_event","id_user");
				
		if($this->sg_post->check_keys($keys, Superglobal::CHK_ISSET) < 0)
		{
			$this->set_error_predefined(AjaxController::ERROR_MISSING_DATA);
			return;
		}

		$model =  new IndependentEventModel();

		$id = $this->connection->user_id();
		if(!$model->isInTeam($this->sg_post->value("id"), $id)){
			$this->set_error_predefined(self::ERROR_ACCESS_DENIED);
			return;
		}

		$team = array("id" => $this->sg_post->value("id_user"),
						"role" => $this->sg_post->value("id_role") );
		if($add)
			$model->setTeam($this->sg_post->value("id_event"), $team);
		else 
			$model->removeFromTeam($this->sg_post->value("id_event"), $this->sg_post->value("id_user"));
			

	}
}