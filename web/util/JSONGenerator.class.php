<?php

	/**
	 * @file
	 * @brief Contains the JSONGenerator interface
	 */

	namespace util;

	/**
	 * @interface JSONGenerator
	 * @brief A interface implementing for any class generating data as an array and that should format it in JSON
	 */
	interface JSONGenerator
	{
		/**
		 * @brief Returns the data in the JSON format
		 * @retval string A text formatted according to the JSON standard
		 */
		public function getJSON();
	}