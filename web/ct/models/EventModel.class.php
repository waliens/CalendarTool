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
		 * @param[in] int   $lock_mode	 One of the Model LOCKMODE_* class constant 
		 * @retval bool True if the event exists, false otherwise
		 * @note A read lock on the event table might be acquired (according to the lock_mode) 
		 */
		public function event_exists($event_id, $lock_mode=Model::LOCKMODE_NO_LOCK)
		{
			if($this->do_lock())
			$ret = $this->sql->count("event", "Id_Event = ".$this->sql->quote($event_id)) > 0;
		}

		/**
		 * @brief Returns the temporal data of the given event
		 * @param[in] int $event_id The identifier of the event
		 * @param[in] int   $lock_mode	 One of the Model LOCKMODE_* class constant 
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
		public function get_event_temporal_data($event_id, $lock_mode=Model::LOCKMODE_NO_LOCK)
		{
			if($this->do_lock($lock_mode))
				$this->sql->lock(array("time_range_event READ", "date_range_event READ", "deadline_event READ"));

			$query  =  "SELECT Start, End, 'time_range' AS Type FROM `time_range_event` WHERE Id_Event = ? 
						UNION ALL
						SELECT Start, End, 'date_range' AS Type FROM `date_range_event` WHERE Id_Event = ?
						UNION ALL
						SELECT '' AS Start, `Limit` AS End, 'deadline' AS Type FROM `deadline_event` WHERE Id_Event = ?";

		 	$event = $this->execute_query($query, array($event_id, $event_id, $event_id));

		 	if($this->do_unlock())
				$this->sql->unlock();

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

		/**
		 * @brief Change the type of the given event. The new type will be a 
		 * @param[in] int $event The event id
		 * @param[in] string $datetime The start datetime
		 * @retval True on success, false on error
		 */
		public function reset_time_type_deadline($event, $datetime)
		{
			$success = $this->delete_time_type($event);

			// insert the new deadline data
			$insert_date = array("Id_Event" => $target['event'], "Limit" => $target['proposition']);
			$success &= $this->sql->insert("deadline_event", $this->quote_all($insert_date));
		}

		/**
		 * @brief Change the type of the given event. The new type will be a 
		 * @param[in] int $event The event id
		 * @param[in] string $start The start datetime
		 * @param[in] string $end The end datetime
		 * @retval True on success, false on error
		 */
		public function reset_time_type_time_range($event, $start, $end)
		{
			$success = $this->delete_time_type($event);
			
			// insert the new time_range data
			$insert_date = array("Id_Event" => $target['event'], "Start" => $start, "End" => $end);
			$success &= $this->sql->insert("time_range_event", $this->quote_all($insert_date));
		}

		/**
		 * @brief Change the type of the given event. The new type will be a 
		 * @param[in] int $event The event id
		 * @param[in] string $start The start date
		 * @param[in] string $end The end date
		 * @retval True on success, false on error
		 */
		public function reset_time_type_date_range($event, $start, $end)
		{
			$success = $this->delete_time_type($event);
			
			// insert the new date_range event data
			$insert_date = array("Id_Event" => $target['event'], "Start" => $start, "End" => $end);
			$success &= $this->sql->insert("date_range_event", $this->quote_all($insert_date));
		}

		/**
		 * @brief Delete the event temporal type of the given event
		 * @param[in] int $event The event id
		 * @retval bool True on success, false on error
		 */
		private function delete_time_type($event)
		{
			$quoted_event = $this->sql->quote($event);
			$success = $this->sql->delete("time_range_event", "Id_Event = ".$quoted_event);
			$success &= $this->sql->delete("date_range_event", "Id_Event = ".$quoted_event);
			$success &= $this->sql->delete("deadline_event", "Id_Event = ".$quoted_event);
			return $success;
		}
	}