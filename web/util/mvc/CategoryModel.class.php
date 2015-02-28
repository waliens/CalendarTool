<?php

	/**
	 * @file
	 * @brief Superclass of any category controller
	 */

	namespace util\mvc;

	/**
	 * @class CategoryModel
	 * @brief Base class for category model. 
	 *
	 * A set of categories is a list of items contained in a table
	 * of the database that can be given to the user in a drop down list for instance (can be colors, jobs,...). 
	 * A category is identified by its key and defined by its name and possible some other attributes.
	 * This class provides methods for accessing, editing and interacting with these items. 
	 */ 
	abstract class CategoryModel extends Model
	{
		protected $category_table; /**< @brief The name of the table containing the categories */
		protected $id_col_name; /**< @brief An array containing the name of the columns composing the table key */
		protected $categ_col_name; /**< @brief A string containing the name of the tablr column containing the category name */
		protected $other_col_name; /**< @brief An array containing the name of the other columns of the database */

		/**
		 * @brief Constructs a category model
		 * @param[in] string $table_name     The name of the table containing the categories
		 * @param[in] array  $id_col_name    Array containing the name of the table columns composing the key of table
		 * @param[in] string $categ_col_name The name of the table column containing the name of the category
		 * @param[in] array  $other_col_name The name of the other field describing a categort
		 */
		public function __construct($table_name, array $id_col_name, $categ_col_name, array $other_col_name = null)
		{
			if($other_col_name == null) $other_col_name = array();

			parent::__construct();

			$this->category_table = $table_name;
			$this->id_col_name = $id_col_name;
			$this->categ_col_name = $categ_col_name;
			$this->other_col_name = $other_col_name;
		}

		/**
		 * @brief Return the list of categories
		 * @retval array Mutli-dimensionnal array of which each subarray is a category. The first item of a subarray is 
		 * the name of the category and the last columns contains the keys.
		 */
		public function get_items()
		{
			return $this->sql->select($category_table);
		}

		/**
		 * @brief Return one categories identifier by the item_id
		 * @param[in] array $item_id The key identifying the item. The values must be ordred
		 * in the same fashion as the column in the id_col_name array given to the constructor
		 * @retval array An array containing the the 
		 */
		public function get_item(array $item_id)
		{
			$where = $this->get_where_key_clause();
			$query = "SELECT * FROM ".$this->table_name." WHERE ".$where.";";

			$result = $this->sql->execute_query($query, $item_id);

			return !empty($result) ? $result[0] : array();
		}

		/**
		 * @brief Add an item in the category table
		 * @param[in] array $item   The data necessary to insert the item
		 * @param[in] bool  $no_key True if the key can be guessed by the database server, false if it is provided at the end of the item array
		 * @retval bool True if the data was inserted successfully, false otherwise
		 * @note The data must sorted in array as follows : name of the categ, other columns' values (in the same order as the 
		 * in the $other_col_name array given at construction), key values (if $no_key is false and ordered as in the $id_col_name
		 * array given at construction)
		 */
		public function add_item($item, $no_key=true)
		{
			$col_val_map = array();
			$name_others = array_merge(array($this->categ_col_name), $this->other_col_name);

			if($no_key)
				$col_val_map = array_combine($name_others, $item);
			else
				$col_val_map = array_combine(array_merge($name_others, $this->id_col_name), $item);

			return $this->sql->insert($table_name, $col_val_map);
		}

		/**
		 * @brief Deletes an item from the category list
		 * @param[in] array $item_id The item id values (in the same order as in the $id_col_name given at construction)
		 * @retval bool True if the data was deleted successfully, false otherwise
		 */
		public function delete_item($item_id)
		{
			return $this->sql->delete($table_name, $this->get_where_key_clause($item_id));
		}

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

		/**
		 * @brief Return a where clause to use for a SQL query (i.e. col_key1=?, col_key2=?,... or col_key1=val1, col_key2=val2,...)
		 * @param[in] array $item_id The item_id data to insert in the where clause (null for a prepared query => question marks instead of values) 
		 * @return string The where clause
		 */
		protected function get_where_key_clause($item_id=null)
		{
			$sql = $this->sql; 

			if($data == null)
				$qmark_array = array_map(function($col) { return $col."=?"; }, $this->id_col_name);
			else
				$data_array = array_map(function($col, $data) use (&$sql) { $col." = ".$sql->quote($data); }, $this->id_col_name, $item_id);

			return implode(", ", $qmark_array);
		}

		/**
		 * @brief Returns an array containing the columns of the table containing the category
		 * @retval array An array of string containing the columns' names
		 * @note The columns' names are ordered as follows : categ_name, keys columns' names, other columns' names
		 */
		protected function get_columns()
		{
			return array_merge(array($this->categ_col_name), $this->id_col_name, $this->other_col_name);
		}
	}