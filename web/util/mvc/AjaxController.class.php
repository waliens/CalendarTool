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
		
		/**
		 * @brief Constructs the AjaxController object
		 */
		public function __construct()
		{
			parent::__construct();

			$this->output_data = array("error" => "");
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
		 * @brief Set the content of the error field to return 
		 * @param[in] array|string The error to return to the client
		 */
		protected function set_error($error)
		{
			$this->output_data['error'] = $error;
		}
	}
