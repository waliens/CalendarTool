<?php

	/**
	 * @file
	 * @brief Contains the FileModel class
	 */

	namespace ct\models;

	use util\mvc\Model;
	use util\superglobals\SG_Files;

	/**
	 * @class FileModel
	 * @brief A class for handling files that are store
	 */
	class FileModel extends Model
	{
		private $sg_file; /**< @brief The superglobal file object */

		/**
		 * @brief Construct a FileModel object
		 */
		public function __construct()
		{
			parent::__construct();	
			$this->sg_file = new SG_Files();
		}

		public function delete_file($fid)
		{

		}

		public function add_file()
		{

		}

		public function move_file($fid)
		{

		}
	}