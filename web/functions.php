<?php
	
	/**
	 * @file 
	 * @brief Contains a set of useful standalone functions
	 */

	namespace ct;

	/**
	 * @brief Checks whether a session was started (session_start())
	 * @retval bool True if the session was started, false otherwise
	 */
	function session_started()
	{
		return session_id() !== "";
	}

	/**
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
	 * @brief Concatenate horizontally two arrays (array rows are concatenated)
	 * @param[in] array $array1 The first array 
	 * @param[in] array $array2 The second array
	 * @retval array The concatenated array
	 */
	function array_concat(array $array1, array $array2)
	{
		$fn = function($elem1, $elem2) 
			  {  
			  	if(!is_array($elem1))
			  		$elem1 = array($elem1);
			  	if(!is_array($elem2))
			  		$elem2 = array($elem2);

			  	return $elem1 + $elem2;
			  };

		return array_map($fn, $array1, $array2);
	}

	/**
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

	/**
	 * @brief Return the subarray of an array based on indexes
	 * @param[in] array $array The array from which to extract the keys
	 * @param[in] array $keys  The keys to extract
	 * @retval array The subarray 
	 * @note The keys that are not present in the array are ignored
	 * @note The keys in the subarray are ordered in the same order as in the $keys array
	 */
	function subarray(array &$array, array $keys)
	{
		$subarray = array();

		foreach ($keys as $key) 
			if(isset($array[$key]))
				$subarray[$key] = $array[$key];

		return $subarray;	
	}

	/**
	 * @brief Return the first element of the array
	 * @param[in] array The array
	 * @retval mixed The first element
	 * @note The function set the internal pointer of the array to its first element
	 */
	function first(array &$array)
	{
		return reset($array);
	}

	/**
	 * @brief Shuffle the given columns of the given array
	 * @param[in] array $array   The array of which some columns must be shuffled
	 * @param[in] array $columns The columns that must be shuffled
	 * @retval array The array of which the columns were shuffled
	 * @note The selected columns are shuffled together
	 */
	function shuffle_rows(array &$array, array $columns)
	{
		if(empty($array))
			return array();

		$non_shuffled = array_diff(array_keys(first($array)), $columns);

		$to_shuffle = array_columns($array, $columns);
		$not_to_shuffle = array_columns($array, $non_shuffled);

		shuffle($to_shuffle);

		return array_concat($to_shuffle, $not_to_shuffle);
	}

	/**
	 * @brief Determine on which os runs the script
	 * @retval string a string indicating the OS (among "WIN", "UNIX", "OSX" and "UNKNOWN")
	 */
	function get_OS()
	{
		if(!!stristr(PHP_OS, 'DAR'))
			return "OSX";
		elseif(!!stristr(PHP_OS, 'WIN'))
		 	return "WIN";
        elseif(!!stristr(PHP_OS, 'LINUX')) 
        	return "UNIX";
        else return "UNKNOWN";
	}

	/**
	 * Custom autoload function for spl autoloading
	 */
	function autoload($class)
	{
		$os = get_OS();
		print_r($os);

		if(!preg_match("#Smarty#", $class))
			if($os === "LINUX")
				include_once(preg_replace("#\\\\#", "/", $class).".class.php");
			else
				include_once($class);
	}

	/**
	 * @brief Flatten an multidimensionnal array
	 * @param[in] array $array The array to flatten
	 * @retval The flattened array
	 * @note Taken from 'too much php' post on stackoverflow (url: http://goo.gl/UUCMTp)
	 */
	function array_flatten(array $array)
	{
	    $return = array();
	    array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
	    return $return;
	}
	