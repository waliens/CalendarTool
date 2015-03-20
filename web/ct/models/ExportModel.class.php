<?php

	/**
	 * @file 
	 * @brief Contains the ExportModel class
	 */

	namespace ct\models;

	use util\mvc\Model;

	use ct\Connection;
	use ct\ICSGenerator;
	
	use ct\models\FilterCollectionModel;
	use ct\models\filters\DateTimeFilter;
	use ct\models\filters\EventCategoryFilter;
	use ct\models\filters\EventTypeFilter;
	use ct\models\filters\AccessFilter;
	use ct\models\filters\GlobalEventFilter;
	use ct\models\filters\PathwayFilter;
	use ct\models\filters\TimeTypeFilter;
	use ct\models\filters\ProfessorFilter;

	/**
	 * @class ExportModel 
	 * @brief A class for hanlind export related database queries
	 */
	class ExportModel extends Model
	{
		private $filter_id_array; /**< @brief Array mapping filters's database id and its class fully qualified name  */

		const FILTER_DATETIME 		= 1; /**< @brief Type of filter : datetime filter */
		const FILTER_ACCESS 		= 2; /**< @brief Type of filter : access filter */
		const FILTER_EVENT_CATEGORY = 3; /**< @brief Type of filter : event category filter */
		const FILTER_EVENT_TYPE 	= 4; /**< @brief Type of filter : event type filter */
		const FILTER_GLOBAL_EVENT 	= 5; /**< @brief Type of filter : global event filter */
		const FILTER_PATHWAY 		= 6; /**< @brief Type of filter : pathway filter */
		const FILTER_PROFESSOR 		= 7; /**< @brief Type of filter : professor filter */
		const FILTER_TIME_TYPE 		= 8; /**< @brief Type of filter : time type filter */

		const PATH_DYNAMIC_EXPORT_FILES = "files/export/dynamic/"; /**< @brief Contains the dynamic export files folder path */
		const PATH_STATIC_EXPORT_FILES = "files/export/static/"; /**< @brief Contains the static export files folder path */
		
		const EXPORT_TYPE_STATIC = "static"; /**< @brief Export type : static */
		const EXPORT_TYPE_DYNAMIC = "dynamic"; /**< @brief Export type : dynamic */

		/** 
		 * @brief Construct the ExportModel object
		 */
		public function __construct()
		{
			parent::__construct();
			$this->set_filter_id_array();
		}

		/**
		 * @brief Initialize the filter id array 
		 */
		private function set_filter_id_array()
		{
			$this->filter_id_array = array();

			$datetime_cls = get_class(new DateTimeFilter("02-02-2015"));
			$access_cls   = get_class(new AccessFilter());
			$ev_categ_cls = get_class(new EventCategoryFilter(array(1)));
			$ev_type_cls  = get_class(new EventTypeFilter(EventTypeFilter::TYPE_ACADEMIC));
			$glob_cls     = get_class(new GlobalEventFilter(array(1)));
			$path_cls     = get_class(new PathwayFilter(array("ABICIV000301")));
			$prof_cls     = get_class(new ProfessorFilter(array(1)));
			$timetype_cls = get_class(new TimeTypeFilter(TimeTypeFilter::TYPE_DEADLINE));

			$this->filter_id_array[$datetime_cls] = self::FILTER_DATETIME;
			$this->filter_id_array[$access_cls]   = self::FILTER_ACCESS;
			$this->filter_id_array[$ev_categ_cls] = self::FILTER_EVENT_CATEGORY;
			$this->filter_id_array[$ev_type_cls]  = self::FILTER_EVENT_TYPE;
			$this->filter_id_array[$glob_cls] 	  = self::FILTER_GLOBAL_EVENT;
			$this->filter_id_array[$path_cls] 	  = self::FILTER_PATHWAY;
			$this->filter_id_array[$prof_cls] 	  = self::FILTER_PROFESSOR;
			$this->filter_id_array[$timetype_cls] = self::FILTER_TIME_TYPE;
		}

		/**
		 * @brief Check whether the given user has already a hash
		 * @param[in] int $user_id The user id (optionnal, default: currently connected user)
		 * @param[in] string $export_type One of the class EXPORT_TYPE_* constant indicating the type of export
		 * @retval bool True if the user has already a hash, false otherwise
		 */
		public function user_has_hash($user_id=null, $export_type)
		{
			if($user_id == null) $user_id = Connection::get_instance()->user_id();

			switch ($export_type) {
				case self::EXPORT_TYPE_STATIC:
					$query  =  "SELECT Id_User FROM event_export 
								WHERE Id_User = ? AND Id_Export NOT IN ( SELECT * FROM dynamic_export );";
					$result = $this->sql->execute_query($query, array($user_id));
					return !empty($result);
				case self::EXPORT_TYPE_DYNAMIC:
					return $this->sql->count("event_export NATURAL JOIN dynamic_export", 
											 "Id_User = ".$this->sql->quote($user_id)) > 0;
				default:
					trigger_error("Invalid export type", E_USER_ERROR);
			}
		}

		/**
		 * @brief Add a hash for the given user
		 * @param[in] int $user_id The user id (optionnal, default: currently connected user)
		 * @param[in] string $export_type One of the class EXPORT_TYPE_* constant indicating the type of export
		 * @retval bool True if the hash was successfully added, false otherwise
		 */
		private function user_add_hash($user_id=null, $export_type)
		{
			if($user_id == null) $user_id = Connection::get_instance()->user_id();

			// if user has already a hash, no need to add one
			if($this->user_has_hash($user_id, $export_type))
				return true;

			// start the insertion
			$this->sql->transaction();

			$export_data = array("User_Hash" => $this->get_hash($user_id),
								 "Id_User" => $user_id);

			// insert the base export data : hash + user id
			$success = $this->sql->insert("event_export", $this->sql->quote_all($export_data));

			if($export_type === self::EXPORT_TYPE_DYNAMIC) // filter is dynamic : add an entry into the dynamic_export table
			{
				$dynam_data = array("Id_Export" => $this->sql->last_insert_id());
				$success &= $this->sql->insert("dynamic_export", $this->sql->quote_all($dynam_data));
			}

			if($success)
				$this->sql->commit();
			else
				$this->sql->rollback();

			return $success;
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
		 * @brief Generate the ics dynamic export file for the given user 
		 * @param[in] int $user_id The user id (optionnal, default: currently connected user)
		 * @retval bool True on success, false on error
		 * @note The filters are extracted from the database
		 */
		public function generate_dynamic_export_file($user_id=null)
		{
			trigger_error("'generate_dynamic_export_file' : Unimplemented", E_USER_ERROR);
			if($user_id == null) $user_id = Connection::get_instance()->user_id();

			// check if the user has already a hash
			if(!$this->user_has_hash($user_id))
				return false;

			// extract the filters set by the user
			$filters_data = $this->get_filters($user_id);
			$filters = array_map(function(array $filter) { return unserialize($filter['Value']); });

			if(empty($filters))
				return false;

			// create the filter collection
			$filter_collection = new FilterCollectionModel();
			$filter_collection->add_filters($filters);
			$filter_collection->add_access_filter(new AccessFilter($user_id));

			// generate the ICS
			$ics_gen = new ICSGenerator($filter_collection);

			return file_put_contents($this->get_export_file_path(self::EXPORT_TYPE_DYNAMIC, $user_id), $ics_gen->get_ics());
		}

		/**
		 * @brief Generate the ics static export file for the given user
		 * @param[in] array $fitlers Array of filters object (should not contain an access filter)
		 * @param[in] int $user_id The user id (optionnal, default: currently connected user)
		 * @retval bool True on success, false on error
		 */
		public function generate_static_export_file(array $filters, $user_id=null)
		{
			if($user_id == null) $user_id = Connection::get_instance()->user_id();

			// add a hash to the user if necessary
			if(!$this->user_add_hash($user_id, self::EXPORT_TYPE_STATIC))
				return false;

			// get the filepath for the export file
			$filepath = $this->get_export_file_path(self::EXPORT_TYPE_STATIC, $user_id);

			if(!$filepath)
				return false;

			// remove access filters from the set of filters
			$filters = array_filter($filters, function($filter) { return !($filter instanceof AccessFilter); });

			// create the filter collection
			$filter_collection = new FilterCollectionModel();
			$filter_collection->add_filters($filters);
			$filter_collection->add_access_filter(new AccessFilter(null, $user_id));

			// generate the ICS
			$ics_gen = new ICSGenerator($filter_collection);

			return $this->write_export_file($filepath, $ics_gen->get_ics());
		}

		/**
		 * @brief Return the filters associated with the given user
		 * @param[in] int $user_id The user id (optionnal, default: currently connected user)
		 * @retval array An array containing the filters data. Each row contain the following keys :
		 * <ul>
		 *  <li>Id_User: the user id</li>
		 *  <li>Id_Filter : filter identifier</li>
		 *  <li>Value : serialized filter object</li>
		 * </ul>
		 * @todo Implement the instantiation of filters 
		 */
		public function get_filters($user_id=null)
		{
			trigger_error("'get_filters' : unimplemented", E_USER_ERROR);
			if($user_id == null) $user_id = Connection::get_instance()->user_id();
		}

		/**
		 * @brief Save the dynamic export filters for the given user
		 * @param[in] array $filters An array of EventFilter object
		 * @param[in] int   $user_id The user id (optionnal, default: currently connected user)
		 * @retval bool True on success, false on error
		 */
		public function set_dynamic_export_filters(array $filters, $user_id=null)
		{
			trigger_error("'set_dynamic_export_filters' : unimplemented");
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

				if(!$this->sql->insert("export_filter", $this->sql->quote_all($insert_data)))
				{
					$this->sql->rollback();
					return false;
				}
			}

			$this->sql->commit();

			return;
		}

		/**
		 * @brief Return the export file name of the given user 
		 * @param[in] int $user_id The user id (optionnal, default: currently connected user)
		 * @retval string The filename of the given user's export file, false on error
		 */
		public function get_export_filename($user_id=null)
		{
			if($user_id == null) $user_id = Connection::get_instance()->user_id();

			$export = $this->sql->select_one("event_export", "Id_User = ".$this->sql->quote($user_id));

			return empty($export) ? false : $export['User_Hash'].".ics";
		}
		
		/**
		 * @brief Return the filepath (with the filename) of the given user's export file
		 * @param[in] string $export_type One of the class EXPORT_TYPE_* constant indicating the type of export
		 * @param[in] int    $user_id     The user id (optionnal, default: currently connected user)
		 * @retval string The filename of the given user's export file, false on error
		 */
		public function get_export_file_path($export_type, $user_id=null)
		{
			if($user_id == null) $user_id = Connection::get_instance()->user_id();

			$filename = $this->get_export_filename($user_id);

			if(!$filename)
				return false;

			return $this->get_export_path($export_type).$filename;
		}

		/**
		 * @brief Return the filepath for the given export type 
		 * @param[in] string $export_type One of the class EXPORT_TYPE_* constant indicating the type of export
		 * @retval string The path
		 */
		private function get_export_path($export_type)
		{
			return $export_type == self::EXPORT_TYPE_STATIC ? 
									self::PATH_STATIC_EXPORT_FILES :
									self::PATH_DYNAMIC_EXPORT_FILES;
		}

		/**
		 * @brief Delete the export settings and export file of the given user
		 * @param[in] int $user_id The user id (optionnal, default: currently connected user)
		 * @param[in] string $export_type One of the class EXPORT_TYPE_* constant indicating the type of export
		 * @retval bool True on success, false on error
		 */
		public function delete_export($user_id=null, $export_type)
		{
			if($user_id == null) $user_id = Connection::get_instance()->user_id();

			if(!$this->user_has_hash())
				return true;

			if(!unlink($this->get_export_file_path($export_type, $user_id)))
				return false;

			return $this->sql->delete("event_export", "Id_User = ".$this->sql->quote($user_id));
		}

		/**
		 * @brief Actually write the export file (content) in the given complete filepath
		 * @param[in] string $path    The path string
		 * @param[in] string $content The content to write into the file
		 * @retval bool True on success, false on error
		 */
		public function write_export_file($path, $content)
		{
			return file_put_contents($path, $content, FILE_USE_INCLUDE_PATH);
		}
	}

