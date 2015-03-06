<?php

	/**
	 * @file
	 * @brief Contains the CalendarViewController class
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

			// set default values for the class member
			$this->filters = array();
			$this->access_filter = null;
			$this->events = array();

			// check params :
			$keys = array("view", "all", "dateRange", "courses", "eventTypes", "pathways", "professors");

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
			$this->extract_view_events();
			$this->extract_upper_view();

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
		 * @brief Extract the filtered event for the view and set the events class member with them
		 */
		private function extract_view_events()
		{
			$filter_collection = new FilterCollectionModel();

			// public events : academic
			$types_map = array("public" => EventTypeFilter::TYPE_ACADEMIC,
							   "private" => EventTypeFilter::TYPE_STUDENT);

		
			foreach ($types_map as $output_key => $event_type) 
			{
				$type_filter = new EventTypeFilter($event_type);

				$filter_collection->add_filters($this->filters);
				$filter_collection->add_filter($type_filter);
				
				if($this->access_filter != null) // add access filter if it is set
					$filter_collection->add_access_filter($this->access_filter);

				$events = $filter_collection->get_events();

				$this->events[$output_key] = $this->format_events($events);

				$filter_collection->reset();
			}
		}

		/** 
		 * @brief Format the event array 
		 * @param[in] array $events The event array
		 * @retval The formatted array
		 * @note The columns (Id_Event, Name, Start, End, Id_Recurrence, Datetype) are renamed to
		 * (id, name, start, end, recursive, timeType) and the dates are converted to the french format
		 */
		private function format_events(array &$events)
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
		 * @brief Extract the events for the upper view
		 */ 
		private function extract_upper_view()
		{
			$filter_collection = new FilterCollectionModel();

			// get datetime filter
			$datetime_filter = $this->get_date_time_filter_for_upper();

			// add user filters
			$filter_collection->add_filters($this->filters);
			// add custom datetime filter (overwrite the user view one)
			$filter_collection->add_filter($datetime_filter);
			
			if($this->access_filter != null) // add access filter if it is set
				$filter_collection->add_access_filter($this->access_filter);

			$events = $filter_collection->get_events();

			$this->events["upperView"] = $this->format_events($events);
		}

		/**
		 * @brief Create a DateTimeFilter from the view type and start date
		 * @retval DateTimeFilter The datetime filter
		 */
		private function get_date_time_filter_for_upper()
		{
			$view = $this->sg_post->value("view");
			$actual_start = $this->sg_post->value("dateRange")['start'];
			$actual_start = \ct\date_fr2sql($actual_start);
			$date = new \DateTime($actual_start);
			$actual_time = strtotime($actual_start);

			// start and end date
			$start = "";
			$end = "";

			switch ($view) 
			{
			case 'day': // uper view is week
				$year_week = $date->format("Y")."W".$date->format("W");
				$start = date("d-m-Y 00:00:00", strtotime($year_week));
				$end   = date("d-m-Y 23:59:59", strtotime("+6 days", strtotime($year_week)));
				break;
			case 'week': // upper view is month
				$start = date("01-m-Y 00:00:00", $actual_time);
				$end   = date("t-m-Y 23:59:59", $actual_time);
				break;
			case 'month': // upper view is semester
				$month = $date->format("m");
				$semester = ($month == 1 || ($month >= 9 && $month <= 12)) ? 1 : 2;

				$start_year = $date->format("Y");
				$end_year   = $date->format("Y");

				// for the first semester, the start year is different from 
				// the end year
				if($month == 1 && $semester == 1) 
					$start_year -= 1;

				if($semester == 1)
				{
					$start = "15-09-".$start_year." 00:00:00";
					$end   = "31-01-".$end_year." 23:59:59";
				}
				else
				{
					$start = "01-02-".$start_year." 00:00:00";
					$end   = "14-09-".$end_year." 23:59:59";
				}

				break; 
			case 'semester': // upper view is year
				$start = date("01-01-Y 00:00:00", $actual_time);
				$end   = date("31-12-Y 23:59:59", $actual_time);
				break;
			default:
				trigger_error("Bad view", E_USER_ERROR);
			}

			return new DateTimeFilter($start, $end);
		}
	}