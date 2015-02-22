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
		protected $input_data; /**< @brief Array containing input data converted from a JSON string (array is left empty there was no input data) */ 
		protected $input_json; /**< @brief String containing the json string given as input (empty string if there was no input JSON) */
		protected $output_data; /**< @brief Array where to store the data to send back as JSON to the client */
		
		/**
		 * @brief Constructs the AjaxController object
		 */
		public function __construct()
		{
			parent::__construct();

			$this->input_json = file_get_contents("php://input");

			if($this->has_json_input())
				$this->input_data = $this->json2array($this->input_json);

			$this->output_data = array();
		}

		/** 
		 * @brief Checks whether there was a json input in the request
		 * @retval True if there was a json input, false otherwise
		 */
		final protected function has_json_input()
		{
			return !empty($this->input_json);
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
	}
