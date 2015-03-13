<?php

	/** 
	 * @file
	 * @brief Contains the EventCategoryFilter class
	 */

	namespace ct\models\filters;

	require_once("functions.php");

	/**
	 * @class EventCategoryFilter
	 * @brief A class for filtering events based on their categories
	 */
	class EventCategoryFilter implements EventFilter
	{
		private $ids; /**< @brief The categories' ids that the filter has to keep */

		/**
		 * @brief Constructs an EventCategoryModel object
		 * @param[in] array $ids The event category ids on which to filter the events
		 */
		public function __construct(array $ids)
		{
			$this->ids = array_unique(array_filter($ids, "\ct\is_valid_id"), SORT_NUMERIC);
		}

		/**
		 * @brief Returns the ids of the categories to keep
		 * @retval array The ids of the categories to keep
		 */
		public function get_ids()
		{
			return $this->ids;
		}

		/**
		 * @copydoc EventFilter::get_sql_query
		 */
		public function get_sql_query()
		{
			return "SELECT Id_Event FROM event WHERE Id_Category IN (".implode(", ", $this->ids).")";
		}

		/**
		 * @copydoc EventFilter::get_table_alias
		 */
		public function get_table_alias()
		{
			return "f_categ_events";
		}
	}