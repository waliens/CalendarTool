<?php
	
	/**
	 * @file
	 * @brief Contains the EventCategoryModel class
	 */

	namespace ct\models\events;

	use util\mvc\CategoryModel;

	use ct\Connection;
	use ct\models\events\GlobalEventModel;

	/**
	 * @class EventCategoryModel 
	 * @brief A class for handling event categories related database queries
	 */
	class EventCategoryModel extends CategoryModel
	{
		private $lang; /**< @brief The language in which the model should select the event category name name */
		private $categ_type; /**< @brief The category type for add operation : will determine if the event added is a 
							  *        a student event category or an academic event category */

		const CATEG_TYPE_STUDENT = "student"; /**< @brief Event category type : student event category */
		const CATEG_TYPE_ACADEMIC = "academic"; /**< @brief Event category type : academic event category */

		/**
		 * @brief Construct the EventCategoryModel object in a given language
		 * @param[in] string $lang 		 One of the GlobalEventModel LANG_* const (optional, default: french) 
		 * @param[in] string $categ_type One of the class CATEG_TYPE_* constant defining the type of 
		 * category to manage (optionnal, default: CATEG_TYPE_STUDENT) at insertion of events
		 */
		public function __construct($lang=GlobalEventModel::LANG_FR, $categ_type=self::CATEG_TYPE_STUDENT)
		{
			parent::__construct("event_category", 
								array("Id_Category"),
								$lang == GlobalEventModel::LANG_EN ? "Name_EN" : "Name_FR",
								$lang == GlobalEventModel::LANG_EN ? 
											array("Name_FR", "Color", "Description_EN", "Description_FR") :
											array("Name_EN", "Color", "Description_EN", "Description_FR"));

			if(!self::valid_categ_type($categ_type))
				trigger_error("Event categ. type does not exist.", E_USER_ERROR);

			$this->lang = ($lang === GlobalEventModel::LANG_EN) ? GlobalEventModel::LANG_EN : GlobalEventModel::LANG_FR;
			$this->categ_type = $categ_type;
		}

		/**
		 * @brief Set the category type to generate when a category is added
		 * @param[in] string $categ_type One of the class CATEG_TYPE_* constant defining the type of 
		 * category to manage (optionnal, default: CATEG_TYPE_STUDENT) at insertion of events
		 */
		public function set_categ_type($categ_type)
		{
			if(!self::valid_categ_type($categ_type))
				trigger_error("Event categ. type does not exist.", E_USER_ERROR);

			$this->categ_type = $categ_type;
		}

		/**
		 * @brief Checks whether the given categ type is valid
		 * @param[in] string $categ_type The event category type to check
		 * @retval bool True if the given category type is valid (equals to one of the class CATEG_TYPE_* constant)
		 */
		public static function valid_categ_type($categ_type)
		{
			return $categ_type === self::CATEG_TYPE_STUDENT ||
					$categ_type === self::CATEG_TYPE_ACADEMIC;
		}

		/**
		 * @copydoc CategoryModel::add_item
		 * @note It is reimplemented because an additionnal entry must be added to specify the category type
		 */
		public function add_item($item, $no_key=true)
		{
			// if user is not a student, cannot add a student event category
			$connection = Connection::get_instance();

			if($this->categ_type !== self::CATEG_TYPE_STUDENT && !$connection->user_is_student())
				return;

			$this->sql->transaction();

			// the the base data
			$success &= parent::add_item($item, $no_key);

			// add the event id into the table specifying its category
			$id = $this->sql->last_insert_id();

			switch ($this->categ_type) 
			{
				case self::CATEG_TYPE_STUDENT:

					$insert_data = array("Id_Category" => $id,	
										 "Id_Sudent" => $connection->user_id());
					$success &= $this->sql->insert("student_event_category", $this->sql->quote_all($insert_data));

					break;

				case self::CATEG_TYPE_ACADEMIC:

					$insert_data = array("Id_Category" => $id);
					$success &= $this->sql->insert("academic_event_category", $this->sql->quote_all($insert_data));

					break;

				default:
					$success = false;
			}

			if($success)
				$this->sql->commit();
			else
				$this->sql->rollback();

			return $success;
		}
		
		/**
		 * @brief Return the list of student event custom and default categories for the given user
		 * @param[in] int $user_id The user of which the categories must be returned (optionnal, default: currently connected user)
		 * @retval array A mutlidimensionnal array containing the student event categories of which the rows contains the following keys :
		 * <ul>
		 *   <li>Id_Category : </li>
		 *   <li>Category : The category name a given language (set at construction)</li>
		 *   <li>Description : The description of the category in a given language (set at construction)</li>
		 *   <li>Color : the color in which the event must be (in the \#hhhhhh format) </li>
		 * </ul>
		 * @note The columns are selected according to the language
		 */
		public function get_student_event_categories($user_id=null)
		{
			if($user_id == null) $user_id = Connection::get_instance()->user_id();

			$lang = $this->lang;

			$query  =  "SELECT Id_Category, Name_$lang AS Category, Description_$lang AS Description, Color
						FROM event_category NATURAL JOIN 
						( SELECT Id_Category FROM student_event_category WHERE Id_Student = ? OR Id_Student IS NULL ) AS customs;";

			return $this->sql->execute_query($query, array($user_id));
		}
		
		/**
		 * @brief Return the list of student event categories
		 * @retval array A mutlidimensionnal array containing the student event categories of which the rows contains the following keys :
		 * <ul>
		 *   <li>Id_Category : </li>
		 *   <li>Category : The category name a given language (set at construction)</li>
		 *   <li>Description : The description of the category in a given language (set at construction)</li>
		 *   <li>Color : the color in which the event must be (in the \#hhhhhh format) </li>
		 * </ul>
		 * @note The columns are selected according to the language
		 */
		public function get_academic_event_categories()
		{
			$lang = $this->lang;

			$query  =  "SELECT Id_Category, Name_$lang AS Category, Description_$lang AS Description, Color
						FROM event_category NATURAL JOIN academic_event_category;";

			return $this->sql->execute_query($query);
		}
	}