<?php

	/**
	 * @file
	 * @brief Contains the AddTeachingTeamMemberController class
	 */

	namespace ct\controllers\ajax;

	use util\mvc\AjaxController;	
	use util\superglobals\Superglobal;
	use ct\models\events\GlobalEventModel;

	/**
	 * @class AddTeachingTeamMemberController
	 * @brief A class for handling the add teaching team member request
	 */
	class AddTeachingTeamMemberController extends AjaxController
	{
		private $glob_mod; /**< @brief The global event model */

		public function __construct()
		{
			parent::__construct();

			// instantiate models
			$this->glob_mod = new GlobalEventModel();

			// check input data
			$data_keys = array("id_role", "id_global_event", "id_user");

			if($this->sg_post->check_keys($data_keys, Superglobal::CHK_ALL, "ct\is_valid_id") < 0)
			{
				$this->set_error_predefined(AjaxController::ERROR_MISSING_ID);
				return;
			}

			// check whether the use has write access on the global event
			if(!$this->has_access())
			{
				$this->set_error_predefined(AjaxController::ERROR_ACCESS_DENIED);
				return;
			}

			// add team member
			$id_data = array("id" => $this->sg_post->value("id_global_event"));

			$success = $this->glob_mod->add_team_member($id_data, 
														$this->sg_post->value("id_role"),
														$this->sg_post->value("id_user"));

			if(!$success)
				$this->set_error_predefined(AjaxController::ERROR_ACTION_ADD_DATA);
		}

		public function has_access()
		{
			$id_data = array("id" => $this->sg_post->value("id_global_event"));
			return $this->glob_mod->global_event_user_has_write_access($id_data);
		}
	}