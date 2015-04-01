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
	use ct\models\filters\AccessFilter;
	use ct\models\filters\TimeTypeFilter;

	use util\mvc\AjaxController;

	/**
	 * @class CalendarBaseDataController
	 * @brief A class for handling the calendar base data request
	 */
	class CalendarBaseDataController extends AjaxController
	{
		const SQL_DATETIME = "Y-m-d H:i:s"; /**< @brief The datetime french format */
		const MAX_NB_DEADLINE = 5; /**< @brief The maximum number of deadlines to return */

		private $access_filter; /**< @brief An access filter */
		private $datetime_filter; /**< @brief The datetime filter for filtering on the following two weeks */

		/**
		 * @brief Construct the CalendarBaseDataController object and process the request
		 */
		public function __construct()
		{
			parent::__construct();
			
			// init the common filters
			// filter on the two incoming weeks
			$start = $this->now_sql();
			$end   = $this->add_time("+2 weeks");
			$this->datetime_filter = new DateTimeFilter($start, $end);

			// use the default policy determine according to the type of user
			$this->access_filter = new AccessFilter(); 
	
			// get events that 
			$this->add_output_data("upcomingDeadlines", $this->get_deadlines());
			$this->add_output_data("upcomingEvents", $this->get_upcoming());
			$this->add_output_data("favorites", $this->get_favorites());
		}

		/**
		 * @brief Return the MAX_NB_DEADLINE closest deadlines happening in the following two weeks
		 * @retval array An array of deadline events, each one being formatted as {'id', 'name', 'limit'}
		 */
		private function get_deadlines()
		{
			$filter_collection = new FilterCollectionModel();

			// add filters
			$filter_collection->add_filter($this->datetime_filter);
			$filter_collection->add_filter(new TimeTypeFilter(TimeTypeFilter::TYPE_DEADLINE));

			// add acces filter
			$filter_collection->add_access_filter($this->access_filter);

			$deadlines = $filter_collection->get_events();

			// extract usefull data
			$deadlines = $this->convert_event_array($deadlines, true);

			// slice the array if it contains more than MAX_NB_DEADLINE events
			if(count($deadlines) > self::MAX_NB_DEADLINE)
				return array_slice($deadlines, 0, self::MAX_NB_DEADLINE);

			return $deadlines;
		}

		/** 
		 * @brief Return the upcoming events (of the two following weeks) sorted from the closest to the furthest
		 * @retval array An array of deadline events, each one being formatted as {'id', 'name', 'start'}
		 */
		private function get_upcoming()
		{
			$filter_collection = new FilterCollectionModel();

			// add filters
			$filter_collection->add_filter($this->datetime_filter);

			// add access filter
			$filter_collection->add_access_filter($this->access_filter);

			$upcoming = $filter_collection->get_events();

			// extract useful data
			$upcoming = $this->convert_event_array($upcoming, false);

			return $upcoming;
		}

		/**
		 * @brief Return the future favorite events
		 */
		public function get_favorites()
		{
			$filter_collection = new FilterCollectionModel();

			// add filters
			$filter_collection->add_filter($this->datetime_filter);
			$filter_collection->add_filter(new EventTypeFilter(EventTypeFilter::TYPE_ALL | EventTypeFilter::TYPE_FAVORITE));

			// add access filter
			$filter_collection->add_access_filter($this->access_filter);

			$favorites = $filter_collection->get_events();

			// extract useful data
			$favorites = $this->convert_event_array($favorites, false);

			return $favorites;
		}

		/**
		 * @brief Get the current datetime in the french format
		 * @retval string The date of the current time in the french format
		 */
		private function now_sql()
		{
			return date(self::SQL_DATETIME, time());
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
			if($start == null) $start = $this->now_sql();
			return date(self::SQL_DATETIME, strtotime($offset, strtotime($start)));
		}

		/**
		 * @brief Convert the event array so that it matches the request format
		 * @param[in] array $events   The array of events structured as the one returned from the FilterCollection get_events function
		 * @param[in] bool  $deadline True if the events are deadlines, false otherwise
		 * @retval array The formatted array
		 * @note The output format for an event is an array containing the following fields ('id', 'recurrence_id') and the time fields 
		 * that are ('start', 'end') if $deadline is false, and ('limit') if $deadline is true
		 */
		private function convert_event_array(array &$events, $deadline)
		{
			$out_events = array(); 

			foreach($events as &$event)
			{
				$curr = array();
				$curr['id'] = $event['Id_Event'];
				$curr['recurrence_id'] = $event['Id_Recurrence'];
				$curr['name'] = $event['Name'];

				if($deadline)
					$curr['limit'] = \ct\date_sql2fullcalendar($event['Start']);
				else
				{
					$curr['start'] = \ct\date_sql2fullcalendar($event['Start']);
					$curr['end'] = \ct\date_sql2fullcalendar($event['End']);
				}

				$out_events[] = $curr;
			}

			// sort array on the limit field
			if($deadline)
				$sort_func = function($event1, $event2) { strtotime($event1['limit']) < strtotime($event2['limit']); };
			else
				$sort_func = function($event1, $event2) { strtotime($event1['start']) < strtotime($event2['start']); };
			
			usort($out_events, $sort_func);

			return $out_events;
		}
	}