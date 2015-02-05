<?php

	/**
	 * @file
	 * @brief Connection class
	 */

	namespace ct;

	use util\superglobals\Superglobal as Superglobal;
	use util\superglobals\SG_Session as SG_Session;

	use util\Redirection as Redirection;

	/**
	 * @class Connection
	 * @brief A class for handling an user connection and accessing information about the currently connected user
	 * @note This class encapsulates the security logic related to the user authentication and disconnection
	 * @todo End the implementation of the constructor (needs a model)
	 */
	class Connection
	{
		private static $instance = null; /**< @brief Singleton instance of the class */
		private $sess; /**< @brief : a SG_Session object */

		/**
		 * @brief Return the singleton instance of the Connection class
		 * @retval Connection The instance of Connection
		 */
		public static function get_instance()
		{
			if(self::$instance == null)
				self::$instance = new Connection();
			return self::$instance;
		}

		/**
		 * @brief Construct a Connection objects
		 */
		private function __construct()
		{
			$this->sess = new SG_Session();

			$ulg_id = $this->extract_ulg_id_from_http();
			
			if(!$this->is_connected()) // no previous connection
				$this->connect($ulg_id);
			else if($this->user_id() !== $ulg_id) // previous ulg id doesn't match the current
				$this->disconnect();
			else
			{
				$this->connect($ulg_id);

				if(/* attemp to connect to root and valid ids*/)
					$this->connect_root(/* root id */);
				elseif(/* invalid password */)
					$this->disconnect_root();
			}
		}

		/**
		 * @brief Extract the connected user's id from the HTTP request header 
		 * @retval string The ulg id of the connected user, an empty string if there is none
		 * @todo specify the format of the ulg id returned by the function
		 */
		private function extract_ulg_id_from_http()
		{
			$http_headers = array();

			foreach (getallheaders() as $key => $value) 
				$http_headers[strtolower($key)] = $value;

			return isset($http_headers['x-remote-user']) ? $http_headers['x-remote-user'] : "";
 		}

		/**
		 * Checks whether an user is already connected for the given session
		 * @retval bool True if an user is connected for the current session, false otherwise
		 */
		public function is_connected()
		{
			return $this->sess->check("ulg_id") == Superglobal::ERR_OK;
		}

		/**
		 * @brief Check whether the user is connected as root
		 * @retval bool True if the user is connected as root, false otherwise
		 */
		public function is_root()
		{
			return $this->sess->check("root_id");
		}

 		/**
 		 * @brief Return the ulg id of the currently connected user
 		 * @retval string the ulg id 
 		 */
		public function user_id()
		{
			if(!$this->is_connected())
				throw new Exception("User not connected");
		
			return $this->sess->value("ulg_id");
		}

		/**
		 * @brief Connects the user
		 * @param[in] string $ulg_id The login of the user to connect
		 */
		private function connect($ulg_id)
		{
			$_SESSION['ulg_id'] = $ulg_id;
		}

		/**
		 * @brief Connects the user as root
		 * @param[in] string $root_id The id of the root user
		 */
		private function connect_root($root_id)
		{
			$_SESSION['root_id'] = $root_id;
		}

		/**
		 * @brief Disconnect the current user and redirect him to the ULg logout page
		 */
		public function disconnect()
		{
			$_SESSION = array();

			session_destroy();

			new Redirection("http://www.intranet.ulg.ac.be/logout");
		}

		/**
		 * @brief Disconnect the current user's root connection
		 */
		public function disconnect_root()
		{
			unset($_SESSION['root_id']);
		}
	}