<?php
	
	/**
	 * @file
	 * @brief Superclass of any model
	 */

	namespace ct\util\mvc;
	
	use ct\util\database\SQLAbstract_PDO as SQLAbstract_PDO;

	/**
	 * @class Model
	 * @brief Base class for model
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
			$this->pdo = Database::getInstance()->getHandle();
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