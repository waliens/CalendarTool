<?php

	/** 
	 * @file
	 * @brief File containing the SQL abstraction classes. The purpose of these tools is to write less code to send SQL queries to the sql server.
	 * See SQLAbstract abstract class and SQLAbstract_PDO class for interfaces of the classes.
	 * 
	 * \@todo Possibly move select, insert, count, delete and update to the abstract class as it doesn't require the usage of any database access API
	 */

 	namespace ct\util\database;

 	use \PDO;

	/**
	 *	@class SQLAbstract
	 *	@brief A class that abstracts the querying of a database by providing a set of methods for usual database operations
	 *
	 *	@authors Romain Mormont
	 *	@date 09/08/2014
	 */
	abstract class SQLAbstract
	{
		const DUMP_MODE_DUMP_ALL = 1;/**< For dumping all queries to the screen */
		const DUMP_MODE_NO_DUMP = 2;/**< For never dumping any query */

		private $dump_mode = self::DUMP_MODE_NO_DUMP;/**< Keeps the dump mode */

		/** 
		 * @brief Method for sending a select query to the database 
		 * @param[in] string $table        The table name
		 * @param[in] string $where_clause A string containing the where clause 
		 * @param[in] array  $column_names The names of the colmuns to select (array of strings). null means "*"
		 * @param[in] int    $limit_first  The number of the first row to display
		 * @param[in] int    $limit_nb     The number of rows to extract
 		 * @param[in] array  $order_by     The names of the columns on which the results must be sorted (array of strings). null means no sorting.
 		 * @param[in] array  $group_by     The names of the columns on which the results must be grouped (array of strings). null means no grouping.
 		 *
 		 * @retval array|bool An array containing the results of the query (each row is an array mapping the columns name with its value) or false on error.
 		 * 
 		 * @note The parameters must be correctly escaped 
 		 * 
 		 * Examples : 
 		 * @code
 		 * // sends "SELECT * FROM my_table;"
 		 * $foo->select("my_table");
 		 *
 		 * // sends "SELECT col1, col2 FROM my_table;"
 		 * $foo->select("my_table", null, array("col1", "col2"));
 		 * 
 		 * // sends "SELECT SUM(row1) AS sum_row1, row2 FROM my_table WHERE row3 > NOW() GROUP BY row2;"
 		 * $foo->select("my_table", "row3 > NOW()", array("SUM(row1) AS sum_row1", "row2"), null, null, array(), array("row2"));
 		 *
 		 * // sends "SELECT * FROM table1 NATURAL JOIN table2;"
 		 * $foo->select("table1 NATURAL JOIN table2");
 		 * @endcode
 		 * 
 		 * @note If an error occurs, the error code and description can be obtained from the errorCode() and errorInfo() methods
		 */
		public function select($table, $where_clause = null, array $column_names = null, $limit_first = null, 
								$limit_nb = null, array $order_by = null, array $group_by = null)
		{
			if($column_names == null) $column_names = array();
			if($order_by == null)	  $order_by = array();
			if($group_by == null)  	  $group_by = array();

			$query = "SELECT ";

			// add columns names
			if(empty($column_names))
				$query .= "* ";
			else
				$query .= implode(", ", $column_names)." ";

			// add FROM
			$query .= "FROM ".$table." ";

			// add WHERE
			if($where_clause != null)
				$query .= "WHERE ".$where_clause." ";

			// add GROUP BY
			if(!empty($group_by))
				$query .= "GROUP BY ".implode(", ", $group_by)." ";

			// add ORDER BY
			if(!empty($order_by))
				$query .= "ORDER BY ".implode(", ", $order_by)." ";

			// add limit
			if(is_numeric($limit_first) && is_numeric($limit_nb))
				$query .= "LIMIT ".$limit_first.", ".$limit_nb;

			$query .= ";";

			return $this->execute_query($query);
		}

		/**
		 * @brief Method for sending an insert query to the database
		 * @param[in] string $table            The table name
		 * @param[in] array  $column_value_map An array mapping columns names and their value for the row to insert
		 * 
		 * @retval bool True if the insertion has succeeded, false otherwise
		 *
		 * @note The parameters must be correctly escaped 
		 *
		 * Examples :
		 * @code
		 * // sends "INSERT INTO my_table(col1, col2) VALUES('val1', 'val2');"
		 * $foo->insert("my_table", array("col1" => "val1", "col2" => "val2"));
		 * @endcode
		 *
		 * @note If an error occurs, the error code and description can be obtained from the errorCode() and errorInfo() methods
		 */
		public function insert($table, array $column_value_map)
		{
			// build query
			$query = "INSERT INTO ".$table."(".implode(", ", array_keys($column_value_map)).") 
					       VALUES (".implode(", ", array_values($column_value_map)).");";

			return $this->execute_query($query);
		}


		/**
		 * @brief Method for sending a delete query to the database
		 * @param[in] string $table The table name
		 * @param[in] string $where The where clause
		 *
		 * @retval bool True if the deletion has succeeded, false otherwise
		 * 
		 * @note The parameters must be correctly escaped 
		 *
		 * Examples :
		 * @code
		 * // sends a "DELETE FROM my_table WHERE col1 = val1 AND col2 < 10;"
		 * $foo->delete("my_table", "col1 = val1 AND col2 < 10");
		 * @endcode
		 *
		 * @note If an error occurs, the error code and description can be obtained from the errorCode() and errorInfo() methods
		 */
		public function delete($table, $where)
		{
			$query = "DELETE FROM ".$table." WHERE ".$where.";";

			return $this->execute_query($query);
		}

		/**
		 * @brief Method for sending an update query to the database
		 * @param[in] string $table The table name
		 * @param[in] array  $set   An array mapping the column name with its value (string => string)
		 * @param[in] string $where A string containing the WHERE clause
		 * 
		 * @retval bool True if the update has succeeded, false otherwise
		 * 
		 * @note The parameters must be correctly escaped 
		 *
		 * Examples :
		 * @code
		 * // sends "UPDATE my_table SET col1 = val1, col2 = 'val2' WHERE col3 = val3;"
		 * $foo->update("my_table", array("col1" => "val1", "col2" => "'val2'"), "col3 = val3");
		 * @endcode
		 *
		 * @note If an error occurs, the error code and description can be obtained from the errorCode() and errorInfo() methods
		 */
		public function update($table, array $set, $where)
		{
			$query = "UPDATE ".$table." SET ";

			$set_final = array();
			// add "columns = new value" query part for each column to modify
			foreach($set as $key => $value)
				$set_final[] = $key." = ".$value;

			$query .= implode(", ", $set_final);
			$query .= " WHERE ".$where.";";

 			return $this->execute_query($query);
		}

		/**
		 * @brief Method for counting rows in the database
		 * @param[in] string $table The table name
		 * @param[in] string $where The WHERE clause. null value means no where clause.
		 *
		 * @retval int The number of rows counted, -1 on error
		 * 
		 * @note The parameters must be correctly escaped 
		 *
		 * Examples :
		 * @code
		 * // sends "SELECT COUNT(*) AS count FROM my_table;"
		 * $foo->count("my_table");
		 *
		 * // sends "SELECT COUNT(*) AS count FROM my_table WHERE col1 = val1;"
		 * $foo->count("my_table", "col1 = val1");
		 * @endcode
		 *
		 * @note If an error occurs, the error code and description can be obtained from the errorCode() and errorInfo() methods
		 */
		public function count($table, $where = null)
		{
			$query = "SELECT COUNT(*) AS count FROM ".$table;

			if($where != null)
				$query .= " WHERE ".$where;
			
			$query .= ";";

			$result = $this->execute_query($query);

			if(!$result)
				return -1;

			return $result[0]['count'];
		}

		/**
		 * @brief Method for sending a procedure call to the database
		 * @param[in] string $procedure_name The name of the procedure
		 * @param[in] array  $arguments      Array with the arguments. An empty array means no argument.
		 * 
		 * @retval array|bool An array containing the result of the procedure call, false on error.
		 *  
		 * @note The parameters must be correctly escaped 
		 *
		 * Examples :
		 * @code
		 * // sends "CALL proc1();"
		 * $foo->procedure_call("proc1");
		 *
		 * // sends "CALL proc1(arg1, 'arg2');"
		 * $foo->procedure_call("proc1", array("arg1", "'arg2'"));
		 * @endcode
		 *
		 * @note If an error occurs, the error code and description can be obtained from the errorCode() and errorInfo() methods
		 */
		public function procedure_call($procedure_name, array $arguments = array())
		{
			$query = "CALL ".$procedure_name."(".implode(", ", $arguments).");";

			return $this->execute_query($query);
		}

		/**
		 * @brief Method for sending a user-defined query to the database
		 * @param[in] string $query      The query to send to the database (parameters must be marked by '?')
		 * @param[in] array  $parameters The parameters to insert into the query. An empty array means no parameters.
		 *
		 * @retval array|bool An array containing the result of the query if some data must be returned. If the query is such that it won't return any data,
		 *		then the method returns true if the query was successfully executed. In both case, if an error occurs then the method returns false.
		 * 
		 * Examples :
		 * @code
		 * // query without parameter
		 * $query = "SELECT * FROM batiment;";
		 * $foo->execute_query($query);
		 *
		 * // query with parameters
		 * $query = "SELECT nom, prenom FROM personne WHERE taille > ? AND age > ?;";
		 * $param = array(150, 18);
		 * $foo->execute_query($query, $param);
		 * 
		 * // query with string parameters
		 * $query = "INSERT INTO personne VALUES nom = ?, prenom = ?, ville = ?, age = ?;";
		 * $param = array("Doe", "John", "Cape Town", 18);
		 * $foo->execute_query($query, $param);
		 * @endcode
		 *
		 * @note If an error occurs, the error code and description can be obtained from the errorCode() and errorInfo() methods
		 */
		abstract public function execute_query($query, array $parameters = array());

		/**
		 * @brief Returns the last error code
		 * @retval int Error code of the last error
		 */
		abstract public function errorCode();

		/**
		 * @brief Returns the last error description
		 * @retval array Error description of the last error
		 */
		abstract public function errorInfo();

		/** 
		 * @brief Prepare a query
		 * @param[in] string $query The query
		 * @retval mixed An object allowing the query execution
		 *
		 * @note This method should be prefered to SQLAbstract::execute_query when the same query has to be repeated several times because, in this situation, the query
		 *    preparation speeds up the multiple querying
		 */
		abstract public function prepare_query($query);

		/**
		 * @brief Return the id of the last inserted line
		 * @retval string|int The last inserted row's id, -1 on error
		 */
		abstract public function last_insert_id();

		/** 
		 * @brief Sets the dump mode 
		 * @param $dump_mode Must be one of the following class constant :
		 *		- SQLAbstract::DUMP_MODE_DUMP_ALL : dump every query to the screen
		 *		- SQLAbstract::DUMP_MODE_NO_DUMP : never dump any query to the screen
		 */
		public function setDumpMode($dump_mode = SQLAbstract::DUMP_MODE_DUMP_ALL)
		{
			$this->dump_mode = $dump_mode;
		}

		/** 
		 * @brief Returns a boolean if the queries must be dumped according to the dump mode
	     * @return true if the queries must be dumped, false otherwise.
		 */
		protected function do_dump()
		{
			return $this->dump_mode == SQLAbstract::DUMP_MODE_DUMP_ALL;
		}

		/**
		 * @brief Dump the given string
		 * @param[in] str The string to dump
		 */
		protected function dump($str)
		{
			echo $str."<br>";
		}
	}
