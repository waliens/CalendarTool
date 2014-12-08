<?php
	
	/**
	 * @file
	 * @brief Database singleton class
	 */

	namespace ct\util\database;

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
		 */
		private function __construct()
		{
			$user = "dilui";
			$pass = "8Cfac9d9";
			$host = "10.64.196.213";
			$db   = "dilui";

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