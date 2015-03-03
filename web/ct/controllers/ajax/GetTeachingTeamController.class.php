<?php

	/**
	 * @file
	 * @brief Contains the GetTeachingTeamController class
	 */

	namespace ct\controllers\ajax;

	use util\mvc\AjaxController;
	use util\superglobals\Superglobal;
	use ct\models\events\GlobalEventModel;

	/**
	 * @class GetTeachingTeamController
	 * @brief A class for handling the getTeachingTeam request
	 */
	class GetTeachingTeamController extends AjaxController
	{
		private $glob_mod; /**< @brief Global event model */

		public function __construct()
		{
			parent::__construct();

			// instantiate models 
			$this->glob_mod = new GlobalEventModel();

			if($this->sg_post->check("global_event_id", Superglobal::CHK_ALL, "ct\is_valid_id") < 0) // check if the input data are valid
			{
				$this->set_error_predefined(self::ERROR_MISSING_ID);
				return;
			}

			// check access to the global event
			if(!$this->has_access())
			{
				$this->set_error_predefined(self::ERROR_ACCESS_DENIED);
				return;
			}

			// get teaching team data
			$id_data = array("id" => $this->sg_post->value("global_event_id"));

			$lang = $this->glob_mod->get_language($id_data);
			$team = $this->glob_mod->get_teaching_team($id_data, $lang);

			$this->output_data['team'] = \ct\darray_transform($team, array("user" => "user_id", "name" => "", "surname" => "", "role" => ""));
		}

		/**
		 * @copydoc AjaxController::has_access
		 */
		protected function has_access()
		{
			$id_data = array("id" => $this->sg_post->value("global_event_id"));
			return $this->glob_mod->global_event_user_has_access($id_data);
		}
	}