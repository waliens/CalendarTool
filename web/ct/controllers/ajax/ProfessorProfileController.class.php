<?php

	/**
	 * @file 
	 * @brief Contains the ProfessorProfileController class 
	 */

	namespace ct\controllers\ajax;

	use util\mvc\AjaxController;

	use ct\models\events\GlobalEventModel;
	use ct\models\

	/**
	 * @class ProfessorProfileController 
	 * @brief A class for handling the getProfessorProfile ajax request
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
				$this->set_error("");
				return 
			}

			// {firstName, lastName, courses:[{id, code, lib_cours_complet, global (boolean)}]}

			// get user id
			$user_id = $this->connection->user_id();

			// instantiate the models
			$glob_mod = new GlobalEventModel();
		}
	}