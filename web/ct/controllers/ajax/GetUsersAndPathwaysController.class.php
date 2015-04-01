<?php

	/**
	 * @file
	 * @brief Contains the GetUsersAndPathwaysController class 
	 */

	namespace ct\controllers\ajax;

	use util\mvc\AjaxController;
	use ct\models\UserModel;
	use ct\models\PathwayModel;
	use ct\models\TeachingRoleModel;

	/**
	 * @class GetUsersAndPathwaysController
	 * @brief A class for handling the request 087 -> getting all the pathways and all the users 
	 */
	class GetUsersAndPathwaysController extends AjaxController
	{
		/**
		 * @brief Construct the GetUsersAndPathwaysController object and process the request 
		 */
		public function __construct()
		{
			parent::__construct();

			// check access
			if($this->connection->user_is_student())
			{
				$this->set_error_predefined(AjaxController::ERROR_ACCESS_PROFESSOR_REQUIRED);
				return;
			}

			// get the users
			$user_mod = new UserModel();
			$users = $user_mod->get_users();
			$users = \ct\darray_transform($users, array("Name" => "name", "Surname" => "surname", "Id_User" => "id"));
		
			// get the pathways 
			$path_mod = new PathwayModel();
			$paths = $path_mod->get_pathways();
			$paths = \ct\darray_transform($paths, array("Id_Pathway" => "id", "Name_Long" => "name"));

			// get the roles
			$role_mod = new TeachingRoleModel();
			$roles = $role_mod->get_items();
			$roles = \ct\darray_transform($roles, array("Id_Role" => "id", "Role_FR" => "role"));

			$this->add_output_data("users", $users);
			$this->add_output_data("roles", $roles);
			$this->add_output_data("pathways", $paths);
		}
	}