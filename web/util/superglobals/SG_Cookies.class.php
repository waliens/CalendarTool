<?php

	/**
	 * @file
	 * @brief Contains the class SG_Cookies
	 */

	namespace util\superglobals;

	/**
	 * @class SG_Cookies
	 * @brief Class for handling the $_COOKIES superglobal using the Superglobal class interface
	 */
	class SG_Cookies extends Superglobal
	{
		/**
		 * @brief Constructor
		 */
		public function __construct()
		{
			$this->superglobal = &$_COOKIES;
		} 
	}