<?php

	/** 
	 * @file
	 * @brief Contains the StudentProfileController class
	 */

	namespace ct\controllers\ajax;

	use util\mvc\AjaxController;
	use ct\models\events\GlobalEventModel;
	use ct\models\PathwayModel;
	use ct\models\UserModel;

	/** 
	 * @class StudentProfileController
	 * @brief A class for handling the get student profile request
	 */
	class StudentProfileController extends AjaxController
	{
		/**
		 * @brief Construct the StudentProfileController object and process the request
		 */
		public function __construct()
		{
			parent::__construct();

			if(!$this->connection->user_is_student())
			{
				$this->set_error_predefined(AjaxController::ERROR_ACCESS_STUDENT_REQUIRED);
				return;
			}

			// instantiate model 
			$glob_mod = new GlobalEventModel();
			$path_mod = new PathwayModel();
			$user_mod = new UserModel();

			$user_id = $this->connection->user_id();

			// get and format the output data
			$mandatory = $glob_mod->get_global_events(GlobalEventModel::GET_BY_STUDENT_NO_OPT, $user_id);
			$optional = $glob_mod->get_global_events_optionnal();

			$trans_mandatory = array("id" => "", "ulg_id" => "code", "name_long" => "lib_cours_complet");
			$mandatory = \ct\darray_transform($mandatory, $trans_mandatory);

			$trans_optionnal = array("id" => "", "ulg_id" => "code", "name_long" => "lib_cours_complet", "selected" => "");
			$optional = \ct\darray_transform($optional, $trans_optionnal);

			// get and format pathway
			$pathway = $path_mod->get_pathway_by_student();

			$trans_pathway = array("Id_Pathway" => "id", "Name_Long" => "nameLong", "Name_Short" => "nameShort");
			$pathway = \ct\array_keys_transform($pathway, $trans_pathway);

			// get the user name and surname
			$user = $user_mod->get_user();
			$trans_user = array("Name" => "firstName", "Surname" => "lastName", "Email" => "email");
			$user = \ct\array_keys_transform($user, $trans_user);

			// set the output data array
			$this->set_output_data($user);
			$this->add_output_data("pathway", $pathway);
			$this->add_output_data("courses", array("mandatory" => $mandatory, "optional" => $optional));
		}
	}