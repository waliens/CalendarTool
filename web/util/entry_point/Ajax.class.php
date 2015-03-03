<?php

    /**
     * @file 
     * @brief Ajax entry point
     */

    namespace util\entry_point;
    
    use ct\controllers\ajax\TestController;
    use ct\controllers\ajax\PrivateEventController;
    use ct\controllers\ajax\AllProfessorsController;
    use ct\controllers\ajax\ProfessorProfileController;
    use ct\controllers\ajax\GetTeachingTeamController;
    
    use util\superglobals\Superglobal;
    use util\superglobals\SG_Get;

    /**
     * @class Ajax
     * @brief This class must be implemented by any request handler
     */
    class Ajax implements EntryPoint
    {
        protected $sg_get; /**< @brief Superglobal object for $_GET */
        /**
         * @copydoc EntryPoint::get_controller
         */
        public function __construct(){
            $this->sg_get = new SG_Get();
             
        }
        public function get_controller()
        {
            if($this->sg_get->check("req") !== Superglobal::ERR_OK)
                return null;

            switch($this->sg_get->value("req"))
            {
                case "000";
                    return new TestController();

                /* User-related requests */
                case "021":
                    return new AllProfessorsController();
                case "022":
                    return new ProfessorProfileController();

                /* Event related */
                case "061":
                    return new PrivateEventController();

                /* Teaching role related */
                case "071":
                    return new GetTeachingTeamController();

                default:
                    return null;
            }
        }
    };