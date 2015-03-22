<?php

/**
 * @file
* @brief Event ControllerClass
*/

namespace ct\controllers\ajax;

use ct\models\filters\TimeTypeFilter;
use ct\models\filters\EventTypeFilter;

use util\mvc\AjaxController;
use util\superglobals\Superglobal;

/**
 * @class Event
 * @brief Class for handling the control of event
 */

class GetEventTypeController extends AjaxController
{
	public function __construct()
	{
		parent::__construct();

		// add event types
		$event_types = array();
		
		array_push($event_types, array("id" => EventTypeFilter::TYPE_ACADEMIC, "name" => "Evénements académiques"));
		array_push($event_types, array("id" => EventTypeFilter::TYPE_INDEPENDENT, "name" => "Evénements indépendants"));
		array_push($event_types, array("id" => EventTypeFilter::TYPE_SUB_EVENT, "name" => "Sous-événements"));

		// add student and favorite filters for student only
		if($this->connection->user_is_student()) 
		{	
			array_push($event_types, array("id" => EventTypeFilter::TYPE_STUDENT, "name" => "Evénements favoris"));
			array_push($event_types, array("id" => EventTypeFilter::TYPE_FAVORITE, "name" => "Evénements privés"));
		}

		$this->add_output_data('event_type', $event_types);

		// add event types
		$time_types = array();
		
		array_push($time_types, array("id" => TimeTypeFilter::TYPE_TIME_RANGE, "name" => "Evénements à intervalle temporel (time range)"));
		array_push($time_types, array("id" => TimeTypeFilter::TYPE_DATE_RANGE, "name" => "Evénements sur la journée (date range)"));
		array_push($time_types, array("id" => TimeTypeFilter::TYPE_DEADLINE, "name" => "Echéances (deadline)"));

		$this->add_output_data('date_type', $time_types);
	}
}

