<?php

	/**
	 * @file
	 * @brief Contains the CalendarViewController class
	 */

	namespace ct\controllers\ajax;

	use util\mvc\AjaxController;
	use util\superglobals\Superglobal;

	use ct\models\FilterCollection;
	use ct\models\filters\DateTimeFilter;

	/**
	 * @class CalendarViewController
	 * @brief A class for handling the selection of events for any calendar view
	 */
	class CalendarViewController extends AjaxController
	{
		private $filters; /**< @brief Array of filters */
		private $access_filter; /**< @brief An access filter */
		private $events; /**< @brief Array containing the events : array("public" => "", "private" => "array") */

		/**
		 * @brief Construct the CalendarViewController object and process the request
		 */
		public function __construct()
		{
			parent::__construct();
			// check params :
			$keys = array("view", "allEvents", "dateRange", "courses", "eventTypes", "pathways", "professors");

			if($this->sg_post->check_keys($keys, Superglobal::CHK_ISSET) < 0)
			{
				$this->set_error_predefined(AjaxController::ERROR_MISSING_DATA);
				return;
			}

			// init filter collection 
			$this->filter_collection = new FilterCollection();
			
			// create datetime filter with start and end date
			if(!$this->extract_datetime_filter())
				return;

			// filters keys
			$filter_keys = array("courses", "eventTypes", "pathways", "professors");

			// extract filters
			for($filter_keys as $key)
				if(!$this->extract_filter_from_query($key))
					return;

			// add access filter
			$this->access_filter = new AccessFilter();

			if($this->filter_collection->empty())
			{
				$this->set_error_predefined(AjaxController::ERROR_MISSING_ID);
				return;
			}

			// structure the data to match the output format
			$this->format_events();

			$this->add_output_data("events", $this->events);
		}

		/**
		 * @brief Extract the datetime filter from the query 
		 * @retval bool True if the filter was successfully extracted, false otherwise
		 * @note If the action fails, the error is set
		 */
		private function extract_datetime_filter()
		{
			$datetime_data = $this->sg_post->value("dateRange");

			try
			{
				if(!isset($datetime_data['startDate'], $datetime_data['endDate']))
				{
					$this->set_error_predefined(AjaxController::ERROR_MISSING_DATA);
					return false;
				}

				array_push($this->filters, new DateTimeFilter($datetime_data['startDate'], $datetime_data['endDate']));
			}
			catch(\Exception $e)
			{
				return false;
			}

			return true;
		}

		/**
		 * @brief Extract the filter corresponding to the word from the query 
		 * @param[in] string $key The key in the input data array corresponding to the filter to extract
		 * @retval bool True if the query was successfully processed (added or not), false on error
		 * @note If an error occurs, then the error field is set with the appropriate error
		 * @note The key must exist in the input data array
		 */
		private function extract_filter_from_query($key)
		{
			$query_entry = $this->sg_post->value($key);

			if(isset($query_entry['isSet']) && !$query_entry['isSet'])
				return true;

			if(!isset($query_entry['id']) || count($query_entry) == 0)
			{
				$this->set_error_predefined(self::ERROR_MISSING_ID);
				return false;
			}

			switch ($key) {
				case "courses": 
					$filter = new GlobalEventFilter($query_entry['id']);
					break; 
			   	case "eventTypes": 
			   		$filter = new EventCategoryFilter($query_entry['id']);
			   		break; 
			    case "pathways": 
			    	$filter = new PathwayFilter($query_entry['id']);
			    	break; 
			    case "professors": 
			    	$filter = new ProfessorFilter($query_entry['id']);
			    	break;
				default:
					trigger_error("Filter item key is invalid", E_USER_ERROR);
					break;
			}

			array_push($this->filters, $filter);
			return true;
		}

		/**
		 * @brief Fetch the events from the database, format them and set the output data array
		 */
		private function format_events()
		{
			$filter_collection = new FilterCollection();
 			/*
{events:{{events:{publicEvents:[{id, name, timeType, start, end, recursive}], 
		  privateEvents:[{id, name, timeType, start, end, recursive}]}, weekEvents:{id, name, timeType, start, end}}}}
 			*/
			// public events : academic
			$types_map = array("public" => EventTypeFilter::TYPE_ACADEMIC,
							   "private" => EventTypeFilter::TYPE_STUDENT);

			$out_keys = array("Id_Event" => "id", "Name" => "name", "Start" => "start", "End" => "end", 
							  "Id_Recurrence" => "recursive", "DateType" => "timeType");

			foreach ($types_map as $output_key => $event_type) 
			{
				$type_filter = new EventTypeFilter($event_type);

				$filter_collection->add_filters($this->filters);
				$filter_collection->add_filter($type_filter);
				$filter_collection->add_access_filter($this->access_filter);

				$events = $filter_collection->get_events();

				$this->events[$output_key] = \ct\darray_transform($events, $out_keys);

				$filter_collection->reset();
			}
		}
	}