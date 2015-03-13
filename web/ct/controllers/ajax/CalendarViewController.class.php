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

			// if an error was already is detected, exit the function
			if($this->error_isset())
				return;

			// check params :
			if($this->sg_post->check("view", Superglobal::CHK_ALL) < 0)
			{
				$this->set_error_predefined(AjaxController::ERROR_MISSING_INPUT_DATA);
				return;
			}
			
			// get the events generated by the FilterCollection
			$current_events = $this->get_output_data();

			// structure the data to match the output format
			$current_events['events']['upperView'] = $this->extract_upper_view();

			$this->set_output_data($current_events);
		}

		/**
		 * @brief Extract the events for the upper view
		 * @retval array The array containing the upper view events
		 */ 
		private function extract_upper_view()
		{
			$filter_collection = new FilterCollectionModel();

			// get datetime filter
			$datetime_filter = $this->get_date_time_filter_for_upper();

			// add user filters
			$filter_collection->add_filters($this->get_filters());
			// add custom datetime filter (overwrite the user view one)
			$filter_collection->add_filter($datetime_filter);
			
			if($this->get_access_filter() != null) // add access filter if it is set
				$filter_collection->add_access_filter($this->get_access_filter());

			$events = $filter_collection->get_events();

			return $this->format_events($events);
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