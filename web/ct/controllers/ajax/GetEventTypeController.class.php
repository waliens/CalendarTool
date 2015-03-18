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

		$data = array();
		array_push($data, array("id" => EventTypeFilter::TYPE_ACADEMIC, "name" => "Academique"));
		array_push($data, array("id" => EventTypeFilter::TYPE_FAVORITE, "name" => "Favoris"));
		array_push($data, array("id" => EventTypeFilter::TYPE_INDEPENDENT, "name" => "Independent"));
		array_push($data, array("id" => EventTypeFilter::TYPE_STUDENT, "name" => "Student"));
		array_push($data, array("id" => EventTypeFilter::TYPE_SUB_EVENT, "name" => "Sous-Evenement"));
		$this->add_output_data('event_type', $data);
		
		$date = array();
		array_push($date, array("id" => TimeTypeFilter::TYPE_TIME_RANGE, "name" => "Time Range"));
		array_push($date, array("id" => TimeTypeFilter::TYPE_DEADLINE , "name" => "Deadline"));
		array_push($date, array("id" => TimeTypeFilter::TYPE_DATE_RANGE, "name" => "Date Range"));
		$this->add_output_data('date_type', $date);
	}
}

