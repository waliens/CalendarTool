<?php

	/** 
	 * @file
  	 * @brief Holds the SQLAbstract_PDO class
	 */

	namespace util\database;

	use \PDO;

	/**
	 *	@class SQLAbstract_PDO
	 *	@brief A class using pdo for implementing methods of the SQLAbstract class
	 *	@authors Romain Mormont
	 *	@date 09/08/2014
	 *  @version 0.2
	 */
	class SQLAbstract_PDO extends SQLAbstract
	{
		private $pdo; /**< @brief PDO object for querying the database */
		private $error_info; /**< @brief error info */

		/**
		 * @brief Construct a SQLAbstract_PDO object 
		 * The constructor is private because the class must be instantiated with the 
		 * function SQLAbstract_PDO::buildByPDO and SQLAbstract_PDO::buildByConnectionInfo
		 */
		private function __construct()
		{
			$this->pdo = null;
			$this->error_info = array(0 => "", 1 => "", 2 => "");
		}

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
				$this->closeCursor($stmt);
				return false;
			}

			// checks if the query has returned a set of data and fetch otherwise return true
			if($stmt->columnCount() != 0)
				$to_return = $stmt->fetchAll();
			else 
				$to_return = true;

			$this->closeCursor($stmt);

			return $to_return;
		}

		/**
		 * @copydoc SQLAbstract::error_code
		 */
		public function error_code()
		{
			return $this->error_info[0];
		}

		/**
		 * @copydoc SQLAbstract::error_info
		 */
		public function error_info()
		{
			return $this->error_info;
		}

		/** 
		 * @copydoc SQLAbstract::prepare_query
		 * 
		 * @note The method returns a PDO::PDOStatement object on which the method PDO::PDOStatement::execute can be called to execute the query
		 */
		public function prepare_query($query)
		{
			return $this->pdo->prepare($query);
		}

		/**
		 * @copydoc SQLAbstract::quote
		 */
		public function quote($string)
		{
			return $this->pdo->quote($string, PDO::PARAM_STR);
		}

		/** 
		 * @copydoc SQLAbstract::last_insert_id
		 */
		public function last_insert_id()
		{
			return $this->pdo->lastInsertId();
		}

		/**
		 * @brief Destroy the cursor associated with the given statement
		 * @param[in] PDO::PDOStatement $stmt The statement of which the cursor must be closed
		 * @param[in] bool $extract_error True if the error must be extracted before closing the cursor, false otherwise 
		 */
		private function closeCursor(&$stmt, $extract_error=true)
		{
			if(!$stmt)
				return;

			if($extract_error)
				$this->error_info = $stmt->errorInfo();
	
			$stmt->closeCursor();
		}
	};