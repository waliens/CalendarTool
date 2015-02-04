<?php

	/**
	 * @file
	 * @brief Superclass of any category controller
	 */

	namespace util\mvc;

	/**
	 * @class CategoryModel
	 * @brief Base class for category model. A set of categories is a list of items contained in a table
	 * of the database that can be given to the user in a drop down list for instance (can be colors, jobs,...) 
	 * This class provides methods for accessing, editing and interacting with these items. 
	 */ 
	abstract class CategoryModel extends Model
	{
		protected $category_table; /**< The name of the table containing the categories */
		protected $id_col_name; /**< An array containing the name of the columns composing the table key */
		protected $categ_col_name;  /**< A string containing the name of the tablr column containing the category name */

		/**
		 * @brief Constructs a category model
		 * @param[in] string $table_name     The name of the table containing the categories
		 * @param[in] array  $id_col_name    Array containing the name of the table columns composing the key of table
		 * @param[in] string $categ_col_name The name of the table column containing the name of the category
		 */
		public function __construct($table_name, array $id_col_name, $categ_col_name)
		{
			parent::__construct();

			$this->category_table = $table_name;
			$this->id_col_name = $id_col_name;
			$this->categ_col_name = $categ_col_name;
		}

		/**
		 * @brief Return the list of categories
		 * @retval array Mutli-dimensionnal array of which each subarray is a category. The first item of a subarray is 
		 * the name of the category and the last columns contains the keys.
		 */
		public function get_items()
		{
			$columns = array_merge(array($this->categ_col_name), $this->id_col_name);
			return $this->sql->select($category_table, null, $columns);
		}

		/**
		 * @brief Return one categories identifier by the item_id
		 * @param[in] array $item_id The key identifying the item. The values must be ordred
		 * in the same fashion as the column in the id_col_name array given to the constructor
		 * @retval array An array containing the the 
		 */
		public function get_item(array $item_id)
		{
			$fn = function($col) { return $col." = ?"; };
			$where = array_map($fn, $this->id_col_name);
			$query = "SELECT ".$this->get_columns_string()." FROM ".$this->table_name." WHERE ".implode(", ", $where).";";

			$result = $this->sql->execute_query($query, $item_id);

			return !empty($result) ? $result[0] : array();
		}

		public function add_item($item);
		public function delete_item($item_id);

		/**
		 * @brief Return a string containing the columns' names of the table for using it in a sql query
		 * @param[in] bool $id_first True if the id column name must the first in the string
		 * @retval string The string containing the comma-separated columns' names 
		 */
		protected function get_columns_string($id_first=false)
		{
			if($id_first)
				return implode(", ", $this->id_col_name).", ".$this->categ_col_name;
			else
				return $this->categ_col_name.", ".implode(", ", $this->id_col_name);
		}
	}