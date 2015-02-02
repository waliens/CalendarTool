<?php
	
	/**
	 * @file 
	 * @brief Contains a set of useful standalone functions
	 */

	namespace ct;

	/**
	 * @fn
	 * @brief Checks whether a session was started (session_start())
	 * @retval bool True if the session was started, false otherwise
	 */
	function session_started()
	{
		return session_id() !== "";
	}

	/**
	 * @fn
	 * @brief Return an array composed of the given column of the input array
	 * @param[in] array $array The array from which the column will be extracted
	 * @param[in] array $keys  The keys to extract from $array
	 * @retval array The array composed of the desired columns
	 */
	function array_columns(array &$array, array $keys)
	{
		$column_map_fn = function($row) use(&$keys) 
						 {
						 	$to_return = array();
						 	foreach($keys as $key)
						 		$to_return[$key] = $row[$key];
						 	return $to_return;
						 }

		return array_map($column_map_fn, $array);
	}