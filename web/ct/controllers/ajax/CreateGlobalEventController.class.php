<?php
	
	/**
	 * @file
	 * @brief Contains the CreateGlobalEventController class
	 */

	namespace ct\controllers\ajax;

	use util\mvc\AjaxController;
	use util\superglobals\Superglobal;

	use ct\models\events\GlobalEventModel;

	/**
	 * @class CreateGlobalEventController
	 * @brief A class for handling the creation of global events ajax request
	 */
	class CreateGlobalEventController extends AjaxController
	{
		/**
		 * @brief Construct the CreateGlobalEventController object and process the request (create the global event)
		 */
		public function __construct()
		{
			parent::__construct();

			// check input parameters
			if(!$this->sg_post->check("ulgId", Superglobal::CHK_ALL) < 0)
			{
				$this->set_error_predefined(AjaxController::ERROR_MISSING_DATA);
				return;
			}

			// instantiate models
			$glob_mod = new GlobalEventModel();

			// create the global event
			$glob_id = $glob_mod->create_global_event($this->sg_post->value("ulgId"));
			
			if(!$glob_id)
			{
				$this->set_error_predefined(AjaxController::ERROR_ACTION_ADD_DATA);
				return;
			}

			// set the global event id as output
			$this->add_output_data("id", $glob_id);
		}
	}