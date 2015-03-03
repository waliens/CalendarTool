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
		const ERROR_OK = 0; /**< @brief No error */
		const ERROR = 1; /**< @brief Generic error */

		/* 2xx : operation failure */ 
		const ERROR_ACTION_FAILURE = 200; /**< @brief Action failure */
		const ERROR_ACTION_ADD_DATA = 201; /**< @brief Action failure : cannot add data */
		const ERROR_ACTION_UPDATE_DATA = 202; /**< @brief Action failure : cannot update data */
		const ERROR_ACTION_DELETE_DATA = 203; /**< @brief Action failure : cannot delete data */
		const ERROR_ACTION_READ_DATA = 204; /**< @brief Action failure : cannot read data */

		/* 3xx : missing data */
		const ERROR_MISSING_DATA = 300; /**< @brief Missing data generic error (300) */
		const ERROR_MISSING_USER = 301; /**< @brief User is missing */
		const ERROR_MISSING_EVENT = 302; /**< @brief Event is missing */
		const ERROR_MISSING_GLOBAL_EVENT = 303; /**< @brief Missing global event */
		const ERROR_MISSING_ID = 304; /**< @brief The id is missing */

		/* 4xx : access error (user has not the given rights) */
		const ERROR_ACCESS_DENIED = 400; /**< @brief Access denied generic error (400) */
		const ERROR_ACCESS_PROFESSOR_REQUIRED = 401; /**< @brief Access denied : only a professor can perform this operation  */
		const ERROR_ACCESS_STUDENT_REQUIRED = 402; /**< @brief Access denied : only a student can perform this operation */
		const ERROR_ACCESS_ROOT_REQUIRED = 403; /**< @brief Access denied : only the root user can perform this operation */

		/**
		 * @brief Constructs the AjaxController object
		 */
		public function __construct()
		{
			parent::__construct();

			$this->output_data = array();
			$this->set_error("", self::ERROR_OK);
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

			/* 000 : no error */
			$this->error_msgs[self::ERROR]
				= array("EN" => "An error occurred.", 
						"FR" => "Une erreur s'est produite.");

			/* 200 : action failure */
			$this->error_msgs[self::ERROR_ACTION_FAILURE] 
				= array("EN" => "Failure : impossible to perform the requested action.", 
						"FR" => "Echec : impossible de traiter l'action demandée.");

			$this->error_msgs[self::ERROR_ACTION_ADD_DATA] 
				= array("EN" => "Failure : impossible to add the sent data.", 
						"FR" => "Echec : impossible d'ajouter les données envoyées.");

			$this->error_msgs[self::ERROR_ACTION_UPDATE_DATA] 
				= array("EN" => "Failure : impossible to update the requested data", 
						"FR" => "Echec : impossible de mettre à jour les données demandées.");

			$this->error_msgs[self::ERROR_ACTION_DELETE_DATA] 
				= array("EN" => "Failure : impossible to delete the requested data.", 
						"FR" => "Echec : impossible de supprimer les données demandées.");

			$this->error_msgs[self::ERROR_ACTION_READ_DATA] 
				= array("EN" => "Failure : impossible to fetch the requested data.", 
						"FR" => "Echec : impossible de récupérer les données demandées.");

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

			$this->error_msgs[self::ERROR_MISSING_ID] 
				= array("EN" => "Missing id : the data identifier is missing.", 
						"FR" => "Identifiant manquant : l'identifiant des données recherchées est manquant.");

			/* 400 : access denied */
			$this->error_msgs[self::ERROR_ACCESS_DENIED] 
				= array("EN" => "Access denied : these information are not accessible from your account.", 
						 "FR" => "Accès refusé : ces données ne sont pas accessible depuis votre compte.");

			$this->error_msgs[self::ERROR_ACCESS_ROOT_REQUIRED] 
				= array("EN" => "Access denied : only the root user can access these information or perform this operation.", 
						"FR" => "Accès refusé : seul le root peut accéder à ces données ou effectuer cette opération.");

			$this->error_msgs[self::ERROR_ACCESS_STUDENT_REQUIRED] 
				= array("EN" => "Access denied : only a student can access these information or perform this operation.", 
						"FR" => "Accès refusé : seul un étudiant peut accéder à ces données ou effectuer cette opération.");

			$this->error_msgs[self::ERROR_ACCESS_PROFESSOR_REQUIRED] 
				= array("EN" => "Access denied : only a professor can access these information or perform this operation.", 
						"FR" => "Accès refusé : seul un professeur peut accéder à ces données ou effectuer cette opération.");
		}

		/**
		 * @brief Check whether the given error code is valid
		 * @param[in] int $error_code The error code to check
		 * @retval bool True if the error code is valid, false otherwise
		 */
		private function is_valid_error_code($error_code)
		{
			return ($error_code >= 0 && $error_code <= 1) 
					|| ($error_code >= 200 && $error_code <= 204)
					|| ($error_code >= 300 && $error_code <= 304)
					|| ($error_code >= 400 && $error_code <= 403); 
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
