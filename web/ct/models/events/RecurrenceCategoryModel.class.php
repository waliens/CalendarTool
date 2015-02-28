<?php
	
	/**
	 * @file
	 * @brief Contains the RecurrenceCategoryModel.class.php
	 */

	namespace ct\models\events;

	use util\

	/**
	 * @class RecurrenceCategoryModel
	 * @brief Class for handling category model
	 */
	class RecurrenceCategoryModel extends CategoryModel
	{
		const LANG_FR = "FR";
		const LANG_EN = "EN";

		/**
		 * @brief Construct the RecurrenceCategoryModel
		 * @param[in] string $lang The language in which the category name must be displayed (one of the class LANG_* constant)
		 */
		public function __construct($lang)
		{
			parent::__construct("recurrence_category", 
								array("Id_Recur_Category"),
								$lang == self::LANG_EN ? "Recur_Category_EN" : "Recur_Category_FR",
								$lang == self::LANG_EN ? array("Recur_Category_FR") : array("Recur_Category_EN"));
		}

		/**
		 * @brief Set the language in which must be displayed the categories 
		 * @param[in] string $lang The language in which the category name must be displayed (one of the class LANG_* constant)
		 */
		public function set_lang($lang)
		{
			if(!$this->is_valid_lang($lang))
				return;

			$this->set_columns($lang == self::LANG_EN ? "Recur_Category_EN" : "Recur_Category_FR",
							   $lang == self::LANG_EN ? array("Recur_Category_FR") : array("Recur_Category_EN"));
		}

		/** 
		 * @brief Check whether the given language is valid
		 * @param[in] string $lang The language to check
		 * @retval bool True if the language string is valid, false otherwise
		 */
		private function is_valid_lang($lang)
		{
			return $lang === self::LANG_EN || $lang === self::LANG_FR;
		}

	}