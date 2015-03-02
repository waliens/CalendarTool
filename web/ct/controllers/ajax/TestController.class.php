<?php
	
	/**
	 * @file
	 * @brief Contains the TestController class
	 */

	namespace ct\controllers\ajax;

	use util\mvc\AjaxController;

	use ct\models\FilterCollectionModel;

	use ct\models\filters\DateTimeFilter;
	use ct\models\filters\EventCategoryFilter;
	use ct\models\filters\EventTypeFilter;
	use ct\models\filters\GlobalEventFilter;
	use ct\models\filters\PathwayFilter;
	use ct\models\filters\ProfessorFilter;

	/**
	 * @class TestController
	 * @brief A controller for testing
	 */
	class TestController extends AjaxController
	{
		/**
		 * @brief Construct the TestController object
		 */
		public function __construct()
		{
			parent::__construct();

			// set up filters
			$filters = array();
			$filters['datetime'] = new DateTimeFilter("02-03-2015"); // adter 02-04-2015
			$filters['event_category'] = new EventCategoryFilter(array(1)); // theoritical courses
			$filters['type_filter'] = new EventTypeFilter(EventTypeFilter::TYPE_ACADEMIC); // academic events
			$filters['glob'] = new GlobalEventFilter(array(40));
			$filters['path'] = new PathwayFilter(array("ABICAR000201"));
			$filters['prof'] = new ProfessorFilter(array(7));

			$filter_collection = new FilterCollectionModel();

			foreach ($filters as $filter) 
				$filter_collection->add_filter($filter);

			$this->output_data['event'] = $filter_collection->get_events();
		}
	}