<?php

	/**
  	 * @file
  	 * @brief Contains the StaticExportController class
	 */

	namespace ct\controllers\ajax;

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
		}
	}