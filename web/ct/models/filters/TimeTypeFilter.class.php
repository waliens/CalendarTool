<?php

	/**
	 * @file
	 * @brief Contains the TimeTypeFilter class
	 */

	namespace ct\models\filters;

	use util\database\Database;
	use util\database\SQLAbstract_PDO;
	
	/**
	 * @class TimeTypeFilter
	 * @brief A class for filtering base on time intervals
	 */
	class TimeTypeFilter implements EventFilter
	{
		private $types; /**< @brief The types of events to keep */

		const TYPE_DEADLINE = 1; /**< @brief The event time type : the deadline type */
		const TYPE_DATE_RANGE = 2; /**< @brief The event time type : the date range type */
		const TYPE_TIME_RANGE = 4; /**< @brief The event time type : the time range type */
		const TYPE_ALL = 7; /**< @brief The event time type : all event types*/

		/** 
		 * @brief Construct a TimeTypeFilter 
		 * @param[in] string $types The flag association specifying the event time types 
		 */
		public function __construct($types)
		{
			if(!$this->valid_types($types))
				throw new \Exception("Bad type");

			$this->types = $types;
		}

		/**
		 * @brief Check if the given type is valid
		 * @param[in] int $types The types to check
		 * @retval bool True if the types is valid, false otherwise
		 */
		public function valid_types($types)
		{
			return $types > 0 && $types <= 7;
		}

		/**
		 * @brief Check if the type time range must be kept
		 * @retval bool True if the type time range must be kept, false otherwise
		 */
		public function keep_time_range()
		{
			return $this->types & self::TYPE_TIME_RANGE;
		}

		/**
		 * @brief Check if the type date range must be kept
		 * @retval bool True if the type date range must be kept, false otherwise
		 */
		public function keep_date_range()
		{
			return $this->types & self::TYPE_DATE_RANGE;
		}

		/**
		 * @brief Check if the type dealine must be kept
		 * @retval bool True if the type deadline must be kept, false otherwise
		 */
		public function keep_deadline()
		{
			return $this->types & self::TYPE_DEADLINE;
		}

		/**
		 * @copydoc EventFilter::get_sql_query
		 */
		public function get_sql_query()
		{
			$queries = array();

			if($this->keep_time_range())
				$queries[] = "( SELECT Id_Event FROM time_range_event )";

			if($this->keep_date_range())
				$queries[] = "( SELECT Id_Event FROM date_range_event )";

			if($this->keep_deadline())
				$queries[] = "( SELECT Id_Event FROM deadline_event )";
			
			return implode(" UNION ", $queries);
		}

		/**
		 * @copydoc EventFilter::get_table_alias
		 */
		public function get_table_alias()
		{
			return "f_timetype_events";
		}
	}