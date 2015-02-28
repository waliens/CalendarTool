<?php

	/** 
	 * @file
	 * @brief Contains the CalendarPageController class
	 */

	namespace ct\controllers\browser;

	use util\mvc\BrowserController;

	/**
	 * @class CalendarPageController
	 * @brief A class for representing the controller that handle the browser for the calendar page
	 */
	class CalendarPageController extends BrowserController
	{
		
		/**
		 * @brief Construct the CalendarPageController object
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
			return $this->smarty->fetch("calendar/calendar_body.tpl");
		}
		
		/**
		 * @copydoc BrowserController::get_popups
		 */
		protected function get_popups()
		{
		  return $this->smarty->fetch("calendar/calendar_popups.tpl");
		}
	
		/**
		 * @copydoc BrowserController::get_footer_inc
		 */
		protected function get_footer_inc()
		{
		  return $this->smarty->fetch("calendar/calendar_footer_inc.tpl");
		}

		/**
		 * @copydoc util\mvc\Controller::perform_action
		 */
		protected function perform_action()
		{
			return true;
		}
	};