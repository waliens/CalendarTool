<?php
	
	/**
	 * @file
	 * @brief Contains the GetTeamAddableUsers class
	 */

	namespace ct\controllers\ajax;

	use util\mvc\AjaxController;
	use util\superglobals\Superglobal;
	use ct\models\events\GlobalEventModel;

	/**
	 * @class GetTeamAddableUsers
	 * @brief A class for handling the get team addable users
	 */
	class GetTeamAddableUsers extends AjaxController
	{
		/**
		 * @brief Construct the GetTeamAddableUsers object and process the request  
		 */
		public function __construct()
		{
			parent::__construct();

			// check input parameters : {id_global_event}
			if($this->sg_post->check("id_global_event", Superglobal::CHK_ALL, "\ct\is_valid_id") < 0)
			{
				$this->set_error_predefined(AjaxController::ERROR_MISSING_ID);
				return;
			}

			// instantiate model
			$glob = new GlobalEventModel();

			$glob_id = array("id" => $this->sg_post->value("id_global_event"));
			$users = $glob->get_team_addable_users($glob_id);
			$trans = array("name" => "", "surname" => "", "id" => "id_user");
			$users = \ct\darray_transform($users, $trans);
			$this->add_output_data("users", $users);
		}
	}