<?php
	
	/**
	 * @file
	 * @brief Contains the GlobalEventModel
	 */

	namespace ct\models\events;

	require_once("functions.php");

	use util\mvc\Model;
	use ct\models\FileModel;
	use ct\Connection;

	/**
	 * @class GlobalEventModel
	 * @brief A class for handling global event related database queries
	 */
	class GlobalEventModel extends Model
	{
		private $connection; /**< @brief The connection object */
		private $file_model; /**< @brief A file model object */

		const LANG_FR = "FR"; /**< @brief Language constant : french */
		const LANG_EN = "EN"; /**< @brief Language constant : english */

		const ROLE_ID_PROFESSOR = 1; /**< @brief Role id : professor */
		const ROLE_ID_TA = 2; /**< @brief Role id : teaching assistant */
		const ROLE_ID_TS = 3; /**< @brief Role id : teaching student */

		const GET_BY_IDS = 1; /**< @brief Way of getting a list of global events : by global event ids */
		const GET_BY_OWNER = 2; /**< @brief Way of getting a list of global events : by owner id */
		const GET_BY_STUDENT = 3; /**< @brief Way of getting a list of global events : by student id (global event to which the student is subscribed) */
		const GET_BY_PATHWAYS = 4; /**< @brief Way of getting a list of global events :  by pathways */
		const GET_BY_TEAM_MEMBER = 5; /**< @brief Way of getting a list of global events : by team member id */
		const GET_BY_STUDENT_NO_OPT = 6; /**< @brief Way of getting a list of global events : same as GET_BY_STUDENT but exclude 
												courses for which the student is a free student*/ 
		const GET_BY_STUDENT_OPT_ONLY = 7; /**< @brief Way of getting a list of global events : same as GET_BY_STUDENT but exclude 
												courses for which the student is not a free student*/ 
		
		/**
		 * @brief Constructs a GlobalEventObject
		 */
		public function __construct()
		{
			parent::__construct();
			$this->connection = Connection::get_instance();
			$this->file_model = new FileModel();
			//$this->sql->set_dump_mode();
		}

		/**
		 * @brief Checks if the given global event exists
		 * @param[in] string $course_id The course ulg id
		 * @param[in] int    $acad_year The academic year
		 * @retval bool True if the global event exists, false otherwise
		 */
		public function global_event_exists($course_id, $acad_year)
		{
			return $this->sql->count("global_event", $this->get_where_clause($course_id, $acad_year)) > 0;
		}

		/**
		 * @brief Create a global event for with the given course id
		 * @param[in] string $course_id The ulg course identifier
		 * @param[in] string $user_id   The user ulg id (optional, default: currently connected user ulg id)
		 * @param[in] int    $acad_year The academic year for which the course must be created (optional, default: current acad year)
		 * @param[in] string $lang      The language in which the course must be created (one of the class 
		 * LANG_* constant) (optional, default: LANG_FR)
		 * @retval int The global event id if the addition worked, 0 otherwise
		 */
		public function create_global_event($course_id, $user_id = null, $acad_year=null, $lang=null)
		{
			if($user_id == null) $user_id = $this->connection->user_id();
			if($acad_year == null) $acad_year = \ct\get_academic_year();
			if($lang == null) $lang = self::LANG_FR;

			if(!checkdate(1, 1, $acad_year)) // check whether the year is valid
				return 0;

			$success = true;

			$this->sql->transaction();

			// check whether the professor/user can create the course
			$query  =  "SELECT COUNT(*) AS cnt
						FROM ulg_course_team_member 
						WHERE Id_ULg_Fac_Staff = ( SELECT Id_ULg FROM user WHERE Id_User = ? ) 
							AND Id_Course = ?;";

			$creation_right_check = $this->sql->execute_query($query, array($user_id, $course_id));

			if(empty($creation_right_check) || $creation_right_check[0]['cnt'] < 1)
			{
				$this->sql->rollback();
				return 0;
			}

			// transfer course ulg data
			$query  =  "INSERT INTO global_event(ULg_Identifier, Name_Short, Name_Long, 
												 Period, Workload_Th, Workload_Pr, 
												 Workload_Au, Workload_St, Acad_Start_Year, 
												 Id_Owner, Language)
						SELECT Id_Course, Name_Short, Name_Long, Period, 
								Hr_Th, Hr_Pr, Hr_Au , Hr_St, ? AS Year, ? AS Owner, ? AS Lang
						FROM ulg_course WHERE Id_Course = ?;";

			$success &= $this->sql->execute_query($query, array($acad_year, $user_id, $lang, $course_id));

			$glob_event_id = $this->sql->last_insert_id();

			if(!$success || $glob_event_id === 0) 
			{			
				$this->sql->rollback();
				return 0;
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
				return 0;
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
				return 0;				
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
				return 0;				
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

			return $success ? $glob_event_id : 0;
		}

		/**
		 * @brief Update the data of the global event taken from the ulg database
		 * @param[in] array $id_data Identifier of the global event
		 * @retval bool True on success, false on error
		 */
		public function update_global_event_ulg_data(array $id_data)
		{
			// extract global event id
			$id_glob = $this->get_global_event_id($id_data);

			if($id_glob < 0)
				return false;

			$query  =  "UPDATE global_event(Name_Short, Name_Long, Period, Workload_Th, 
											Workload_Pr, Workload_Au, Workload_St)
						FROM 
						SELECT Name_Short, Name_Long, Period, Hr_Th, Hr_Pr, Hr_Au, Hr_St
						FROM ulg_course WHERE Id_Course IN (SELECT ULg_Identifer FROM global_event WHERE Id_Global_Event = ?);";

			return $this->sql->execute_query($query, array($id_glob));
		}

		/**
		 * @brief Update the standalone data of a global event 
		 * @param[in] array $id_data Identifier of the global event
		 * @param[in] array $data 	 The data to update 
		 * @retval bool True on success, false on error
		 * @note The structure of the data array is the following :
		 * <ul>
		 *  <li>desc : a string containing the description</li>
		 *  <li>feedback : a string containing the feedback </li>
		 *  <li>lang : one of the LANG_* class constant (optional)</li>
		 * </ul>
		 */
		public function update_global_event_non_ulg_data(array $id_data, $data)
		{
			// extract global event id
			$id_glob = $this->get_global_event_id($id_data);

			if($id_glob < 0)
				return false;

			$update_array = array("Description" => $data['desc'],
								  "Feedback" => $data['feedback']);

			// check if the language must be updated
			if(isset($data['lang']))
			{
				if(self::valid_lang($data['lang']))
					$update_array['Language'] = $data['lang'];
				else
					return false;
			}

			return $this->sql->update("global_event", 
									  $this->sql->quote_all($update_array), 
									  "Id_Global_Event = ".$this->sql->quote($id_glob));
		}

		/**
		 * @brief Check whether the language string is valid
		 * @param[in] string $lang The language string
		 * @retval bool True if the string is valid, false otherwise
		 */
		public static function valid_lang($lang)
		{
			return $lang === self::LANG_FR || $lang === self::LANG_EN;
		}

		/**
		 * @brief Check whether the role id is valid
		 * @param[in] int $role_id THe role id to check
		 * @retval bool True if the role id is valid, false otherwise
		 */
		private function valid_role_id($role_id)
		{
			return $role_id == self::ROLE_ID_PROFESSOR ||
					$role_id == self::ROLE_ID_TA ||
					$role_id == self::ROLE_ID_TS;
		}

		/**
		 * @brief Get the data of a global event
		 * @param[in] array $id_data The data identifying the global event
		 * @retval array An array containing the data, an empty array if the event wasn't found
		 * @note See GlobalEventModel::get_global_event_id function for details about the structure of the
		 * id_data array
		 * @note The returned array will contain the given keys :
		 * <ul>
		 * 	<li>id: the global event id</li>
		 * 	<li>ulg_id: the course ulg id</li>
		 * 	<li>name_short : the short name</li>
		 * 	<li>name_long : name_long</li>
		 * 	<li>owner_name: the owners id</li>
		 * 	<li>owner_surname : the owner surname</li>
		 * 	<li>period : the string identifying the period of the year at which the course take place</li>
		 * 	<li>desc : the global event description</li>
		 * 	<li>feedback : the global event feedback</li>
		 * 	<li>wk_th : theoritical workload </li>
		 * 	<li>wk_pr : practical workload</li>
		 * 	<li>wk_au : auxiliary workload</li>
		 * 	<li>wk_st : ??? workload</li>
		 * 	<li>lang : the language ('FR' or 'EN')</li>
		 * 	<li>acad_year  : a string containing the academic year ("YYYY-YYYY", "2014-2015")</li>
		 * </ul>
		 */
		public function get_global_event(array $id_data)
		{
			// extract global event id
			$id_glob = $this->get_global_event_id($id_data);

			if($id_glob < 0)
				return array();

			$query  =  "SELECT Id_Global_Event AS id, ULg_Identifier AS ulg_id, Name_Short AS name_short,
							   Name_Long AS name_long, owner_name, owner_surname, Period AS period, 
							   Description AS `desc`, Feedback AS feedback, Workload_Th AS wk_th,
							   Workload_Pr AS wk_pr, Workload_Au AS wk_au, Workload_St AS wk_st,
							   Language AS lang, CONCAT(Acad_Start_Year, '-', Acad_Start_Year + 1) AS acad_year
						FROM 
						( SELECT * FROM global_event WHERE Id_Global_Event = ? ) AS glob
						NATURAL JOIN
						( SELECT Name AS owner_name, Surname AS owner_surname, Id_User AS Id_Owner FROM user ) as owner;";

			$result = $this->sql->execute_query($query, array($id_glob));

			return empty($result) ? array() : $result[0];
		}

		/**
		 * @brief Return the global event id corresponding the given id data
		 * @param[in] array $id_data An array containing the data for identifying the global event
		 * @retval int The global event id, -1 on error
		 * @note The id_data array must contain either only one 'id' key mapping the global event id
		 * or two keys, 'ulg_id' and 'year', respectively the course ulg id and the year starting the 
		 * academic year of the course
		 */
		public function get_global_event_id(array $id_data)
		{
			if(array_key_exists('id', $id_data))
				return is_numeric($id_data['id']) && $id_data['id'] > 0 ? $id_data['id'] : -1;
			
			$where = $this->get_where_clause($id_data['ulg_id'], $id_data['year']);
			$id = $this->sql->select_one("global_event", $where, array("Id_Global_Event"));

			return empty($id['Id_Global_Event']) ? -1 : $id['Id_Global_Event'];
		}

		/**
		 * @brief Constructs the where clause for a sql queries containing to select an global event with
		 * the course ulg identifier and a year (year starting the academic year)
		 * @param[in] string $course_id The course ulg id
		 * @param[in] int    $acad_year The academic year
		 * @retval string The where clause
		 */
		private function get_where_clause($course_id, $acad_year)
		{
			return "ULg_Identifier = ".$this->sql->quote($course_id)." AND Acad_Start_Year = ".$this->sql->quote($acad_year);
		}

		/**
		 * @brief Return the teaching team of the given global_event
		 * @param[in] array  $id_data An array containing the data for identifying the global event
		 * @param[in] string $lang    The language in which the role name must be (optionnal, default : FR)
		 * @retval array An array of which each row is an array containing the informations of one team member
		 * @note See GlobalEventModel::get_global_event_id function for details about the structure of the $id_data array
		 * @note The rows are structured as follows :
		 * <ul>
		 *  <li>user : the user id </li>
		 * 	<li>name : the team member name</li>
		 *  <li>surname : the team member surname</li>
		 *  <li>role : its role in the given language</li>
		 * </ul> 
		 */
		public function get_teaching_team(array $id_data, $lang = self::LANG_FR)
		{
			// extract global event id
			$id_glob = $this->get_global_event_id($id_data);

			if($id_glob < 0)
				return array();

			// get the teaching team
			// select the language
			if($lang === self::LANG_FR)
				$lang_col = "Role_FR AS role";
			else
				$lang_col = "Role_EN AS role";

			$query  =  "SELECT Id_User AS user, Name AS name, Surname AS surname, role 
						FROM  user NATURAL JOIN
						( SELECT * FROM teaching_team_member WHERE Id_Global_Event = ? ) AS ttm
						NATURAL JOIN 
						( SELECT Id_Role, ".$lang_col." FROM teaching_role ) AS roles";

			return $this->sql->execute_query($query, array($id_glob));
		}

		/**
		 * @brief Return the language of the given global event
		 * @param[in] array $id_data The data for identifying the global event
		 * @retval string One of the class LANG_* constant
		 */
		public function get_language(array $id_data)
		{
			// extract global event id
			$id_glob = $this->get_global_event_id($id_data);

			if($id_glob < 0)
				return array(); 

			$lang = $this->sql->select_one("global_event", "Id_Global_Event = ".$this->sql->quote($id_glob), array("Language"));
			return $lang['Language'];
		}

		/**
		 * @brief Return the list of student that have subscribed to the event
		 * @param[in] array $id_data The data for identifying the global event
		 * @retval array Array of which the rows contains the data about a student
		 * that has subscribed for the given lesson
		 * @note See GlobalEventModel::get_global_event_id function for details about the structure of the
		 * $id_data array
		 * @note The rows' structure is the following : 
		 * <ul>
		 * 	<li>id : the user id</li>
		 *  <li>ulg_id : the user ulg_id</li>
		 *  <li>surname : the user surname</li>
		 *  <li>name : the user name</li>
		 *  <li>free_student : 1 if the student is a free student, 0 otherwise</li>
		 * </ul> 
		 */
		public function get_subscribed_student(array $id_data)
		{
			// extract global event id
			$id_glob = $this->get_global_event_id($id_data);

			if($id_glob < 0)
				return array();

			// get the students
			$query  =  "SELECT Id_User AS id, Id_ULg AS ulg_id, Name AS name, 
							   Surname AS surname, Free_Student AS free_student
						FROM user NATURAL JOIN 
						( SELECT Id_Student AS Id_User, Free_Student 
						  FROM global_event_subscription 
						  WHERE Id_Global_Event = ? ) AS studs;";

			return $this->sql->execute_query($query, array($id_glob));
		}

		/**
		 * @brief Return the list of pathways that are associated with the given global event
		 * @param[in] array $id_data The data for identifying the global event
		 * @retval array Array of which the rows contains the data about a pathway
		 * that has subscribed for the given lesson
		 * @note See GlobalEventModel::get_global_event_id function for details about the structure of the
		 * $id_data array
		 * @note The rows' structure is the following : 
		 * <ul>
		 * 	<li>id : a string containing the pathway id</li>
		 *  <li>name_long : the pathway long name</li>
		 *  <li>name_short : the pathway short name</li>
		 * </ul> 
		 */
		public function get_global_event_pathways(array $id_data)
		{
			// extract global event id
			$id_glob = $this->get_global_event_id($id_data);

			if($id_glob < 0)
				return array();

			// get the students
			$query  =  "SELECT Id_Pathway AS id, Name_Long AS name_long, Name_Short AS name_short
						FROM pathway NATURAL JOIN 
						( SELECT Id_Pathway 
						  FROM global_event_pathway 
						  WHERE Id_Global_Event = ? ) AS paths;";

			return $this->sql->execute_query($query, array($id_glob));
		}

		/**
		 * @brief Get the files associated with the event
		 * @param[in] array $id_data The data for identifying the global event
		 * @retval array Array of which the rows contains the data about a file
		 * that has subscribed for the given lesson
		 * @note See GlobalEventModel::get_global_event_id function for details about the structure of the
		 * $id_data array
		 * @note The rows' structure is the following : 
		 * <ul>
		 * 	<li>id : file id</li>
		 *  <li>path : the filepath</li>
		 *  <li>id_owner : id of the user that owns the file</li>
		 *  <li>f_owner_name : name of the file owner</li>
		 *  <li>f_owner_surname : surname of the file owner</li>
		 *  <li>filename : name of the file</li>
		 *  <li>name_short : the pathway short name</li>
		 * </ul> 
		 */
		public function get_global_event_files(array $id_data)
		{
			// extract global event id
			$id_glob = $this->get_global_event_id($id_data);

			if($id_glob < 0)
				return array();

			// get the files
			$query  =  "SELECT Id_File AS id, Filepath AS `path`, Id_User AS id_owner, f_owner_name, f_owner_surname,
							   Name AS filename 
						FROM 
						( SELECT Id_User, Name AS f_owner_name, Surname AS f_owner_surname FROM user ) as users
						NATURAL JOIN
						( SELECT * FROM global_event_file WHERE Id_Global_Event = ? ) as ids_files
						NATURAL JOIN 
						file";

			$files = $this->sql->execute_query($query, array($id_glob));

			return empty($files) ? array() : $files[0];
		}

		/**
		 * @brief Deletes a global event from the database
		 * @param[in] array $id_data The data for identifying the global event
		 * @retval bool True on success, false on error
		 * @note See GlobalEventModel::get_global_event_id function for details about the structure of the
		 * $id_data array
		 */
		public function delete_global_event(array $id_data)
		{
			// extract global event id
			$id_glob = $this->get_global_event_id($id_data);

			if($id_glob < 0)
				return false;

			$quoted_id = $this->sql->quote($id_glob);
			$success = true;

			$this->sql->transaction();

			$success &= $this->sql->delete("global_event", "Id_Global_Event = ".$quoted_id);
			
			if($success)
				$this->sql->commit();
			else
				$this->sql->rollback();

			return $success;
		}

		/**
		 * @brief Get all the data related to the given global event (global event itself + teaching team, students, pathways, files)
		 * @param[in] array  $id_data The data for identifying the global event
		 * @param[in] string $lang    The language in which the data must be (optional, default: the one of the global event)
		 * @retval array An array containing the data
		 * @note See GlobalEventModel::get_global_event_id function for details about the structure of the
		 * $id_data array
		 * @note The array is structured as the one returned from the get_global_event function but some new fields are added :
		 * <ul>
		 * 	<li>pathways : the pathways associated with the event</li>
		 *  <li>files : the fukes associated with the event</li>
		 *  <li>students : the student that are registered for the event</li>
		 *  <li>team : the teaching team</li>
		 * </ul>
		 */
		public function get_whole_global_event(array $id_data, $lang = null)
		{
			// extract global event id
			$id_glob = $this->get_global_event_id($id_data);

			if($id_glob < 0)
				return array();

			$id_data_int = array('id' => $id_glob);

			$global_event = $this->get_global_event($id_data_int);

			if(empty($global_event))
				return array();
			
			$global_event['pathways'] = $this->get_global_event_pathways($id_data_int);
			$global_event['files'] = $this->get_global_event_files($id_data_int);
			$global_event['students'] = $this->get_subscribed_student($id_data_int);
			$global_event['team'] = $this->get_teaching_team($id_data_int, $lang == null ? $global_event['lang'] : $lang);

			return $global_event;
		}

		/**
		 * @brief Return the global events selected with the given GET_* method and the given identifier
		 * @param[in] int 	$method 	The GET_* method
		 * @param[in] mixed $identifier The identifier associated with the GET_* method
		 * @retval array An multidimensionnal array of which each row corresponds to a global event and is structured as 
		 * the array returned by the get_global_event function
		 * @note For the method GET_BY_IDS, the identifier can either be an array of integers (the indexes) or an 
		 * array of which the sub_arrays have the structure of the id_data array as in the get_global_event_id function
		 */
		public function get_global_events($method, $identifier)
		{
			if($method !== self::GET_BY_IDS)
				$ids = $this->get_global_ids($method, $identifier);
			else
				$ids = $identifier;

			return $this->get_global_event_by_ids($ids);
		}

		/**
		 * @brief Returns an array of global event id corresponding to the given identifier for a given GET_* method
		 * @param[in] int 	$method 	The GET_* method
		 * @param[in] mixed $identifier The identifier associated with the GET_* method (except GET_BY_IDS)
		 * @retval array An array containing the integer global event ids
		 */
		private function get_global_ids($method, $identifier)
		{
			$quoted_id = $this->sql->quote($identifier);
			$column = array("Id_Global_Event");

			switch($method)
			{
			case self::GET_BY_OWNER:
				$ids = $this->sql->select("global_event", "Id_Owner = ".$quoted_id, $column);
				break;
			case self::GET_BY_STUDENT:
				$ids = $this->sql->select("global_event_subscription", "Id_Student = ".$quoted_id, $column);
				break;
			case self::GET_BY_STUDENT_NO_OPT:
				$ids = $this->sql->select("global_event_subscription", "Id_Student = ".$quoted_id." AND Free_Student IS FALSE", $column);
				break;
			case self::GET_BY_STUDENT_OPT_ONLY:
				$ids = $this->sql->select("global_event_subscription", "Id_Student = ".$quoted_id." AND Free_Student IS TRUE", $column);
				break;
			case self::GET_BY_PATHWAYS:
				$ids = $this->sql->select("global_event_pathway", "Id_Pathway = ".$quoted_id, $column);
				break;
			case self::GET_BY_TEAM_MEMBER:
				$ids = $this->sql->select("teaching_team_member", "Id_User =".$quoted_id, $column);
				break;
			}

			// the select returns a multidimensionnal array -> need to flatten it
			return \ct\array_flatten($ids);
		}

		/**
		 * @brief Returns an array of global events for the given ids
		 * @param[in] array $ids The array of ids (can be either integers or id_data array, see get_global_event_id function)
		 * @retval An multidimensionnal array of which each row corresponds to a global event and is structured as 
		 * the array returned by the get_global_event function
		 */
		public function get_global_event_by_ids(array $ids)
		{
			$global_events = array();

			foreach($ids as $id)
			{
				if(!is_array($id))
					$id = array("id" => $id);

				$glb = $this->get_global_event($id);

				if(!empty($glb))
					$global_events[] = $glb;
			}

			return $global_events;
		}

		/** 
		 * @brief Returns all the global events given user is associated to
		 * @param[in] int $user_id The user id (optionnal, default: the currently connected user)
		 * @retval array The array of global events
		 */
		public function get_global_events_by_user_role($user_id = null)
		{
			if($user_id == null) $user_id = $this->connection->user_id();

			$query  =  "SELECT Id_Global_Event AS id, ULg_Identifier AS ulg_id, Name_Short AS name_short,
							   Name_Long AS name_long, owner_name, owner_surname, Period AS period, 
							   Description AS `desc`, Feedback AS feedback, Workload_Th AS wk_th,
							   Workload_Pr AS wk_pr, Workload_Au AS wk_au, Workload_St AS wk_st,
							   Language AS lang, CONCAT(Acad_Start_Year, '-', Acad_Start_Year + 1) AS acad_year
						FROM global_event 
						NATURAL JOIN
						( SELECT Id_Global_Event FROM teaching_team_member WHERE Id_User = ? ) AS glob
						NATURAL JOIN
						( SELECT Name AS owner_name, Surname AS owner_surname, Id_User AS Id_Owner FROM user ) as owner;";

			return $this->sql->execute_query($query, array($user_id));
		}

		/**
		 * @brief Delete the subscription of the given student to the given global event
		 * @param[in] array $id_data    The data for identifying the global event
		 * @param[in] int   $student_id The student identifier
		 * @retval bool True on success, false on error 
		 */
		public function delete_subscription(array $id_data, $student_id)
		{
			// extract global event id
			$id_glob = $this->get_global_event_id($id_data);

			if($id_glob < 0)
				return false;

			$where = "Id_Global_Event = ".$this->sql->quote($id_glob).
					 " AND Id_Student = ".$this->sql->quote($student_id);

			return $this->sql->delete("global_event_subscription", $where);
		}

		/**
		 * @brief Add a subscription to the given student id
		 * @param[in] array $id_data      The data for identifying the global event
		 * @param[in] int   $student_id   The student id
		 * @param[in] bool  $free_student True if the student is a free student (optional, default: true)
		 * @param[in] int   $year  		  The year starting the acad. year for which the subscription must be added (optional, default: current acad year)
		 * @retval bool True on success, false on error
		 * @note The function checks whether the student has a pathway that can take this course
		 * @note The given year should be the same as the one of the given global event
		 */
		public function add_subscription(array $id_data, $student_id, $free_student=null, $year=null)
		{
			if($free_student == null) $free_student = true;
			if($year == null) 		  $year = \ct\get_academic_year();

			// extract global event id
			$id_glob = $this->get_global_event_id($id_data);

			if($id_glob < 0)
				return false;

			// insert the subscription only if the student has one of the global event's pathway for the given year
			$query  =  "INSERT INTO global_event_subscription(Id_Global_Event, Id_Student, Free_Student)
						SELECT Id_Global_Event, Id_Student, ? AS free 
						FROM 
						( SELECT Id_Pathway, Id_Student FROM student_pathway WHERE Id_Student = ? AND Acad_Start_Year = ? ) as stud_path 
						NATURAL JOIN
						( SELECT * FROM global_event_pathway WHERE Id_Global_Event = ? ) AS glob_path;";

			$param_array = array($free_student, $student_id, $year, $glob_id);

			// use the last inserted id to check whether the subscriptions were added
			$id = $this->sql->last_insert_id();

			return $this->sql->execute_query($query, $param_array) 
					&& !empty($id);
		}

		/**
		 * @brief Add a new file and associate it to the global event
		 * @param[in] array  $id_data The data for identifying the global event
		 * @param[in] string $spf_key The key of the uploaded file data in the $_FILES superglobal
		 * @param[in] int    $user    The id of the user that should be the owner of the file
		 * @retval bool True on success, false on error
		 * @note The file updload data should be located in the $_FILES superglobal 
		 */
		public function add_file(array $id_data, $spf_key, $user=null)
		{
			// extract global event id
			$id_glob = $this->get_global_event_id($id_data);

			if($id_glob < 0)
				return false;

			$this->sql->transaction();

			// get file data
			$path = $this->get_global_event_filepath(array("id" => $glob_id));
			$fid = $this->file_model->add_file($path, $spf_key, $user);
			$success = $fid !== 0;

			if($success)
			{
				// insert the global event/file association
				$insert_array = array("Id_Global_Event" => $glob_id,
									  "Id_File" => $fid);

				$success &= $this->sql->insert("global_event_file", $this->sql->quote_all($insert_array));
			}

			if($success)
				$this->sql->commit();
			else
				$this->sql->rollback();

			return $success;
		}

		/**
		 * @brief Return the path in which to store a file associated with the given global event
		 * @param[in] int $id_glob The global event id
		 * @retval string The path, false on error
		 */
		private function get_global_event_filepath($id_glob)
		{
			// extract global event id
			$id_glob = $this->get_global_event_id($id_data);

			if($id_glob < 0)
				return false;

			$columns = array("ULg_Identifier", "Acad_Start_Year");
			$where = "Id_Global_Event = ".$this->sql->quote($id_glob);
			$global_event = $this->sql->select_one("global_event", $where, $columns);

			if(empty($global_event))
				return false;

			return "global_events/".$global_event['Acad_Start_Year']."/".$global_event['ULg_Identifier'];
		}

		/**
		 * @brief Delete a file associated with a global event
		 * @param[in] array $id_data The data for identifying the global event
		 * @param[in] int   $file_id The id of the file to delete
		 * @retval bool True on success, false on error
		 */
		public function delete_file(array $id_data, $file_id)
		{
			// extract global event id
			$id_glob = $this->get_global_event_id($id_data);

			if($id_glob < 0)
				return false;

			$this->sql->transaction();

			// delete the file/global event association
			$where = "Id_Global_Event = ".$this->sql->quote($id_glob).
					 " AND Id_File = ".$this->sql->quote($file_id);
			$success = $this->sql->delete("global_event_file", $where);

			// delete the actual file
			$success &= $this->file_model->delete_file($file_id);

			if(!$success)
				$this->sql->rollback();
			else
				$this->sql->commit();

			return $success;
		}

		/**
		 * @brief Add a pathway to the current global event
		 * @param[in] array  $id_data    The data for identifying the global event
		 * @param[in] string $pathway_id The id of the pathway to add
		 * @retval bool True on success, false on error
		 */
		public function add_pathway(array $id_data, $pathway_id)
		{
			// extract global event id
			$id_glob = $this->get_global_event_id($id_data);

			if($id_glob < 0)
				return false;

			$insert_array = array("Id_Global_Event" => $glob_id,
								  "Id_Pathway" => $pathway_id);
			return $this->sql->insert("global_event_pathway", $this->sql->quote_all($insert_array));
		}

		/**
		 * @brief Delete a pathway for the given global event
		 * @param[in] array  $id_data    The data for identifying the global event
		 * @param[in] string $pathway_id The pathway id
		 * @retval bool True on success, false on error
		 */
		public function delete_pathway(array $id_data, $pathway_id)
		{
			// extract global event id
			$id_glob = $this->get_global_event_id($id_data);

			if($id_glob < 0)
				return false;

			$this->sql->transaction();

			$where = "Id_Global_Event = ".$this->sql->quote($id_glob).
					 " AND Id_Pathway = ".$this->sql->quote($pathway_id);

			if(!$this->sql->delete("global_event_pathway", $where))
			{
				$this->sql->rollback();
				return false;
			}

			// delete subevent excluded pathways
			$query  =  "DELETE FROM sub_event_excluded_pathway 
						WHERE Id_Event IN ( SELECT Id_Event FROM sub_event WHERE Id_Global_Event = ? ) 
							AND Id_Pathway = ?;";

			if($this->sql->execute_query($query, array($id_glob, $pathway_id)))
			{
				$this->sql->commit();
				return true;
			}
			
			$this->sql->rollback();
			return false;
		}

		/**
		 * @brief Add a member in the team of the given global event
		 * @param[in] array $id_data The data for identifying the global event
		 * @param[in] int   $role_id One of the class ROLE_ID_* constant 
		 * @param[in] int   $user_id The user to get the role in the team (optionnal, default: currently connected user)
 		 * @retval bool True on success, false on error
		 */
		public function add_team_member(array $id_data, $role_id, $user_id=null)
		{
			if(!$this->valid_role_id($role_id))
				return false; 

			// default value for argument
			if($user_id == null) $user_id = $this->connection->user_id();
		
			// extract global event id
			$id_glob = $this->get_global_event_id($id_data);

			if($id_glob < 0)
				return false;

			$insert_data = array("Id_User" => $user_id,
								 "Id_Global_Event" => $id_glob,
								 "Id_Role" => $role_id);

			return $this->sql->insert("teaching_team_member", $this->sql->quote_all($insert_data));
		}

		/**
		 * @brief Delete a team member from the given global event's team
		 * @param[in] array $id_data The data for identifying the global event
		 * @param[in] int   $user_id The user identifier
		 * @retval bool True on success, false on error
		 */
		public function delete_team_member(array $id_data, $user_id)
		{
			// extract global event id
			$id_glob = $this->get_global_event_id($id_data);

			if($id_glob < 0)
				return false;

			$where = "Id_Global_Event = ".$this->sql->quote($id_glob).
					 " AND Id_User = ".$this->sql->quote($user_id);

			// delete from teaching team member : entry from 
			// sub_event_excluded_team_member are removed on cascade
			return $this->sql->delete("teaching_team_member", $where);
		}

		/**
		 * @brief Checks whether the user has access to the global event 
		 * @param[in] array $id_data The data for identifying the global event
		 * @param[in] int   $user_id The user id (optionnal, default: the currently connected user)
		 * @retval True if the user can access the global event, false otherwise
		 * @note If the user is a student he can access the event if he is either in the teaching team or 
		 * if he is registered to the global event. If the user is a professor, than he can access the global event if he
		 * is in the teaching team of the global event.
		 */
		public function global_event_user_has_read_access(array $id_data, $user_id=null)
		{
			if($user_id == null) $user_id = $this->connection->user_id();

			// extract global event id
			$id_glob = $this->get_global_event_id($id_data);

			if($id_glob < 0)
				return false;

			if($this->connection->user_is_student())
			{
				$query  =  "SELECT COUNT(what) AS cnt FROM  
							(
							    ( SELECT 'team_member' AS what
							     FROM teaching_team_member 
							     WHERE Id_Global_Event = ? AND Id_User = ? )
							    UNION 
							    ( SELECT 'student' AS what
							     FROM global_event_subscription 
							     WHERE Id_Global_Event = ? AND Id_Student = ? )
							) as what;";

				$result = $this->sql->execute_query($query, array($id_glob, $user_id, $id_glob, $user_id));

				return empty($result) ? false : $result[0]['cnt'] >= 1;
			}
			else
			{
				$q_id_glob = $this->sql->quote($id_glob);
				$q_id_user = $this->sql->quote($user_id);

				return $this->sql->count("teaching_team_member", "Id_Global_Event = ".$q_id_glob." AND Id_User = ".$q_id_user) > 0;
			}
		}

		/**
		 * @brief Checks whether the user has the write acces on the global event
		 * @param[in] array $id_data The data for identifying the global event
		 * @param[in] int   $user_id The user id (optionnal, default: the currently connected user)
		 * @retval bool True if the user has write access, false otherwise
		 * @note The user has write access if he is either a professor or a teaching assistant
		 */
		public function global_event_user_has_write_access(array $id_data, $user_id=null)
		{
			if($user_id == null) $user_id = $this->connection->user_id();

			// extract global event id
			$id_glob = $this->get_global_event_id($id_data);

			if($id_glob < 0)
				return false;

			$query  =  "SELECT COUNT(*) AS cnt FROM teaching_team_member 
						WHERE Id_Global_Event = ? AND Id_User = ? AND (Id_Role = ? OR Id_Role = ?);";

			$result = $this->sql->execute_query($query, array($id_glob, $user_id, self::ROLE_ID_PROFESSOR, self::ROLE_ID_TA));

			return empty($result) ? false : $result[0]['cnt'] > 0;
		}

		/**
		 * @brief Get the optionnal events to which the student has already subscribed or not
		 * @param[in] int $user_id   The user id (optionnal, default: the currently connected user)
		 * @param[in] int $acad_year The year starting the academic year (optionnal, default: current one)
 		 * @retval array A multidimensionnal array containing the optionnal global events of the given user
		 * @note Each row of the returned array contains the following fields :
		 * <ul>
		 *   <li>id: global event id</li>
		 *   <li>name_long: course name (long version)</li>
		 *   <li>name_short: course name (short version)</li>
		 *   <li>ulg_id: course ulg_id</li>
		 *   <li>acad_year: academic year (i.e. "2014-2015")</li>
		 *   <li>selected: boolean value specifying if the user is subscribed to the course</li>
		 * </ul>
		 */
		public function get_global_events_optionnal($user_id=null, $start_acad_year=null)
		{
			if($user_id == null) $user_id = $this->connection->user_id();
			if($start_acad_year == null) $start_acad_year = \ct\get_academic_year();

			$query  =  "SELECT Id_Global_Event AS id, Name_Long AS name_long, Name_Short AS name_short,
							   ULg_Identifier AS ulg_id, CONCAT(Acad_Start_Year, '-', Acad_Start_Year + 1) AS acad_year,
							   selected
						FROM global_event NATURAL JOIN
						(
						    (
						        SELECT Id_Global_Event, 0 AS selected FROM
						        ( SELECT Id_Global_Event FROM global_event WHERE Acad_Start_Year = ? ) AS globs
						        NATURAL JOIN
						        ( SELECT Id_Global_Event FROM global_event_pathway
						         NATURAL JOIN
						         ( SELECT Id_Pathway 
						           FROM student_pathway 
						           WHERE Id_Student = ? AND Acad_Start_Year = ? ) AS stud_path
						        ) AS stud_globs
						        WHERE Id_Global_Event NOT IN 
						        ( SELECT Id_Global_Event FROM global_event_subscription WHERE Id_Student = ? ) 
						    )
						    
						    UNION ALL
						    
						    ( SELECT Id_Global_Event, 1 AS selected FROM
						      ( SELECT Id_Global_Event FROM global_event WHERE Acad_Start_Year = ? ) AS globs 
						      NATURAL JOIN
						      ( SELECT Id_Global_Event 
						      	FROM global_event_subscription 
						      	WHERE Id_Student = ? AND Free_Student IS TRUE ) as subs
						    ) 
						) AS ids";
	
			$params = array($start_acad_year, $user_id, $start_acad_year, $user_id, $start_acad_year, $user_id);
			return $this->sql->execute_query($query, $params);
		}

		/**
		 * @brief Return the list of courses that a given professor can still create for the given academic year
		 * @param[in] int $acad_year The year starting the academic year (optionnal, default: current one)
 		 * @param[in] int $user_id   The user id (optionnal, default: the currently connected user)
 		 * @retval array An array containing the courses' id and names (short and long) (row keys : Id_Course, Name_Short, Name_Long)
		 */
		public function get_available_global_events($acad_year=null, $user_id=null)
		{
			if($acad_year == null) $acad_year = \ct\get_academic_year();
			if($user_id == null) $user_id = $this->connection->user_id();

			$query  =  "SELECT Id_Course, Name_Long, Name_Short 
						FROM ulg_course NATURAL JOIN
						( SELECT Id_Course FROM ulg_course_team_member 
						  WHERE Id_ULg_Fac_Staff = ( SELECT Id_ULg FROM user WHERE Id_User = ? ) ) AS fac_staff_courses
						WHERE Id_Course NOT IN
							( SELECT ULg_Identifier 
							  FROM global_event
							  WHERE Acad_Start_Year = ? );";

			return $this->sql->execute_query($query, array($user_id, $acad_year));
		}
	}