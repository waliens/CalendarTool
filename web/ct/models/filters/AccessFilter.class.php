<?php

	/**
	 * @file
	 * @brief Contains the AccessFilter class
	 */

	namespace ct\models\filters;

	require_once("functions.php");

	use ct\models\filters\EventFilter;
	use ct\models\events\GlobalEventModel;
	use ct\models\UserModel;

	use ct\Connection;

	/**
	 * @class AccessFilter
	 * @brief A class for filtering event according to user rights tuned with a policy (can a user see this event?)
	 * The AS_STUDENT policy is the following :
	 * - A student can see his private events
	 * - A student can see the subevents of global event he is subscribed to and for which his pathway is not excluded
	 * - A student can see the independent event to which his pathway is associated
	 * The AS_TEACHING_STUDENT policy is the following : 
	 * - A student can see the subevent of the global event he his a teaching student for
	 * - A student can see the independent event he his a teaching student for
	 * The AS_TEACHING_STUDENT | AS_STUDENT is the combination of the two previous policies
	 * The AS_FACULTY_MEMBER policy is the following :
	 * - A faculty member can see all the academic events
	 * - A faculty member cannot see the private events
	 * The AS_FACULTY_MEMBER_OWN policy is the following :
	 * - The faculty member only sees the events he is involved in
	 */
	class AccessFilter implements EventFilter
	{
		private $policy; /**< @brief The selected policy */
		private $connection; /**< @brief A connection object */
		private $user_id; /**< @brief The user id for which the access restriction must be computed */

		const POLICY_AS_STUDENT = 1; /**< @brief Events followed by the user as a student only */
		const POLICY_AS_TEACHING_STUDENT = 2; /**< @brief Events followed by the user as a teaching student only */
		const POLICY_AS_FACULTY_MEMBER = 4; /**< @brief All events except the private events */
		const POLICY_AS_FACULTY_MEMBER_OWN = 8; /**< @brief Events followed by the user as a faculty member only */

		/**
		 * @brief Construct the AccessFilter 
		 * @param[in] int $policy The policy for selecting events (a combination of POLICY_* 
		 * flags) (optionnal, default:the one that suits the most the user)
		 * @note The flags POLICY_AS_STUDENT and POLICY_AS_TEACHING_STUDENT cannot be combined with the POLICY_AS_FACULTY_MEMBER flag
		 * @note Default policy is given by the static functions get_***_def_policy according to the user type 
		 */
		public function __construct($policy=null,$user_id=null)
		{
			$this->connection = Connection::get_instance();

			// manage default user parameter 
			if($user_id == null) 
				$user_id = $this->connection->user_id();
			$this->user_id = $user_id;
			
			// manage default policy parameter
			if($policy == null) 
				$policy = $this->get_def_policy();
			
			if(!$this->valid_policy($policy))
				throw new \Exception("Bad policy");

			$this->policy = $policy;
		}

		/**
		 * @brief Get the default policy according to the type of user
		 * @retval int A policy
		 */
		private function get_def_policy()
		{
			$user_mod = new UserModel();

			if($user_mod->user_is_student($this->user_id))
				return self::get_student_def_policy();
			else
				return self::get_fac_member_def_policy();
		}

		/**
		 * @brief The default policy for the student : combination of as_teaching_student and as_student
		 * @retval int The default policy 
		 */
		public static function get_student_def_policy()
		{
			return self::POLICY_AS_TEACHING_STUDENT | self::POLICY_AS_STUDENT;
		}

		/**
		 * @brief The default policy for the student : as_faculty_member
		 * @retval int The default policy 
		 */
		public static function get_fac_member_def_policy()
		{
			return self::POLICY_AS_FACULTY_MEMBER;
		}

		/**
		 * @brief Checks whether the policy is valid 
		 * @param[in] int $policy A policy (a combination of POLICY_* flags)
		 * @retval bool True if the policy is valid, false otherwise
		 */
		private function valid_policy($policy)
		{
			return $policy == 1 || $policy == 2 || $policy == 3 || $policy == 4 || $policy = 8;
		}

		/** 
		 * @brief Return the query for selecting the events followed by the student as a student
		 * @retval string The SQL query
		 */
		private function get_as_student_query()
		{
			$q_user_id = "'".$this->user_id."'";
			$q_acad    = "'".\ct\get_academic_year()."'";

			return "SELECT Id_Event FROM 
					( 
						( SELECT Id_Event FROM student_event WHERE Id_Owner = $q_user_id) 
						UNION ALL
						( SELECT Id_Event -- independent event to which the user is associated
						  FROM independent_event_pathway NATURAL JOIN
						  ( SELECT Id_Pathway 
						  	FROM student_pathway 
						  	WHERE Id_Student = $q_user_id AND Acad_Start_Year = $q_acad) AS user_path
						) 
						UNION ALL
						( SELECT Id_Event FROM sub_event 
						  NATURAL JOIN -- id of global events to which a student is subscribed
						  ( SELECT Id_Global_Event
						  	FROM global_event_subscription
						  	WHERE Id_Student = $q_user_id
						  ) AS subs
						  NATURAL JOIN -- id of the global event that are associated with the student current pathway (+ pathway id)
						  ( SELECT Id_Global_Event, Id_Pathway 
						  	FROM global_event_pathway
						  	NATURAL JOIN 
							( SELECT Id_Pathway 
							  FROM student_pathway 
							  WHERE Id_Student = $q_user_id AND Acad_Start_Year = $q_acad) AS stud_path
						  ) AS paths
						  WHERE (Id_Event, Id_Pathway, Id_Global_Event) 
 						  NOT IN (SELECT Id_Event, Id_Pathway, Id_Global_Event 
 						  		  FROM sub_event_excluded_pathway )
						) 
					) AS user_events";
		}

		/**
		 * @brief Returns the query for selecting events followed by a student as a teaching student
		 * @retval string The SQL query
		 */ 
		private function get_as_teaching_student_query()
		{
			return $this->get_as_team_member_query(array(GlobalEventModel::ROLE_ID_TS));
		}

		private function get_as_faculty_member_query()
		{
			return "SELECT Id_Event FROM event  
					WHERE Id_Event NOT IN ( SELECT Id_Event FROM student_event )";
		}

		/**
		 * @brief Return the query for selecting events followed by a student as a teaching team member
		 * @param[in] array $roles A array of integers containing the role identifiers to restrict the selection
		 * @retval string The SQL query
		 */
		private function get_as_team_member_query(array $roles)
		{
			$quoted_roles = array_map(function($role) { return "'".$role."'"; }, $roles);
			$q_role_ids = "(".implode(", ", $quoted_roles).")";
			$q_user_id = "'".$this->user_id."'";

			return "SELECT Id_Event FROM 
					(
						( SELECT Id_Event FROM sub_event
						  NATURAL JOIN
						  ( SELECT Id_Global_Event, Id_User -- get global events for which the user is has the given roles
						    FROM teaching_team_member 
						    WHERE Id_Role IN $q_role_ids AND Id_User = $q_user_id ) as user_globs
						  WHERE (Id_Event, Id_Global_Event, Id_User) 
							  NOT IN ( SELECT Id_Event, Id_Global_Event, Id_User 
									   FROM sub_event_excluded_team_member )
						)
						UNION ALL
						( SELECT Id_Event -- independent event the user for which the user has the given roles
						  FROM independent_event_manager
						  WHERE Id_Role IN $q_role_ids AND Id_User = $q_user_id
						)
					) AS ts_events";
		}

		/**
		 * @copydoc EventFilter::get_sql_query
		 */
		public function get_sql_query()
		{
			switch ($this->policy) 
			{
			case self::POLICY_AS_STUDENT:
				return $this->get_as_student_query();
			case self::POLICY_AS_TEACHING_STUDENT:
				return $this->get_as_teaching_student_query();
			case 3 : // both as student and as teaching student
				return "(".$this->get_as_student_query().") UNION (".
						  $this->get_as_teaching_student_query().")";
			case self::POLICY_AS_FACULTY_MEMBER:
				return $this->get_as_faculty_member_query();
			case self::POLICY_AS_FACULTY_MEMBER_OWN:
				return $this->get_as_team_member_query(array(GlobalEventModel::ROLE_ID_PROFESSOR, 
												GlobalEventModel::ROLE_ID_TA));
			default:
				trigger_error("Invalid policy", E_USER_WARNING);
			}
		}

		/**
		 * @copydoc EventFilter::get_table_alias
		 */
		public function get_table_alias()
		{
			return "f_access_events";
		}
	}
