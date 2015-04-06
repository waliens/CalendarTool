<?php

  /**
   * @file
   * @brief Contains the PrivateEventsController class
   */

  namespace ct\controllers\browser;

  use util\mvc\BrowserController;

  /**
   * @class PrivateEventsController
   * @brief A class for generating the private events page
   */
  class PrivateEventsController extends BrowserController
  {
    /**
     * @brief Construct the PrivateEventsController object
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
      return $this->smarty->fetch("student/private_events_body.tpl");
    }

    /**
     * @copydoc BrowserController::get_popups
     */
    protected function get_popups()
    {
      return $this->smarty->fetch("student/private_events_popups.tpl");
    }

    /**
     * @copydoc BrowserController::get_footer_inc
     */
    protected function get_footer_inc()
    {
      return $this->smarty->fetch("student/private_events_footer_inc.tpl");
    }

    /**
     * @copydoc util\mvc\Controller::perform_action
     */
    protected function perform_action()
    {
      return true;
    }
  };