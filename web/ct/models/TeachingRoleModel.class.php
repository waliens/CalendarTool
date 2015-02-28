<?php

	/**
	 * @file
	 * @brief Contains the TeachingRoleModel class
	 */

	namespace ct\models;

	use util\mvc\CategoryModel;

	/**
	 * @brief A class for handling teaching role data related database queries
	 */
	class TeachingRoleModel extends CategoryModel
	{
		private $lang; /**< @brief The language in which the model should select the role name */

		/**
		 * @brief Construct the TeachingRoleModel object in a given language
		 * @param[in] string $lang One of the GlobalEventModel LANG_* const (optional, default: french)  
		 */
		public function __construct($lang=null)
		{
			parent::__construct("teaching_role",
								array("Id_Role"),
								$lang == GlobalEventModel::LANG_EN ? "Role_EN" : "ROLE_FR",
								array("Description", $lang == GlobalEventModel::LANG_EN ? "Role_FR" : "ROLE_EN"));

			$this->lang = ($lang == GlobalEventModel::LANG_EN ? "Role_EN" : "ROLE_FR");
		}
	}