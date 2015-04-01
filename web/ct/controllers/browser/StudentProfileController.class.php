<?php

	/**
	 * @file
	 * @brief Contains the student profile page
	 */

	namespace ct\controllers\browser;

	use util\mvc\BrowserController;

	/**
	 * @class StudentProfileController
	 * @brief A class for generating the student profile page
	 */
	class StudentProfileController extends BrowserController
	{
		/**
		 * @brief Construct the StudentProfileController object
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
			return $this->smarty->fetch("student/student_profile_body.tpl");
		}

		/**
		 * @copydoc BrowserController::get_popups
		 */
		protected function get_popups()
		{
			return $this->smarty->fetch("student/student_profile_popups.tpl");
		}

		/**
		 * @copydoc BrowserController::get_footer_inc
		 */
		protected function get_footer_inc()
		{
			return $this->smarty->fetch("student/student_profile_footer_inc.tpl");
		}

		/**
		 * @copydoc util\mvc\Controller::perform_action
		 */
		protected function perform_action()
		{
			return true;
		}
	};