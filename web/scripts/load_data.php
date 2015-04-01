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

	require_once("../functions.php");

	// namespace
	use util\database\Database as Database;
	use util\database\SQLAbstract_PDO as SQLAbs;

	// functions 
	/**
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
		$all_keys = array_keys(ct\first($array));
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
		}	

		$content_array = array_filter(preg_split("#(\\r|\\n|\\r\\n)#U", $file_content));

		if(count($content_array) <= 1)
		{
			echo "Error : empty file '".$file."'";
			return null;
		}

		// extract indexes and remove first row
		$indexes = explode($sep, $content_array[0]);
		unset($content_array[0]);

		// modify the array structure
		return array_map(function($row) use (&$indexes, $sep) 
						 { 
						 	return array_combine($indexes, explode($sep, $row)); 
						 },
						 $content_array);
	}

	/**
	 * @brief Insert the courses into the course table in the database
	 * @param[in] SQLAbstract_PDO $sql_abs      The sql abstract object
	 * @retval bool True on success, false on failure
	 */
	function insert_courses(SQLAbs $sql_abs)
	{
		// get courses data from file
		$courses_data = file_into_array("ulg_data/cours.txt");

		// insert them into the database
		$query = "INSERT INTO ulg_course VALUES (?, ?, ?, ?, ?, ?, ?, ?);";
		$stmt = $sql_abs->prepare_query($query);

		$success = true; // false if an error occurred

		$sql_abs->transaction();
		$success &= $sql_abs->execute_query("SET foreign_key_checks = 0;");
		$success &= $sql_abs->execute_query("TRUNCATE TABLE ulg_course;");
		$success &= $sql_abs->execute_query("SET foreign_key_checks = 1;");

		foreach($courses_data as $course)
		{
			$success &= $stmt->execute(array($course['code_cours'],
											 $course['lib_cours'],
											 $course['lib_cours_complet'],
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
		$pathways_data = file_into_array("ulg_data/seqform.txt");

		// insert them into the database

		$success = true; // false if an error occurred

		$sql_abs->transaction();
		$success &= $sql_abs->execute_query("SET foreign_key_checks = 0;");
		$success &= $sql_abs->execute_query("TRUNCATE TABLE ulg_pathway;");
		$success &= $sql_abs->execute_query("SET foreign_key_checks = 1;");

		$query = "INSERT INTO ulg_pathway VALUES (?, ?, ?);";
		$stmt = $sql_abs->prepare_query($query);


		foreach($pathways_data as $pathway)
		{
			$success &= $stmt->execute(array($pathway['code_ae'],
											 $pathway['lib_ae'],
											 $pathway['lib_long_ae']));

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
	 * @retval True on success, false on error
	 * @note Must be called after that the ulg_course table was properly set
	 */
	function insert_teachers(SQLAbs $sql_abs)
	{
		// load teacher data from file into the array
		$teachers_courses_info = file_into_array("ulg_data/enseignant.txt");

		// lowercase the ulg id
		$teachers_courses_info = ct\array_col_map($teachers_courses_info, "strtolower", "idulg_ens");

		$teachers_info = common_regroup($teachers_courses_info, array("idulg_ens", "nom_ens", "prenom_ens"));

		$success = true; // false if an error occurred

		$sql_abs->transaction();
		$success &= $sql_abs->execute_query("SET foreign_key_checks = 0;");
		$success &=	$sql_abs->execute_query("TRUNCATE TABLE ulg_fac_staff;");
		$success &= $sql_abs->execute_query("TRUNCATE TABLE ulg_course_team_member");
		$success &= $sql_abs->execute_query("SET foreign_key_checks = 1;");

		// insert teachers
		$sql_abs->insert_batch("ulg_fac_staff", ct\array_columns($teachers_info, array('idulg_ens', 'prenom_ens', 'nom_ens')));

		// insert teacher courses
		$teacher_courses = array();

		foreach($teachers_info as $teacher)	
			foreach ($teacher['varying'] as $course) 
				array_push($teacher_courses, array($teacher['idulg_ens'], $course['code_cours']));

		$sql_abs->insert_batch("ulg_course_team_member", $teacher_courses);

		// rollback the transaction if an error occurred
		if($success)
			$sql_abs->commit();
		else
			$sql_abs->rollback();

		return $success;
	}

	/** 
	 * @brief Insert the students into the database
	 * @param[in] SQLAbstract_PDO $sql_abs 				 The sql abstract object
	 * @param[in] bool 			  $shuffle 				 True for shuffling students courses and pathways
	 * @retval True on success, false on error
	 * @note Must be called when the ulg_course and ulg_pathway tables were properly initialized
	 */
	function insert_students(SQLAbs $sql_abs, $shuffle=false)
	{
		// load cursus data into an array
		$student_courses_info = array_unique(file_into_array("ulg_data/cursus.txt"), SORT_REGULAR);

		// lowercase the ulg_id
		$student_courses_info = ct\array_col_map($student_courses_info, "strtolower", "id_ulg");

		$students_info = common_regroup($student_courses_info, array("id_ulg", "code_ae"));

		if($shuffle)
			$students_info = ct\shuffle_rows($students_info, array("id_ulg"));

		// prepare insertion query 
		$success = true; // false if an error occurred

		$sql_abs->transaction();
		$success &= $sql_abs->execute_query("SET foreign_key_checks = 0;");
		$success &=	$sql_abs->execute_query("TRUNCATE TABLE ulg_student;");
		$success &= $sql_abs->execute_query("TRUNCATE TABLE ulg_has_course");
		$success &= $sql_abs->execute_query("SET foreign_key_checks = 1;");

		// add students : make a batch insert because one per one is too slow
		$success &= $sql_abs->insert_batch("ulg_student", ct\array_columns($students_info, array("id_ulg", "code_ae")));

		// add students' courses
		$tuples = array();

		foreach ($students_info as $student) 
			foreach ($student['varying'] as $course) 
				array_push($tuples, array($student['id_ulg'], $course['code_cours']));
	
		$success &= $sql_abs->insert_batch("ulg_has_course", $tuples);

		if($success)
			$sql_abs->commit();
		else
			$sql_abs->rollback();
		
		return $success;
	}

	// database connection
	$db = Database::get_instance(); 
	$sql_abs = SQLAbs::buildByPDO($db->get_handle());

	insert_pathways($sql_abs);
	insert_courses($sql_abs);
	insert_students($sql_abs, true);
	insert_teachers($sql_abs);