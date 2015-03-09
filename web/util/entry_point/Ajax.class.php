<?php

    /**
     * @file 
     * @brief Ajax entry point
     */

    namespace util\entry_point;
    
    use ct\controllers\ajax\GetPrivateEventController;

				use ct\controllers\ajax\DeleteEventController;

				use ct\controllers\ajax\ViewEventController;

				use ct\controllers\ajax\GetEventTypeController;

				use ct\controllers\ajax\DeleteController;
	use ct\controllers\ajax\AddNoteController;
	use ct\controllers\ajax\AddNote;
	use ct\controllers\ajax\TestController;
    use ct\controllers\ajax\PrivateEventController;
    use ct\controllers\ajax\AllProfessorsController;
    use ct\controllers\ajax\ProfessorProfileController;
    use ct\controllers\ajax\GetTeachingTeamController;
    use ct\controllers\ajax\AddTeachingTeamMemberController;
    use ct\controllers\ajax\DeleteTeachingTeamMemberController;
    use ct\controllers\ajax\CalendarBaseDataController;
    use ct\controllers\ajax\CalendarViewController;
    use ct\controllers\ajax\GetPathwaysController;
    use ct\controllers\ajax\DeleteGlobalEventController;
    use ct\controllers\ajax\ViewGlobalEventController;
    use ct\controllers\ajax\StudentProfileController;
    use ct\controllers\ajax\GetTeachingRolesController;
    use ct\controllers\ajax\StaticExportController;
    use ct\controllers\ajax\GetGlobalEventsByStudentController;
    
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
        public function __construct()
        {
            header('Content-Type: text/html; charset=utf-8');
            $this->sg_get = new SG_Get();
        }

        /**
         * @copydoc EntryPoint::get_controller
         */
        public function get_controller()
        {
            if($this->sg_get->check("req") !== Superglobal::ERR_OK)
                return null;

            switch($this->sg_get->value("req"))
            {
                case "000";
                    return new TestController();

                /* Student-related requests */
                case "011":
                    return new StudentProfileController();

                /* User-related requests */
                case "021":
                    return new AllProfessorsController();
                case "022":
                    return new ProfessorProfileController();

<<<<<<< HEAD
                 /* Note related  */
                case "041":
                	return new GetEventTypeController();
                case "042":
                	return new AddNoteController();
                case "043":
                	return new AddNoteController(true);
                case "044":
                	return new DeleteController();                	
                /* Sub Event related */
                case "051":
                	return new ViewEventController("SUB");
                case "055":
                	return new DeleteEventController("SUB");
                /* Private Event related */
                case "061":
                    return new PrivateEventController();
                case "062":
                	return new GetPrivateEventController();
                case '063':
                	return new DeleteEventController("PRIVATE");
                case '064':
                	return new ViewEventController("PRIVATE");
=======
                /* Global event related */
                case "031": 
                    return new GetGlobalEventsByStudentController();
                case "032":
                    return new ViewGlobalEventController();
                case "033":
                    return new DeleteGlobalEventController();

                /* Event related */
                case "061":
                    return new PrivateEventController();

                /* Teaching role related */
                case "071":
                    return new GetTeachingTeamController();
                case "072":
                    return new AddTeachingTeamMemberController();
                case "073":
                    return new DeleteTeachingTeamMemberController();
                case "074":
                    return new GetTeachingRolesController();

                /* Export related */
                case "091":
                    return new StaticExportController();

                /* Calendar views */
                case "101":
                    return new CalendarBaseDataController();
                case "102":
                    return new CalendarViewController();

                /* Pathways */
                case "111":
                    return new GetPathwaysController();

>>>>>>> 274c94718919d0a38d35a799789d0496df103595
                default:
                    return null;
            }
        }
    };