<?php

	/**
	 * @file
	 * @brief Contains the DateTimeFilter class
	 */

	namespace ct\filters;

	/**
	 * @class DateTimeFilter
	 * @brief A class for filtering base on time intervals
	 */
	class DateTimeFilter
	{
		private $start; /**< @brief Contains the datetime (SQL format) */
		private $end; /**< @brief Contains the end datetime (SQL format) when the mode is self::MODE_BETWEEN, null otherwise */
		private $mode; /**< @brief Contains the mode of selection (one of the MODE_* class constant) */

		const MODE_BEFORE = 0; /**< @brief The selection mode : before a given date */
		const MODE_AFTER = 1; /**< @brief The selection mode : after a given date */
		const MODE_BETWEEN = 2; /**< @brief The selection mode : between two dates */

		/** 
		 * @brief Construct a DateTimeFilter 
		 * @param[in] string $start A datetime in the french format
		 * @param[in] string $end   A datetime in the french format (optional)
		 * @param[in] int    $mode  The selection mode (optionnal, default: see notes)
		 * @note The $mode is set to self::MODE_BETWEEN if an end date is given. If only a start 
		 * date is given, then the default mode is self::MODE_AFTER and cannot be self::MODE_BETWEEN 
		 */
		public function __construct($start, $end=null, $mode=null)
		{
			if($mode == null) $mode = ($end == null ? self::MODE_AFTER : self::MODE_BETWEEN);

			$this->start = \ct\date_fr2sql($start);
			$this->end   = ($end == null) ? null : \ct\date_fr2sql($end);

			// check date formatting
			if($this->start === false || $this->end === false)
				throw new \Exception("Date mal formattée");

			// check if start is before end if necessary
			if($this->end !== null && \ct\date_cmp($this->start, $this->end) >= 0)
				throw new \Exception("La date 'start' doit précéder la date 'end'");

			if(!$this->valid_mode($mode))
				throw new \Exception("Mode invalide");

			$this->mode = $mode;
		}

		/**
		 * @brief Check if the given mode is valid based on the start and end datetime
		 * @param[in] int $mode The mode to check
		 * @retval bool True if the mode is valid, false otherwise
		 */
		public function valid_mode($mode)
		{
			return ($mode >= 0 && $mode <= 2)
					&& ($mode === self::MODE_BETWEEN && $this->end !== null); 
		}

		/**
		 * @brief Check the mode of the filter
		 * @retval bool True if the filter mode is MODE_AFTER, false otherwise 
		 */
		public function is_after()
		{
			return $this->mode === self::MODE_AFTER;
		}

		/**
		 * @brief Check the mode of the filter
		 * @retval bool True if the filter mode is MODE_BEFORE, false otherwise 
		 */
		public function is_before()
		{
			return $this->mode === self::MODE_BEFORE;
		}

		/**
		 * @brief Check the mode of the filter
		 * @retval bool True if the filter mode is MODE_BETWEEN, false otherwise 
		 */
		public function is_between()
		{
			return $this->mode === self::MODE_BETWEEN;
		}

		/**
		 * @brief Return the start datetime
		 * @retval string The datetime in the SQL format
		 */
		public function get_start()
		{
			return $this->start;
		}

		/**
		 * @brief Return the end datetime
		 * @retval string The datetime in the SQL format
		 */
		public function get_end()
		{
			return $this->end;
		}
	}