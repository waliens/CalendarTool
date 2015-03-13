<?php

	/**
	 * @file
	 * @brief Contains the PathwayFilter class
	 */

	namespace ct\models\filters;

	use ct\models\PathwayModel;
	use util\database\Database;
	use util\database\SQLAbstract_PDO;

	/**
	 * @class PathwayFilter
	 * @brief A class for filtering events for some pathways
	 */
	class PathwayFilter implements EventFilter
	{
		private $pathways; /**< @brief The list of pathways to keep (array of strings) */
		private $keep_students; /**< @brief Boolean: true for adding all the students event, false otherwise */

		/**
		 * @brief Construct a PathwayFilter object with a set of pathways to keep
		 * @param[in] array $pathways 	   Array containing the ids of the pathways
		 * @param[in] bool  $keep_students True for keeping the student ids, false otherwise (optionnal, default: true)
		 */
		public function __construct(array $pathways, $keep_students=true)
		{
			$filter_fn = function($pathway) { return PathwayModel::valid_pathway($pathway); };
			$filtered_pathways = array_filter($pathways, $filter_fn);
			$this->pathways = array_unique($filtered_pathways);
			$this->keep_students = !!$keep_students;
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
		 */
		public function get_sql_query()
		{
			$sql = SQLAbstract_PDO::buildByPDO(Database::get_instance()->get_handle());
			$q_pathways = $sql->quote_all($this->pathways);
			$imploded_q_p = implode(", ", $q_pathways);

			$query  =  "( SELECT Id_Event FROM 
						 ( SELECT Id_Global_Event FROM global_event ) AS glob
						 NATURAL JOIN 
						 ( SELECT * FROM global_event_pathway WHERE Id_Pathway IN (".$imploded_q_p.")) AS glob_path
						 NATURAL JOIN sub_event
						 WHERE (Id_Pathway, Id_Event) NOT IN (SELECT Id_Pathway, Id_Event FROM sub_event_excluded_pathway)
						) 
						UNION 
						( SELECT Id_Event FROM independent_event_pathway WHERE Id_Pathway IN (".$imploded_q_p."))";

			if($this->keep_students)
				$query = "( ".$query." ) UNION ALL (SELECT Id_Event FROM student_event )";
			
			return $query;
		}

		/**
		 * @copydoc EventFilter::get_table_alias
		 */
		public function get_table_alias()
		{
			return "f_pathway_events";
		}
	}