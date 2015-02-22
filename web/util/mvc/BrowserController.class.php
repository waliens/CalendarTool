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
			$includes = $this->get_includes();
			$this->smarty->assign("title", $this->get_title());
			$this->smarty->assign("includes", $includes);
			return $this->smarty->fetch("starter.tpl");
		}


		/**
		 * @brief Return as a string the html code of the popups frame to add to the page footer
		 * @retval string The html code of the popups
		 * @note Re-implement this function for adding some popups to a page
		 */
		protected function get_popups()
		{
			return "";
		}

		/**
		 * @brief Return as a string the html code of the includes frame to add to the page starter
		 * @retval string The html code of the popups
		 * @note Re-implement this functon for adding some includes to a page
		 */
		protected function get_includes()
		{
			return $this->smarty->fetch("includes_default.tpl");
		}
		
		/**
		 * @brief Return the additionnal title of the page 
		 * @return string The additionnal title
		 * @note Re-implement this function for adding an additionnal title
		 */
		protected function get_title()
		{
			return "";
		}

		/**
		 * @brief Return a string containing the HTML code of the page's footer
		 * @retval string End of the HTML page
		 */
		protected function get_footer()
		{
			$popups = $this->get_popups();
			$this->smarty->assign("popups", $popups);
			$this->smarty->assign("footer_inc", $this->get_footer_inc());
			return $this->smarty->fetch("footer.tpl");
		}

		/**
		 * @brief Return a string containing the HTML code of the footer's includes
		 * @retval string The code of the footer's includes
		 * @note Re-implement this function for adding an additionnal title
		 */
		protected function get_footer_inc()
		{
			return "";
		}

		/**
		 * @brief Return a string containing the HTML code of the content of the HTML page
		 * @retval string The content of the HTML page
		 */
		abstract protected function get_content();
	}