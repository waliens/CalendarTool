<?php

	/**
	 * @file
	 * @brief Contains the GetProfessorAvailableCoursesController class
	 */

	namespace ct\controllers\ajax;

	use util\mvc\AjaxController;
	use util\superglobals\Superglobal;

	use ct\models\events\GlobalEventModel;

	/** 
	 * @class GetProfessorAvailableCoursesController
	 * @brief A class for handling the get professor available courses request
	 *
	 * @todo Improve the documentation of the controllers
	 */
	class GetProfessorAvailableCoursesController extends AjaxController	
	{
		/**
		 * @brief Construct the GetProfessorAvailableCoursesController object and process the request
		 */
		public function __construct()
		{
			parent::__construct();

			// check if the user is a professor
			if($this->connection->user_is_student())
			{
				$this->set_error_predefined(AjaxController::ERROR_ACCESS_PROFESSOR_REQUIRED);
				return;
			}

			// check input parameters 
			if($this->sg_post->check("year", Superglobal::CHK_ALL) < 0)
			{
				$this->set_error_predefined(AjaxController::ERROR_MISSING_DATA);
				return;
			}

			// instantiate models
			$glob_mod = new GlobalEventModel();

			$avail_courses = $glob_mod->get_available_global_events();
			$trans_avail = array("Id_Course" => "id", "Name_Long" => "nameLong", "nameShort" => "nameShort");
			$this->add_output_data("courses", \ct\darray_transform($avail_courses, $trans_avail));
		}
	}