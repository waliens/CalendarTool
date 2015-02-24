<?php

	/**
	 * @file
 	 * @brief Contains the EventTypeFilter class
	 */

	namespace ct\models\filters;

	/**
	 * @class EventTypeFilter
	 * @brief A class for filtering event according to their type (student, academic,...) 
	 */
	class EventTypeFilter implements EventFilter
	{
		public $types; /**< @brief The mask containing the event type to keep */

		const TYPE_SUB_EVENT = 0x1;/**< @brief The type to keep : only subevents */
		const TYPE_INDEPENDENT = 0x2;/**< @brief The type to keep : only independent event */
		const TYPE_ACADEMIC = 0x3;/**< @brief The type to keep : only academic event */
		const TYPE_STUDENT = 0x4;/**< @brief The type to keep : only student event */
		const TYPE_ALL = 0x7;/**< @brief The type to keep : all types of events */

		/**
	 	 * @brief Constructs a EventTypeFilter object for filtering the given types of event
	 	 * @param[in] int $types A combination of TYPE_* masks indicating the type of events to keep
	 	 * (for instance : EventTypeFilter::SUB_EVENT | EventTypeFilter::TYPE_STUDENT)
 	 	 */
		public function __construct(array $types)
		{
			if(!$this->valid_mask($types))
				throw new \Exception("Invalid type mask");
			$this->types = $types;
		}

		/**
		 * @brief Check whether the given mask is valid
		 * @param[in] int $mask The mask 
		 */
		public function valid_mask($mask)
		{
			return $mask > 0 && $mask <= 7;
		}

		/** 
		 * @brief Checks whether the given event type must be kept
		 * @param[in] int $type The type to check (one of the TYPE_* class constant)
		 * @retval bool True if the given type must be kept, false otherwise
		 */
		public function do_keep($type)
		{
			return $this->valid_mask($type) && ($this->types === $type);
		}

		/** 
		 * @brief Checks whether the given event type must be excluded
		 * @param[in] int $type The type to check (one of the TYPE_* class constant)
		 * @retval bool True if the given type must be excluded, false otherwise
		 */
		public function do_exclude($type)
		{
			return !$this->do_keep($type) && !($this->types & $type);
		}
		
		/**
		 * @copydoc EventFilter::get_sql_query
		 */
		public function get_sql_query()
		{
			$queries = array();

			if($this->type === self::TYPE_ALL)
				trigger_error("The event type filter should only be used if it has a category of event to filter (thus other than TYPE_ALL)", E_USER_WARNING);

			if($this->do_keep(self::TYPE_ACADEMIC))
				$queries[] = "( SELECT Id_Event FROM academic_event)";
			else
			{
				if($this->do_keep(self::TYPE_SUB_EVENT))
					$queries[] = "( SELECT Id_Event FROM sub_event ) ";
				if($this->do_keep(self::TYPE_INDEPENDENT))
					$queries[] = "( SELECT Id_Event FROM independent_event ) ";
			}

			if($this->do_keep(self::TYPE_STUDENT))
				$queries[] = "( SELECT Id_Event FROM student_event ) ";

			return implode("\nUNION\n", $queries);
 		}

		/**
		 * @copydoc EventFilter::get_table_alias
		 */
		public function get_table_alias()
		{
			return "f_type_events";
		}
	}