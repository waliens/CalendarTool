<?php

	/**
	 * @file
	 * @brief Contains the FilterCollectionModel class
	 */

	namespace ct\models;

	use util\mvc\Model;
	use ct\models\filters\EventFilter;
	use ct\models\filters\AccessFilter;
	use ct\models\events\EventModel;

	/**
	 * @class FilterCollectionModel
	 * @brief A class that contains a collection of event filters and that can retrieve from the db
	 * the filtered events from the set of filters that it contains. 
	 */
	class FilterCollectionModel extends Model
	{
		private $association_mode; /**< @brief The filter association mode (one of the MODE_* class constant) */
		private $filters; /**< @brief Array of filters (contain at most one filter of a given type). 
									The filters are mapped by their class name*/
		private $event_mod; /**< @brief The event model */
		private $access_filter; /**< @brief An access filter, null if no filtering linked to the access should be done */

		const MODE_OR = "OR"; /**< @brief One of the filter association : disjunction */
		const MODE_AND = "AND"; /**< @brief One of the filter association : conjunction */

		/**
		 * @brief Construct a FilterCollectionModel object
		 * @param[in] string $association_mode The association mode to apply between the 
		 * filter (one of the MODE_* class constant) (optionnal, default: MODE_AND)
		 * @throws Exception bad association mod
		 */
		public function __construct($association_mode=null)
		{
			parent::__construct();
			
			if($association_mode == null) 
				$association_mode = self::MODE_AND;
			elseif(!$this->valid_association_mode($association_mode))
				throw new \Exception("Bad association mode");

			$this->association_mode = $association_mode;
			$this->filters = array();
			$this->event_mod = new EventModel();
			$this->access_filter = null;
			//$this->sql->set_dump_mode();
		}

		/**
		 * @brief Checks whether the given association mode is valid
		 * @param[in] string The mode to check
		 * @retval bool True if the mode is valid, false otherwise
		 */
		private function valid_association_mode($mode)
		{
			return $mode === FilterCollectionModel::MODE_AND 
					|| $mode == FilterCollectionModel::MODE_OR;
		}

		/** 
		 * @brief Reset the filter collection : all the added filters are removed 
		 */
		public function reset()
		{
			$this->filters = array();
			$this->access_filter = null;
		}

		/**
		 * @brief Count the number of filters registered in the collection
		 * @retval int The number of filters registered in the collection
		 */
		public function count()
		{
			return count($this->filters);
		}

		/**
		 * @brief Check whether the collection is empty
		 * @retval bool True if the collection is empty, false otherwise
		 */
		public function is_empty()
		{
			return $this->count() === 0;
		}

		/**
		 * @brief Change the association mode of the filter collection to the given mode
		 * @param[in] string $mode The new association mode 
		 */
		public function set_association_mode($mode)
		{
			if(!$this->valid_association_mode($association_mode))
				throw new \Exception("Bad association mode");

			$this->association_mode = $mode;
		}

		/**
		 * @brief Checks whether the filters collection is in the disjunctive mode
		 * @retval bool True if the collection is in the disjunctive mode, false otherwise 
		 */
		public function is_disjunctive()
		{
			return $this->association_mode === self::MODE_OR;
		}

		/**
		 * @brief Checks whether the filters collection is in the conjunctive mode
		 * @retval bool True if the collection is in the conjunctive mode, false otherwise 
		 */
		public function is_conjunctive()
		{
			return $this->association_mode === self::MODE_AND;
		}

		/**
		 * @brief Add filter to the object
		 * @param[in] EventFilter $filter The filter
		 * @note If a filter of the given type was already added, it is discarded
		 */
		public function add_filter(EventFilter $filter)
		{
			$this->filters[get_class($filter)] = $filter;
		}

		/**
		 * @brief Add the filters from the array into the collection
		 * @param[in] array $filters The filters to add
		 */
		public function add_filters(array $filters)
		{
			foreach ($filters as $filter)
				$this->add_filter($filter);
		}

		/**
		 * @brief Add an AccessFilter to the collection
		 * @param AccessFilter $filter An access filter object
		 * @note This filter will be used whatever the MODE_* of the object to exclude the events
		 * that currently connected user shouldn't be getting access to 
		 */
		public function add_access_filter(AccessFilter $filter)
		{
			$this->access_filter = $filter;
		}

		/**
		 * @brief Get from the database the selected event for the given set of filters
		 * @retval array|bool Plain array containing the selected events' ids, false on error 
		 */
		public function get_filtered_events_ids()
		{
			return $this->sql->execute_query($this->get_filters_query());
		}

		/**
		 * @brief Get the query for getting the selected event ids from the database according to
		 * filters and the association mode
		 */
		private function get_filters_query()
		{
			switch ($this->association_mode)
			{
			case self::MODE_AND:
				// function for mapping a filter to an aliased sql query : ( filter_query ) AS table_alias 
				$fn = function(EventFilter $filter) 
					  { return "( ".$filter->get_sql_query()." ) AS ".$filter->get_table_alias(); };

				// check whether the access filter must be applied
				if($this->access_filter == null)
					$filter_arr = $this->filters;
				else
					$filter_arr = array_merge($this->filters, array($this->access_filter));

				$filter_queries = array_map($fn, $filter_arr);

				return "SELECT * FROM ".implode(" NATURAL JOIN ", $filter_queries);
			case self::MODE_OR:
				// function for mapping a filter to an sql query into parenthesis : ( filter_quer)
				$fn = function(EventFilter $filter)
					  { return "( ".$filter->get_sql_query()." )"; };
				$filter_queries = array_map($fn, $this->filters);

				$query = "SELECT * FROM ( ".implode(" UNION ")." )";
				
				// check if the access filter must applied
				if($this->access_filter == null)
					return $query;

				$access = " NATURAL JOIN ( ".$this->get_sql_query()." ) ".$this->access_filter->get_table_alias();

				return $query.$access;

			default:
				trigger_error("Bad association mode", E_USER_ERROR);
			}
		}

		/**
		 * @brief Return the ids of the event filtered by the set of filters
		 * @retval array An array of integers containing the ids 
		 */
		public function get_event_ids()
		{
			$ids = $this->sql->execute_query($this->get_filters_query());
			return \ct\array_flatten($ids);
		}

		/**
		 * @brief Return the filtered event data
		 * @retval array A multidimensionnal array containing the event data
		 * The rows contains the following keys : 
		 * <ul>
		 *   <li> Id_Event : id of the event </li>
		 *   <li> Name : event name </li>
		 *   <li> Description : event description </li>
		 *   <li> Place : location where the event take place (or NULL) </li>
		 *   <li> Start : start date/datetime (for deadline events, this field contains the limit datetime) </li>
		 *   <li> End : end date/datetime (for deadline events, this field contains an empty string) </li>
		 *   <li> DateType : a string specifying the date type of the event ('time_range', 'date_range' or 'deadline') </li>
		 *   <li> EventType : a string specifying the event type ('sub_event', 'indep_event' or 'student_event') </li>
		 *   <li> Color : event category color </li>
		 *   <li> Categ_Name_EN : the event category name in english </li>
		 *   <li> Categ_Name_FR : the event category name in french </li>
		 *   <li> Categ_Desc_EN : the event category description in english </li>
		 *   <li> Categ_Desc_FR : the event category description in french </li>
		 *   <li> Recur_Category_EN : the recurrence category name in english </li>
		 *   <li> Recur_Category_FR : the recurrence category name in french </li>
		 *   <li> Id_Recur_Category : the id of the recurrence category </li>
		 *   <li> Id_Recurrence : the recurrence id of the event (1 for never) </li>
		 *   <li> Id_Category : the event category id </li>
		 * </ul>
		 */
		public function get_events()
		{
			return $this->event_mod->getEventFromIds($this->get_event_ids());
		}
	}