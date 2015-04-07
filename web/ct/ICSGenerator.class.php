<?php

	/**
	 * @file
	 * @brief Contains the ICSGenerator class
	 */

	namespace ct;

	use \Smarty;
	use ct\models\FilterCollectionModel;

	/**
	 * @class ICSGenerator
	 * @brief A class for generating ICS calendar files with a collection of filters
	 */
	class ICSGenerator
	{
		private $smarty; /**< @brief The smarty object for generating ICS */
		private $filter_collection; /**< @brief A filter collection object */

		const PROD_ID = "ULg//Montefiore//PROJ0010//Group2//CalendarTool"; /**< @brief ICS Calendar product id */

		/**
		 * @brief Construct an ICSGenerator object
		 * @param[in] FilterCollectionModel A filter collection object
		 */
		public function __construct(FilterCollectionModel $filter_collection)
		{
			$this->smarty = new Smarty();
			$this->smarty->setTemplateDir("views/tpl/export");
			$this->smarty->setCompileDir("views/compiled/export");

			$this->filter_collection = $filter_collection;
		}

		/**
		 * @brief Generate a ICS calendar string from the stored events
		 * @retval string The ICS string
		 */
		public function get_ics()
		{
			$events = $this->filter_collection->get_events();
			$formatted_events = array(); 

			// format the event's data for the ics file
			foreach($events as &$event)
			{
				$ics_event = array();
				
				$ics_event['uid'] = $event['Id_Event']."_".$event['Id_Event']."@calendartool.ulg.ac.be";
				$ics_event['dtstamp'] = self::get_ics_date(date("Y-m-d H:i:s"));
				$ics_event['dtstart'] = self::get_ics_date($event['Start']);
				$ics_event['summary'] = self::get_ics_txt($event['Name']);

				if(!empty($event['End']))
					$ics_event['dtend'] = self::get_ics_date($event['End']);

				$ics_event['location'] = self::get_ics_txt($event['Place']);
				$ics_event['description'] = self::get_ics_txt($event['Description']);
				$ics_event['categories'] = self::get_ics_txt($event['Categ_Name_FR']);

				$formatted_events[] = $ics_event;
			}

			$this->smarty->assign("events", $formatted_events);
			$this->smarty->assign("prod_id", self::PROD_ID);

			return $this->smarty->fetch("ics.tpl");
		}


		/**
		 * @brief Return the given date in the ICS format (YYYYMMDD "T" HHMMSS)
		 * @param[in] string $date The date in SQL datetime/date format
		 * @retval string The date in the ics format
		 */
		private static function get_ics_date($date)
		{
			$matches = array();

			// workaround for transforming any date/datetime string into a datetime one
			$date = date("Y-m-d H:i:s", strtotime($date));

			if(!preg_match("#^([0-9]{4})[/-]([0-9]{2})[/-]([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})$#", $date, $matches))
				return false;

			return $matches[1].$matches[2].$matches[3]."T".$matches[4].$matches[5].$matches[6];
		}

		/**
		 * @brief Format a text string so that it can be used in an ICS file
		 * @param[in] string $text The text to process
		 * @retval string The processed text string 
		 */
		private static function get_ics_txt($text)
		{
			/**
			 * The following char must be escaped : '\', ';', ',' 
			 * The newline character should be represented as linefeed '\n'
			 */
			$rep_map = array("#\\n?\\r#" => "\n", 
							  "#;#"    => "\\;", 
							  "#,#"    => "\\,",
							  "#\\\\#"   => "\\\\");

			return preg_replace(array_keys($rep_map), array_values($rep_map), $text);
		}
	}