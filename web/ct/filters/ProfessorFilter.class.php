<?php

	/**
	 * @file
	 * @brief Contains the ProfessorFilter class
	 */

	namespace ct\filters;

	/**
	 * @class ProfessorFilter
	 * @brief A class for filtering event according the professors
	 */
	class ProfessorFilter
	{
		private $prof_ids; /**< @brief The professor ids to keep */
		
		/**
		 * @brief Construct a ProfessorFilter object for keeping only the teachers having the given ids
		 * @param[in] array $ids An array containing the ids of the professor of which the courses must be kept
		 */
		public function __construct(array $ids)
		{
			$this->prof_ids = array_unique(array_filter($ids, "\ct\is_positive_integer", SORT_NUMERIC);
		}

		/**
		 * @brief Returns the ids of the professors to keep
		 * @retval array The ids of the professors to keep
		 */
		public function get_ids()
		{
			return $this->prof_ids;
		}
	}