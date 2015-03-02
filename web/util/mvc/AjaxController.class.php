<?php
	
	/**
	 * @file
	 * @brief Contains the AjaxController class
	 */

	namespace util\mvc;

	/**
	 * @class AjaxController
	 * @brief A base class for any controller which is supposed to handle ajax request
	 */
	abstract class AjaxController extends Controller
	{
		protected $output_data; /**< @brief Array where to store the data to send back as JSON to the client */
		private $error_msgs; /**< @brief Array mapping error code and pre-defined error messages */
		
		/* 000 : generic error */
		const ERROR = 0;
		/* 3xx : missing data */
		const ERROR_MISSING_DATA = 300; /**< @brief Missing data generic error (300) */
		const ERROR_MISSING_USER = 301; /**< @brief User is missing */
		const ERROR_MISSING_EVENT = 302; /**< @brief Event is missing */
		const ERROR_MISSING_GLOBAL_EVENT = 303; /**< @brief Missing global event */

		/* 4xx : access error (user has not the given rights) */
		const ERROR_ACCESS_DENIED = 400; /**< @brief Access denied generic error (400) */
		const ERROR_ACCESS_PROFESSOR_REQUIRED = 401; /**< @brief Access denied because the user is not a professor */
		const ERROR_ACCESS_STUDENT_REQUIRED = 402; /**< @brief Access denied because the user is not a student */
		const ERROR_ACCESS_ROOT_REQUIRED = 403; /**< @brief Access denied because the user is not the root */

		/**
		 * @brief Constructs the AjaxController object
		 */
		public function __construct()
		{
			parent::__construct();

			$this->output_data = array("error" => "");
			$this->set_error_msg_array();
		}

		/**
		 * @brief Set the pre defined error message array
		 * @note The array map error code with an subarray. This latter contains two keys "EN" and "FR" mapping
		 * respectively the error message in english and french
		 */
		private function set_error_msg_array()
		{
			$this->error_msgs = array();

			// $this->error_msgs[] = array("EN" => "", "FR" => "");

			/* 000 : no error */
			$this->error_msgs[self::ERROR]
				= array("EN" => "An error occurred.", 
						"FR" => "Une erreur s'est produite.");

			/* 300 : missing */
			$this->error_msgs[self::ERROR_MISSING_DATA] 
				= array("EN" => "Missing data : the data you're looking for was not found.", 
						 "FR" => "Données manquantes : les données recherchées sont introuvables.");

			$this->error_msgs[self::ERROR_MISSING_USER] 
				= array("EN" => "User not found : the user you're looking for was not found.", 
						"FR" => "Utilisateur manquant : l'utilisateur recherché n'a pas été trouvé.");

			$this->error_msgs[self::ERROR_MISSING_EVENT] 
				= array("EN" => "Event not found : the event you were looking for wasn't found", 
						"FR" => "Evénement manquant : l'événement recherché n'a pas été trouvé.");

			$this->error_msgs[self::ERROR_MISSING_GLOBAL_EVENT] 
				= array("EN" => "Global event not found : the global event you're looking for wasn't found.", 
						"FR" => "Evénement global manquant : l'événement global recherché n'a pas été trouvé.");

			/* 400 : access denied */
			$this->error_msgs[self::ERROR_ACCESS_DENIED] 
				= array("EN" => "Access denied : these information are not accessible from your account.", 
						 "FR" => "Accès refusé : ces données ne sont pas accessible depuis votre compte.");

			$this->error_msgs[self::ERROR_ACCESS_ROOT_REQUIRED] 
				= array("EN" => "Access denied : you must be the root user to access these information.", 
						"FR" => "Accès refusé : vous devez être l'utilisateur root pour accéder à ces données.");

			$this->error_msgs[self::ERROR_ACCESS_STUDENT_REQUIRED] 
				= array("EN" => "Access denied : you must be a student to access these information.", 
						"FR" => "Accès refusé : vous devez être un étudiant pour accéder à ces données.");

			$this->error_msgs[self::ERROR_ACCESS_PROFESSOR_REQUIRED] 
				= array("EN" => "Access denied : you must be a professor to access these information.", 
						"FR" => "Accès refusé : vous devez être un professeur pour accéder à ces données.");
		}

		/**
		 * @brief Check whether the given error code is valid
		 * @param[in] int $error_code The error code to check
		 * @retval bool True if the error code is valid, false otherwise
		 */
		private function is_valid_error_code($error_code)
		{
			return $error_code == 0 || ($error_code >= 400 && $error_code < 403); 
		}

		/**
		 * @brief Convert a JSON string to a PHP array
		 * @param[in] string $json The JSON string
		 * @retval array The PHP array containing the data from the JSON string
		 */
		final protected function json2array($json)
		{
			return json_decode($json);
		} 

		/**
		 * @brief Convert a PHP array to a JSON string
		 * @param[in] array $array The PHP array
		 * @retval string The JSON string containing the data from the PHP array
		 */
		final protected function array2json(array &$array)
		{
			return json_encode($array);
		}

		/**
		 * @copydoc Controller::get_output
		 */
		public function get_output()
		{
			return $this->array2json($this->output_data);
		}

		/**
		 * @brief Checks whether the currently connected user has access to the ressource
		 * @retval bool True if the user has access to the ressource, false otherwise
		 * @note Default behavior is to give access to everything. Should be reimplemented according to
		 * the check that has to be performed
		 */
		protected function has_access()
		{
			return true;
		}

		/**
		 * @brief Set the content of the error fields to return 
		 * @param[in] array|string The error to return to the client
		 * @param[in] int 		   The error code for the given error
		 */
		private function set_error($error, $code)
		{
			$this->output_data['error'] = $error;
			$this->output_data['error_code'] = $code;
		}

		/**
		 * @brief Set the content of the error fields to return
		 * @param[in] int $error_code One of the class ERROR* constant specifying the error
		 */
		protected function set_error_predefined($error_code)
		{
			if(!$this->is_valid_error_code($error_code))
				return;

			$this->set_error($this->error_msgs[$error_code], $error_code);
		}
	}
