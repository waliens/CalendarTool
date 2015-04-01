<?php
	
	/**
	 * @file
	 * @brief Contains the UserModel class
	 */

	namespace ct\models;

	use ct\array_flatten;

	use util\mvc\Model;
	use ct\Connection;

	/**
	 * @class UserModel
	 * @brief A class for handling all user related database queries
	 */
	class UserModel extends Model
	{
		/**
		 * @brief Construct a UserModel object
		 */
		public function __construct()
		{
			parent::__construct();
		}

		/** 
		 * @brief Create an user entry in the table for the user having the given ulg id
		 * @param[in] string $user_ulg_id The ulg id of the user
		 * @retval bool True if the user has an entry (that was created before the function call or not), false otherwise
		 * @note This function might acquire the locks on the following tables : user, ulg_student, student, user, pathway, ulg_pathway, 
		 * ulg_fac_staff, faculty_staff_member, student_pathway
		 */
		public function create_user($user_ulg_id)
		{
			$user_ulg_id = trim($user_ulg_id);
			$pathway_model = new PathwayModel();

			if(!UserModel::check_ulg_id($user_ulg_id))
				return false;

			$success = true;

			$this->sql->transaction(); 

			if(!$this->user_exists($user_ulg_id)) // check if an entry exists already in the user table
			{
			 	if(UserModel::is_student_id($user_ulg_id)) // checks if the user is a student or a faculty member
			 	{
			 		// get student data
			 		$student = $this->sql->select_one("ulg_student", "Id_ULg_Student = ".$this->sql->quote($user_ulg_id));

			 		// insert new user
			 		$student_data = array("Id_ULg" => $user_ulg_id,
			 							  "Name" => "",
			 							  "Surname" => "");
			 		$success &= $this->sql->insert("user", $this->sql->quote_all($student_data));

			 		$student_id = $this->sql->last_insert_id();
			 		
			 		// insert student
			 		$query1 =  "INSERT INTO `student`(Id_Student, Mobile_User) SELECT Id_User, 0 FROM `user` WHERE Id_ULg = ?;";
			 		$success &= $this->sql->execute_query($query1, array($user_ulg_id));

					
			 		// check if the pathway should be inserted
			 		if(!$pathway_model->pathway_exists($student['Id_Pathway']))
			 			$success &= $pathway_model->transfer_pathway($student['Id_Pathway']);
			 		
			 		// insert student pathway
			 		$query2 =  "INSERT INTO `student_pathway`(Id_Student, Id_Pathway, Acad_Start_Year) SELECT Id_User, ?, ? FROM `user` WHERE Id_ULg = ?;";
			 		$success &= $this->sql->execute_query($query2, array($student['Id_Pathway'], \ct\get_academic_year(), $user_ulg_id));

			 		// add subscription for the courses followed by the student
			 		$query3 =  "INSERT INTO `global_event_subscription`(Id_Student, Id_Global_Event)
			 					SELECT ? AS stud, Id_Global_Event 
			 					FROM
			 					( SELECT Id_Global_Event, ULg_Identifier AS Id_Course 
			 					  FROM global_event 
			 					  WHERE Acad_Start_Year = ? ) AS glob_events
			 					NATURAL JOIN 
			 					( SELECT Id_Course 
		 					  	  FROM ulg_has_course 
		 					  	  WHERE Id_ULg_Student = ? ) AS courses_of_student;";
					$success &= $this->sql->execute_query($query3, array($student_id, \ct\get_academic_year(), $user_ulg_id));
			 	}
			 	else 
			 	{
			 		// get fac. staff. member data
			 		$fac_mem = $this->sql->select_one("ulg_fac_staff", "Id_ULg_Fac_Staff = ".$this->sql->quote($user_ulg_id));

			 		// insert user data
			 		
			 		$fac_mem_data = array("Id_ULg" => $user_ulg_id,
			 							  "Name" => $fac_mem['Name'],
			 							  "Surname" => $fac_mem['Surname']);
			 		$success &= $this->sql->insert("user", $this->sql->quote_all($fac_mem_data));

			 		// insert fac mem data
			 		$query  =  "INSERT INTO `faculty_staff_member`(Id_Faculty_Member) SELECT Id_User FROM `user` WHERE Id_ULg = ?;";
			 		$success &= $this->sql->execute_query($query, array($user_ulg_id));
			 	} 
			}

			if($success)
				$this->sql->commit();
			else
				$this->sql->rollback();

			return $success;
		}

		/**
		 * @brief Return the user id associated with the given ulg id
		 * @param[in] string $ulg_id The ulg id
		 * @retval int The user id, -1 on error
		 */
		public function get_user_id_by_ulg_id($ulg_id)
		{
			$user = $this->sql->select_one("user", "Id_ULg = ".$this->sql->quote($ulg_id));
			return !empty($user) ? $user['Id_User'] : -1;
		}

		/**
		 * @brief Checks whether the user having the given ulg id exists in the user table
		 * @param[in] string $user_ulg_id The ulg id of the user
		 * @retval bool True if the user exists, false otherwise
		 */
		public function user_exists($user_ulg_id)
		{
			return $this->sql->count("user", "Id_ULg = ".$this->sql->quote($user_ulg_id)) > 0;
		}

		/**
		 * @brief Checks whether the ulg id is correctly formatted
		 * @param[in] string $ulg_id The ulg identifier to check
		 * @retval bool True if the ulg id is correctly formatted, false otherwise
		 */
		public static function check_ulg_id($ulg_id)
		{
			return preg_match("#^[a-z][0-9]{6}$#", $ulg_id);
		}

		/**
		 * @brief Checks whether the given ulg id is a student id
		 * @param[in] string $ulg_id The ulg identifier to check
		 * @retval bool True if the ulg id is a student's one, false otherwise
		 */
		public static function is_student_id($ulg_id)
		{
			return \ct\starts_with($ulg_id, "s");
		}

		/**
		 * @brief Returns the list of professors registered on the platform
		 * @retval array An array containing the professors
		 * @note A professor is described by the following items (given by array key) :
		 * <ul>
		 *  <li>Id_User : user id </li>
		 *  <li>Id_ULg : user ulg id </li>
		 *  <li>Name : user name </li>
		 *  <li>Surname : user surname </li>
		 * </ul>
		 */
		public function get_professors()
		{
			$query  =  "SELECT * FROM user NATURAL JOIN
						(SELECT Id_Faculty_Member AS Id_User FROM faculty_staff_member ) AS fac_meme";

			return $this->sql->execute_query($query);
		}

		/**
		 * @brief Get the given user data
		 * @param[in] int $user_id The user id (optionnal, default: the currently connected user data)
		 * @retval array The user data
		 * @note The array contains the following data :
		 * <ul>
		 * 	<li> Id_User : user id </li>
		 * 	<li> Id_ULg : user ulg id </li>
		 * 	<li> Name : user name </li>
		 * 	<li> Surname : user surname </li>
		 * </ul>
		 */
		public function get_user($user_id=null)
		{
			if($user_id == null) $user_id = Connection::get_instance()->user_id();
			return $this->sql->select_one("user", "Id_User = ".$this->sql->quote($user_id));
		}
		
		/**
		 * @brief Checks whether the user having the given id exists in the user table
		 * @param[in] string $user_id the id of the user
		 * @retval bool True if the user exists, false otherwise
		 */
		public function user_id_exists($user_id)
		{
			return $this->sql->count("user", "Id_User = ".$this->sql->quote($user_id)) > 0;
		}

		/**
		 * @brief Checks whether the given user is a student
		 * @param[in] string $user_id the id of the user
		 * @retval bool True if the user is a student, false otherwise
		 */
		public function user_is_student($user_id)
		{
			return $this->sql->count("student", "Id_Student = ".$this->sql->quote($user_id)) > 0;
		}
		
		/**
		 * @brief Get a list of all student following a same pathway
		 * @param[in] string $path the pathwayt
		 * @return array with a list of id or false if error
		 */
		public function get_student_by_pathway($path){
			$query = "SELECT Id_Student as id FROM student_pathway WHERE Id_Pathway = ?";
			$ret = $this->sql->execute_query($query, array($path));
			if(!$ret)
				return false;
			else 
				return array_flatten($ret);
		}
	}