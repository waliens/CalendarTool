<?php
/**
 * @file 
 *
 */
namespace  ct\models\notifiers;

use ct\models\events\EventModel;

use ct\models\events\IndependentEventModel;
use ct\models\events\SubEventModel;
use ct\models\events\GlobalEventModel;
use ct\models\UserModel;
use ct\models\events\AcademicEventModel;
use ct\models\notifiers\Notifier;
use \DateTime;

/**
 * @class EventModificationNotifier
 * @author charybde
 * @brief a class to send email when a event is modified/deleted 
 */
class EventModificationNotifier extends Notifier 
{
	const ADD_DL =  0;/**<@brief constante add deadline*/
	const UPDATE_TIME = 1;/**<@brief constant update time of academic event*/
	const DELETE = 2;/**<@brief constant for deletion of an event*/
	
	private $id; /**< @brief id field */
	private $mode; /**< @brief mode field */
	private $model; /**< @brief model field */
	private $type; /**< @brief type field */

	/**
	 * @brief construct a Notification, then send it
	 * @param EventModificationNotifier::CONST $const one of the const describing why there is a notif
	 * @param int $eventId the id of the event
	 */
	public function __construct($const, $eventId){
		parent::__construct();
		$this->id = $eventId;
		if($const >= 0 && $const <= 2)
			$this->mode = $const;
		else 
			return;

		$this->model = new EventModel();
		if($this->model->is_sub_event($eventId)){
			$this->model = new SubEventModel();
			$this->type = "sub";
		}
		else{
			$this->model = new IndependentEventModel();
			$this->type = "indep";
		}
			
		$this->notify();
	}
	
	/**
	 * @brief Return the text message for the email
	 * @retval string The text message
	 */
	protected function get_txt_message(){
		if($this->mode == self::ADD_DL){
			$event = $this->model->getEvent(array("id_event" => $this->id), array("name", "start"));
			if(!$event)
				return;
			$event = $event[0];
			$start = new DateTime($event["Start"]);
			return "Une nouvelle deadline a étée ajoutée a votre planning 
					\n\t ".$event['Name']." pour le ".$start->format("d/m/Y H:i:s")."\n\n Ce message a été envoyé automatiquement merci de ne pas répondre";
		}
		elseif($this->mode == self::DELETE){
			$event = $this->model->getEvent(array("id_event" => $this->id), array("name", "start","end", "DateType"));
			
				
			if(!$event)
				return;
			
			$event = $event[0];
			$start = new DateTime($event["Start"]);
			if($event["DateType"] != "deadline"){
				$end = new DateTime($event["End"]);
			return "L'évenement que vous suiviez : ".$event["Name"]." du ".$start->format("d/m/Y H:i:s")." au ".$end->format("d/m/Y H:i:s"). " a été supprimé.
						\n\n Ce message a été envoyé automatiquement merci de ne pas répondre";
			}
			else 
				return "La deadline que vous suiviez : ".$event["Name"]." pour le ".$start->format("d/m/Y H:i:s"). " a été supprimée.
				\n\n Ce message a été envoyé automatiquement merci de ne pas répondre";
			
		}
		elseif($this->mode == self::UPDATE_TIME){
			$event = $this->model->getEvent(array("id_event" => $this->id), array("name", "start","end", "DateType"));
			if(!$event)
				return;
			$event = $event[0];
			$start = new DateTime($event["Start"]);
			if($event["DateType"] != "deadline"){
				$end = new DateTime($event["End"]);
				return "L'évenement que vous suivez : ".$event["Name"]." a une nouvelle date : du ".$start->format("d/m/Y H:i:s")." au ".$end->format("D/M/Y H:i:s")." 
				\n\n Ce message a été envoyé automatiquement merci de ne pas répondre";
			}
			else
				return "La deadline que vous suivez : ".$event["Name"]." est maintenant pour le ".$start->format("d/m/Y H:i:s"). " .
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
	 	if($this->mode == self::ADD_DL)
	 		return "[MyULg Calendar] Nouvelle Deadline !";
	 	elseif($this->mode == self::DELETE)
	 		return "[MyULg Calendar] Un évènement a été supprimé!";
	 	elseif($this->mode == self::UPDATE_TIME)
	 		return "[MyULg Calendar] Modification horaire !";
	 	 
	 	 
	 }
	
	/**
	 * @brief Return the addressee's mail address for the email
	 * @retval string The addressee's mail address
	 */
	protected function get_addressee(){
		$uM = new UserModel();
		$studentsMails = array();
		
		if($this->type == "sub"){
			$gM = new GlobalEventModel();
			$idG = $this->model->getIdGlobal($this->id);
			$students = $gM->get_list_student($idG);
			$path = $this->model->getPathways($this->id);
			foreach($path as $o => $value){
				foreach($students as $a => $stu){
					if($value["id"] == $stu['pathway'])
						array_push($studentsMails, $uM->get_user_email($stu['id']));
				}
			}
		}
		else{
			$path = $this->model->getPathways($this->id);
			foreach($path as $o => $value){
				$students = $uM->get_student_by_pathway($value["Id_Pathway"]);
				foreach($students as $b => $id){
					$mail = $uM->get_user_email($value['id']);
					if($mail != "")
						array_push($studentsMails, $mail);
				}
			}
		}
		
		$studentsMails = array_unique($studentsMails);
		return implode(",",$studentsMails);
	}
}