<?php
	
	/**
	 * @file
	 * @brief Contains the FilterController class
	 */

	namespace ct\controllers\ajax;

	use util\mvc\AjaxController;
	use util\superglobals\Superglobal;

	use ct\models\FilterCollectionModel;
	use ct\models\filters\DateTimeFilter;
	use ct\models\filters\AccessFilter;
	use ct\models\filters\PathwayFilter;
	use ct\models\filters\GlobalEventFilter;
	use ct\models\filters\ProfessorFilter;
	use ct\models\filters\EventCategoryFilter;
	use ct\models\filters\EventTypeFilter;

	/**
	 * @class FilterController
	 * @brief A class for handling the event selection with filters
	 */
	class FilterController extends AjaxController
	{
		private $filters; /**< @brief Array of filters */
		private $access_filter; /**< @brief An access filter */

		/**
		 * @brief Construct the FilterController object and process the request
		 * @note Set the output data array with the events. The output data array is structured as an array mapped by
		 * the key "events" and containing the keys "publicEvents" and "privateEvents" :
		 * {events:{publicEvents:[{id, name, timeType, start, end, recursive}], privateEvents:[{id, name, timeType, start, end, recursive}]}}}
		 */
		public function __construct()
		{
			parent::__construct();

			// set default values for the class member
			$this->filters = array();
			$this->access_filter = null;

			// check params :
			$keys = array("all", "dateRange", "courses", "eventTypes", "pathways", "professors");

			if($this->sg_post->check_keys($keys, Superglobal::CHK_ISSET) < 0)
			{
				$this->set_error_predefined(AjaxController::ERROR_MISSING_INPUT_DATA);
				return;
			}
			
			// create datetime filter with start and end date
			if(!$this->extract_datetime_filter())
				return;

			// set the filters if necessary
			if(!$this->sg_post->value("all")) 
			{
				// filters keys
				$filter_keys = array("courses", "eventTypes", "pathways", "professors");

				// extract filters
				foreach($filter_keys as $key)
					if(!$this->extract_filter_from_query($key))
						return;

				// add access filter
				$this->access_filter = new AccessFilter();						
			}

			// structure the data to match the output format
			$events = $this->extract_events();

			$this->add_output_data("events", $events);
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
				if(!isset($datetime_data['start'], $datetime_data['end']))
				{
					$this->set_error_predefined(AjaxController::ERROR_MISSING_INPUT_DATA);
					return false;
				}

				array_push($this->filters, new DateTimeFilter($datetime_data['start'], $datetime_data['end']));
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

			if(!isset($query_entry['id']) || count($query_entry['id']) == 0)
			{
				$this->set_error_predefined(self::ERROR_MISSING_ID);
				return false;
			}

			// convert the string ids to actual integers
			$ids = array_map("intval", $query_entry['id']);

			switch ($key) {
				case "courses": 
					$filter = new GlobalEventFilter($ids);
					break; 
			   	case "eventTypes": 
			   		$filter = new EventCategoryFilter($ids);
			   		break; 
			    case "pathways": 
			    	$filter = new PathwayFilter($ids);
			    	break; 
			    case "professors": 
			    	$filter = new ProfessorFilter($ids);
			    	break;
				default:
					trigger_error("Filter item key is invalid", E_USER_ERROR);
					break;
			}

			array_push($this->filters, $filter);
			return true;
		}

		/** 
		 * @brief Format the event array 
		 * @param[in] array $events The event array
		 * @retval The formatted array
		 * @note The columns (Id_Event, Name, Start, End, Id_Recurrence, Datetype) are renamed to
		 * (id, name, start, end, recursive, timeType) and the dates are converted to the french format
		 */
		protected function format_events(array &$events)
		{
			// data for columns renaming
			$out_keys = array("Id_Event" => "id", "Name" => "name", 
							  "Start" => "start", "End" => "end", 
							  "Id_Recurrence" => "recursive", 
							  "DateType" => "timeType");

			$renamed = \ct\darray_transform($events, $out_keys);
			$start_formatted = \ct\array_col_map($renamed, "\ct\date_sql2fr", "start");
			return \ct\array_col_map($start_formatted, "\ct\date_sql2fr", "end");
		}

		/**
		 * @brief Extract the filtered event
		 * @retval array An array containing the filtered events 
		 */
		private function extract_events()
		{
			$filter_collection = new FilterCollectionModel();

			$events_array = array();

			// public events : academic
			$types_map = array("public" => EventTypeFilter::TYPE_ACADEMIC,
							   "private" => EventTypeFilter::TYPE_STUDENT);

			foreach ($types_map as $output_key => $event_type) 
			{
				$type_filter = new EventTypeFilter($event_type);

				$filter_collection->add_filters($this->get_filters());
				$filter_collection->add_filter($type_filter);
				
				if($this->get_access_filter() != null) // add access filter if it is set
					$filter_collection->add_access_filter($this->get_access_filter());

				$events = $filter_collection->get_events();

				$events_array[$output_key] = $this->format_events($events);

				$filter_collection->reset();
			}

			return $events_array;
		}

		/**
		 * @brief Return the set of filters extracted from the request
		 * @retval array An array of EventFilter objects
		 */
		protected function get_filters()
		{
			return $this->filters;
		}

		/**
		 *
		 */
		protected function get_access_filter()
		{
			return $this->access_filter;
		}
	}