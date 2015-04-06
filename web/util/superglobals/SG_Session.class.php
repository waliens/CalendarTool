<?php

	/**
	 * @file
	 * @brief Contains the class SG_Session
	 */

	namespace util\superglobals;


	/**
	 * @class SG_Session
	 * @brief Class for handling the $_SESSION superglobal using the Superglobal class interface
	 */
	class SG_Session extends Superglobal
	{
		/**
		 * @brief Constructor
		 */
		public function __construct()
		{
			parent::__construct();
			if(!\ct\session_started())
				session_start();
			
			$this->superglobal = &$_SESSION;
		}
	}