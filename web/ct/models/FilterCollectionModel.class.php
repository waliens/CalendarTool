<?php

	/**
	 * @file
	 * @brief Contains the FilterCollectionModel class
	 */

	namespace ct\models;

	use util\mvc\Model;

	/**
	 * @class FilterCollectionModel
	 * @brief A class that contains a collection of event filters and that can retrieve from the db
	 * the filtered events from the set of filters that it contains. 
	 */
	class FilterCollectionModel extends Model
	{
		private $association_mode; /**< @brief The filter association mode (one of the LOGIC_* class constant) */

		const LOGIC_OR = "OR"; /**< @brief One of the filter association : disjunction */
 		const LOGIC_AND = "AND"; /**< @brief One of the filter association : conjunction */

		/**
		 * @brief Construct a FilterCollectionModel object
		 * @param[in] string $association_mode The association mode to apply between the 
		 * filter (one of the LOGIC_* class constant) (optionnal, default: LOGIC_OR)
		 * @throws Exception bad association mod
		 */
		public function __construct($association_mode=null)
		{
			parent::__construct();

			if($association_mode == null) 
				$association_mode = self::LOGIC_OR;
			elseif(!$this->valid_association_mode($association_mode))
				throw new \Exception("Bad association mode");

			$this->association_mode = $association_mode;
		}

		/**
		 * @brief Checks whether the given association mode is valid
		 * @param[in] string The mode to check
		 * @retval bool True if the mode is valid, false otherwise
		 */
		private function valid_association_mode($mode)
		{
			return $mode === FilterCollectionModel::LOGIC_AND 
					|| $mode == FilterCollectionModel::LOGIC_OR;
		}
	}