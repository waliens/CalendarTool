<?php

	/**
	 * @file
	 * @brief Contains the Notifier class
	 * 
	 */

	namespace ct\models\notifiers;

	use ct\models\Model;

	/**
	 * @class Notifier
	 * @brief A base class for all notifiers objects
	 */
	abstract class Notifier extends Model
	{
		private $mail; /**< @brief A Mail object */
		private $smarty; /**< @brief A Smarty object */

		/**
		 * @brief Construct a Notifier object
		 */
		public function __construct()
		{
			parent::__construct();
			$this->mail = new Mail("", "", "");
			$this->smarty = new Smarty();
			$this->smarty->setTemplateDir("views/tpl/mail");
			$this->smarty->setCompileDir("views/compiled/mail");
		}

		/**
		 * @brief Return the text message for the email
		 * @retval string The text message 
		 */		
		abstract private function get_txt_message();

		/**
		 * @brief Return the html message for the email
		 * @retval string The html message 
		 */		
		abstract private function get_html_message();

		/**
		 * @brief Return the subject for the email
		 * @retval string The subject 
		 */		
		abstract private function get_subject();

		/**
		 * @brief Return the addressee's mail address for the email
		 * @retval string The addressee's mail address
		 */
		abstract private function get_addressee();

		/**
		 * @brief Send a notification to the user (an email)
		 * @retval bool True on success, false on error
		 * @note The data filling this message are retrieved thanks to the functions get_txt_message, 
		 * get_html_message, get_subject and get_addressee.
		 */
		protected function notify()
		{
			$this->mail->set_subject($this->get_subject());
			$this->mail->set_msg_txt($this->get_txt_message());
			$this->mail->set_msg_html($this->get_html_message());
			return $this->mail->send($this->get_addressee());
		}
	}