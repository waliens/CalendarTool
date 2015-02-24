<?php

	/**
	 * @file
	 * @brief Contain te Superglobal class.
	 */

	namespace util\superglobals;

	/**
	 * @class Superglobal
	 * @brief This class encapsulates the access to a PHP superglobal array. 
	 * It provides methods for checking :
	 * <ul>
	 *   <li>the presence or absence of a key</li>
	 *	 <li>the validity of a value associated with a key</li>
	 * </ul>
	 */
	abstract class Superglobal
	{
		// Error constants 
		const ERR_NOT_SET = -1; /**< @brief Error : key doesn't exist */
		const ERR_CALLBACK = -2; /**< @brief Error : callback predicate returned false */
		const ERR_EMPTY = -3; /**< @brief Eror : value is "empty" */
		const ERR_OK = 1; /**< @brief No error */

		// Checking type constants : they can be combine with |
		const CHK_NONE = 0; /**< @brief Can be used in order to check the callback only */
		const CHK_ISSET = 1; /**< @brief Only check if the key exists */
		const CHK_NOT_EMPTY = 2; /**< @brief Only check if the value is empty (no isset) */
		const CHK_TRIM = 6; /**< @brief Only check if the trimmed value is empty (no isset) */
		const CHK_ALL = 7; /**< @brief Perform the isset|trim|empty check */

		// data members
		protected $superglobal; /**< @brief A reference to the superglobal array */

		/**
		 * @brief Perform a check on the given key in the superglobal
		 * @param[in] string   $key 	 The superglobal array key
		 * @param[in] int      $chk 	 Define the type of check to perform (see below) (default: null => CHK_ISSET | CHK_NOT_EMPTY)
		 * @param[in] function $callback A predicate taking the value associated with the key as argument and returning
		 * true if this value is valid, false otherwise (default: null => callback not evaluated)
		 * @retval int The negative error code specifying which check has failed (see ERR_* class negative constants) if it has failed, ERR_OK otherwise
		 * 
		 * The CHK_* flags should be used for the $chk parameter. Moreover, they can be combined with the "|" operator to specify 
		 * combination of checks.
		 * 
		 * Examples :
		 * <ul>
		 * 	<li>CHK_ALL = CHK_TRIM | CHK_ISSET</li>
		 *  <li>Check if the key is set and the value not empty = CHK_ISSET | CHK_NOT_EMPTY</li>
		 * </ul>
		 */
		public function check($key, $chk = null, $callback = null)
		{
			// dflt params
			if(\is_null($chk)) 
				$chk = Superglobal::CHK_ISSET | Superglobal::CHK_NOT_EMPTY;

			// check isset
			if($this->do_isset($chk) && !$this->is_set($key))
				return Superglobal::ERR_NOT_SET;

			// store the value
			$value = $this->value($key);

			// trim if neccessary
			if($this->do_trim($chk))
				$value = \trim($value);

			// check emptiness
			if($this->do_not_empty($chk) && $this->is_empty($value))
				return Superglobal::ERR_EMPTY;

			// apply callback
			if(!is_null($callback) && !$callback($value))
				return Superglobal::ERR_CALLBACK;

			return 1;
		}

		/**
		 * @brief Check if the value (associated with a key) is empty
		 * @param mixed $value A reference to the value to check
		 * @retval bool True if the value is empty, false otherwise
		 */
		protected function is_empty(&$value) 
		{
			return empty($value);
		}

		/**
		 * @brief Checks whether the key is set in the superglobal array
		 * @param[in] string $key The superglobal array key
		 * @retval bool True if the key is set, false otherwise
		 */
		protected function is_set($key)
		{
			return isset($this->superglobal[$key]);
		}

		/**
		 * @brief Check whether the isset check must be performed
		 * @retval bool True if the check must be performed, false otherwise
		 */
		final protected function do_isset($chk)
		{
			return $chk & Superglobal::CHK_ISSET;
		}

		/**
		 * @brief Check whether the not_empty check must be performed
		 * @retval bool True if the check must be performed, false otherwise
		 */
		final protected function do_not_empty($chk)
		{
			return $chk & Superglobal::CHK_NOT_EMPTY;
		}

		/**
		 * @brief Check whether the value must be trimmed
		 * @retval bool True if the value must be trimmed, false otherwise
		 */
		final protected function do_trim($chk)
		{
			return ($chk & 4);
		}

		/**
		 * @brief Return the value associated with the given key in the superglobal array
		 * @param[in] string $key The superglobal array key
		 */
		public function value($key)
		{
			return $this->superglobal[$key];
		}
	};