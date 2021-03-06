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
	 * @brief Request Nr : 091
	 * 		INPUT :{view:'viewId' ,allEvent:{isSet:'false'},dateRange: {start: datetime, end: datetime},courses: {isSet: 'false', id:[]},eventTypes: {isSet: 'false', timeType:[], eventType:[]},eventCategories:{isSet:'false', id:[]}pathways: {isSet: 'false', id:[]},professors:{isSet: 'false', id:[]}}
	 * 		OUTPUT : {url}
	 * 		Method : POST
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

			// reset the output data array that was formatted by the FilterCollection constructor
			$this->reset_output_data();

			$exp_mod = new ExportModel();

			if(!$exp_mod->generate_static_export_file($this->get_filters()))
			{
				$this->set_error_predefined(AjaxController::ERROR_ACTION_CREATE_STATIC_EXPORT);
				return false;
			}

			$url = $exp_mod->get_export_file_path(ExportModel::EXPORT_TYPE_STATIC);
			$this->add_output_data("url", $url);
		}
	}