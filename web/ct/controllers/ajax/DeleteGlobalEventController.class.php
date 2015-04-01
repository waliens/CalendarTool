<?php

	/**
	 * @file
	 * @brief Contains the DeleteGlobalEventController class
	 */

	namespace ct\controllers\ajax;

	use ct\models\notifiers\GlobalEventNotification;

	use util\mvc\AjaxController;
	use util\superglobals\Superglobal;
	use ct\models\events\GlobalEventModel;

	/** 
	 * @class DeleteGlobalEventController
	 * @brief A class for handling the global event deletion request
	 */
	class DeleteGlobalEventController extends AjaxController
	{
		/** 
	 	 * @brief Construct the DeleteGlobalEventController object and process the request
	 	 */
		public function __construct()
		{
			parent::__construct();

			// check parameters
			if($this->sg_post->check("id", Superglobal::CHK_ALL, "\ct\is_valid_id") < 0)
			{
				$this->set_error_predefined(AjaxController::ERROR_MISSING_ID);
				return;
			}

			// instantiate model
			$glob_mod = new GlobalEventModel();

			new GlobalEventNotification(GlobalEventNotification::DELETE, $this->sg_post->value("id"));
			// delete the global event model
			if(!$glob_mod->delete_global_event(array("id" => $this->sg_post->value("id"))))
				$this->set_error_predefined(AjaxController::ERROR_ACTION_DELETE_DATA);
			
		}
	}