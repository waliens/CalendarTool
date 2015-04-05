<?php

	/**
	 * @file
	 * @brief Contains the class SG_Files
	 */

	namespace util\superglobals;

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
			parent::__construct();
			$this->superglobal = &$_FILES;
		}

		/**
		 * @brief Check whether the file at the given key was successfully uploaded
		 * @param[in] string $key The key identifying the element in the $_FILES superglobal
		 * @retval bool True if the file was successfully downloaded, false otherwise
		 */
		public function check_upload($key)
		{
			if(!$this->check($key, Superglobal::CHK_ISSET))
				return false;
			$value = $this->value($key);
			return $value['error'] == \UPLOAD_ERR_OK;
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

			if(is_null($name)) $name = $this->name($key);
			
			move_uploaded_file($this->tmp_name($key), $dest."/".$name);
		}


		/**
		 * @brief Get the value of the field 'type'
		 * @param[in] string $key The key identifying the element in the $_FILES superglobal
		 * @retval string The field, false on error
		 */	
		public function mime_type($key)
		{
			return $this->get_field($key, 'type');
		}

		/**
		 * @brief Get the value of the field 'tmp_name'
		 * @param[in] string $key The key identifying the element in the $_FILES superglobal
		 * @retval string The field, false on error
		 */
		public function tmp_name($key)
		{
			return $this->get_field($key, 'tmp_name');
		}

		/**
		 * @brief Get the value of the field 'error'
		 * @param[in] string $key The key identifying the element in the $_FILES superglobal
		 * @retval string The field, false on error
		 */
		public function error($key)
		{
			return $this->get_field($key, 'error');
		}

		/**
		 * @brief Get the value of the field 'name'
		 * @param[in] string $key The key identifying the element in the $_FILES superglobal
		 * @retval string The field, false on error
		 */
		public function name($key)
		{
			return $this->get_field($key, 'name');
		}

		/**
		 * @brief Get the value of the field 'size'
		 * @param[in] string $key The key identifying the element in the $_FILES superglobal
		 * @retval string The field, false on error
		 */
		public function size($key)
		{
			return $this->get_field($key, 'size');
		}

		/**
		 * @brief Get the value of the field for the given key in the $_FILES array
		 * @param[in] string $key   The key identifying the element in the $_FILES superglobal
		 * @param[in] string $field The field to get
		 * @retval string The field, false on error
		 */
		private function get_field($key, $field)
		{
			if(!$this->check($key, Superglobal::CHK_ISSET))
				return false;

			return $_FILES[$key][$field];
		}
	}
