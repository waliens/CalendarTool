<?php
	
	/**
	 * @file
	 * @brief Contains the CalendarBaseDataController class
	 */

	namespace ct\controllers\ajax;

	use ct\models\FilterCollectionModel;
	use ct\models\filters\DateTimeFilter;
	use ct\models\filters\EventCategoryFilter;
	use ct\models\filters\EventTypeFilter;
	use ct\models\filters\GlobalEventFilter;
	use ct\models\filters\PathwayFilter;
	use ct\models\filters\ProfessorFilter;

	/**
	 * @class CalendarBaseDataController
	 * @brief A class for handling the calendar base data request
	 */
	class CalendarBaseDataController extends AjaxController
	{
		const FR_DATETIME = "d-m-Y H:i:s"; /**< @brief The datetime french format */
		const MAX_NB_DEADLINE = 5; /**< @brief The maximum number of deadlines to return */

		private $access_filter; /**< @brief An access filter */

		/**
		 * @brief Construct the CalendarBaseDataController object and process the request
		 */
		public function __construct()
		{
			parent::__construct();
			/*
{upcomingDeadlines:[{id, limit, name}], upcomingEvents:[{id, start, name}],favorites:[{id,start,name}]}
			*/
			
			// init a common access filter
			// use the default policy determine according to the type of user
			$this->access_filter = new AccessFilter(); 

			// get events that 
			$this->add_output_data("upcomingDeadlines", $this->get_deadlines());
			$this->add_output_data("upcomingEvents", $this->get_upcoming());
		}

		/**
		 * @brief Return the MAX_NB_DEADLINE closest deadlines happening in the following two weeks
		 * @retval array An array of deadline events, each one being formatted as {'id', 'name', 'limit'}
		 */
		private function get_deadlines()
		{
			$filter_collection = new FilterCollectionModel();

			// add filters
			$start = $this->now_fr();
			$end   = $this->add_time("+2 weeks");

			$filter_collection->add_filter(new DateTimeFilter($start, $end));
			$filter_collection->add_filter(new TimeTypeFilter(TimeTypeFilter::DEADLINE_EVENT));

			// add access filter
			//$filter_collection->add_access_filter($this->access_filter);

			$deadlines = $filter_collection->get_events();

			// extract usefull data
			$transform = array("Id_Event" => "id", "Start" => "limit", "Name" => "name");
			$deadlines = \ct\darray_transform($deadlines, $transform);

			// sort array on the limit field
			usort($deadlines, function($event1, $event2) { strtotime($event1['limit']) < strtotime($event2['limit']); } );

			// slice the array if it contains more than MAX_NB_DEADLINE events
			if(count($deadlines) > self::MAX_NB_DEADLINE)
				return array_slice($deadlines, 0, self::MAX_NB_DEADLINE);

			return $deadlines;
		}

		private function get_upcoming()
		{
			$filter_collection = new FilterCollectionModel();

			// add filters
			$start = $this->now_fr();
			$end   = $this->add_time("+2 weeks");

			$filter_collection->add_filter(new DateTimeFilter($start, $end));
			$filter_collection->add_filter(new EventTypeFilter(EventTypeFilter::ACADEMIC_EVENT));

			// add access filter
			//$filter_collection->add_access_filter($this->access_filter);

			$upcoming = $filter_collection->get_events();

			// extract usefull data
			$transform = array("Id_Event" => "id", "Start" => "start", "Name" => "name");
			$upcoming = \ct\darray_transform($upcoming, $transform);

			// sort array on the limit field
			usort($upcoming, function($event1, $event2) { strtotime($event1['start']) < strtotime($event2['start']); } );

			return $upcoming;
		}

		/**
		 * @brief Get the current datetime in the french format
		 * @retval string The date of the current time in the french format
		 */
		private function now_fr()
		{
			return date(self::FR_DATETIME, time());
		}

		/**
		 * @brief Add an offset to the given start date and time
		 * @param[in] string $offset A string indicating the offset
		 * @param[in] string $start  The start datetime in the french format (optionnal, default: now)
		 * @retval string The resulting date in the frenchformat
		 * @note The offset must be indicated in terms of minute, hour, day, month or year : +1 month, -1 month,...
		 */
		private function add_time($offset, $start=null)
		{
			if($start == null) $start = $this->now_fr();
			return date(self::FR_DATETIME, strtotime($offset, strtotime($start)));
		}
	}