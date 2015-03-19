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

	/**
	 * @class CalendarViewController
	 * @brief A class for handling the selection of events for any calendar view
	 */
	class CalendarViewController extends FilterController
	{
		/**
		 * @brief Construct the CalendarViewController object and process the request
		 */
		public function __construct()
		{
			parent::__construct();

			// if an error was already detected, exit the construction
			if($this->error_isset())
				return;

			// check params : view
			if($this->sg_post->check("view", Superglobal::CHK_ALL) < 0)
			{
				$this->set_error_predefined(AjaxController::ERROR_MISSING_INPUT_DATA);
				return;
			}
			
			// get the events from the filters
			$filter_collection = new FilterCollectionModel();
			$filter_collection->add_filters($this->get_filters());
			$filter_collection->add_access_filter(new AccessFilter());
			$events = $filter_collection->get_events();

			// split events into private and public + format data
			$public = array();
			$private = array();

			foreach ($events as &$event) 
			{
				$f_event = $this->format_event_data($event);

				if($event['EventType'] === "student")
					$private[] = $f_event;
				else
					$public[] = $f_event;
			}

			// add events as output
			$events = array("public" => $public, "private" => $private);
			$this->add_output_data("events", $events);

			// get the formatted event for the upper view
			$this->add_output_data("upperView", $this->extract_upper_view());
		}

		/**
		 * @brief Format an event array to output
		 * @param[in] array $event The event to format
		 * @retval array An array containing the formatted event
		 * @note The date are converted to the full calendar format
		 * @note The keys are changed from -> to : 
		 * ('Id_Event', 'Name', 'Start', 'End', 'Id_Recurrence', 'DateType', 'Color') 
		 * -> ('id', 'name', 'start', 'end', 'recursive', 'timeType', 'color')
		 */
		private function format_event_data(array &$event)
		{
			$f_event = array(); // formatted event
			$f_event['id'] = $event['Id_Event'];
			$f_event['name'] = $event['Name'];
			$f_event['start'] = \ct\date_sql2fullcalendar($event['Start']);
			$f_event['end'] = \ct\date_sql2fullcalendar($event['End']);
			$f_event['recursive'] = $event['Id_Recurrence'];
			$f_event['timeType'] = $event['DateType'];
			$f_event['color'] = $event['Color'];
			return $f_event;
		}

		/**
		 * @brief Extract the events for the upper view
		 * @retval array The array containing the upper view events
		 */ 
		private function extract_upper_view()
		{
			// get datetime filter for the upper view
			$datetime_filter = $this->get_upper_view_datetime_filter();

			// add user filters
			$filter_collection = new FilterCollectionModel();
			$filter_collection->add_filters($this->get_filters());
			$filter_collection->add_filter($datetime_filter); // overwrite the previous datetime filter
			$filter_collection->add_access_filter(new AccessFilter());

			$events = $filter_collection->get_events();

			return array_map(array($this, "format_event_data"), $events);
		}

		/**
		 * @brief Create a DateTimeFilter from the view type and start date
		 * @retval DateTimeFilter The datetime filter
		 */
		private function get_upper_view_datetime_filter()
		{
			$view = $this->sg_post->value("view");
			$actual_start = $this->sg_post->value("dateRange");
			$actual_start = $actual_start['start'];
			$actual_start = \ct\date_fullcalendar2sql($actual_start);
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