<?php

	/**
	 * @file
	 * @brief Contains the FileModel class
	 */

	namespace ct\models;

	require_once("functions.php");

	use util\mvc\Model;
	use util\superglobals\SG_Files;
	use ct\Connection;

	/**
	 * @class FileModel
	 * @brief A class for handling files that are store
	 */
	class FileModel extends Model
	{
		private $sg_file; /**< @brief The superglobal file object */
		private $root; /**< @brief The root file path */
		private $connection; /**< @brief The connection object */

		const TYPE_GLOBAL = "global"; /**< @brief Type of file : global event file*/
		const TYPE_ACAD = "acad"; /**< @brief Type of file : academic event file*/

		/**
		 * @brief Construct a FileModel object
		 */
		public function __construct()
		{
			parent::__construct();	
			$this->sg_file = new SG_Files();
			$this->root = "files";
			$this->connection = Connection::get_instance();
		}

		/**
		 * @brief Delete a file from the file table in the database (and delete the actual file)
		 * @param[in] int $fid The file id
		 * @retval bool True on success false on error
		 * @note If the mysql app does not support cascade delete and that the file id is used
		 * in another table, this entry must be deleted before calling this function
		 */
		public function delete_file($fid)
		{
			$file_data = $this->sql->select_one("file", "Id_File = ".$this->sql->quote($fid));

			if(file_exists($file_data['Filepath']) && !unlink($file_data['Filepath']))
				return false;

			return $this->sql->delete("file", "Id_File = ".$this->sql->quote($fid));
		}

		/**
		 * @brief Add a file into the database 
		 * @param[in] string $path    The new path of the file (without filename)
		 * @param[in] string $spf_key The key of the file entry in the $_FILES superglobal
		 * @param[in] int    $user    The file owner id (optionnal, default: currently connected user)
		 * @retval int The id of the added file on success, 0 on error
		 */
		public function add_file($path, $spf_key, $user=null)
		{
			if($user == null) $user = $connection->user_id();

			if(!$this->sg_file->check_upload($spf_key))
				return 0;

			$name = $this->sg_file->name($spf_key); 
			if(!preg_match("#^.+\.[a-z]+$#", $name))
				$name .= ".".$this->extract_extension_from_mime($spf_key);

			if(!\ct\ends_with($path, "/"))
				$path = substr($path, 0, -1);

			$path = $this->add_root_to_path($path);

			if(!$this->sg_file->move_file($spf_key, $path, $name))
				return 0;

			$comp_path = $path."/".$name;

			$insert_array = array("Filepath" => $comp_path, 
								  "Name" => $name,
								  "Id_User" => $user);

			if(!$this->sql->insert("file", $this->sql->quote_all($insert_array)))
				return 0;

			return $this->sql->last_insert_id();
		}

		/**
		 * @brief Move the file having the given id to the new path
		 * @param[in] int 	   $fid 	 The file id
		 * @param[in] new_path $new_path The complete path where the file must be moved (including filename)
		 * @retval bool True on success, false on error
		 * @note The file extension in $new_path must be the same than the one of the file having the given file id
		 */
		public function move_file($fid, $new_path)
		{
			if(empty($new_path))
				return false;

			$file = $this->sql->select_one("file", "Id_File = ".$this->sql->quote($fid));

			$new_name = \basename($new_path);

			// check extension 
			if($this->extract_extension($file['Name']) !== $this->extract_extension($new_name))
				return false;

			$new_comp_path = $this->add_root_to_path($new_path);

			// move the file
			if(!rename($file['Filepath'], $new_comp_path))
				return false;

			// update database file entry
			$update_array = array("Filepath" => $new_comp_path,
								  "Name" => $new_name);

			return $this->sql->update("file", $this->sql->quote_all($update_array), "Id_File = ".$this->sql->quote($fid));
		}

		/**
		 * @brief Extract the file extension from the filepath
		 * @param[in] string $filepath The file path
		 * @retval string The extension, an empty string if nothing was found
		 */
		private function extract_extension($filepath)
		{
			$matches = array();

			if(!preg_match("#.*\.([a-z0-9]+)$#", $filepath, $matches))
				return "";

			return $matches[1];
		}

		/**
		 * @brief Add the root path to the given path
		 * @param[in] string $path The path to which the root path must be added
		 * @retval string The final path
		 */
		private function add_root_to_path($path)
		{
			if(\ct\starts_with($path, "/"))
				return $this->root.$path;
			else
				return $this->root."/".$path;
		}

		/**
		 * @brief Extrect file extension from the mime type of the uploaded file
		 * @param[in] string $spf_key The key of the uploaded file in the $_FILES array
		 * @retval string|bool The file extension, false on error 
		 */
		private function extract_extension_from_mime($spf_key)
		{
			$mime = $this->sg_file->type($spf_key);
			$matches = array();

			// match the mime type string
			if(!preg_match("#^([a-z]+)(?:/([a-z0-9]+))?$#", $mime, $matches))
				return false;

			switch($matches[1]) // check the first part of the mime_type
			{
			case "application":
			case "video":
			case "audio":
			case "image":
			case "text":

				switch($matches[2]) // modify the second part so that we can use it as a file extension
				{
				case "javascript": $matches[2] = "js"; break;
				case "plain": $matches[2] = "txt"; break;
				}

				return $matches[2];

			default:
				return false;
			}
		}

		/**
		 * @brief Check whether the given user has access to given file
		 * @param[in] int $file_id   The file identifier
		 * @param[in] int $acad_year The year starting the academic year for which the access must be checked (optionnal, default: current)
		 * @param[in] int $user_id   The user identifier (optionnal, default: currently connected user)
		 * @retval bool True if the user has access, false otherwise
		 */
		public function file_user_has_read_access($file_id, $acad_year=null, $user_id=null)
		{
			if($user_id == null) $user_id = Connection::get_instance()->user_id();

			// check whether the user is a student 
			$student = Connection::get_instance()->user_is_student();

			$file = $this->get_file_type($file_id);

			switch ($file['type']) 
			{
			case self::TYPE_GLOBAL:
				$glob_mod = new GlobalEventModel();

				return $glob_mod->global_event_user_has_read_access($file['id']);
			/** @todo implement access check for academic events */
			case self::TYPE_ACAD: return false;
				break;
			
			default: return false;

			}	
		}

		/**
		 * @brief Create a link for downloading the file having the given identifier
		 */
		public static function make_file_link($file_id)
		{
			return "download.php?file=".$file_id;
		}

		/**
		 * @brief Return the type of the given file
		 * @param[in] int $file_id The file identifier
		 * @retval array An array containing two items : 
		 * <ul>
		 *  <li>type : one of the class TYPE_* constant, empty string if the file does not exist </li>
		 *  <li>id : the identifier of the global event if the file is a global event file, the identifier of the event if 
		 *  the file is a global event file, 0 otherwise</li>
		 * </ul>
		 */
		public function get_file_type($file_id)
		{
			$query  =  "SELECT 'global' AS type, Id_Global_Event AS id FROM global_event_file WHERE Id_File = ?
						UNION ALL
						SELECT 'acad' AS type, Id_Event AS id FROM academic_event_file WHERE Id_File = ?;";

			$file_type = $this->sql->execute_query($query, array($file_id, $file_id));
			
			return empty($file_type) ? array("type" => "", "id" => 0) : $file_type[0];
		}

	}