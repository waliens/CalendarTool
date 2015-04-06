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
			if(!$this->sg_post->check("ulgId", Superglobal::CHK_ALL) < 0) // ulg id
			{
				$this->set_error_predefined(AjaxController::ERROR_MISSING_ID);
				return;
			}
			
			// language
			$lang_check = $this->sg_post->check("language", 
												Superglobal::CHK_ALL, 
												function($lang) { return GlobalEventModel::valid_lang($lang); });
			if($lang_check < 0)
			{
				if($lang_check === Superglobal::ERR_CALLBACK)
					$this->set_error_predefined(AjaxController::ERROR_FORMAT_INVALID);
				else
					$this->set_error_predefined(AjaxController::ERROR_MISSING_DATA);
				return;
			}

			// description and feedback (can be empty)
			$chk_arr = array("description", "feedback");

			if($this->sg_post->check_keys($chk_arr, Superglobal::CHK_ISSET) < 0)
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

			// update the non ulg data
			$insert_data_keys = array("description" => "desc", "feedback" => "", "language" => "lang");
			$insert_data = $this->sg_post->values($insert_data_keys, true);
			$id_data = array("id" => $glob_id);

			if(!$glob_mod->update_global_event_non_ulg_data($id_data, $insert_data))
				$this->set_error_predefined(AjaxController::ERROR_ACTION_ADD_DATA);

			// set the global event id as output
			$this->add_output_data("id", $glob_id);
		}
	}