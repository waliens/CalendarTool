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
		protected $pdo; /**< @brief A PDO object */
		protected $sql; /**< @brief A SQL abstract object */
		protected $filter; /**< @brief A phpSec Filter object */

		const LOCKMODE_NO_LOCK = 1; /**< @brief Lock behaviour : no lock must be acquired */
		const LOCKMODE_LOCK    = 2; /**< @brief Lock behaviour : only acquire the lock (no unlock) */
		const LOCKMODE_UNLOCK  = 3; /**< @brief Lock behaviour : acquire the lock and then unlock */

		/**
		 * @brief Build a Model object
		 */
		public function __construct()
		{
			$this->pdo = Database::get_instance()->get_handle();
			$this->sql = SQLAbstract_PDO::buildByPDO($this->pdo);
			$psl = new \phpSec\Core();
			$this->filter = $psl['text/filter'];
		}

		/**
		 * @brief Return the sql abstract object
		 * @retval SQLAbstract_PDO The sql abstract object
		 */
		protected function get_abstract() 
		{
			return $this->sql;
		}

		/**
		 * @brief Check if the given lock behavior implies a unlock
		 * @param[in] int $lock_mode One of the LOCKMODE_* class constant
		 * @retval bool True if an unlock must be performed, false otherwise
		 */
		protected function do_unlock($lock_mode)
		{
			return $lock_mode === Model::LOCKMODE_UNLOCK;
		}

		/**
		 * @brief Check if the given lock behavior implies a lock
		 * @param[in] int $lock_mode One of the LOCKMODE_* class constant
		 * @retval bool True if a lock must be performed, false otherwise
		 */
		protected function do_lock($lock_mode)
		{
			return $lock_mode !== Model::LOCKMODE_NO_LOCK;
		}

		/**
		 * @brief As the unlock can only be performed when the complete set of operation was performed, then
		 * a function that receives a LOCKMODE_UNLOCK lock mode and call other function that should acquire lock
		 * cannot propagate this lock mode to these function. This function returns the lock mode that should be propagated
		 * these functions.
		 * @param[in] int $lock_mode One of the LOCKMODE_* class constant
		 * @retval int $lock_mode The lock_mode to propagate
		 */
		protected function get_sub_lockmode($lock_mode)
		{
			return $lock_mode === Model::LOCKMODE_UNLOCK ? Model::LOCKMODE_LOCK : $lock_mode;
		}
	}