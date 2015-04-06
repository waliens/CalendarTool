<?php

	/** 
	 * @file
	 * @brief Contains the AskUserDataController class
	 */

	namespace ct\controllers\browser;

	use ct\models\UserModel;
	use ct\Connection;
	
	use util\superglobals\Superglobal;	
	use util\mvc\BrowserController;
	use util\Redirection;

	/**
	 * @class AskUserDataController
	 * @brief A class for representing the controller for generating the page that asks the user for his personnal data
	 */
	class AskUserDataController extends BrowserController
	{
		private $user_mod; /**< @brief Contains a user model instance */
		private $error; /**< @brief Error text message */

		/**
		 * @brief Construct the AskUserDataController object
		 */
		public function __construct()
		{
			parent::__construct();
			$this->user_mod = new UserModel();
			$this->error = "";

			if(!empty($_POST) && $this->perform_action())
				new Redirection("index.php");
		}

		/**
		 * @copydoc BrowserController::get_content
		 */
		protected function get_content()
		{
			$user = $this->user_mod->get_user();

			$this->smarty->assign("name", $user['Name']);
			$this->smarty->assign("surname", $user['Surname']);
			$this->smarty->assign("email", $user['Email']);
			$this->smarty->assign("error", $this->error);

			return $this->smarty->fetch("ask_user_data.tpl");
		}

		/**
		 * @copydoc util\mvc\Controller::perform_action
		 */
		protected function perform_action()
		{
			$form_fields = array("surname", "email", "name");

			// check form fields
			if($this->sg_post->check_keys($form_fields, Superglobal::CHK_TRIM | Superglobal::CHK_ISSET) < 0)
			{
				$this->error = "tous les champs doivent être initialisés!";
				return false;
			}

			// check validity of the email field
			if(!preg_match("#^.+@.*ulg\.ac\.be$#", $this->sg_post->value("email")))
			{
				$this->error = "email ULg invalide!";
				return false;
			}

			if(!$this->user_mod->update_user($_POST))
			{
				$this->error = "impossible de mettre à jour les informations!";
				return false;
			}

			return true;
		}
	};