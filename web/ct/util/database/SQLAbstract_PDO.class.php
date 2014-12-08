<?php

	/** 
	 * @file
  	 * Holds the SQLAbstract_PDO class
	 */

	namespace nhitec\sql;

	use \PDO;

	/**
	 *	@class SQLAbstract_PDO
	 *	@brief A class using pdo for implementing methods of the SQLAbstract class
	 *	@authors Romain Mormont
	 *	@date 09/08/2014
	 */
	class SQLAbstract_PDO extends SQLAbstract
	{
		private $pdo; /**< PDO object for querying the database */
		private $error_info = array(0 => "", 1 => "", 2 => ""); /**< error info */
		/**
		 * @brief Initializes the SQLAbstract_PDO object with a initialized PDO object
		 * @param[in] PDO $pdo The PDO object 
		 *
		 * @retval SQLAbstract_PDO An initialized instance of the class
		 *
		 * @note The constructor changes the attribute of the pdo object :
		 *         - PDO::ATTR_DEFAULT_FETCH_MODE -> PDO::FETCH_ASSOC
		 */
		public static function buildByPDO(PDO $pdo) 
		{
			$instance = new self();
			$instance->pdo = $pdo;
			$instance->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

			return $instance;
		}

		/**
		 * @brief Initializes the SQLAbstract_PDO object with database connection informations
		 * @param[in] string $username Database username
		 * @param[in] string $password Database password for the given user
		 * @param[in] string $db_name  Name of the database to use
		 * @param[in] string $db_host  Hostname of the database
		 * @retval SQLAbstract_PDO An initialized instance of the class
		 * @throws PDOException if the connection to the database fails
		 */
		public static function buildByConnectionInfo($username, $password, $db_name, $db_host)
		{
			$instance = new self();
			$instance->pdo = new PDO('mysql:dbname='.$db_name.';host='.$db_host, $username, $password, 
										array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));
			return $instance;
		}

		/**
		 * @copydoc SQLAbstract::execute_query
		 */
		public function execute_query($query, array $parameters = array())
		{
			if($this->do_dump())
				$this->dump($query); 

			// prepare query
			$stmt = $this->pdo->prepare($query);

			if(!$stmt)
				return false;

			// bind parameters and execute
			if(empty($parameters))
				$result = $stmt->execute();  
			else
				$result = $stmt->execute($parameters);

			if(!$result)
			{
				$stmt->closeCursor();
				return false;
			}

			// checks if the query has returned a set of data and fetch otherwise return true
			if($stmt->columnCount() != 0)
				$to_return = $stmt->fetchAll();
			else 
				$to_return = true;

			$this->error_info = $stmt->errorInfo();
			
			$stmt->closeCursor();
			return $to_return;
		}

		/**
		 * @copydoc SQLAbstract::errorCode
		 */
		public function errorCode()
		{
			return $this->error_info[0];
		}

		/**
		 * @copydoc SQLAbstract::errorInfo
		 */
		public function errorInfo()
		{
			return $this->error_info;
		}

		/** 
		 * @copydoc SQLAbstract::prepare_query
		 * 
		 * @note The method returns a PDOStatement object on which the method PDOStatement::execute can be called to execute the query
		 */
		public function prepare_query($query)
		{
			return $this->pdo->prepare($query);
		}

		/**
		 * @brief Same behavior as the method PDO::quote()
		 * @param[in] string $str            The string to escape
		 * @param[in]        $parameter_type The type of parameter guven as argument
		 * 
		 * @return The quoted and escaped $str
		 */
		public function quote($str, $parameter_type = PDO::PARAM_STR)
		{
			return $this->pdo->quote($str, $parameter_type);
		}

		/** 
		 * @copydoc SQLAbstract::last_insert_id
		 */
		public function last_insert_id()
		{
			return $this->pdo->lastInsertId();
		}

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
			$that = $this; // workaround => passing just $this in the 'use' part of the anonymous function does not work
			return (function ($string) use (&$that) { return $that->quote($string); });
		}
		
	};
