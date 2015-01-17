<?php

    /**
     * @file 
     * @brief ICS entry point (request from external calendar application)
     */

    namespace ct\util\entry_point;
    
    /**
     * @class ICS
     * @brief This class must be implemented by any request handler
     */
    class ICS implements EntryPoint
    {
        /**
         * @copydoc EntryPoint::get_controller
         */
        public function get_controller()
        {

        }
    };