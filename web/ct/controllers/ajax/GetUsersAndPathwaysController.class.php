<?php

	/**
	 * @file
	 * @brief Contains the GetUsersAndPathwaysController class 
	 */

	namespace ct\controllers\ajax;

	use util\mvc\AjaxController;
	use util\superglobals\Superglobal;

	use ct\models\UserModel;
	use ct\models\PathwayModel;
	use ct\models\TeachingRoleModel;
	use ct\models\events\IndependentEventModel;

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

			// true if the list of pathways must be restricted to the addable ones
			$do_restrict = false;

			if($this->sg_post->check("id_event", Superglobal::CHK_ISSET) > 0)
			{
				$id_event = $this->sg_post->value("id_event");
				$do_restrict = true;
				$indep_mod = new IndependentEventModel();
			}

			// check whether the independent event managers must be excluded
			if($do_restrict)
				$users = $indep_mod->getAddableUsers($id_event); // get only addable user
			else 
			{
				// get the all users
				$user_mod = new UserModel();
				$users = $user_mod->get_users();
			}
			
			$users = \ct\darray_transform($users, array("Name" => "name", "Surname" => "surname", "Id_User" => "id"));

			// get the pathways 
			if($do_restrict)
				$paths = $indep_mod->getAddablePathways($id_event); // get only addable pathways
			else
			{
				$path_mod = new PathwayModel();
				$paths = $path_mod->get_pathways();
			}

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