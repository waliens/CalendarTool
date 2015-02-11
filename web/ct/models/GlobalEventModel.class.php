<?php
	
	/**
	 * @file
	 * @brief Contains the GlobalEventModel
	 */

	namespace ct\models;

	require_once("functions.php");

	/**
	 * @class GlobalEventModel
	 * @brief A class for handling global event related database queries
	 */
	class GlobalEventModel
	{
		private $connection; /**< @brief The connection object */

		const LANG_FR = "FR"; /**< @brief Language constant : french */
		const LANG_EN = "EN"; /**< @brief Language constant : english */
		/**
		 * @brief Constructs a GlobalEventObject
		 */
		public function __construct()
		{
			parent::__construct();
			$this->connection = Connection::get_instance();
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
			if($user_id == null) $user_id = $this->connection->user_ulg_id();
			if($acad_year == null) $acad_year = \ct\get_academic_year();

			if(!checkdate(1, 1, $acad_year)) // check whether the year is valid
				return false;

			$query  =  "INSERT INTO global_event(ULg_Identifier, Name_Short, Name_Long, 
												 Period, Workload_Th, Workload_Pr, 
												 Workload_Au, Workload_St, Acad_Start_Year, 
												 Id_Owner)
						SELECT Id_Course, Name_Short, Name_Long, Period, 
								Hr_Th, Hr_Pr, Hr_Au , Hr_St, ? AS Year, ? AS Owner
						FROM ulg_course WHERE Id_Course = ?;";

			if(!$this->execute_query($query, array($acad_year, $user_id, $course_id)))
				return false;

			return $this->sql->count("global_event", 
									 "ULg_Identifier = ".$this->sql->quote($course_id).
									 " AND Acad_Start_Year = ".$this->sql->quote($acad_year));
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
	}