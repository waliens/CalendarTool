<?php

	/**
  	 * @file
  	 * @brief Contains the StaticExportController class
	 */

	namespace ct\controllers\ajax;

	use ct\models\ExportModel;
	use util\mvc\AjaxController;

	/**
	 * @class StaticExportController
	 * @brief A class for handling the settings for the static export
	 */
	class StaticExportController extends FilterController
	{
		/**
		 * @brief Construct the StaticExportController and process the request
		 */
		public function __construct()
		{
			parent::__construct();

			// check if an error occurred
			if($this->error_isset())
				return;

			$exp_mod = new ExportModel();

			if(!$exp_mod->set_export_filters($this->get_filters()))
				$this->set_error_predefined(AjaxController::ERROR_ACTION_SAVE_EXPORT);
		}
	}