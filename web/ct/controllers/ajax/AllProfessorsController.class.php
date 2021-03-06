<?php
	
	/**
	 * @file
	 * @brief Contains the AllProfessorsController class
	 */

	namespace ct\controllers\ajax;

	use util\mvc\AjaxController;
	use ct\models\UserModel;

	/**
	 * @class AllProfessorsController
	 * @brief Request Nr : 021
	 * 		INPUT :	None
  	* 		OUTPUT : {professors:[{id, name, surname}]}
	 * 		Method : GET
	 */
	class AllProfessorsController extends AjaxController
	{
		public function __construct()
		{
			parent::__construct();

			$user_mod = new UserModel();

			// get and format the professors array
			$profs = $user_mod->get_professors();

			// key transformation 
			$trans = array("Id_User" => "id", "Name" => "name", "Surname" => "surname");
			$this->add_output_data("professors", \ct\darray_transform($profs, $trans));
		}
	}