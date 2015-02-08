<?php
	
	/**
	 * @file
	 * @brief Superclass of any model
	 */

	namespace util\mvc;
	
	use util\database\Database as Database;
	use util\database\SQLAbstract_PDO as SQLAbstract_PDO;

	/**
	 * @class Model
	 * @brief Base class for models
	 */
	abstract class Model
	{
		protected $pdo; /**< pdo object */
		protected $sql; /**< sql abstract object */

		/**
		 * Build a Model object
		 */
		public function __construct()
		{
			$this->pdo = Database::get_instance()->get_handle();
			$this->sql = SQLAbstract_PDO::buildByPDO($this->pdo);
		}

		/**
		 * Return the sql abstract object
		 * @retval SQLAbstract_PDO The sql abstract object
		 */
		protected function get_abstract() 
		{
			return $this->sql;
		}
	}