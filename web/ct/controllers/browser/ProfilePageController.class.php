<?php

	/** 
	 * @file
	 * @brief Contains the ProfilePageController class
	 */

	namespace ct\controllers\browser;

	use util\mvc\BrowserController;

	/**
	 * @class ProfilePageController
	 * @brief A class for representing the controller that handle the browser for the profile page
	 */
	class ProfilePageController extends BrowserController
	{
		/**
		 * @brief Construct the ProfilePageController object
		 */
		public function __construct()
		{
			parent::__construct();
		}

		/**
		 * @copydoc BrowserController::get_content
		 */
		protected function get_content()
		{
			return $this->smarty->fetch("profile_body.tpl");
		}

		/**
		 * @copydoc util\mvc\Controller::perform_action
		 */
		protected function perform_action()
		{
			return true;
		}
	};