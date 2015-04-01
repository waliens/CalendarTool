<?php

	/**
	 * @file
	 * @brief Contains the class SG_Get
	 */

	namespace util\superglobals;

	/**
	 * @class SG_Get
	 * @brief Class for handling the $_GET superglobal using the Superglobal class interface
	 */
	class SG_Get extends Superglobal
	{
		/**
		 * @brief Constructor
		 */
		public function __construct()
		{
			$this->superglobal = &$_GET;
		} 
	}