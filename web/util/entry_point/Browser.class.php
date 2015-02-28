<?php

    /**
     * @file 
     * @brief Browser entry point
     */

    namespace util\entry_point;
    
    use util\superglobals\SG_Get;

    use ct\controllers\browser\StudentProfileController;
    use ct\controllers\browser\StaticExportController;
    use ct\controllers\browser\PrivateEventsController;
    use ct\controllers\browser\CalendarPageController;
    use ct\controllers\browser\ProfessorProfileController;

    use ct\Connection;

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
            if($this->spg_get->check("page") < 0)
                $page = "";
            else
                $page = $_GET['page'];

            $connection = Connection::get_instance();

            switch($page)
            {
            case "profile":

                if($connection->user_is_student())
                    return new StudentProfileController();
                else
                    return new ProfessorProfileController();

            case "static_export":
                return new StaticExportController();
            case "private_events":
                return new PrivateEventsController();
            default:
                return new CalendarPageController();
            }
        }
    };