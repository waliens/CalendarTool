<?php

	/**
	 * @file
	 * @brief Contains the ProfessorFilter class
	 */

	namespace ct\models\filters;

	use \ct\models\events\GlobalEventModel;

	/**
	 * @class ProfessorFilter
	 * @brief A class for filtering event according the professors
	 */
	class ProfessorFilter implements EventFilter
	{
		private $prof_ids; /**< @brief The professor ids to keep (array of integers) */
		
		/**
		 * @brief Construct a ProfessorFilter object for keeping only the teachers having the given ids
		 * @param[in] array $ids An array containing the ids of the professor of which the courses must be kept
		 */
		public function __construct(array $ids)
		{
			$this->prof_ids = array_unique(array_filter($ids, "\ct\is_positive_integer"), SORT_NUMERIC);
		}

		/**
		 * @brief Returns the ids of the professors to keep
		 * @retval array The ids of the professors to keep
		 */
		public function get_ids()
		{
			return $this->prof_ids;
		}
		
		/**
		 * @copydoc EventFilter::get_sql_query
		 */
		public function get_sql_query()
		{
			$role_id = GlobalEventModel::ROLE_ID_PROFESSOR;
			$ids_str = implode(", ", $this->prof_ids);(".$ids_str.");
			$where_clause = "Id_Role = ".$role_id." AND Id_User IN (".$ids_str.")";

			return "( SELECT Id_Event FROM sub_event NATURAL JOIN
					  ( SELECT Id_Global_Event FROM teaching_team_member WHERE ".$where_clause.") AS glob_events )
					UNION ALL
					( SELECT Id_Event FROM independent_event NATURAL JOIN
					  ( SELECT Id_Event FROM independent_event_manager WHERE ".$where_clause.") AS indep_event )";
		}

		/**
		 * @copydoc EventFilter::get_table_alias
		 */
		public function get_table_alias()
		{
			return "f_prof_events";
		}
	}