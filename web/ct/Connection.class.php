<?php

	/**
	 * @file
	 * @brief Contains the Connection class
	 */

	namespace ct;

	use util\superglobals\Superglobal;
	use util\superglobals\SG_Session;
	use util\superglobals\SG_Post;
	use util\Redirection;
	use ct\models\RootModel;
	use ct\models\UserModel;

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
		private $remote_user; /**< @brief The ULg id of the remote user (null, if it is not set) */
		private $host; /**< @brief The address of server that has issued the request */
		private $user_mod; /**< @brief The user model */

		/**
		 * @brief Return the singleton instance of the Connection class
		 * @retval Connection The instance of Connection
		 * @note The instantiation can trigger a 401 HTTP error if the server issuing the request is invalid
		 * @note The instantiation redirect the user to the logout page if the authentication fails
		 */
		public static function get_instance()
		{
			if(self::$instance == null)
				self::$instance = new Connection();
			return self::$instance;
		}

		/**
		 * @brief Construct a Connection objects
		 * @note Trigger a 401 HTTP error if the server issuing the request is not the ULg SSO
		 * @note Redirect the user to the ulg logout page if the authentication fails
		 * 
		 * @todo enable proper server checking and authentication 
		 */
		private function __construct()
		{
			$this->sess = new SG_Session();
			$this->user_mod = new UserModel();

			// set the http headers variables
			$this->extract_http_headers();
			$this->remote_user = "u216357";
			$this->host = "";
			$this->connect($this->remote_user);
			// check host
			// if(!$this->check_host()) // host different from the reverse proxy 
			// {
			// 	http_response_code(401);
			// 	exit();
			// }

			if(!$this->is_connected()) // no previous connection
				$this->connect($this->remote_user);
			else if($this->user_ulg_id() !== $this->remote_user || !$this->user_mod->user_exists($this->user_ulg_id())) 
				$this->disconnect(); // previous ulg id doesn't match the current or user does not exists
		}

		/**
		 * @brief Extract the given headers from the http request headers and initialize the corresponding
		 * class variables
		 * @note The variables initialized are 'remote_user' and 'host'
		 */
		private function extract_http_headers()
		{
			$this->remote_user = null;
			$this->host = null;

			foreach (getallheaders() as $key => $value) 
				switch (strtolower($key)) 
				{
				case 'x-remote-user':
					$this->remote_user = $value;
					break;
				case 'host':
					$this->host = $value;
					break;
				}
 		}

 		/**
 		 * @brief Check if the server issuing the request is the ULg sso
 		 * @retval bool True if the host is valid, false otherwise
 		 */
 		private function check_host()
 		{
 			return true;// $this->host === "reverse_proxy_ip";
 		}

 		/**
 		 * @brief Check for root identification data in the post array
 		 * @retval bool True if there are connection data, false otherwise
 		 */
 		public function check_for_root_data()
 		{
 			$sg_post = new SG_Post();
 			return $sg_post->check("root_login") == Superglobal::ERR_OK 
 					&& $sg_post->check("root_pass") == Superglobal::ERR_OK;
 		}

		/**
		 * @brief Checks whether the user is already connected for the given session
		 * @retval bool True if an user is connected for the current session, false otherwise
		 */
		public function is_connected()
		{
			return $this->sess->check("ulg_id") == Superglobal::ERR_OK 
					&& $this->sess->check("user_id") == Superglobal::ERR_OK;
		}

		/**
		 * @brief Checks whether the user is connected as root
		 * @retval bool True on success, false on error
		 */
		public function is_connected_as_root()
		{
			return $this->sess->check("root_id") == Superglobal::ERR_OK;
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
		public function user_ulg_id()
		{
			if(!$this->is_connected())
				throw new \Exception("User not connected");
		
			return $this->sess->value("ulg_id");
		}

		/**
 		 * @brief Return the user id of the currently connected user
 		 * @retval string the user id 
 		 */
		public function user_id()
		{
			if(!$this->is_connected())
				throw new \Exception("User not connected");
		
			return $this->sess->value("user_id");
		}

		/**
		 * @brief Connects the user 
		 * @param[in] string $ulg_id The login of the user to connect
		 * @note Disconnect the user on error (implies a redirection to the ulg logout page)
		 * @note The ulg_id and the user_id are initialized in the session superglobal array 
		 */
		private function connect($ulg_id)
		{
			$_SESSION['ulg_id'] = $ulg_id;

			// try to create an user if necessary
			if(!$this->user_mod->user_exists($ulg_id) && !$this->user_mod->create_user($ulg_id))
				$this->disconnect();

			$_SESSION['user_id'] = $this->user_mod->get_user_id_by_ulg_id($ulg_id);
		}

		/**
		 * @brief Try to connect the root user
		 * @param[in] string $root_login The login of the root user
		 * @param[in] string $root_pass  The password of the root user
		 */
		private function connect_root($root_login, $root_pass)
		{
			$root_mod = new RootModel();

			if(!$this->root_mod->check_root_auth($root_login, $root_pass))
				return false;

			$_SESSION['root_id'] = $this->root_mod->get_root_id($root_login, $root_pass);

			return true;
		}

		/**
		 * @brief Disconnect the current user and redirect him to the ULg logout page
		 */
		public function disconnect()
		{
			$_SESSION = array();

			session_destroy();

			//new Redirection("http://www.intranet.ulg.ac.be/logout");
			exit();
		}

		/**
		 * @brief Disconnect the current user's root connection
		 */
		public function disconnect_root()
		{
			unset($_SESSION['root_id']);
		}

		/**
		 * @brief Check whether the user is a student
		 * @retval bool True if the user is a student, false otherwise
		 */
		public function user_is_student()
		{
			return preg_match("#^[sS][0-9]{6}$#", $this->user_ulg_id());
		}

		/**
		 * @brief Check whether the user is a faculty staff
		 * @retval bool True if the user is a faculty staff member, false otherwise
		 */
		public function user_is_faculty_staff()
		{
			return preg_match("#^[uU][0-9]{6}$#", $this->user_ulg_id());
		}
	}