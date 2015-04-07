<?php

	/**
	 * @file
	 * @brief Contains the EditGlobalEventController class
	 */

	namespace ct\controllers\ajax;

	use util\mvc\AjaxController;
	use util\superglobals\Superglobal;

	use ct\models\events\GlobalEventModel;

/**
 * @class EditGlobalEventController
 * @brief Request Nr : 034
 * 		INPUT : {id, description, feedback, language}
 * 		OUTPUT :
 * 		Method : POST
 */
	class EditGlobalEventController extends AjaxController
	{
		/**
		 * @brief Constructs the EditGlobalEventController object and process the request
		 */
		public function __construct()
		{
			parent::__construct();

			// check input parameters
			if(!$this->sg_post->check("id", Superglobal::CHK_ALL, "\ct\is_valid_id") < 0) // ulg id
			{
				$this->set_error_predefined(AjaxController::ERROR_MISSING_ID);
				return;
			}

			// description and feedback (can be empty strings)
			$chk_arr = array("description", "feedback");

			if($this->sg_post->check_keys($chk_arr, Superglobal::CHK_ISSET) < 0)
			{
				$this->set_error_predefined(AjaxController::ERROR_MISSING_DATA);
				return;
			}
			
			$update_data = array("desc" => $this->sg_post->value("description"),
								 "feedback" => $this->sg_post->value("feedback"));

			// language
			$lang_check = $this->sg_post->check("language", 
												Superglobal::CHK_ALL, 
												function($lang) { return GlobalEventModel::valid_lang($lang); });

			// 
			if($lang_check === Superglobal::ERR_CALLBACK) // language given but in a bad format
			{
				$this->set_error_predefined(AjaxController::ERROR_FORMAT_INVALID);
				return;
			}
			else if($lang_check > 0)
				$update_data['lang'] = $this->sg_post->value("language");


			// instantiate models
			$glob_mod = new GlobalEventModel();

			// update the non ulg data
			$id_data = array("id" => $this->sg_post->value("id"));

			if(!$glob_mod->update_global_event_non_ulg_data($id_data, $update_data))
				$this->set_error_predefined(AjaxController::ERROR_ACTION_UPDATE_DATA);
		}
	}