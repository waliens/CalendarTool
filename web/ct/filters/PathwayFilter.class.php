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
	class PathwayFilter
	{
		private $pathways; /**< @brief The list of pathways to keep */

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
	}