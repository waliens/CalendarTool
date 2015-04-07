<?php
	
	/**
	 * @file
	 * @brief Database singleton class
	 */

	namespace util\database;

	use \PDO;

	/**
	 * @class Database
	 * @brief Singleton class encapsulating the initialization of the database handle
	 */
	class Database
	{
		private static $instance = null;
		private $pdo;

		/** 
		 * @brief Construct a Database object with predefined credentials
		 */
		private function __construct()
		{
			$user = "root";
			$pass = "";
			$host = "localhost";
			$db   = "calendar_tool";

			$this->pdo = new PDO('mysql:host='.$host.';dbname='.$db, $user, $pass); 
			$this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			$stmt = $this->pdo->query("SET NAMES utf8;");
			$stmt->closeCursor();
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
		 * @brief Return an initialized database object (PDO)
		 * @retval PDO PDO database object
		 */
		public function get_handle()
		{
			return $this->pdo;
		}
	}
