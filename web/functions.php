<?php
	
	/**
	 * @file 
	 * @brief Contains a set of useful standalone functions
	 */

	namespace ct;

	/**
	 * Checks whether a session was started (session_start())
	 * @retval bool True if the session was started, false otherwise
	 */
	function session_started()
	{
		return session_id() !== "";
	}