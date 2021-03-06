<?php

	/**
	 * @file
	 * @brief Contains the AddTeachingTeamMemberController class
	 */

	namespace ct\controllers\ajax;

	use util\mvc\AjaxController;	
	use util\superglobals\Superglobal;
	use ct\models\events\GlobalEventModel;
	use ct\models\UserModel;
	/**
	 * @class AddTeachingTeamMemberController
	 * @brief Request Nr : 072
	 * 		INPUT :	{id_user, id_global_event, id_role}
  	* 		OUTPUT : 
	 * 		Method : POST
	 */
	class AddTeachingTeamMemberController extends AjaxController
	{
		private $glob_mod; /**< @brief The global event model */

		/**
		 * @brief Construct the AddTeachingTeamMemberController and process the request
		 */
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
				$this->set_error_predefined(AjaxController::ERROR_ACCESS_PROFESSOR_REQUIRED);
				return;
			}

			// check whether the given user can be given the given role
			$user_mod = new UserModel();
			$is_student = $user_mod->user_is_student($this->sg_post->value("id_user"));
			$role = $this->sg_post->value("id_role");

			if($is_student XOR $role == GlobalEventModel::ROLE_ID_TS)
			{
				$this->set_error_predefined(AjaxController::ERROR_ACTION_BAD_TEACHING_ROLE);
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

		/**
		 * @copydoc AjaxController::has_access
		 */
		public function has_access()
		{
			$id_data = array("id" => $this->sg_post->value("id_global_event"));
			return $this->glob_mod->global_event_user_has_write_access($id_data);
		}
	}