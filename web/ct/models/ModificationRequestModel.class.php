<?php
	
	/**
	 * @file 
	 * @brief Contains the ModificationRequestModel
	 */

	namespace ct\models;

	use util\mvc\Model;

	/**
	 * @class ModificationRequestModel
	 * @brief Class for handling database queries related to modification requests
	 */
	class ModificationRequestModel extends Model
	{
		/**
		 * @brief Construct the ModificationRequestModel object
		 */
		public function __construct()
		{
			parent::__construct();
		}

		/**
		 * @brief Checks the integrity of the data of a modification request and cleans it if necessary
		 * @param[in]  array $data 		 An array containing the data to check 
		 * @param[out] array $error_desc Contains an error description for each field which wasn't valid
		 * @retval bool True if the data were correctly formatted, false otherwise
		 * @note The $data array should be structured as follows :
		 * <ul>
		 * 	<li>event : the event id</li>
		 * 	<li>sender : the sender id</li>
		 * 	<li>status : status string</li>
		 * 	<li>description : description text</li>
		 * </ul>
		 */
		public function check_modification_request_data(array &$data, array &$error_desc)
		{
			
		}
	}