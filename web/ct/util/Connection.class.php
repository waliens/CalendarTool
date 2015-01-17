<?php

	/**
	 * @file
	 * @brief Connection class
	 */

	namespace ct\util;

	use model\LoginModel;

	/**
	 * @class Connection
	 * @brief A class for handling connection to the website
	 */
	class Connection
	{
		private $is_connected = false;
		private $login;
		private $pass;
		private $user; 

		/**
		 * @brief Construct a Connection objects
		 */
		public function __construct()
		{
			if(!$this->session_ok())
				session_start();
			/*
			$log_mod = new LoginModel();
			
			if(isset($_SESSION['user'], $_SESSION['login'], $_SESSION['pass']) 
					&& $log_mod->checkPassword($_SESSION['login'], $_SESSION['pass']))
			{
				// check connexion data in the database
				$this->login = $_SESSION['login'];
				$this->pass = $_SESSION['pass'];
				$this->user = $_SESSION['user'];
				$this->is_connected = true;
			}
			else
				$this->is_connected = false;
			*/
		}

		public function get_user()
		{
			if(!$this->isConnected())
				throw new Exception("User not connected");
		
			return $this->login;
		}

		/**
		 * @brief Check whether the user is connected
		 * @retval bool True if the user is connected, false otherwise
		 */
		public function is_connected()
		{
			return $this->is_connected;
		}

		/**
		 * @brief Check whether the session is started
		 * @retval bool True if the session was started, false otherwise
		 */
		private function session_ok()
		{
			return session_id() !== "";
		}

		/**
		 * @brief Connects the user
		 * @param[in] string $login The login of the user to connect
		 * @param[in] string $pass ????
		 * @retval bool True if the connection has succeeded, false otherwise
		 */
		public function connect($login, $pass)
		{
			$log_mod = new LoginModel();

			if($log_mod->checkPassword($login, $pass))
			{
				if(!$this->session_ok())
					session_start();

				$user = $log_mod->getUserByLogin($login);

				$_SESSION['login'] = $login;
				$_SESSION['pass'] = $pass;
				$_SESSION['user'] = $user['Id_User'];

				$this->login = $login;
				$this->pass = $pass;
				$this->user = $user['Id_User'];
				$this->is_connected = true;

				return true;
			}

			return false;
		}

		/**
		 * @brief Disconnect the current user
		 * @retval bool True if the user was successfully disconnected, false otherwise
		 */
		public function disconnect()
		{
			if(!$this->session_ok())
				session_start();

			$_SESSION = array();

			session_destroy();

			return true;
		}

		/**
		 * @brief Return information about the currently connected user
		 * @retval array The user information
		 */
		public function userConnected()
		{

		}
	}