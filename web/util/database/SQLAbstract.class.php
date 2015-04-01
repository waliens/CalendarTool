<?php

	/** 
	 * @file
	 * @brief File containing the SQL abstraction classes. The purpose of these tools is to write less code to send SQL queries to the sql server.
	 * See SQLAbstract abstract class and SQLAbstract_PDO class for interfaces of the classes.
	 * 
	 * \@todo Possibly move select, insert, count, delete and update to the abstract class as it doesn't require the usage of any database access API
	 */

	namespace util\database;

	use \PDO;

	/**
	 *	@class SQLAbstract
	 *	@brief A class that abstracts the querying of a database by providing a set of methods for usual database operations
	 *
	 *	@authors Romain Mormont
	 *	@date 09/08/2014
	 *  @version 0.2
	 */
	abstract class SQLAbstract
	{
		const DUMP_MODE_DUMP_ALL = 1;/**< @brief For dumping all queries to the screen */
		const DUMP_MODE_NO_DUMP = 2;/**< @brief For never dumping any query */

		private $dump_mode = self::DUMP_MODE_NO_DUMP;/**< @brief Keeps the dump mode */

		/** 
		 * @brief Method for sending a select query to the database 
		 * @param[in] string $table        The table name
		 * @param[in] string $where_clause A string containing the where clause 
		 * @param[in] array  $column_names The names of the colmuns to select (array of strings). null means "*"
		 * @param[in] array  $order_by     The names of the columns on which the results must be sorted (array of strings). null means no sorting.
		 * @param[in] array  $group_by     The names of the columns on which the results must be grouped (array of strings). null means no grouping.
		 * @param[in] int    $limit_first  The number of the first row to display
		 * @param[in] int    $limit_nb     The number of rows to extract
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
		 * @note If an error occurs, the error code and description can be obtained from the error_code() and error_info() methods
		 */
		public function select($table, $where_clause = null, array $column_names = null, array $order_by = null, 
								array $group_by = null, $limit_first = null, $limit_nb = null)
		{
			if($column_names == null) $column_names = array();
			if($order_by == null)	  $order_by = array();
			if($group_by == null) 	  $group_by = array();

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
		 * @brief Method for sending a select query to the database and extracting only the first result
		 * @param[in] string $table        The table name
		 * @param[in] string $where_clause A string containing the where clause 
		 * @param[in] array  $column_names The names of the colmuns to select (array of strings). null means "*"
		 * @param[in] array  $order_by     The names of the columns on which the results must be sorted (array of strings). null means no sorting.
		 * @param[in] array  $group_by     The names of the columns on which the results must be grouped (array of strings). null means no grouping.
		 * @param[in] int    $limit_first  The number of the first row to display
		 * @param[in] int    $limit_nb     The number of rows to extract
		 *
		 * @retval array|bool The row extracted from the query result (mapping the column names with the value), an empty array if there was no result
		 * or false on error.
		 * @note The parameters must be correctly escaped 
		 * @note If an error occurs, the error code and description can be obtained from the error_code() and error_info() methods
		 */
		public function select_one($table, $where_clause, array $column_names = null, array $order_by = null, 
									array $group_by = null, $limit_first = null, $limit_nb = null)
		{
			$result = $this->select($table, $where_clause, $column_names, $order_by, $group_by, $limit_first, $limit_nb);

			if(!is_array($result))
				return false;

			if(empty($result))
				return array();

			return $result[0];
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
		 * @note If an error occurs, the error code and description can be obtained from the error_code() and error_info() methods
		 */
		public function insert($table, array $column_value_map)
		{
			// build query
			$query = "INSERT INTO ".$table."(".implode(", ", array_keys($column_value_map)).") 
					       VALUES (".implode(", ", array_values($column_value_map)).");";

			return $this->execute_query($query);
		}

		/**
		 * @brief Method for sending an insert query to the database in order to insert multiple rows at once
		 * @param[in] string $table   The table in which the rows must be inserted
		 * @param[in] array  $values  A multidimensionnal array of which the subarrays (rows) are the values
		 * @param[in] array  $columns The columns in which the values must be inserted (optionnal)
		 * to insert as new rows. The values must be ordered in the same way as in $columns
		 * 
		 * @note The values are should not be escaped
		 * 
		 * Example :
		 * @code
		 * // sends "INSERT INTO my_table(col1, col2) VALUES('val1', 'val2'), ('val1bis', 'val2bis');"
		 * $columns = array('col1', 'col2');
		 * $values  = array(array('val1', 'val2'), array('val1bis', 'val2bis'));
		 * $foo->insert_batch("my_table", $columns, $values);
		 * @endcode
		 */
		public function insert_batch($table, array $values, array $columns = null)
		{
			if($columns == null)
				$query = "INSERT INTO ".$table." VALUES ";
			else
				$query = "INSERT INTO ".$table."(".implode(", ", $columns).") VALUES ";

			$col_count = ($columns != null ? count($columns) : (empty($values) ? 0 : count($values[0])));
			$qmark_str_array = array_fill(0, count($values), "(".implode(",", array_fill(0, $col_count, "?")).")");
			$data_array = $this->array_flatten($values);
			
			return $this->execute_query($query.implode(",", $qmark_str_array).";", $data_array);
		}

		/**
		 * @brief Flatten an multidimensionnal array
		 * @param[in] array $array The array to flatten
		 * @retval array The flattened array
		 * @note Taken from 'too much php' post on stackoverflow (url: http://goo.gl/UUCMTp)
		 */
		private function array_flatten(array $array)
		{
		    $return = array();
		    array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
		    return $return;
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
		 * @note If an error occurs, the error code and description can be obtained from the error_code() and error_info() methods
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
		 * @note If an error occurs, the error code and description can be obtained from the error_code() and error_info() methods
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
		 * @note If an error occurs, the error code and description can be obtained from the error_code() and error_info() methods
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
		 * @note If an error occurs, the error code and description can be obtained from the error_code() and error_info() methods
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
		 * @note If an error occurs, the error code and description can be obtained from the error_code() and error_info() methods
		 */
		abstract public function execute_query($query, array $parameters = array());

		/**
		 * @brief Returns the last error's code
		 * @retval int Error code of the last error
		 */
		abstract public function error_code();

		/**
		 * @brief Returns the last error's description
		 * @retval array Error description of the last error
		 */
		abstract public function error_info();

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
		 * @brief Same behavior as the method PDO::quote()
		 * @param[in] string $string         The string to escape
		 * @return The quoted and escaped string
		 */
		abstract public function quote($string);

		/** 
		 * @brief Return an anonymous function that quotes its string argument
		 * @retval function Function that takes a string argument and returns it quoted
		 *
		 * Examples :
		 * @code
		 * $str_array = array("param1", "param2", "param3");
		 *
		 * //  Array 
		 * //  (
		 * //	   [0] => param1
		 * //	   [1] => param2
		 * //	   [2] => param3
		 * //  )
		 *
		 * print_r(array_map($sql->quote_fn(), $str_array)); 
		 *  
		 * //  Displays : 
		 * //  Array 
		 * //  (
		 * //	   [0] => 'param1'
		 * //	   [1] => 'param2'
		 * //	   [2] => 'param3'
		 * //  )
		 * @endcode
		 */
		public function quote_fn()
		{
			$that = $this;
			return function($string) use ($that) { return $that->quote($string); };
		}

		/**
		 * @brief Returns an array containing the strings of the $strings array quoted 
		 * @retval array The array of quoted strings
		 */
		public function quote_all(array $strings)
		{
			return array_map($this->quote_fn(), $strings);
		}
		
		/** 
		 * @brief Sets the dump mode 
		 * @param[in] $dump_mode Must be one of the following class constant :
		 *		- SQLAbstract::DUMP_MODE_DUMP_ALL : dump every query to the screen
		 *		- SQLAbstract::DUMP_MODE_NO_DUMP : never dump any query to the screen
		 */
		public function set_dump_mode($dump_mode = SQLAbstract::DUMP_MODE_DUMP_ALL)
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
		 * @param[in] string $str The string to dump
		 */
		protected function dump($str)
		{
			echo $str."<br>";
		}

		/**
		 * @brief Acquires the lock for tables given in the array
		 * @param[in] array $tables_lock Array of strings. Each string contains information about a table to lock and the type of lock
		 * @retval bool True if the locks are acquired, false otherwise
		 *
		 * Example : 
		 * @code
		 * // lock the tables my_table and my_table2 respectively in READ and WRITE mode
		 * $db->lock(array("my_table READ", "my_table2 WRITE");
		 *
		 * // mess around with my_table and my_table2 through the SQLAbstract object : 
		 * $db->select("my_table", null, array("col1", "col2"));
		 * // ...
		 * $db->update("my_table2", array("col1" => "val1"), "Id = 18");
		 * // ...
		 * 
		 * // finally unlock tables
		 * $db->unlock();
		 * @endcode
		 */
		public function lock(array $tables_lock)
		{
			$query = "LOCK TABLES ".implode(",", $tables_lock).";";
			return $this->execute_query($query);
		}

		/**
		 * @brief Release all the locks acquired with lock
		 * @retval bool True if the tables were unlocked
		 * @note Refer to SQLAbstract::lock for an example
		 */
		public function unlock()
		{
			return $this->execute_query("UNLOCK TABLES;");
		}

		/**
		 * @brief Starts a transaction 
		 * @retval bool True if the transaction was successfully started, false otherwise
		 * 
		 * Example :
		 * @code
		 * // initiate the transaction
		 * $db->transaction();
		 * 
		 * // mess around with the database through the SQLAbstract object
		 * // ...
		 * 
		 * // if an error occurs, one can rollback the transaction
		 * if ($error_occurred) 
		 *   $db->rollback();
		 * else // if everything went fine, then one can commit
		 *   $db->commit();	
		 * @endcode
		 */
		public function transaction()
		{
			return $this->execute_query("START TRANSACTION;");
		}

		/**
		 * @brief Ends the current transaction and commit the set of queries
		 * @retval bool True if the commit was successfully executed, false otherwise
		 * @note See SQLAbstract::transaction for an example
		 */
		public function commit()
		{
			return $this->execute_query("COMMIT;");
		}

		/**
		 * @brief Rollback the current transaction
		 * @retval bool True if the rollback was successfully executed, false otherwise
		 * @note See SQLAbstract::transaction for an example
		 */
		public function rollback()
		{
			return $this->execute_query("ROLLBACK;");
		}
	}