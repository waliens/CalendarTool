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
	 * @param[in] array $keys  The keys to extract from $array (must be array keys)
	 * @retval array The array composed of the desired columns
	 */
	function array_columns(array &$array, array $keys)
	{
		// select columns
		$column_map_fn = function($row) use(&$keys) 
						 {
						 	$to_return = array();
						 	foreach($keys as $key)
						 		$to_return[$key] = $row[$key];
						 	return $to_return;
						 };

		return array_map($column_map_fn, $array);
	}

	/**
	 * @fn
	 * @brief Return a function for comparing two subarrays of an array based on the given subarray's index
	 * @param[in] string   $index  The index on which to compare the subarrays
	 * @param[in] function $cmp_fn A comparison function for comparing the element indexed by $index
	 * This function must have the same specification as the one below
	 * @retval function The comparison function
	 * @note A comparison function (passed as argument and returned) takes two arguments 
	 * (the two subarrays) and returns an integer : 
	 * - < 0 : if the first element is less that the second
	 * - 0   : if the elements are equal
	 * - > 0 : if the first element is greater than the second 
	 */
	function rows_compare_fn($index, $cmp_fn)
	{
		return function($elem1, $elem2) use ($index, $cmp_fn) { return $cmp_fn($elem1[$index], $elem2[$index]); };
	}