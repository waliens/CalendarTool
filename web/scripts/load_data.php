<?php

	/**
	 * @file
	 * @brief This script loads the content of the file downloaded from the ulg server into the database
	 * The files must be named as follows : 
	 * - cours.txt : information about courses
	 * - cursus.txt : list of students and their courses
	 * - enseignant.txt : list of courses and their teachers
	 * - seqform.txt : information about pathways
	 */

	// inclusions
	require_once("../util/database/Database.class.php");
	require_once("../util/database/SQLAbstract.class.php");
	require_once("../util/database/SQLAbstract_PDO.class.php");

	// namespace
	use ct/util/database/Database as Database;
	use ct/util/database/SQLAbstract_PDO as SQLAbs;

	// functions 
	/** 
	 * @brief Read a formatted file into an array
	 * @param[in] string $file Filename
	 * @param[in] string $sep  The string separating the fields in the file
	 * @retval array The array on success, null on error
	 * The file's first line must contain the name of the fields separated by $sep
	 * and the other lines containes the fields' values to insert into each row of
	 * the array.
	 */
	function file_into_array($file, $sep = "\t")
	{
		// load file content as string
		$file_content = file_get_contents($file);

		if(!$file_content)
		{
			echo "Error : cannot open file '".$file."'";
			return null;
		}			return null;

		$content_array = explode(PHP_EOL, $file_content);

		if(count($content_array) <= 1)
		{
			echo "Error : empty file '".$file."'";
			return null;
		}

		// extract content
		$indexes = explode($sep, $content_array[0]);
		$n_indexes = count($indexes); // count the number of indexes for each row
		$ret_array = array();
 
 		// iterates over the content rows in the file
		for($i = count($file_content) - 1; $i > 0; --$i)
		{ 
			$exploded_row = explode($sep, $content_array[$i]);
			$curr_row = array();
			
			// insert the current element at the right index
			foreach($j = 0; $j < $n_indexes; ++$j)
				$curr_row[$indexes[$j]] = $exploded_row[$j];

			// add row in the array to be returned
			$ret_array[] = $curr_row;
		}

		return $ret_array;
	}

	// database connection
	$db = new Database("", "", "localhost", "calendar_tool");
	$sql_abs = SQLAbs::buildByPDO($db->get_handle());



