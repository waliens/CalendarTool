<?php

	/**
	 * @file
	 * @brief Contains the FilterCollectionModel class
	 */

	namespace ct\models;

	use util\mvc\Model;
	use ct\models\filters\EventFilter;

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

		const MODE_OR = "OR"; /**< @brief One of the filter association : disjunction */
 		const MODE_AND = "AND"; /**< @brief One of the filter association : conjunction */

		/**
		 * @brief Construct a FilterCollectionModel object
		 * @param[in] string $association_mode The association mode to apply between the 
		 * filter (one of the MODE_* class constant) (optionnal, default: MODE_OR)
		 * @throws Exception bad association mod
		 */
		public function __construct($association_mode=null)
		{
			parent::__construct();

			if($association_mode == null) 
				$association_mode = self::MODE_OR;
			elseif(!$this->valid_association_mode($association_mode))
				throw new \Exception("Bad association mode");

			$this->association_mode = $association_mode;
			$this->filters = array();
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
				$filter_queries = array_map($fn, $this->filters);
				return "SELECT * FROM ".implode("\nNATURAL JOIN\n", $filter_queries);
			case self::MODE_OR:
				// function for mapping a filter to an sql query into parenthesis : ( filter_quer)
				$fn = function(EventFilter $filter)
					  { return "( ".$filter->get_sql_query()." )"; };
				$filter_queries = array_map($fn, $this->filters);
				return "SELECT * FROM ( ".implode("\nUNION\n")." )";
			default:
				trigger_error("Bad association mode", E_USER_ERROR);
			}
		}

	}