<?php

	/**
	 * @file
	 * @brief Contains the ICSModel class
	 */

	namespace ct\models;

	use util\mvc\Model;

	/**
	 * @class ICSModel
	 * @brief A class for generating ICS calendars for a set of events
	 */
	class ICSModel extends Model
	{
		private $smarty; /**< @brief The smarty object for generating ICS */
		private $events; /**< @brief Array containing the events */

		/**
		 * @brief Construct an ICSModel object
		 */
		public function __construct()
		{
			parent::__construct();

			$this->smarty = new Smarty();
			$this->smarty->setTemplateDir("views/tpl/export");
			$this->smarty->setCompileDir("views/compiled/export");

			$this->events = array();
		}

		/**
		 * @brief Add some events to add into the final ics file
		 * @param[in] array $events An array of integers being event ids
		 */
		public function add_events(array $events)
		{
			$this->events = array_unique(array_merge($this->events, $events));
		}

		/**
		 * @brief Generate a ICS calendar string from the stored events
		 * @retval string The ICS content
		 */
		public function get_ics()
		{
			$events = array(); // get events;

			foreach(array() as $event)
			{
				$ics_event = ;
			}
		}

		/**
		 * @brief Return the date 
		 * @param[in] string $date The date in french format
		 * @retval string The ics date (YYYYMMDD "T" HHMMSS)
		 */
		private function get_ics_date($date)
		{
			$matches = array();

			if(!preg_match("#^([0-9]{2})[/-]([0-9]{2})[/-]([0-9]{4}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$#", $date, $matches))
				return false;

			return $matches[3].$matches[2].$matches[1]."T".$matches[4].$matches[5].$matches[6];
		}
	}