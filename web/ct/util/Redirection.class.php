<?php

	/**
	 * @file 
	 * @brief Contain the Redirection class
	 */

	namespace ct\util;

	/**
	 * @class Redirection 
	 * @brief A class for executing redirections
	 */
	class Redirection
	{
		/**
		 * @brief The construction of the object triggers the redirection to the given URL
		 * @param[in] string $url The url to which the user must be redirected
		 */
		public function __construct($url)
		{
			header("Location: ".$url);
			exit();
		}
	}