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
	 * @param[in] array $array The array
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
	 * @brief Custom autoload function for spl autoloading
	 */
	function autoload($class)
	{
		if(preg_match("#Smarty#", $class))
			include_once("util/Smarty/libs/Smarty.class.php");
		elseif(preg_match("#phpSec#", $class))
			include_once("util/".preg_replace("#\\\\#", "/", $class).".class.php");
		else
			include_once(preg_replace("#\\\\#", "/", $class).".class.php");
	}

	/**
	 * @brief Flatten an multidimensionnal array
	 * @param[in] array $array The array to flatten
	 * @retval The flattened array
	 * @note Taken from 'too much php' post on stackoverflow (url: http://goo.gl/UUCMTp)
	 */
	function array_flatten(array& $array)
	{
	    $return = array();
	    array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
	    return $return;
	}	

	/**
	 * @brief Checks whether the string starts with a given string
	 * @param[in] string $haystack The string to check
	 * @param[in] string $needle   The prefix to check for
	 * @retval bool True if $needle is a prefix of $haystack, false otherwise
	 */
	function starts_with($haystack, $needle)
	{
		return strstr($haystack, $needle) === $haystack;
	}

	/**
	 * @brief Checks whether the string ends with a given string
	 * @param[in] string $haystack The string to check
	 * @param[in] string $needle   The suffix to check for
	 * @retval bool True if $needle is a suffix of $haystack, false otherwise
	 */
	function ends_with($haystack, $needle)
	{
		return strpos($haystack, $needle) === (strlen($haystack) - strlen($needle));
	}

	/**
	 * @brief Return an array composed of which the elements are the key and value of $array's elements
	 * glued with $glue
	 * @param[in] array  $array The input array
	 * @param[in] string $glue  The glue string
	 * @retval array The output array containing the glued key-value
	 */
	function array_key_val_merge(array &$array, $glue="")
	{
		$out_array = array();
		array_walk($array, function(&$val, $key) use (&$out_array, $glue) { $out_array[] = $key.$glue.$val; });
		return $out_array;
	}

	/**
	 * @brief Checks whether the given date is valid (i.e. it is not a 30th of February for instance)
	 * @param[in] string $date The string containing a date in the SQL format (YYYY-MM-DD)
	 * @retval bool True if the date is valide, false otherwise
	 * @note The date can be contained in a wider string containing other items than a date 
	 */
	function date_exists($date)
	{
		$matches = array();

		if(!preg_match("#([0-9]{4})-([0-9]{2})-([0-9]{2})#", $date))
			return false;

		if(!empty($matches))
			return false;

		return checkdate($matches[2], $matches[3], $matches[1]);
	}

	/** 
	 * @brief Compare two datetime  
	 * @param[in] string $date1 First datetime
	 * @param[in] string $date2 Second datetime
	 * @retval int A value < 0 if $date1 < $date2, > 0 if $date1 > $date2 and 0 if the datetime are equal
	 */
	function date_cmp($date1, $date2)
	{
		$d1 = strtotime($date1);
		$d2 = strtotime($date2);

		if($d1 < $d2)
			return -1;
		elseif($d1 > $d2)
			return 1;
		else
			return 0;
	}

	/**
	 * @brief Return the year starting the current academic year
	 * @retval int The starting academic year
	 */
	function get_academic_year()
	{
		return date("n") < 9 || date("j") < 12 ? date("Y") - 1 : date("Y");
	}

	/**
	 * @brief Function for converting a sql date(time) to a french date format
	 * @param[in] string $sql_date The sql date(time)
	 * @param[in] string $sep 	   The separator to place between the date elements
	 * @retval string The formatted date(time)
	 */ 
	function date_sql2fr($sql_date, $sep="/")
	{
		$matches = array();

		if(!preg_match("#^([0-9]{4})-([0-9]{2})-([0-9]{2})(.*)#", $sql_date, $matches))
			return $sql_date;

		return $matches[3].$sep.$matches[2].$sep.$matches[1].$matches[4];
	}

	/** 
	 * @brief Function for converting a french formatted date(time) in the sql date(time) format
	 * @param[in] string $fr_date The fr date(time)
	 * @retval string The formatted date(time)
	 */
	function date_fr2sql($fr_date)
	{
		$matches = array();

		if(!preg_match("#^([0-9]{2})[/-]([0-9]{2})[/-]([0-9]{4})(.*)#", $fr_date, $matches))
			return $sql_date;

		return $matches[3]."-".$matches[2]."-".$matches[1].$matches[4];
	}

	/**
	 * @brief Check whether the argument is a positive integer differenet from 0
	 * @param[in] mixed $int Check whether the given argument is a strictly positive integer
	 * @retval bool True if $int is an integer > 0, false otherwise 
	 */
	function is_positive_integer($int)
	{
		return is_int($int) && $int > 0;
	}

	/**
	 * @brief Check whether the given data can be a database integer key
	 * @param[in] mixed $data The data to check;
	 * @retval bool True if it is valid, false otherwise
	 */
	function is_valid_id($data)
	{
		return is_numeric($data) && intval($data) > 0;
	}

	/**
	 * @brief Return the given multi-dimensionnal array from which the column having the given index
	 * was modified with the given callback function
	 * @param[in] array    $array  		The array to modify
	 * @param[in] function $callback    The callback function to apply to the column
	 * @param[in] string   $column_name The column to modify
	 * @retval The modified array
	 */
	function array_col_map(array& $array, $callback, $column_name)
	{
		$fn = function($row) use ($callback, $column_name) 
			  {
			  	$row[$column_name] = $callback($row[$column_name]);
			  	return $row;
			  };

		return array_map($fn, $array); 
	}

	/**
	 * @brief Duplicate the given array n times
	 * @param[in] array $array The array to duplicate (must not contain string keys)
	 * @param[in] int   $n     The number of duplication
	 * @retval array The duplicated array
	 * 
	 * Example :
	 * @code
	 * $array = array(1, 2);
	 * // $dup_array is (1, 2, 1, 2);
	 * $dup_array = array_dup($array, 2);
	 * @endcode
	 */
	function array_dup(array &$array, $n)
	{
		$out_array = $array;
		for($i = 1; $i < $n; ++$i)
			$out_array = array_merge($out_array, $array);
		return $out_array;
	}
	
	/**
	 * @brief instantiate a specific Event model according to the params
	 * @param string $type the type of the event model
	 * @retval mixed an instance of $type.EventModel false otherwise
	 */
	function instantiateEventModel($type){
		switch($type){
			case "Independent":
				return new IndependentEventModel();
				break;
			case "Academic":
				return new AcademicEventModel();
				break;
			case "Student":
				return new StudentEventModel();
				break;
			case "Sub":
				return new SubEventModel();
				break;
			case "Event":
				return new EventModel();
				break;
			default :
				return false;
				break;
		}
	}

	/**
	 * @brief Transform a one-dimensionnal array to transform 
	 * @param[in] array $array     The one-idimensionnal array to transform 
	 * @param[in] array $transform Array specifying which transformations must be executed on the array
	 * @note The operations exposed by the function are item removal and key replacement
	 * @note The transform array must be formatted as for the darray_transform function
	 * @note The transform array must be formatted as follows : its keys are the keys to keep from $array and they must map
	 * the new key name or an empty string if the key name must not change
	 */
	function array_keys_transform(array &$array, $transform)
	{
		$out = array();
		$transform_fn = function($val, $key) use (&$out, $transform)
						{
							if(array_key_exists($key, $transform)) // add element if the key exists
							{
								// 
								$new_key = empty($transform[$key]) ? $key : $transform[$key];
								$out[$new_key] = $val;
							}
						};

		array_walk($array, $transform_fn);
		return $out;
	}

	/**
	 * @brief Transform a database-like array (see below). 
	 * @param[in] array $array     The array to transform 
	 * @param[in] array $transform Array specifying which transformations must be executed on the array
	 * @note The operations exposed by the function are the selection and renaming of columns
	 * @note A database-like array is a two dimensionnal array (array of arrays) of which each
	 * subarrays contains the same keys. 
	 */
	function darray_transform(array &$array, $transform)
	{
		return array_map(function(&$row) use (&$transform) { return array_keys_transform($row, $transform);},
						 $array);
	}

	/**
	 * @brief Transform the item in a column of a given database-like array (see below) with a callback
	 * @param[in] array    $array    The database like array of which a column must be modified
	 * @param[in] string   $col      The column name
	 * @param[in] callback $callback The callback function taking the value to modify and returning the new value
	 */
	function darray_col_map(array &$array, $col, $callback)
	{
		foreach($array as &$row)
			$row[$col] = $callback($array[$col]);
	}
