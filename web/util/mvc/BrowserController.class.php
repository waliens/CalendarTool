<?php
	
	/** 
	 * @file
	 * @brief Contains the BrowserController class
	 */

	namespace util\mvc;

	/**
	 * @class BrowserController
	 * @brief A base class for any controller that is made for handling requests from the browser
	 */
	abstract class BrowserController extends Controller
	{
		/**
		 * @brief Construct the BrowserController object
		 */
		public function __construct()
		{
			parent::__construct();
		}

		/**
		 * @copydoc Controller::get_output
		 */
		final public function get_output()
		{
			return $this->get_starter().$this->get_content().$this->get_footer();
		}	

		/**
		 * @brief Return a string containing the HTML code beginning the HTML page
		 * @retval string Beginning of the HTML page
		 */
		protected function get_starter()
		{
			$this->smarty->assign("title", "");
			return $this->smarty->fetch("starter.tpl");
		}

		/**
		 * @brief Return a string containing the HTML code containing thefooter of the HTML page
		 * @retval string End of the HTML page
		 */
		protected function get_footer()
		{
			return $this->smarty->fetch("footer.tpl");
		}

		/**
		 * @brief Return a string containing the HTML code of the content of the HTML page
		 * @retval string The content of the HTML page
		 */
		abstract protected function get_content();
	}