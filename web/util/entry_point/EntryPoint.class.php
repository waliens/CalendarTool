<?php

    /**
     * @file 
     * @brief Entry point interface 
     */

    namespace util\entry_point;

    /**
     * @interface EntryPoint
     * @brief This interface must be implemented by any request handler
     */
    interface EntryPoint
    {
        /**
         * @brief Return the controller for handling the current request based on the request parameters
         */
        function get_controller();
    };