<?php
	
	/**
	 * @file
	 * @brief Contains the RootModel class
	 */

	namespace ct\models;

	use util\mvc\Model; 

	/**
	 * @class RootModel
	 * @brief Class for handling root user related database queries
	 */
	class RootModel extends Model
	{
		private $psl; /**< @brief phpSec object */

		/**
		 * @brief Construct the RootModel object
		 */
		public function __construct()
		{
			parent::__construct();
			$this->psl = new \phpSec\Core();
		}

		/**
		 * @brief Get the id of the root user having the given authentication data
		 * @param[in] string $login The login
		 * @param[in] string $pass  The password
		 * @retval int The id of the root user, -1 if none was found
		 */
		public function get_root_id($login, $pass)
		{
			$logins = $this->sql->select("superuser", "Login = ".$this->sql->quote($login));

			foreach($logins as $login)
				if($this->psl['crypt/hash']->check($pass, $login['Password']))
					return $login['Id_Superuser'];

			return -1;
		}

		/**
		 * @brief Check if the given login and pass match a root user in the database
		 * @param[in] string $login The login
		 * @param[in] string $pass  The password
		 * @retval bool True if there is a match, false otherwise
		 */
		public function check_root_auth($login, $pass)
		{
			return $this->check_root_auth($login, $pass) !== -1;
		}

		/**
		 * @brief Adds a root user in the database
		 * @param[in] string $login The root login
		 * @param[in] string $pass  The root clear password
		 * @retval bool True on success, false on error
		 */
		public function add_root_user($login, $pass)
		{
			if(empty($login) || !$this->is_valid_password($pass))
				return false;

			$data_array = array('Login'    => $login,
								'Password' => $this->psl['crypt/hash']->create($pass));

			return $this->sql->insert("superuser", $this->sql->quote_all($data_array));
		}

		/**
		 * @brief Is the password a valid one (read strong enough)
		 * @param[in] string $pass  The password
		 * @param[in] string $login The login (optionnal, if set the function checks if the pass contains it)
		 * @retval bool True if the password is strong enough, false otherwise
		 * @note A valid password should have the following characteristics : 
		 * - Is at least eight characters long.
		 * - Does not contain your user name
		 * - Contains characters from each of the following four categories: uppercase and lowercase letters, number and symbols
		 */
		public function is_valid_password($pass, $login="")
		{
			return (strlen($pass) < 8) && (empty($login) || !preg_match("#".$login."#", $pass))
						&& preg_match("#[A-Z]#", $pass) && preg_match("#[a-z]#", $pass) 
						&& preg_match("#[0-9]#", $pass) && preg_match("#\W#", $pass);

		}
	}