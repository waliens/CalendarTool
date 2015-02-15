<?php
	
	/**
	 * @file
	 * @brief Contains the GlobalEventFilter class
	 */

	namespace ct\filters;

	use ct\models\GlobalEventModel;

	/**
	 * @class GlobalEventFilter
	 * @brief A class for representing a filter on global events
	 */
	class GlobalEventFilter
	{
		private $ge_ids; /**< @brief The global event ids to filter on (array of integers)*/ 

		const FORMAT_INT; /**< @brief Represents the format of the id_data array row : global event id */
		const FORMAT_ARR_ID; /**< @brief Represents the format of the id_data array row : array containing the id */
		const FORMAT_ARR_ULG; /**< @brief Represents the format of the id_data array row : array containing the year and ulg_id */

		/**
		 * @brief Construct a GlobalEventFilter object for filtering on the given global events
		 * @param[in] array $id_data The data for identifying the ids on which global events the 
		 * event must be filtered
		 * @note The id data rows can be formatted in three ways :
		 * <ul>
		 * 	<li>integer : the row is a global event id (FORMAT_INT)</li>
		 *	<li>array (1) : the row is an array containing one key 'id' mapping a global event id (FORMAT_ARR_ID)</li>
		 *  <li>array (2) : the row is an array containing two keys 'ulg_id', 'year' which 
		 *      are respectively the ulg id of the course and the year starting its academic year (FORMAT_ARR_ULG)</li>
		 * </ul>
		 * The array can contain these various format simultaneously
		 */
		public function __construct(array $id_data)
		{
			$this->set_ge_ids($id_data);
		}

		/**
		 * @brief Initialize the data member ge_ids with a id_data array
		 * @param[in] array $id_data The data for identifying global events (see construction documentation for the format)
		 */
		private function set_ge_ids(array $id_data)
		{
			$this->ge_ids = array();
			$ge_mod = new GlobalEventModel();

			foreach ($id_data as $row) 
			{
				switch($this->get_row_format($row))
				{
				case self::FORMAT_INT:
					$this->ge_ids[] = $id_data;
				case self::FORMAT_ARR_ID:
					$this->ge_ids[] = $id_data['id'];
				case self::FORMAT_ARR_ULG:
					$this->ge_ids[] = $ge_mod->get_global_event_id($id_data);
				}
			}

			$this->ge_ids = array_unique($this->ge_ids, SORT_NUMERIC);
		}

		/**
		 * @brief Returns the format of the array 
		 */
		private function get_row_format($row)
		{
			if(is_array($row))
			{	
				$row_keys = array_keys($row);
				if(in_array("id", $row_keys))
					return self::FORMAT_ARR_ID;
				else
					return self::FORMAT_ARR_ULG;
			}
			else
				return self::FORMAT_INT;
		}

		/**
		 * @brief Return the global event ids on which to filter
		 * @retval array An array containing the global event ids on which to filter
		 */
		public function get_ids()
		{
			return $this->ge_ids;
		}
	}