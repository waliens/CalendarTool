<?php

	/**
	 * @file
	 * @brief Contains the EventCategoriesController class
	 */

	namespace ct\controllers\ajax;

	use util\mvc\AjaxController;
	use util\superglobals\Superglobal;

	use ct\models\events\GlobalEventModel;
	use ct\models\events\EventCategoryModel;

	/**
	 * @class EventCategoriesController
	 * @brief A class for handling the get event category request
	 */
	class EventCategoriesController extends AjaxController
	{
		/**
		 * @brief Construct the EventCategoriesController object and process the request
		 */
		public function __construct()
		{
			parent::__construct();

			// check input param : language (optionnal)
			if($this->sg_post->check("lang", Superglobal::CHK_ALL, function($lang) { return GlobalEventModel::valid_lang($lang); }) > 0)
				$language = $this->sg_post->value("lang");
			else
				$language = GlobalEventModel::LANG_FR; // default language

			// instantiate model :
			$categ_mod = new EventCategoryModel($language);

			$academic = $categ_mod->get_academic_event_categories();
			$private  = $categ_mod->get_student_event_categories();

			// format the arrays 
			$trans = array("Id_Category" => "id", "Category" => "name", "Description" => "desc", "Color" => "color");

			$academic = \ct\darray_transform($academic, $trans);
			$private = \ct\darray_transform($private, $trans);

			$this->add_output_data("academic", $academic);
			$this->add_output_data("student", $private);
		}
	}