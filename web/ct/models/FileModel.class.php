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
		 * @brief
		 * @param[in]
		 * @retval
		 */
		public function delete_file($fid)
		{

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
			if($user == null) $user = ;

			if(!$this->sg_file->check_upload($spf_key))
				return 0;

			$name = $this->sg_file->name($spf_key)); 
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
		 * @brief
		 * @param[in]
		 * @retval
		 */
		public function move_file($fid, $)
		{

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
			if(!preg_match("#^([a-z]+)(?:/([a-z]+))?$#", $mime, $matches))
				return false;

			switch($matches[1])
			{
			case "application":
			case "video":
			case "audio":
			case "image":
			case "text":

				switch($matches[2])
				{
				case "javascript": $matches[2] = "js"; break;
				case "plain": $matches[2] = "txt"; break;
				}

				return $matches[2];

			default:
				return false;
			}
		}
	}