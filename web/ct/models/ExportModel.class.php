<?php

	/**
	 * @file 
	 * @brief Contains the ExportModel class
	 */

	namespace ct\models;

	use util\mvc\Model;
	use ct\Connection;

	use ct\models\filters\DateTimeFilter;
	use ct\models\filters\EventCategoryFilter;
	use ct\models\filters\EventTypeFilter;

	/**
	 * @class ExportModel 
	 * @brief A class for hanlind export related database queries
	 */
	class ExportModel extends Model
	{
		private $filter_id_array; /**< @brief Array mapping filters' class name and filter database id */

		const FILTER_DATETIME 		= 1; /**< @brief Type of filter : datetime filter */
		const FILTER_ACCESS 		= 2; /**< @brief Type of filter : access filter */
		const FILTER_EVENT_CATEGORY = 3; /**< @brief Type of filter : event category filter */
		const FILTER_EVENT_TYPE 	= 4; /**< @brief Type of filter : event type filter */
		const FILTER_GLOBAL_EVENT 	= 5; /**< @brief Type of filter : global event filter */
		const FILTER_PATHWAY 		= 6; /**< @brief Type of filter : pathway filter */
		const FILTER_PROFESSOR 		= 7; /**< @brief Type of filter : professor filter */
		const FILTER_TIME_TYPE 		= 8; /**< @brief Type of filter : time type filter */

		/** 
		 * @brief Construct the ExportModel object
		 */
		public function __construct()
		{
			parent::__construct();
		}

		/**
		 * @brief Initialize the filter id array 
		 */
		private function set_filter_id_array()
		{
			$this->filter_id_array = array();

			$datetime_cls = get_class(new DateTimeFilter("02-02-2015"));
			$access_cls   = get_class(new AccessFilter());
			$ev_categ_cls = get_class(new EventCategoryFilter(array(1));
			$ev_type_cls  = get_class(new EventTypeFilter(EventTypeFilter::TYPE_ACADEMIC));
			$glob_cls     = get_class(new GlobalEventFilter(array(1)));
			$path_cls     = get_class(new PathwayFilter(array("ABICIV000301"));
			$prof_cls     = get_class(new ProfessorFilter(array(1)));
			$timetype_cls = get_class(new TimeTypeFilter(TimeTypeFilter::TYPE_DEADLINE));

			$this->filter_id_array[$datetime_cls] 	= self::FILTER_DATETIME;
			$this->filter_id_array[$access_cls] 	= self::FILTER_ACCESS;
			$this->filter_id_array[$ev_categ_cls] 	= self::FILTER_EVENT_CATEGORY;
			$this->filter_id_array[$ev_type_cls] 	= self::FILTER_EVENT_TYPE;
			$this->filter_id_array[$glob_cls] 		= self::FILTER_GLOBAL_EVENT;
			$this->filter_id_array[$path_cls] 		= self::FILTER_PATHWAY;
			$this->filter_id_array[$prof_cls] 		= self::FILTER_PROFESSOR;
			$this->filter_id_array[$timetype_cls] 	= self::FILTER_TIME_TYPE;
		}

		/**
		 * @brief Check whether the given user has already a hash
		 * @param[in] int $user_id The user id (optionnal, default: currently connected user)
			 * @retval bool True if the user has already a hash, false otherwise
			 */
		public function user_has_hash($user_id=null)
		{
			if($user_id == null) $user_id = Connection::get_instance()->user_id();

			return $this->sql->count("event_export", "Id_User = ".$this->sql->quote($user_id)) > 0;

		}

		/**
		 * @brief Add a hash for the given user
		 * @param[in] int $user_id The user id (optionnal, default: currently connected user)
			 * @retval bool True if the hash was successfully added, false otherwise
			 */
		public function add_user_hash($user_id=null)
		{
			if($user_id == null) $user_id = Connection::get_instance()->user_id();

			if($this->user_has_hash)
				return true;

			$insert_data = array("User_Hash" => $this->get_hash($user_id),
								 "Id_User" => $user_id);

			return $this->sql->insert("event_export", $this->sql->quote_all($insert_data));
		}

		/**
		 * @brief Generate a hash for the given user id
		 * @retval string A 32-byte hash
		 */
		private function get_hash($user_id)
		{
			return md5(rand().$user_id);
		}


		/**
		 * @brief Genereate the ics export file for the given user 
		 * @param[in] int $user_id The user id (optionnal, default: currently connected user)
		 * @retval bool True on success, false on error
		 */
		public function generate_file($user_id=null)
		{
			if($user_id == null) $user_id = Connection::get_instance()->user_id();

			// unserialize the filters
			// get the events
			// generate the ICS

		}

		/**
		 * @brief Set the static export filter for the given user
		 * @param[in] array $filters An array of EventFilter object
		 * @param[in] int   $user_id The user id (optionnal, default: currently connected user)
		 * @retval bool True on success, false on error
		 */
		public function set_export_filters(array $filters, $user_id=null)
		{
			if($user_id == null) $user_id = Connection::get_instance()->user_id();

			$this->sql->transaction();

			// add hash
			if(!$this->user_add_hash($user_id))
			{
				$this->sql->rollback();
				return false;
			}

			// delete previous filters
			if(!$this->sql->delete("export_filter", "Id_User = ".$this->sql->quote($user_id)))
			{
				$this->sql->rollback();
				return false;
			}

			// add the filters
			foreach($filters as $filter)
			{
				$insert_data = array("Id_User" => $user_id,
									 "Id_Filter" => $this->filter_id_array[get_class($filter)],
									 "Value" => serialize($filter));

				if(!$this->sql->insert("export_filter", $this->sql->quote_all($insert_data))
				{
					$this->sql->rollback();
					return false;
				}
			}

			$this->sql->commit();

			return;
		}

		/**
		 * @brief Return the filename of the given user 
		 */
		public function get_export_filename()
		{

		}			
	}