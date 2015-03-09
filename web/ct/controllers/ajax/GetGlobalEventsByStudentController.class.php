<?php

	/**
	 * @file 
	 * @brief Contains the GetGlobalEventsByStudentController class
	 */

	namespace ct\controllers\ajax;

	use util\mvc\AjaxController;
	use ct\models\events\GlobalEventModel;

	/**
	 * @class GetGlobalEventsByStudentController
	 * @brief A class for handling the get global event by student controller 
	 */
	class GetGlobalEventsByStudentController extends AjaxController
	{
		/**
		 * @brief Constrcut the GetGlobalEventsByStudentController and process the request
		 */ 
		public function __construct()
		{
			parent::__construct();

			// check student access
			if(!$this->connection->user_is_student())
			{
				$this->set_error_predefined(AjaxController::ERROR_ACCESS_STUDENT_REQUIRED);
				return;
			}

			// instantiate models 
			$glob_mod = new GlobalEventModel();

			// fetch student's global events
			$global_events = $glob_mod->get_global_events(GlobalEventModel::GET_BY_STUDENT, $this->connection->user_id());

			$trans_glob = array("id" => "", "ulg_id" => "code", "name_long" => "name");
			$this->add_output_data("courses", \ct\darray_transform($global_events, $trans_glob));
		}
	}