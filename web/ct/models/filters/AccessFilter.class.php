<?php

	/**
	 * @file
	 * @brief Contains the AccessFilter class
	 */

	namespace ct\models\filters;

	require_once("functions.php");

	use ct\models\filters\EventFilter;
	use ct\models\events\GlobalEventModel;
	use ct\Connection;

	/**
	 * @class AccessFilter
	 * @brief A class for filtering event according to user rights (can a user see this event?)
	 */
	class AccessFilter extends EventFilter
	{
		private $policy; /**< @brief The selected policy */

		const POLICY_AS_STUDENT = 1; /**< @brief Events followed by the user as a student only */
		const POLICY_AS_TEACHING_STUDENT = 2; /**< @brief Events followed by the user as a teaching student only */
		const POLICY_AS_FACULTY_MEMBER = 4; /**< @brief Events followed by the user as a faculty member only */

		/**
		 * @brief Construct the AccessFilter 
		 * @param[in] int $policy The policy for selecting events (a combination of POLICY_* flags)
		 * @note The flags POLICY_AS_STUDENT and POLICY_AS_TEACHING_STUDENT cannot be combined with the POLICY_AS_FACULTY_MEMBER flag
		 */
		public function __construct($policy)
		{
			if(!$this->valid_policy($policy))
				throw new \Exception("Bad policy");

			$this->policy = $policy;
		}

		/**
		 * @brief Checks whether the policy is valid 
		 * @param[in] int $policy A policy (a combination of POLICY_* flags)
		 * @retval bool True if the policy is valid, false otherwise
		 */
		private function valid_policy($policy)
		{
			return $policy == 1 || $policy == 2 || $policy == 3 || $policy == 4;
		}

		/** 
		 * @brief Return the query for selecting the events followed by the student as a student
		 * @retval string The SQL query
		 */
		private function get_as_student_query()
		{
			$q_user_id = "'".$this->connection->user_id()."'";
			$q_acad    = "'".\ct\get_academic_year()."'";

			return "SELECT Id_Event FROM 
					( 
						( SELECT Id_Event FROM student_event WHERE Id_Owner = $q_user_id) 
						UNION
						( SELECT Id_Event 
						  FROM independent_event_pathway NATURAL JOIN
						  ( SELECT Id_Pathway 
						  	FROM student_pathway 
						  	WHERE Id_Student = $q_user_id AND Acad_Start_Year = $q_acad) AS user_path
						) 
						UNION
						( SELECT Id_Event FROM event 
						  NATURAL JOIN
						  ( SELECT Id_Global_Event, Id_Student 
						  	FROM global_event_subscription
						  	WHERE Id_Student = $q_user_id
						  ) AS subs
						  NATURAL JOIN
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
			return $this->get_as_team_member_query(array(GlobalEventModel::ROLE_ID_TA,
														 GlobalEventModel::ROLE_ID_PROFESSOR))
		}

		/**
		 * @brief Return the query for selecting events followed by a student as a teaching team member
		 * @param[in] array $roles A array of integers containing the role identifiers to restrict the selection
		 * @retval string The SQL query
		 */
		private function get_as_team_member_query(array $roles)
		{
			$quoted_roles = array_map(function($role) { return "'".$role."'"}, $roles);
			$q_role_ids = "(".implode(", ", $quoted_roles).")";
			$q_user_id = "'".$this->connection->user_id()."'";

			return "SELECT Id_Event FROM 
					(
						( SELECT Id_Event FROM event
						  NATURAL JOIN
						  ( SELECT Id_Global_Event, Id_User 
						    FROM teaching_team_member 
						    WHERE Id_Role IN $q_role_ids AND Id_User = $q_user_id ) as user_globs
						  WHERE (Id_Event, Id_Global_Event, Id_User) 
							  NOT IN ( SELECT Id_Event, Id_Global_Event, Id_User 
									   FROM sub_event_excluded_team_member )
						)
						UNION ALL
						( SELECT Id_Event 
						  FROM independent_event_manager
						  WHERE Id_Role IN $q_role_ids AND Id_User = $q_user_id
						)
					) AS ts_events";
		}

		public function get_sql_query()
		{
			switch ($this->policy) 
			{
			case self::POLICY_AS_STUDENT:
				return $this->get_as_student_query();
			case self::POLICY_AS_TEACHING_STUDENT:
				return $this->get_as_teaching_student_query();
			case 3 : // both as student and as teaching student
				return "(".$this->get_as_student_query().") UNION ALL (".
						  $this->get_as_teaching_student_query().")";
			case self::POLICY_AS_FACULTY_MEMBER:
				return $this->get_as_faculty_member_query();
				
			default:
				trigger_error("Invalid policy", E_USER_WARNING);
			}
		}

		public function get_table_alias()
		{
			return "f_access_events";
		}
	}
