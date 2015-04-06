<?php

	/**
	 * @file
	 * @brief Contains the RecurrenceFilter class
	 */

	namespace ct\models\filters;

	use util\mvc\Model;

	/**
	 * @class RecurrenceFilter
	 * @brief A class for filtering instances of events to get only one instance for the recurrent events
	 */
	class RecurrenceFilter implements EventFilter
	{
		private $exclude_non_recursive;
		private $get_closest;

		/**
		 * @brief Construct the RecurrenceFilter object 
		 * @param[in] bool $exclude_non_recursive True for fitlering (excluding) non recursive events (optionnal, default: false)
		 * @param[in] bool $get_closest			  Return the ids of the soonest events (optionnal, default: false)
		 */
		public function __construct($exclude_non_recursive = false, $get_closest = false)
		{
			$this->exclude_non_recursive = $exclude_non_recursive;
			$this->get_closest = $get_closest;
		}

		/**
		 * @copydoc EventFilter::get_sql_query
		 */
		public function get_sql_query()
		{
			if(!$this->get_closest)
				$query  =  "SELECT Id_Event FROM `event` WHERE Id_Recurrence > 1 GROUP BY Id_Recurrence";	
			else
				$query  =  "SELECT Id_Event FROM
							( SELECT Id_Event, Id_Recurrence FROM `event` WHERE Id_Recurrence > 1 ) AS recurrent2
							NATURAL JOIN
							(
								SELECT MIN(Start) mins, Id_Recurrence FROM
								( SELECT Id_Event, Id_Recurrence FROM `event` WHERE Id_Recurrence > 1 ) AS recurrent
								NATURAL JOIN 
								( 
									SELECT Id_Event, Start FROM `time_range_event` WHERE Start >= NOW()
									UNION ALL
									SELECT Id_Event, CAST(Start AS DATETIME) AS Start FROM `date_range_event` WHERE CAST(Start AS DATETIME)  >= NOW()
									UNION ALL
									SELECT Id_Event, `Limit` AS Start FROM `deadline_event` WHERE `Limit` >= NOW()
								) AS time_data
								GROUP BY Id_Recurrence
							) AS date_recur
							WHERE (Id_Event, mins) IN 
							( 
								SELECT Id_Event, Start FROM `time_range_event`
								UNION ALL
								SELECT Id_Event, CAST(Start AS DATETIME) AS Start FROM `date_range_event`
								UNION ALL
								SELECT Id_Event, `Limit` AS Start FROM `deadline_event`
							)";
			
			if(!$this->exclude_non_recursive)
				$query = "( ".$query." ) UNION ALL ( SELECT Id_Event FROM `event` WHERE Id_Recurrence = 1 )";	

			return $query;		
		}

		/**
		 * @copydoc EventFilter::get_table_alias
		 */
		public function get_table_alias()
		{
			return "f_reccurent_events";
		}
	}