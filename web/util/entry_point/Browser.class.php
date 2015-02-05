<?php

    /**
     * @file 
     * @brief Browser entry point
     */

    namespace util\entry_point;
    
    use util\superglobals\SG_Get;

    /**
     * @class Browser
     * @brief This class must be implemented by any request handler
     */
    class Browser implements EntryPoint
    {
        private $spg_get; 
        /**
         * @brief Construct the Browser EntryPoint object
         */
        public function __construct()
        {
            $this->spg_get = new SG_Get();
        }

        /**
         * @copydoc EntryPoint::get_controller
         */
        public function get_controller()
        {
            if(!$this->spg_get->check("page"))
                $_GET['page'] = "";

            switch($_GET['page'])
            {
            case "profile":
                return new ProfilePageController();
            case "login":
                return new LoginPageController();
            default:
                return new CalendarPageController();
            }
        }
    };