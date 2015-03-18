<?php

	/**
	 * @file
	 * @brief Contains the class SG_Post
	 */

	namespace util\superglobals;

	/**
	 * @class SG_Post
	 * @brief Class for handling the $_POST superglobal using the Superglobal class interface
	 */
	class SG_Post extends Superglobal
	{
		/**
		 * @brief Constructor
		 */
		public function __construct()
		{
			$this->superglobal = &$_POST;
		} 
	}