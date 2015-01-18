<?php
	
	/**
	 * @file
	 * @brief Database singleton class
	 */

	namespace util\database;

	use \PDO;

	/**
	 * @class Database
	 * @brief Singleton class encapsulating the initialization of the database
	 */
	class Database
	{
		private static $instance = null;
		private $pdo;

		/** 
		 * @brief Construct a Database object
		 * @param[in] string $user The database username
		 * @param[in] string $pass The user password
		 * @param[in] string $host The ip address of the sql server
		 * @param[in] string $db   The name of the database to be opened
		 */
		private function __construct($user, $pass, $host, $db)
		{
			$this->pdo = new PDO('mysql:host='.$host.';dbname='.$db, $user, $pass); 
			$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		}

		/**
		 * @brief Get the singleton instance of the class
		 * @retval Database Singleton instance of the Database class
		 */
		public static function get_instance()
		{
			if(!self::$instance)
				self::$instance = new Database();

			return self::$instance;
		}

		/**
		 * @brief Return the initialized database object (PDO)
		 * @retval PDO PDO database object
		 */
		public function get_handle()
		{
			return $this->pdo;
		}
	}