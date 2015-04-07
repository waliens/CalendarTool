<?php

	/**
	 * @file 
	 * @brief Contains the GetGlobalEventsByUserController class
	 */

	namespace ct\controllers\ajax;

	use util\mvc\AjaxController;
	use ct\models\events\GlobalEventModel;
	/**
	 * @class GetGlobalEventsByUserController
	 * @brief Request Nr : 031
	 * 		INPUT :
	 * 		OUTPUT : {courses:[{id, code, name}]}
	 * 		Method : POST
	 */
	class GetGlobalEventsByUserController extends AjaxController
	{
		/**
		 * @brief Constrcut the GetGlobalEventsByUserController and process the request
		 */ 
		public function __construct()
		{
			parent::__construct();

			// instantiate models 
			$glob_mod = new GlobalEventModel();

			// check user access
			if($this->connection->user_is_student())
			{	
				$by = GlobalEventModel::GET_BY_STUDENT;
				$id = $this->connection->user_id();
			}
			else
			{
				$by = GlobalEventModel::GET_BY_ACAD_YEAR;
				$id = \ct\get_academic_year();
			}

			// fetch user's global events
			$global_events = $glob_mod->get_global_events($by, $id);

			$trans_glob = array("id" => "", "ulg_id" => "code", "name_long" => "name");
			$this->add_output_data("courses", \ct\darray_transform($global_events, $trans_glob));
		}
	}