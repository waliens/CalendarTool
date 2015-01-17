<?php

	/**
	 * @file
	 * @brief Contains the class SG_Files
	 */

	namespace ct\util\superglobals;

	/**
	 * @class SG_Files
	 * @brief Class for handling the $_FILES superglobal using the Superglobal class interface
	 */
	class SG_Files extends Superglobal
	{
		/**
		 * @brief Constructor
		 */
		public function __construct()
		{
			$this->superglobal = &$_FILES;
		}

		/**
		 * @brief Check whether the file at the given key was successfully uploaded
		 * @param[in] string $key The key identifying the element in the $_FILES superglobal
		 * @retval bool True if the file was successfully downloaded, false otherwise
		 */
		public function check_upload($key)
		{
			return $this->check($key, Superglobal::CHK_ISSET) && $this->value($key)['error'] == \UPLOAD_ERR_OK; 
		}

		/**
		 * @brief Move an uploaded file to a new location
		 * @param[in] string $key  The key identifying the element in the $_FILES superglobal
		 * @param[in] string $dest The path of the destination folder (must not be terminated by a slash)
		 * @param[in] string $name The new name of the file (default: null => initial name is ketp)
		 * @retval bool True if the file was successfully moved, false otherwise
		 * 
		 * Example :
		 * @code
		 * // moving a png image to a new location : '../images/my_image.png'
		 * $sg_file->move_file("logo", "../images", "my_image.png");
		 * @endcode
		 */
		public function move_file($key, $dest, $name=null)
		{
			if(!$this->check_upload($key))
				return false;

			if(is_null($name)) $name = $this->value($key)['name'];
			
			move_uploaded_file($this->value($key)['tmp_name'], $dest."/".$name);
		}
	}