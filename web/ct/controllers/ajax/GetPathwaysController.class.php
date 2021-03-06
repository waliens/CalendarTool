<?php 

	/**
	 * @file
	 * @brief Contains the GetPathwaysController class
	 */

	namespace ct\controllers\ajax;

	use util\mvc\AjaxController;
	use ct\models\PathwayModel;
	/**
	 * @class GetPathwaysController
	 * @brief Request Nr : 111
	 * 		INPUT :
	 * 		OUTPUT : {pathways:[{id, name}]}
	 * 		Method : POST
	 */
	class GetPathwaysController extends AjaxController
	{
		/**
		 * @brief Construct the GetPathwaysController and process the request
		 */
		public function __construct()
		{
			parent::__construct();

			// instantiate model
			$path_mod = new PathwayModel();

			// extract and format pathways
			$pathways = $path_mod->get_pathways();
			$pathways = \ct\darray_transform($pathways, array("Id_Pathway" => "id", "Name_Short" => "name"));

			$this->add_output_data("pathways", $pathways);
		}
	}