<?php
	
	/**
	 * @file
	 * @brief Contains the UserModel class
	 */

	namespace ct\models;

	require_once("functions.php");

	use util\mvc\Model;

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

			$this->sql->lock(array("user WRITE"));

			$success = true;

			if(!$this->user_exists($user_ulg_id)) // check if an entry exists already in the user table
			{
			 	if(UserModel::is_student_id($user_ulg_id)) // checks if the user is a student or a faculty member
			 	{
			 		$this->sql->lock(array("ulg_student READ", "student WRITE"));

			 		// get student data
			 		$student = $this->sql->select_one("ulg_student", "Id_ULg_Student = ".$this->sql->quote($user_ulg_id));

			 		// insert new user
			 		$student_data = array("Id_ULg" => $user_ulg_id,
			 							  "Name" => "",
			 							  "Surname" => "");
			 		$success &= $this->sql->insert("user", $this->sql->quote_all($student_data));

			 		// insert student
			 		$query1 =  "INSERT INTO `student`(Id_Student, Mobile_User) SELECT Id_User, 0 FROM `user` WHERE Id_ULg = ?;";
			 		$success &= $this->sql->execute_query($query1, array($user_ulg_id));

			 		// check if the pathway should be inserted
			 		$this->sql->lock(array("pathway WRITE", "ulg_pathway READ"));

			 		if(!$pathway_model->pathway_exists($student['Id_Pathway']))
			 			$success &= $pathway_model->transfer_pathway($student['Id_Pathway']);

			 		// insert student pathway
			 		$query2 =  "INSERT INTO `student_pathway`(Id_Student, Id_Pathway, Acad_Start_Year) SELECT Id_User, ?, get_acad_year() FROM `user` WHERE Id_ULg = ?;";
			 		$success &= $this->sql->execute_query($query2, array($student['Id_Pathway'], $user_ulg_id));
			 	}
			 	else 
			 	{
			 		$this->sql->lock(array("ulg_fac_staff READ", "faculty_staff_member WRITE"));

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

			$this->sql->unlock();

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
			return $this->sql->count("user", "Id_ULg = ".$this->sql->quote()) > 0;
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
			return ct\starts_with($ulg_id, "s");
		}
	}