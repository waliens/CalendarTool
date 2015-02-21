<?php
	
	/**
	 * @file 
	 * @brief COntains the URL class
	 */

	namespace util;

	require_once("functions.php");

	/**
	 * @class URL
	 * @brief A class for constructing url 
	 */
	class URL
	{
		protected $params; /**< @brief Array mapping parameters name and value */
		protected $ressource; /**< @brief the ressource path */
		protected $protocol; /**< @brief the protocol */
		protected $domain; /**< @brief the domain name/ip */

		const PROTOCOL_HTTP = "http"; /**< @brief URL protocol : HTTP */
		const PROTOCOL_HTTPS = "https"; /**< @brief URL protocol : HTTPS */
		const PROTOCOL_FTP = "ftp"; /**< @brief URL protocol : FTP */
		const PROTOCOL_SFTP = "sftp"; /**< @brief URL protocol : SFTP */
		const PROTOCOL_SVN = "svn"; /**< @brief URL protocol : SVN */
		const PROTOCOL_NONE = "none"; /**< @brief URL protocol : no protocol */

		/**
		 * @brief Adds a param and its value to the current 
		 * @param[in] string $param_name The name of the parameter
		 * @param[in] string $value 	 The value of the parameter
		 * @note If a parameter with same name has already been added then it is overwritten*/
		public function add_param($param_name, $value)
		{
			$this->$params[$param_name] = $value;
		}

		/** 
		 * @brief Adds a set of parametes and their values to the url
		 * @param[in] array $params An array mapping param. names with their values
		 * @note If a parameter with same name has already been added then it is overwritten
		 */
		public function add_params(array $params)
		{
			foreach ($params as $param_name => $param_value) 
				$this->add_param($param_name, $param_value);
		}

		/**
		 * @brief Adds a ressource path to the url
		 * @param[in] string $rsc_path The ressource path
		 * @note If a ressource path has already been added then it is overwritten by the new one
		 */
		public function add_ressource_path($rsc_path)
		{
			$this->ressource = "";
			if(!ct\starts_with($rsc_path, "/"))
				$this->ressource .= "/";
			$this->ressource .= $rsc_path;
		}

		/**
		 * @brief Set the protocol for the url
		 * @param[in] string $protocol The protocol (must be one of the PROTOCOL_* class constant)
		 * @note If a protocol has already been set then it is overwritten by the new one
		 */
		public function add_protocol($protocol)
		{
			switch($protocol)
			{
			case self::PROTOCOL_SVN:
			case self::PROTOCOL_SFTP:
			case self::PROTOCOL_FTP:
			case self::PROTOCOL_HTTPS:
			case self::PROTOCOL_HTTP:
				$this->ressource = $protocol;
			default:
				$this->ressource = "";
			}
		}

		/**
		 * @brief Set a domain name/ip address
		 * @param[in] string $domain_name The domain name/ip address
	 	 * @note If a domain name has already been set then it is overwritten by the new one
	  	 */
		public function set_domain_name($domain_name)
		{
			$this->domain = $domain_name;
		}

		/**
		 * @brief Return the final constructed url as a string
		 * @retval string The url
		 */
		public function get_url($html_encode=false)
		{
			$ret_url = "";

			if(!empty($this->protocol) && !empty($this->domain))
				$ret_url .= $this->protocol."://";

			if(!empty($this->domain))
				$ret_url .= $this->domain;

			if(!empty($this->ressouce))
				$ret_url .= $this->ressouce;

			if(!empty($this->params))
			{
				$params_str = implode("&", ct\array_key_val_merge($this->params, "="));
				$ret_url .= "?".$params_str;
			}
		}
	}