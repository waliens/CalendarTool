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

			if(!$this->is_connected()) // no previous connection
				$this->connect($this->remote_user);
			else if($this->user_ulg_id() !== $this->remote_user) // the user sending the queryis not the same as the previous one
				$this->disconnect();

			// redirect the user if he hasn't given his credentials yet
			if(!$this->user_mod->user_subscription_complete($this->user_id()) && (!isset($_GET['page']) || $_GET['page'] !== "ask_data"))
				new Redirection("index.php?page=ask_data");
		}

		/**
		 * @brief Extract the given headers from the http request headers and initialize the corresponding
		 * class variables
		 * @note The variables initialized are 'remote_user'
		 */
		private function extract_http_headers()
		{
			$this->remote_user = null;

			foreach (getallheaders() as $key => $value)
				switch (strtolower($key))
				{
				case 'x-remote-user':
					$this->remote_user = $value;
					break;
				}
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
					&& $this->sess->check("user_id") == Superglobal::ERR_OK
					&& intval($this->sess->value("user_id")) > 0;
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
			if($this->sess->check("ulg_id") < 0)
				throw new \Exception("User not connected");

			return $this->sess->value("ulg_id");
		}

		/**
 		 * @brief Return the user id of the currently connected user
 		 * @retval string the user id 
 		 */
		public function user_id()
		{
			if($this->sess->check("user_id") < 0)
				throw new \Exception("User not connected");

			return $this->sess->value("user_id");
		}

		/**
		 * @brief Connects the user 
		 * @param[in] string $ulg_id The login of the user to connect
		 * @note If the user account does not exists the function tries to create him one
		 * @note Disconnect the user on error (implies a redirection to the ulg logout page)
		 * @note The ulg_id and the user_id are initialized in the session superglobal array 
		 */
		private function connect($ulg_id)
		{
			$_SESSION['ulg_id'] = $ulg_id;

			// if the user does not exist, try to create his account
			// first try to create the user from the database
			// if it doesn't work and that the user is a faculty staff member his account is created anyway
			// otherwise disconnect the user because his account couldn't be created
			if(!$this->user_mod->user_exists($ulg_id)
				 && !$this->user_mod->create_user($ulg_id)
				 && (UserModel::is_student_id($ulg_id)
				 		|| !$this->user_mod->create_unknown_faculty_staff($ulg_id)))
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

			return session_regenerate_id(true); // renew session id to avoid session fixation attacks
		}

		/**
		 * @brief Disconnect the current user and redirect him to the ULg logout page
		 */
		public function disconnect()
		{
			$_SESSION = array();

			session_destroy();

			new Redirection("http://www.intranet.ulg.ac.be/logout");
			exit();
		}

		/**
		 * @brief Disconnect the current user's root connection
		 */
		public function disconnect_root()
		{
			unset($_SESSION['root_id']);
			session_regenerate_id(true); // regenerate id to avoid session fixation
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
