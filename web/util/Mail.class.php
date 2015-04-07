<?php

	/**
	 * @file
	 * @brief Contains the mail class
	*
	 */

	namespace util;
	
	
	use \Smarty;
	
	/**
	 * @class Mail
	 * @brief A class providing useful methods for sending emails
	 */
	class Mail
	{
		private $msg_txt; /**< @brief The text message for the given mail */
		private $msg_html; /**< @brief The html message for the given mail */
		private $subject; /**< @brief The mail subject */
		private $smarty; /**< @brief A smarty object */
		private $boundary; /**< @brief The boundary */
		private $boundary_alt; /**< @brief The alternative boundary */

		/**
		 * @brief Construct a Mail object
		 * @param[in] string $msg_txt  The text message
		 * @param[in] string $msg_html The html message 
		 * @param[in] string $subject  The mail subject
		 */
		public function __construct($msg_txt, $msg_html, $subject)
		{
			$this->msg_txt = $msg_txt;
			$this->msg_html = $msg_html;
			$this->subject = $subject;
			$this->smarty = new Smarty();
			$this->smarty->setTemplateDir("views/tpl/mail");
			$this->smarty->setCompileDir("views/compiled/mail");
		}

		/**
		 * @brief Set the text message
		 * @param[in] string $msg_txt The text message
		 */
		public function set_msg_txt($msg_txt)
		{
			$this->msg_txt = $msg_txt;
		}

		/**
		 * @brief Set the html message
		 * @param[in] string $msg_html The html message 
		 */
		public function set_msg_html($msg_html)
		{
			$this->msg_html = $msg_html;
		}

		/**
		 * @brief Set the html message
		 * @param[in] string $subject The mail subject
		 */
		public function set_subject($subject)
		{
			$this->subject = $subject;
		}

		/**
		 * @brief Send the email to the given address
		 * @param[in] string $to The email of the addressee
		 * @retval bool True on success, false on error
		 */
		public function send($to)
		{
			// check if the mail is valid and if there is a subject
			if(!preg_match("#^.+@[a-zA-Z0-9-\.]+$#", $to) || empty($this->subject))
				return false;

			// set the boundary
			$this->boundary = "-----".md5(rand());
			$this->boundary_alt = "-----".md5(rand());

			// build the headers
			$headers = $this->get_headers();

			// build the message body
			$this->smarty->assign("message_html", $this->msg_html);
			$this->smarty->assign("message_txt", $this->msg_txt);
			$this->smarty->assign("boundary", $this->boundary);
			$this->smarty->assign("boundary_alt", $this->boundary_alt);
			$message = $this->smarty->fetch("mail_content.tpl");

			return true;//mail($to, $this->subject, $message, $headers);
		}

		/**
		 * @brief Return the headers of the email as a string
		 * @retval string The headers
		 */
		private function get_headers()
		{
			$this->smarty->assign("from", "CalendarTool");
			$this->smarty->assign("from_mail", "ct@ulg.ac.be");
			$this->smarty->assign("reply_to", "NoReply");
			$this->smarty->assign("reply_to_mail", "ct-no_reply@ulg.ac.be");
			$this->smarty->assign("boundary", $this->boundary);

			return $this->smarty->fetch("mail_headers.tpl");
		}
	}