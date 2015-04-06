<?php
namespace  ct\models\notifiers;

use ct\models\events\GlobalEventModel;
use ct\models\UserModel;
use ct\models\notifiers\Notifier;

/**
 * @class GlobalEventNotification
 * @author charybde
 * @brief a class to send email when a global event is created/deleted
 *
 */
class GlobalEventNotification extends Notifier {
	const CREATE = 0;/**<@brief CONSTANT for creating a global event*/
	const DELETE = 1;/**<@breif constant for deleting a global event*/
	
	
	private $id;
	private $mode;
	private $model;

	/**
	 * @brief construct a Notification, then send it
	 * @param GlobalEventNotification::CONST $const one of the const describing why there is a notif
	 * @param int $eventId the id of the event
	 */
	public function __construct($const, $eventId){
		parent::__construct();
		$this->id = $eventId;
		if($const >= 0 && $const <= 1)
			$this->mode = $const;
		else
			return;
			
		
		$this->model = new GlobalEventModel(); 
		
		$this->notify();
	}
	
	/**
	 * @brief Return the text message for the email
	 * @retval string The text message
	 */
	protected function get_txt_message(){
		$name = $this->model->get_global_event(array("id" => $this->id));
		if(!$name)
			return;
		$name = $name['name_long'];
		
		if($this->mode == self::CREATE){
			return "Un nouveau cours a été ajouté a votre planning : ".$name."
					\n\n Ce message a été envoyé automatiquement merci de ne pas répondre";
		}
		elseif($this->mode == self::DELETE){
			return "Le cours que vous suiviez : ".$name." a été supprimé.
						\n\n Ce message a été envoyé automatiquement merci de ne pas répondre";
		}
	}
	
	/**
	 * @brief Return the html message for the email
	 * @retval string The html message
	 */
	 protected function get_html_message(){
	 	return $this->get_txt_message();
	 }
	
	/**
	 * @brief Return the subject for the email
	 * @retval string The subject
	 */
	 protected function get_subject(){
	 	if($this->mode == self::CREATE)
	 		return "[MyULg Calendar] Nouveau cours !";
	 	elseif($this->mode == self::DELETE)
	 		return "[MyULg Calendar] Un cours a été supprimé!";	 	 
	 }
	
	/**
	 * @brief Return the addressee's mail address for the email
	 * @retval string The addressee's mail address
	 */
	protected function get_addressee(){
		$uM = new UserModel();
		$studentsMails = array();
		$students = $this->model->get_list_student($this->id);

		foreach($students as $o => $value){
			$mail = $uM->get_user_email($value['id']);
			if($mail != "")
				array_push($studentsMails, $mail);
		}
		
		$studentsMails = array_unique($studentsMails);
		return implode(",",$studentsMails);
	}
}