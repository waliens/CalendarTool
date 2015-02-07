<?php

	/**
	 * @file 
	 * @brief Contains the EventModel.class.php
	 */

	namespace ct\models;

	use util\mvc\Model;

	/**
	 * @class EventModel
	 * @brief A class for handling event related database queries
	 */
	class EventModel extends Model
	{
		const TEMP_DEADLINE = 1; /**< @brief Constant identifying the temporal type of event : deadline event */
		const TEMP_TIME_RANGE = 2; /**< @brief Constant identifying the temporal type of event : time range event */
		const TEMP_DATE_RANGE = 3; /**< @brief Constant identifying the temporal type of event : date range event */

		/**
		 * @brief Construct the EventModel object
		 */
		public function __construct()
		{
			parent::__construct();
		}

		/**
		 * @brief Checks whether the event having the given id is an academic event or not
		 * @param[in] int $event_id The event id
		 * @retval bool True if the event is an academic event, false otherwise
		 */
		public function is_academic_event($event_id)
		{
			$ret = $this->sql->execute_query("SELECT event_is_academic(?) AS ret;", array($event_id));
			return !!$ret[0]['ret'];
		}

		/**
		 * @brief Checks whether the event having the given id is a private event or not
		 * @param[in] int $event_id The event id
		 * @retval bool True if the event is a private event, false otherwise
		 */
		public function is_private_event($event_id)
		{
			$ret = $this->sql->execute_query("SELECT event_is_student(?) AS ret;", array($event_id));
			return !!$ret[0]['ret'];
		}

		/**
		 * @brief Checks whether the event having the given id is a subevent or not
		 * @param[in] int $event_id The event id
		 * @retval bool True if the event is a subevent, false otherwise
		 */
		public function is_sub_event($event_id)
		{
			$ret = $this->sql->execute_query("SELECT event_is_sub_event(?) AS ret;", array($event_id));
			return !!$ret[0]['ret'];
		}

		/**
		 * @brief Checks whether the event having the given id is an independent event or not
		 * @param[in] int $event_id The event id
		 * @retval bool True if the event is an independent event, false otherwise
		 */
		public function is_independent_event($event_id)
		{
			$ret = $this->sql->execute_query("SELECT event_is_independent(?) AS ret;", array($event_id));
			return !!$ret[0]['ret'];
		}

		/**
		 * @brief Checks whether the event having the given id is a deadline event or not
		 * @param[in] int $event_id The event id
		 * @retval bool True if the event is a deadline event, false otherwise
		 */
		public function is_deadline_event($event_id)
		{
			$ret = $this->sql->execute_query("SELECT event_is_deadline(?) AS ret;", array($event_id));
			return !!$ret[0]['ret'];
		}

		/**
		 * @brief Checks whether the event having the given id is a time range event or not
		 * @param[in] int $event_id The event id
		 * @retval bool True if the event is a time range event, false otherwise
		 */
		public function is_time_range_event($event_id)
		{
			$ret = $this->sql->execute_query("SELECT event_is_time_range(?) AS ret;", array($event_id));
			return !!$ret[0]['ret'];
		}

		/**
		 * @brief Checks whether the event having the given id is a date range event or not
		 * @param[in] int $event_id The event id
		 * @retval bool True if the event is a date range event, false otherwise
		 */
		public function is_date_range_event($event_id)
		{
			$ret = $this->sql->execute_query("SELECT event_is_date_range(?) AS ret;", array($event_id));
			return !!$ret[0]['ret'];
		}

		/** 
		 * @brief Checks if the given event exists
		 * @param[in] int $event_id The event id
		 * @retval bool True if the event exists, false otherwise
		 */
		public function event_exists($event_id)
		{
			return $this->sql->count("event", "Id_Event = ".$this->sql->quote($event_id)) > 0;
		}

		/**
		 * @brief Returns the temporal data of the given event
		 * @param[in] int $event_id The identifier of the event
		 * @retval array Array containing the temporal data (empty array means that the event does not exist)
		 * @note The array contains a field 'Type' of which the value is one of the TEMP_* class constant. This 
		 * field identify the temporal type of the event
		 * @note This function accesses the tables time_range_event, date_range_event and deadline_event 
		 * @note According to the type the event is structured as follows :
		 * <ul>
		 *  <li>deadline : array('End' => datetime, 'Type' => ...)</li>
		 *  <li>time_range : array('Start' => datetime, 'End' => datetime, 'Type' => ...)</li>
		 *  <li>date_range : array('Start' => date, 'End' => date, 'Type' => ...)</li>
		 * </ul>
		 */
		public function get_event_temporal_data($event_id)
		{
			$query  =  "SELECT Start, End, 'time_range' AS Type FROM `time_range_event` WHERE Id_Event = ? 
						UNION ALL
						SELECT Start, End, 'date_range' AS Type FROM `date_range_event` WHERE Id_Event = ?
						UNION ALL
						SELECT '' AS Start, `Limit` AS End, 'deadline' AS Type FROM `deadline_event` WHERE Id_Event = ?";

		 	$event = $this->execute_query($query, array($event_id, $event_id, $event_id));

		 	if(empty($event))
		 		return array();

		 	$event = $event[0];

		 	if($event['Type'] === "time_range_event")
		 		$event['Type'] = self::TEMP_TIME_RANGE;
		 	elseif($event['Type'] === "date_range_event")
		 		$event['Type'] = self::TEMP_DATE_RANGE;
		 	else
		 	{
		 		$event['Type'] = self::TEMP_DEADLINE;
		 		unset($event['Start']);
		 	}

		 	return $event;
		}
	}