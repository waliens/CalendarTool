<?php

    /**
     * @file 
     * @brief Browser entry point
     */

    namespace ct\util\entry_point;
    
    /**
     * @class Browser
     * @brief This class must be implemented by any request handler
     */
    class Browser implements EntryPoint
    {
        /**
         * @copydoc EntryPoint::get_controller
         */
        public function get_controller()
        {

        }
    };