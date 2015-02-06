<?php

	/**
	 * @file
	 * @brief Contains the PathwayModel class
	 */

	namespace ct\models;

	use util\mvc\Model;

	/**
	 * @class PathwayModel
	 * @brief A class for handling database queries related to pathways
	 */
	class PathwayModel extends Model
	{
		/**
		 * @brief Construct the PathwayModel object
		 */
		public function __construct()
		{
			parent::__construct();
		}

		/**
		 * @brief Transfer the given pathway data from ulg tables to ct tables
		 * @param[in] string $pathway_id The pathway identifier
		 * @retval bool True on success, false on error
		 */
		public function transfer_pathway($pathway_id)
		{
			$query  =  "INSERT INTO `pathway`(Id_Pathway, Name_Long, Name_Short)
						SELECT * FROM `ulg_pathway` WHERE Id_Pathway = ?;";

			return $this->sql->execute_query($query, array($pathway_id));			
		}

		/**
		 * @brief Checks whether the pathway having the given id exists
		 * @param[in] string $pathway_id The pathway identifier
		 * @retval bool True on success, false on error
		 */
		public function pathway_exists($pathway_id)
		{
			return $this->sql->count("pathway", "Id_Pathway = ".$this->sql->quote($pathway_id)) > 0;
		}

	};