<?php
	
	/**
	 * @file
	 * @brief Contains the TypeChecker class
	 */

	namespace util;

	/** 
	 * @class TypeChecker
	 * @brief Class for checking if a string matches a SQL-like type
	 */
	class TypeChecker
	{
		const TYPE_VARCHAR  = "VARCHAR"; /**< @brief Type : varchar */ 
		const TYPE_CHAR     = "CHAR"; /**< @brief Type : char */
		const TYPE_BOOL	    = "BOOL"; /**< @brief Type : bool */
		const TYPE_SET      = "SET"; /**< @brief Type : set */
		const TYPE_FILE     = "FILE"; /**< @brief Type : file */
		const TYPE_ENUM     = "ENUM"; /**< @brief Type : enum */
		const TYPE_EREG     = "EREG"; /**< @brief Type : ereg */
		const TYPE_INT      = "INT"; /**< @brief Type : int */
		const TYPE_SMALLINT = "SMALLINT"; /**< @brief Type : smallint */
		const TYPE_TEXT     = "TEXT"; /**< @brief Type : text */
		const TYPE_TINYINT  = "TINYINT"; /**< @brief Type : tinyint (alias of bool) */

		const ERROR_NOT_NUMERIC    = 1; /**< @brief Error code : the value is not a number */
		const ERROR_REGEX_NO_MATCH = 2; /**< @brief Error code : the value didn't match the regex */
		const ERROR_NOT_IN_ENUM    = 3; /**< @brief Error code : the value is not in the enumeration */
		const ERROR_NOT_IN_SET     = 4; /**< @brief Error code : the value is not in the set */

		const REGEX_TYPE = "#^([a-zA-Z]+)\((.*)\)(.)*$#i"; /**< @brief Regex matching a valid type string */

		private $type = null; /**< @brief Current type */
		private $parameters = null; /**< @brief Type unquoted parameters */

		/**
		 * @brief Set the type of the checker
		 * @param[in] string $type The string representation of the type
		 */
		public function set_type($type)
		{
			if(!TypeChecker::valid_type($type))
				throw new \Exception("Invalid type format");

			$this->parse_type_string($type);
		}

		/**
		 * @brief Checks the validity of a type string
		 * @param[in] string $type The type string
		 * @retval bool True if the type is valid, false otherwise
		 */
		public static function valid_type($type)
		{
			$matches = array();
			$valid = preg_match(TypeChecker::REGEX_TYPE, $type, $matches);

			if(!$valid)
				return false;

			$type_name = strtoupper($matches[1]);
			$type_params = $matches[2];

			switch($type_name)
			{
				case TypeChecker::TYPE_BOOL :
				case TypeChecker::TYPE_TEXT :
					return empty($type_params);
					
				case TypeChecker::TYPE_CHAR :
				case TypeChecker::TYPE_VARCHAR :
				case TypeChecker::TYPE_SMALLINT :
				case TypeChecker::TYPE_TINYINT :
				case TypeChecker::TYPE_INT :
					return is_numeric($type_params);
					
				case TypeChecker::TYPE_EREG :
				case TypeChecker::TYPE_FILE :
					$exploded = TypeChecker::explode_unquoted(",", $type_params);
					return (count($exploded) == 1);
					
				case TypeChecker::TYPE_ENUM :
				case TypeChecker::TYPE_SET :
					$exploded = TypeChecker::explode_unquoted(",", $type_params);
					return !empty($exploded);
		
				default:
					return false; // type name doesn't match any type
			}
		}
		
		/**
		 * @brief Explodes a string around a delimiter if the delimiter is not in a quoted string
		 * @param[in] char   $delim The delimiter
		 * @param[in] string $str   The string
		 * @retval array Exploded string
		 */
		private static function explode_unquoted($delim, $str)
		{
			$exploded = array();
			$splitted = str_split($str);

			$cnt = 0;
			$is_inside_quotes = false;
			$previous_is_backslash = false;

			foreach($splitted as $char)
			{ 
				if($char !== $delim && $char !== '\'' && !$is_inside_quotes) // char met outside of the quotes
					continue;

				if($char === $delim && !$is_inside_quotes) // meets delim outside quotes
				{
					$cnt++;
					continue;
				}

				if($char === '\'') // meet quote
					$is_inside_quotes = ($is_inside_quotes == $previous_is_backslash);

				if(empty($exploded[$cnt]))
					$exploded[$cnt]  = $char;
				else
					$exploded[$cnt] .= $char;

				$previous_is_backslash = ($char === '\\');
			}
			
			return $exploded;
		}

		/**
		 * @brief Parses a string type and initializes the class variables
		 * @param[in] string $type The type string
		 * @retval bool True on success, false on error
		 */
		private function parse_type_string($type)
		{
			$matches = array();
			$valid = preg_match(TypeChecker::REGEX_TYPE, $type, $matches);

			if(!$valid)
				return false;

			$this->type = strtoupper($matches[1]);
			$this->parameters = TypeChecker::explode_unquoted(",", $matches[2]);

			if($this->type == TypeChecker::TYPE_EREG || $this->type == TypeChecker::TYPE_FILE
					|| $this->type == TypeChecker::TYPE_ENUM || $this->type == TypeChecker::TYPE_SET)
				$this->parameters = array_map(function($str) { return substr($str, 1, strlen($str) - 2); }, $this->parameters);
			
			return true;
		}

		/**
		 * @brief Return the type
		 * @retval string The type
		 */
		public function get_type()
		{
			return $this->type;
		}

		/**
		 * @brief Return the parameters
		 * @retval array The parameters array
		 */
		public function get_parameters()
		{
			return $this->parameters; 
		}

		/** 
		 * @brief Checks if the data is valid according to the type
		 * @param[in]  mixed  $data 	  The data to valid
		 * @retval int An error code (among TypeChecker::ERROR_*) if the data does not match the type, 0 otherwise
		 */
		public function valid_data($data)
		{
			switch($this->get_type())
			{
				case TypeChecker::TYPE_SMALLINT : // check if the data is a numeric type
				case TypeChecker::TYPE_INT :
					if(!is_numeric($data))
						return TypeChecker::ERROR_NOT_NUMERIC;

					break;

				case TypeChecker::TYPE_EREG :  // check the if the regex match the input value
					$regex = $this->parameters[0];

					if(!preg_match("#".$regex."#", $data))
						return TypeChecker::ERROR_REGEX_NO_MATCH;

					break;

				case TypeChecker::TYPE_ENUM : // Check that all the values checked are in the type param list

					if(!in_array($data, $this->parameters))
						return TypeChecker::ERROR_NOT_IN_ENUM;

					break;

				case TypeChecker::TYPE_SET :

					foreach($data as $input_value)
						if(!in_array($input_value, $this->parameters))
							return TypeChecker::ERROR_NOT_IN_SET;

					break;
			}

			return 0;
		}
	};

?>