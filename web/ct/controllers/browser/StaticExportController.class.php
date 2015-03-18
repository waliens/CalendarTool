<?php

  /**
   * @file
   * @brief Contains the StaticExportController class
   */

  namespace ct\controllers\browser;

  use util\mvc\BrowserController;

  /**
   * @class StaticExportController
   * @brief A class for generating the private events page
   */
  class StaticExportController extends BrowserController
  {
    /**
     * @brief Construct the StaticExportController object
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
		return $this->smarty->fetch("student/static_export_body.tpl");
    }

    /**
     * @copydoc BrowserController::get_popups
     */
    protected function get_popups()
    {
		return $this->smarty->fetch("student/static_export_popups.tpl");
    }

	/**
	 * @copydoc BrowserController::get_footer_inc
	 */
	protected function get_footer_inc()
	{
		return $this->smarty->fetch("student/static_export_footer_inc.tpl");
	}

    /**
     * @copydoc util\mvc\Controller::perform_action
     */
    protected function perform_action()
    {
		return true;
    }
  };