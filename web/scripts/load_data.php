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
	require_once("web/util/database/Database.class.php");
	require_once("web/util/database/SQLAbstract.class.php");
	require_once("web/util/database/SQLAbstract_PDO.class.php");

	require_once("web/functions.php");

	// namespace
	use ct\util\database\Database as Database;
	use ct\util\database\SQLAbstract_PDO as SQLAbs;

	// functions 
	/**
	 * @fn
	 * @brief Creates an array that maps the values indexed by the column index (indexing a string) and a subarray containing the rows
	 * of which the value index by column were the same (the $column_index field is removed).
	 * @param[in] array $array  	      The array to modify
	 * @param[in] array $constant_columns The indexes of the columns containing repeated values
	 * @retval array The reshaped array. If the array does not contain the index $column, the initial array is returned.
	 * @note The relative order in the subarrays might not be preserved
	 */
	function common_regroup(array $array, array $constant_columns)
	{
		if(empty($array))
			return array();

		// extract keys of the array
		$all_keys = array_keys($array[0]);
		$keys_to_extract = array_diff($all_keys, $constant_columns);

		// check whether the key exists in the array
		if(count($keys_to_extract) === count($all_keys))
			return $array;

		// sort the initial array on $column
		usort($array, ct\rows_compare_fn($constant_columns[0], "strcmp"));

		$curr_constants = array(0 => "");
		$curr_subarray = array();
		$ret_array = array();

		// create the subarrays
		foreach ($array as $row) 
		{
			$row_constants = ct\subarray($row, $constant_columns);

			if(ct\first($curr_constants) !== ct\first($row_constants)) // next subarray
			{
				if(!isset($curr_constants[0])) // not the first subarray
				{
					$new_row = $curr_constants;
					$new_row['varying'] = $curr_subarray;
					$ret_array[] = $new_row;
				}

				$curr_constants = $row_constants;
				$curr_subarray = array();
			}

			// add one new row to the subarray
			$subarray_row = array();

			foreach ($keys_to_extract as $key) 
				$subarray_row[$key] = $row[$key];

			if(!empty($subarray_row))
				$curr_subarray[] = $subarray_row;
		}

		// add last subarray
		$last_row = $curr_constants;
		$last_row['varying'] = $curr_subarray;
		$ret_array[] = $last_row;
	
		return $ret_array;
	}

	/** 
	 * @fn
	 * @brief Read a formatted file into an array
	 * @param[in] string $file Filename
	 * @param[in] string $sep  The string separating the fields in the file
	 * @retval array The array on success, null on error
	 * The file's first line must contain the name of the fields separated by $sep
	 * and the other lines containes the fields' values to insert into each row of
	 * the array.
	 */
	function file_into_array($file, $sep="\t")
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
			for($j = 0; $j < $n_indexes; ++$j)
				$curr_row[$indexes[$j]] = $exploded_row[$j];

			// add row in the array to be returned
			$ret_array[] = $curr_row;
		}

		return $ret_array;
	}

	/**
	 * @brief Insert the courses into the course table in the database
	 * @param[in] SQLAbstract_PDO $sql_abs      The sql abstract object
	 * @param[in] array& 		  $courses_data The courses data (formatted as in the cours.txt file)
	 * @retval bool True on success, false on failure
	 */
	function insert_courses(SQLAbs $sql_abs)
	{
		// get courses data from file
		$courses_data = file_to_array("cours.txt");

		// insert them into the database
		$query = "INSERT INTO ulg_course VALUES (?, ?, ?, ?, ?, ?, ?, ?);";
		$stmt = $sql_abs->prepare_query($query);

		$succes = true; // false if an error occurred

		$sql_abs->transaction();
		$success &= $sql_abs->execute_query("TRUNCATE TABLE ulg_course;");

		foreach($courses_data as $course)
		{
			$success &= $stmt->execute(array($course['code_cours'],
											 $course['lib_cours_complet'],
											 $course['lib_cours'],
											 $course['hr_th'],
											 $course['hr_pr'],
											 $course['hr_st'],
											 $course['hr_au'], 
											 $course['cod_org']));

			if(!$success)
				break;
		}

		// rollback the transaction if an error occurred
		if($success)
			$sql_abs->commit();
		else
			$sql_abs->rollback();

		$stmt->closeCursor();
		return $success;
	}

	/**
	 * @brief Insert the pathways into the pathway table in the database
	 * @param[in] SQLAbstract_PDO $sql_abs      The sql abstract object
	 * @retval bool True on success, false on failure
	 */
	function insert_pathways(SQLAbs $sql_abs)
	{
		// get pathways data from file
		$pathways_data = file_to_array("seqform.txt");

		// insert them into the database
		$query = "INSERT INTO ulg_pathway VALUES (?, ?, ?);";
		$stmt = $sql_abs->prepare_query($query);

		$success = true; // false if an error occurred

		$sql_abs->transaction();
		$success &= $sql_abs->execute_query("TRUNCATE TABLE ulg_pathway;");

		foreach($pathways_data as $pathway)
		{
			$success &= $stmt->execute(array($course['code_ae'],
											 $course['lib_ae'],
											 $course['lib_long_ae']));

			if(!$success)
				break;
		}

		// rollback the transaction if an error occurred
		if($success)
			$sql_abs->commit();
		else
			$sql_abs->rollback();

		$stmt->closeCursor();
		return $success;
	}

	/**
	 * @brief Insert the teachers into the database
	 * @param[in] SQLAbstract_PDO $sql_abs 				 The sql abstract object
	 * @param[in] array 		  $teachers_courses_info List of courses given by a teacher (id, name, surname)
	 * @retval True on success, false on error
	 */
	function insert_teachers(SQLAbs $sql_abs, array &$teachers_courses_info)
	{
		$teachers_info = ct\array_columns($teachers_courses_info, array("idulg_ens", "nom_ens", "prenom_ens"));
			
		// sort the array on the teacher ulg id
		usort($teachers_info, ct\rows_compare_fn("idulg_ens", "strcmp"));

		// insert teacher data into the database
		$curr_id = ""; // id previously inserted (as there are doubloons in the teachers_info array)

		// prepare query
		$query = "INSERT INTO ulg_fac_staff VALUES (?, ?, ?);";
		$stmt = $sql_abs->prepare_query($query);

		$success = true; // false if an error occurred

		$sql_abs->transaction();
		$sql_abs->execute_query("TRUNCATE TABLE ulg_fac_staff;");

		foreach($teachers_info as $teacher)
		{
			if($teacher['idulg_ens'] === $curr_id) // id already inserted
				continue;

			$success &= $stmt->execute(array($teacher['idulg_ens'], $teacher['prenom_ens'], $teacher['nom_ens']));
			$curr_id = $teacher['idulg_ens'];

			if(!$success)
				break;
		}

		// rollback the transaction if an error occurred
		if($success)
			$sql_abs->commit();
		else
			$sql_abs->rollback();

		$stmt->closeCursor();
		return $success;
	}

	/** 
	 * @brief Insert the students into the database
	 * @param[in] SQLAbstract_PDO $sql_abs 				 The sql abstract object
	 * @param[in] array 		  $student_courses_info  List of courses followed by a student (ulg id and pathway)
	 * @param[in] bool 			  $shuffle 				 True for shuffling students courses and pathways
	 * @retval True on success, false on error
	 */
	function insert_students(SQLAbs $sql_abs, array &$student_courses_info, $shuffle=false)
	{
		$students_info = common_regroup($student_courses_info, array("id_ulg", "code_ae"));

		if($shuffle)
			ct\shuffle_rows($students_info, array("code_cours", "code_ae"));

		// prepare insertion query 
		$query = "INSERT INTO ulg_student VALUES (?, ?);";
		$stmt = $sql_abs->prepare_query($query);

		$success = true; // false if an error occurred

		$sql_abs->transaction();
		$sql_abs->execute_query("TRUNCATE TABLE ulg_student;");

		foreach($students_info as $student)
		{
			if($student['id_ulg'] === $curr_id)
				continue;

			$success &= $stmt->execute(array($student['id_ulg'], $student['code_ae']));
			$curr_id = $student['id_ulg'];

			if(!$success)
				break;
		}

		if($success)
			$sql_abs->commit();
		else
			$sql_abs->rollback();

		$stmt->closeCursor();
		return $success;
	}

	// database connection
	//$db = new Database("", "", "localhost", "calendar_tool");
	//S$sql_abs = SQLAbs::buildByPDO($db->get_handle());

