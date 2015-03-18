<?php

	/** 
	 * @file
	 * @brief Contains the LoginPageController class
	 */

	namespace ct\controllers\browser;

	use util\mvc\BrowserController;

	/**
	 * @class LoginPageController
	 * @brief A class for representing the controller that handle the browser for the calendar page
	 */
	class LoginPageController extends BrowserController
	{
		/**
		 * @brief Construct the LoginPageController object
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
			return $this->smarty->fetch("login.tpl");
		}

		/**
		 * @copydoc BrowserController::get_starter
		 */
		protected function get_starter()
		{
			return "";
		}

		/**
		 * @copydoc BrowserController::get_footer
		 */
		protected function get_footer()
		{
			return "";
		}

		/**
		 * @copydoc util\mvc\Controller::perform_action
		 */
		protected function perform_action()
		{
			return true;
		}
	};