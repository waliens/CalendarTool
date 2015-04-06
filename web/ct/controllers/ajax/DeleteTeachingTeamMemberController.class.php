<?php

	/**
	 * @file
	 * @brief Contains the class DeleteTeachingTeamMemberController class
	 */

	namespace ct\controllers\ajax;

	use util\mvc\AjaxController;
	use util\superglobals\Superglobal;
	use ct\models\events\GlobalEventModel;

	/**
	 * @class DeleteTeachingTeamMemberController 
	 * @brief A class for handling the delete teaching 
	 */
	class DeleteTeachingTeamMemberController extends AjaxController
	{
		private $glob_mod; /**< @brief A GlobalEventModel object */

		/**
		 * @brief Construct the DeleteTeachingTeamMemberController object and process the request
		 */
		public function __construct()
		{
			parent::__construct();

			$this->glob_mod = new GlobalEventModel();

			// check input data
			$keys = array("id_user", "id_global_event");
			if($this->sg_post->check_keys($keys, Superglobal::CHK_ALL, "\ct\is_valid_id") < 0)
			{
				$this->set_error_predefined(AjaxController::ERROR_MISSING_ID);
				return;
			}

			// check write access
			if(!$this->has_access())
			{
				$this->set_error_predefined(AjaxController::ERROR_ACCESS_PROFESSOR_REQUIRED);
				return;
			}

			// check whether the user to delete is the event owner
			$id_data = array("id" => $this->sg_post->value("id_global_event"));
			$user = $this->sg_post->value("id_user");
			if($this->glob_mod->is_global_event_owner($id_data, $user))
			{
				$this->set_error_predefined(AjaxController::ERROR_ACTION_DELETE_DATA);
				return;
			}

			// delete data 
			if(!$this->glob_mod->delete_team_member($id_data, $user))
				$this->set_error_predefined(AjaxController::ERROR_ACTION_DELETE_DATA);
		}

		/**
		 * @copydoc AjaxController::has_access
		 */
		protected function has_access()
		{
			$id_data = array("id" => $this->sg_post->value("id_global_event"));
			return $this->glob_mod->global_event_user_has_write_access($id_data);
		}
	}