<?php
	
	/**
	 * @file
	 * @brief Contains the GetTeachingRolesController class
	 */

	namespace ct\controllers\ajax;

	use util\mvc\AjaxController;
	use ct\models\TeachingRoleModel;
	use ct\models\events\GlobalEventModel;
	use util\superglobals\Superglobal;

	/**
	 * @class GetTeachingRolesController
	 * @brief Request Nr : 074
	 * 		INPUT : {lang}
	 * 		OUTPUT : {roles:[{id, role}]}
	 * 		Method : POST
	 */
	class GetTeachingRolesController extends AjaxController
	{
		/**
		 * @brief Construct the GetTeachingRolesController object and process the query
		 */
		public function __construct()
		{
			parent::__construct();

			// check input parameters
			$chk_lang_fn = function ($lang) { return GlobalEventModel::valid_lang($lang); };
			if(!$this->sg_post->check("lang", Superglobal::CHK_ALL, $chk_lang_fn))
			{
				$this->set_error_predefined(AjaxController::ERROR_MISSING_DATA);
				return;
			}

			// instantiate models
			$lang = $this->sg_post->value("lang");
			$role_mod = new TeachingRoleModel($lang);

			// extract and format data
			$roles = $role_mod->get_items();
			$roles = \ct\darray_transform($roles, array("Id_Role" => "id", "Role_".$lang => "role"));

			$this->add_output_data("roles", $roles);
		}
	}