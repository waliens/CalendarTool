<?php

	/** 
	 * @file
	 * @brief Contains the EventCategoryFilter class
	 */

	namespace ct\filters;

	/**
	 * @class EventCategoryFilter
	 * @brief A class for filtering events based on their categories
	 */
	class EventCategoryModel
	{
		private $ids; /**< @brief The categories' ids that the filter has to keep */

		/**
		 * @brief Constructs an EventCategoryModel object
		 * @param[in] array $ids The event category ids on which to filter the events
		 */
		public function __construct(array $ids)
		{
			$this->ids = array_unique(array_filter($ids, "\ct\is_positive_integer", SORT_NUMERIC);
		}

		/**
		 * @brief Returns the ids of the categories to keep
		 * @retval array The ids of the categories to keep
		 */
		public function get_ids()
		{
			return $this->ids;
		}
	}