<?php

	/**
	 * @file 
	 * @brief Contains the ProfessorProfileController class 
	 */

	namespace ct\controllers\ajax;

	use util\mvc\AjaxController;

	use ct\models\events\GlobalEventModel;
	use ct\models\UserModel;

	use ct\models\FilterCollectionModel;
	use ct\models\filters\EventTypeFilter;
	use ct\models\filters\AccessFilter;
	use ct\models\filters\RecurrenceFilter;
	/**
	 * @class ProfessorProfileController
	 * @brief Request Nr : 022
	 * 		INPUT : 
	 * 		OUTPUT : {firstName, lastName, email, courses:[{id, code, lib_cours_complet}], indep_events:[{id, name, start, recurrence_type}]}
	 * 		Method : GET
	 */
	class ProfessorProfileController extends AjaxController
	{
		/**
		 * @brief Construct the getProfessorProfile object
		 */
		public function __construct()
		{
			parent::__construct();

			if($this->connection->user_is_student())
			{
				$this->set_error_predefined(AjaxController::ERROR_ACCESS_PROFESSOR_REQUIRED);
				return;
			}

			// {firstName, lastName, courses:[{id, code, lib_cours_complet, global (boolean)}]}

			// get user id
			$user_id = $this->connection->user_id();

			// instantiate the models
			$glob_mod = new GlobalEventModel();
			$user_mod = new UserModel();

			// the prof data
			$prof_data = $user_mod->get_user();
			$prof_data = \ct\array_keys_transform($prof_data, array("Name" => "firstName", "Surname" => "lastName", "Email" => "email"));

			// get courses data
			$glob_events = $glob_mod->get_global_events_by_user_role();
			$prof_data['courses'] = \ct\darray_transform($glob_events, array("id" => "", "ulg_id" => "code", "name_long" => "lib_cours_complet"));

			// get independent events
			$filter_collection = new FilterCollectionModel();
			$filter_collection->add_filter(new EventTypeFilter(EventTypeFilter::TYPE_INDEPENDENT));
			$filter_collection->add_filter(new RecurrenceFilter(false, true));
			$filter_collection->add_access_filter(new AccessFilter(AccessFilter::POLICY_AS_FACULTY_MEMBER_OWN));

			$indep_events = $filter_collection->get_events();
			$trans_indep = array("Id_Event" => "id", "Name" => "name", "Start" => "start", "Id_Recur_Category" => "recurrence_type");
			$prof_data['indep_events'] = \ct\darray_transform($indep_events, $trans_indep);

			$this->set_output_data($prof_data);
		}
	}