<?php

	/**
	 * @file
	 * @brief Contains the PathwayFilter class
	 */

	namespace ct\filters;

	use ct\models\PathwayModel;

	/**
	 * @class PathwayFilter
	 * @brief A class for filtering events for some pathways
	 */
	class PathwayFilter implements EventFilter
	{
		private $pathways; /**< @brief The list of pathways to keep (array of strings) */

		/**
		 * @brief Construct a PathwayFilter object with a set of pathways to keep
		 * @param array Array containing the ids of the pathways
		 */
		public function __construct(array $pathways)
		{
			$filter_fn = function($pathway) { return PathwayModel::valid_pathway($pathway); };
			$filtered_pathways = array_filter($pathways, $filter_fn);
			$this->pathways = array_unique($filtered_pathways);
		}

		/**
		 * @brief Returns the list of pathways to keep
		 * @retval array The pathway array
		 */
		public function get_pathways()
		{
			return $this->pathways;
		}
		
		/**
		 * @copydoc EventFilter::get_sql_query
		 * @todo find an appropriate quoting mechanism
		 */
		public function get_sql_query()
		{
			$q_pathways = quote_all($this->pathways);
			return "SELECT DISTINCT Id_Event FROM academic_event_pathway WHERE Id_Pathway IN (".implode(", ", $q_pathways).")"; 
		}

		/**
		 * @copydoc EventFilter::get_table_alias
		 */
		public function get_table_alias()
		{
			return "f_pathway_events";
		}
	}