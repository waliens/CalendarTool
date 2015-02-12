<?php
	
	/**
	 * @file
	 * @brief Contains the GlobalEventModel
	 */

	namespace ct\models;

	require_once("functions.php");

	use util\mvc\Model;
	use ct\Connection;

	/**
	 * @class GlobalEventModel
	 * @brief A class for handling global event related database queries
	 */
	class GlobalEventModel extends Model
	{
		private $connection; /**< @brief The connection object */

		const LANG_FR = "FR"; /**< @brief Language constant : french */
		const LANG_EN = "EN"; /**< @brief Language constant : english */

		const ROLE_ID_PROFESSOR = 1; /**< @brief Rold id : 1 */

		/**
		 * @brief Constructs a GlobalEventObject
		 */
		public function __construct()
		{
			parent::__construct();
			$this->connection = Connection::get_instance();
		}

		/**
		 * Checks if the given global event exists
		 * @param[in] string $course_id The course ulg id
		 * @param[in] int    $acad_year The academic year
		 * @retval bool True if the global event exists, false otherwise
		 */
		public function global_event_exists($course_id, $acad_year)
		{
			return $this->sql->count("global_event",
									 "ULg_Identifier = ".$this->sql->quote($course_id).
									 " AND Acad_Start_Year = ".$this->sql->quote($acad_year)) > 0;
		}

		/**
		 * @brief Create a global event for with the given course id
		 * @param[in] string $course_id The ulg course identifier
		 * @param[in] string $user_id   The user ulg id (optional, default : currently connected user ulg id)
		 * @param[in] int    $acad_year The academic year for which the course must be created (optional, default: current acad year)
		 * @retval bool True on success, false on error
		 */
		public function create_global_event($course_id, $user_id = null, $acad_year=null)
		{
			if($user_id == null) $user_id = $this->connection->user_id();
			if($acad_year == null) $acad_year = \ct\get_academic_year();

			if(!checkdate(1, 1, $acad_year)) // check whether the year is valid
				return false;

			$success = true;

			$this->sql->transaction();
			$this->sql->set_dump_mode();

			// transfer course ulg data
			$query  =  "INSERT INTO global_event(ULg_Identifier, Name_Short, Name_Long, 
												 Period, Workload_Th, Workload_Pr, 
												 Workload_Au, Workload_St, Acad_Start_Year, 
												 Id_Owner)
						SELECT Id_Course, Name_Short, Name_Long, Period, 
								Hr_Th, Hr_Pr, Hr_Au , Hr_St, ? AS Year, ? AS Owner
						FROM ulg_course WHERE Id_Course = ?;";

			$success &= $this->sql->execute_query($query, array($acad_year, $user_id, $course_id));

			$glob_event_id = $this->sql->last_insert_id();
			echo $glob_event_id."<br>";

			if(!$success || $glob_event_id === 0) 
			{			
				$this->sql->rollback();
				return false;
			}

			// update the pathways table in case if the pathways are not stored in it yet
			$query  =  "INSERT INTO pathway(Id_Pathway, Name_Long, Name_Short) 
						SELECT Id_Pathway, Name_Long, Name_Short 
						FROM 
						( SELECT Id_Pathway, Name_Long, Name_Short 
						  FROM ulg_pathway NATURAL JOIN
						  ( SELECT DISTINCT(Id_Pathway) 
						    FROM ulg_student NATURAL JOIN 
						    ( SELECT Id_ULg_Student FROM ulg_has_course WHERE Id_Course = ? ) AS students
						  ) AS pathways_ids
						) AS pathways
						ON DUPLICATE KEY UPDATE 
							pathway.Id_Pathway = pathways.Id_Pathway, 
							pathway.Name_Long = pathways.Name_Long,
							pathway.Name_Short = pathways.Name_Short;";

			$success &= $this->sql->execute_query($query, array($course_id));

			if(!$success)
			{
				$this->sql->rollback();
				return false;
			}

			// only add the pathways of the ulg student that have the added course
			$query  =  "INSERT INTO global_event_pathway(Id_Global_Event, Id_Pathway)
						SELECT ? AS glob_event, Id_Pathway 
						FROM 
						( SELECT Id_Pathway
						  FROM ulg_pathway NATURAL JOIN
						  ( SELECT DISTINCT(Id_Pathway)
						    FROM ulg_student NATURAL JOIN 
						    ( SELECT Id_ULg_Student FROM ulg_has_course WHERE Id_Course = ? ) AS students
						  ) AS pathways_ids
						) AS pathways;";

			$success &= $this->sql->execute_query($query, array($glob_event_id, $course_id));
			
			if(!$success)
			{
				$this->sql->rollback();
				return false;				
			}

			// add to the event the student that are registered on the calendar tool and 
			// that matches one of the pathway of the course
			$query  =  "INSERT INTO `global_event_subscription` ( Id_Student, Id_Global_Event, Free_Student ) 
						SELECT Id_User, Id_Global_Event, '0' AS free FROM `global_event_pathway` NATURAL JOIN 
						( SELECT Id_User, Id_ULg_Student, Id_Pathway FROM `ulg_student` NATURAL JOIN  
						 ( SELECT Id_ULg AS Id_ULg_Student, Id_User FROM `user` NATURAL JOIN
						  ( SELECT Id_Student AS Id_User FROM `student`) AS stud ) AS reg_stud ) AS users
						WHERE Id_Global_Event = ?;";

			$success &= $this->sql->execute_query($query, array($glob_event_id));

			if(!$success)
			{
				$this->sql->rollback();
				return false;				
			}

			// add the creator as professor for this global event
			$team_member_data = array("Id_Global_Event" => $glob_event_id,
									  "Id_User" => $user_id,
									  "Id_Role" => self::ROLE_ID_PROFESSOR);

			$success &= $this->sql->insert("teaching_team_member", $this->sql->quote_all($team_member_data));

			if($success)
				$this->sql->commit();
			else
				$this->sql->rollback();

			return $success;
		}

		/**
		 * @brief Update the data of the global event taken from the ulg database
		 * @param[in] int $id_glob Identifier of the global event
		 * @retval bool True on success, false on error
		 */
		public function update_global_event_ulg_data($id_glob)
		{
			$query  =  "UPDATE global_event(Name_Short, Name_Long, Period, Workload_Th, 
											Workload_Pr, Workload_Au, Workload_St)
						FROM 
						SELECT Name_Short, Name_Long, Period, Hr_Th, Hr_Pr, Hr_Au, Hr_St
						FROM ulg_course WHERE Id_Course IN (SELECT ULg_Identifer FROM global_event WHERE Id_Global_Event = ?);";

			return $this->sql->execute_query($query, array($id_glob));
		}

		/**
		 * @brief Update the standalone data of a global event 
		 * @param[in] int   $id_glob Identifier of the global event
		 * @param[in] array $data 	 The data to update 
		 * @retval bool True on success, false on error
		 * @note The structure of the data array is the following :
		 * <ul>
		 *  <li>desc : a string containing the description</li>
		 *  <li>feedback : a string containing the feedback </li>
		 *  <li>lang : one of the LANG_* class constant</li>
		 * </ul>
		 */
		public function update_global_event_non_ulg_data($id_glob, $data)
		{
			if(!$this->valid_lang($data['lang']))
				return false;

			$update_array = array("Description" => $data['desc'],
								  "Feedback" => $data['feedback'],
								  "Language" => $data['lang']);

			return $this->sql->update("global_event", 
									  $this->sql->quote_all($update_array), 
									  "Id_Global_Event = ".$this->sql->quote($id_glob));
		}

		/**
		 * @brief Check whether the language string is valid
		 * @param[in] string $lang The language string
		 * @retval bool True if the string is valid, false otherwise
		 */
		private function valid_lang($lang)
		{
			return $lang === self::LANG_FR || $lang === self::LANG_EN;
		}

		public function get_global_event($glob_id)
		{

		}

		public function 
	}