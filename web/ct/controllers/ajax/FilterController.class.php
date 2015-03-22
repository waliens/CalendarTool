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
	 * @note This class perform a basic preprocessing on a query containing event filters in the following form
	 * {all:'true', 
	 *  dateRange: {start: datetime, end: datetime},
	 *  courses: {isSet: 'false', id:[]},
	 *  eventTypes: {isSet: 'false', id:[]},
	 *  eventCategories:{isSet:'false', id:[]},
	 *  pathways: {isSet: 'false', id:[]},
	 *  professors:{isSet: 'false', id:[]}} 
	 */
	class FilterController extends AjaxController
	{
		private $filters; /**< @brief Array of filters */

		/**
		 * @brief Construct the FilterController object and process the request
		 * @note Only initialize the filters according to the array input data. The filters can be obtained in the 
		 * derived classes with the get_filters function. An access filter is never created.
		 */
		public function __construct()
		{
			parent::__construct();

			// set default values for the class member
			$this->filters = array();
			$this->access_filter = null;

			// check params :
			$keys = array("allEvent", "dateRange", "courses", "eventTypes", "eventCategories", "pathways", "professors");

			if($this->sg_post->check_keys($keys, Superglobal::CHK_ISSET) < 0)
			{
				$this->set_error_predefined(AjaxController::ERROR_MISSING_INPUT_DATA);
				return;
			}

			// check structure of allEvent 
			$all_event = $this->sg_post->value("allEvent");

			if(!isset($all_event['isSet']) || !\ct\is_bool_str($all_event['isSet']))
			{
				$this->set_error_predefined(AjaxController::ERROR_MISSING_INPUT_DATA);
				return;
			}

			// check whether the user only used filters he was authorized to use
			if(!$this->only_authorized_filters())
			{
				$this->set_error_predefined(AjaxController::ERROR_ACCESS_PROFESSOR_REQUIRED);
				return;
			}
			
			// create datetime filter with start and end date
			if(!$this->extract_datetime_filter())
				return;

			// set the filters if necessary
			if($all_event['isSet'] !== "true") 
			{
				// filters keys
				$filter_keys = array("courses", "eventTypes", "eventCategories", "pathways", "professors");

				// extract filters
				foreach($filter_keys as $key)
					if(!$this->extract_filter_from_query($key))
						return;
			}
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
				$this->set_error_predefined(AjaxController::ERROR_DATE_FORMAT);
				return false;
			}

			return true;
		}

		/**
		 * @brief Extract the filter corresponding to the word from the query 
		 * @param[in] string $key The key in the input data array corresponding to the filter to extract
		 * @retval bool True if the query was successfully processed (added or not), false on error
		 * @note If an error occurs, then the returned error field is set with the appropriate error
		 * @note The key must exist in the input data array
		 */
		private function extract_filter_from_query($key)
		{
			$query_entry = $this->sg_post->value($key);

			if(isset($query_entry['isSet']) && $query_entry['isSet'] !== "true")
				return true;

			// check validity of the field
			if(!$this->check_filter_field($key))
				return false;

			// convert the string ids to actual integers
			if($key !== "eventType")
				$ids = array_map("intval", $query_entry['id']);

			switch ($key) {
				case "courses": 
					array_push($this->filters, new GlobalEventFilter($ids));
					break;

			   	case "eventTypes": 

					if(!empty($query_entry['timeType']))
					{
						$ids = array_map("intval", $query_entry['timeType']);
						// build the mask
						$ids = array_reduce($ids, "\ct\bitwise_or", 0);
						array_push($this->filters, new TimeTypeFilter($ids));
					}

					if(!empty($query_entry['eventType']))
					{
						$ids = array_map("intval", $query_entry['eventType']);
						// build the mask
						$ids = array_reduce($ids, "\ct\bitwise_or", 0);
						array_push($this->filters, new EventTypeFilter($ids));
					}

			   		break;

			   	case "eventCategories":
			   		array_push($this->filters, new EventCategoryFilter($ids));
			   		break;

			    case "pathways": 
			    	array_push($this->filters, new PathwayFilter($ids));
			    	break;

			    case "professors": 
			    	array_push($this->filters, new ProfessorFilter($ids));
			    	break;

				default:
					trigger_error("Filter item key is invalid", E_USER_ERROR);
					break;
			}

			return true;
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
		 * @brief Checks if the set filters are only authorized filter for the currently connected user
		 * @retval bool True if the filters used are all valid, false otherwise
		 */
		protected function only_authorized_filters()
		{
			// user cannot use the pathway filters
			return !($this->connection->user_is_student() && $this->sg_post->value("pathways")['isSet'] === "true");
		}


		/**
		 * @brief Checks whether the field input structure for the given key is valid 
		 * @retval bool True if the structure is valid, false otherwise
		 * @note The error is set if the error is not valid
		 */
		private function check_filter_field($key)
		{
			$query_entry = $this->sg_post->value($key);

			switch($key)
			{
				case "courses": 
			   	case "eventCategories":
			    case "pathways":  
			    case "professors": 
			    	
			    	if(!isset($query_entry['id']) || count($query_entry['id']) == 0)
			    	{
			    		$this->set_error_predefined(AjaxController::ERROR_ACTION_FILTER_EXTRACTION);
						return false;
			    	}

			    	break;

			    case "eventTypes": 

			    	if(!isset($query_entry['timeType'], $query_entry['eventType']) || 
			    			(count($query_entry['timeType']) === 0 && count($query_entry['eventType'] === 0)))
			    	{
			    		$this->set_error_predefined(AjaxController::ERROR_ACTION_FILTER_EXTRACTION);
			    		return false;
			    	}

			    	break;
				default:
					trigger_error("Filter item key is invalid", E_USER_ERROR);
					break;
			}

			return true;
		}
	}