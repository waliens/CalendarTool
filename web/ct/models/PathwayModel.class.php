<?php

	/**
	 * @file
	 * @brief Contains the PathwayModel class
	 */

	namespace ct\models;

	use util\mvc\Model;
	use ct\Connection;

	/**
	 * @class PathwayModel
	 * @brief A class for handling database queries related to pathways
	 */
	class PathwayModel extends Model
	{
		/**
		 * @brief Construct the PathwayModel object
		 */
		public function __construct()
		{
			parent::__construct();
		}

		/**
		 * @brief Transfer the given pathway data from ulg tables to ct tables
		 * @param[in] string $pathway_id The pathway identifier
		 * @retval bool True on success, false on error
		 */
		public function transfer_pathway($pathway_id)
		{
			$query  =  "INSERT INTO `pathway`(Id_Pathway, Name_Long, Name_Short)
						SELECT * FROM `ulg_pathway` WHERE Id_Pathway = ?;";

			return $this->sql->execute_query($query, array($pathway_id));			
		}

		/**
		 * @brief Checks whether the pathway having the given id exists
		 * @param[in] string $pathway_id The pathway identifier
		 * @retval bool True on success, false on error
		 */
		public function pathway_exists($pathway_id)
		{
			return $this->sql->count("pathway", "Id_Pathway = ".$this->sql->quote($pathway_id)) > 0;
		}

		/**
		 * @brief Checks the validity of a pathway string (its format)
		 * @param[in] string $pathway The pathway to check
		 * @retval bool True if the pathway is valid, false otherwise
		 * @note The function does not check if the pathway exists in the database
		 */
		public static function valid_pathway($pathway)
		{
			return preg_match("#^[A-Z]{6}[0-9]{6}$#", $pathway);
		}

		/**
		 * @brief Get the list of pathways
		 * @retval array The pathways array containing the keys (Id_Pathway, Name_Long, Name_Short)
		 */
		public function get_pathways()
		{
			return $this->sql->select("pathway", null, null, array("Name_Long"));
		}

		/**
		 * @brief Get the pathway of a student for a given academic year
		 * @param[in] int $stud_id   The student id
		 * @param[in] int $acad_year The year starting the academic year
		 * @retval array An array containing the pathway info : Id_Pathway, Name_Long, Name_Short. An 
		 * empty array if the student has no pathway 
		 */
		public function get_pathway_by_student($stud_id=null, $acad_year=null)
		{
			if($stud_id == null) $stud_id = Connection::get_instance()->user_id();
			if($acad_year == null) $acad_year = \ct\get_academic_year();

			$query  =  "SELECT Id_Pathway, Name_Long, Name_Short 
						FROM pathway NATURAL JOIN 
						( SELECT Id_Pathway FROM student_pathway WHERE Id_Student = ? AND Acad_Start_Year = ?) AS stud_path;";

			$ret = $this->sql->execute_query($query, array($stud_id, $acad_year));

			return empty($ret) ? array() : $ret[0];
		}
	};