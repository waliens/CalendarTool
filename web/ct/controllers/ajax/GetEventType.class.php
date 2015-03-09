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


		$this->output_data['event_types'] = array();
		$this->output_data['date_types'] = array();
		
		$data = array();
		array_push($data, array("id" => EventTypeFilter::TYPE_ACADEMIC, "Name" => "Académique"));
		array_push($data, array("id" => EventTypeFilter::TYPE_FAVORITE, "Name" => "Favoris"));
		array_push($data, array("id" => EventTypeFilter::TYPE_INDEPENDENT, "Name" => "Independent"));
		array_push($data, array("id" => EventTypeFilter::TYPE_STUDENT, "Name" => "Student"));
		array_push($data, array("id" => EventTypeFilter::TYPE_SUB_EVENT, "Name" => "Sous Evenement"));
		array_push($data, array("id" => EventTypeFilter::TYPE_ALL, "Name" => "Tous"));
		$this->output_data['event_types'] = $data;
		
		$date = array();
		array_push($data, array("id" => TimeTypeFilter::TYPE_TIME_RANGE, "Name" => "Time Range"));
		array_push($data, array("id" => TimeTypeFilter::TYPE_DEADLINE , "Name" => "Deadline"));
		array_push($data, array("id" => TimeTypeFilter::TYPE_ALL, "Name" => "Tous"));
		array_push($data, array("id" => TimeTypeFilter::TYPE_DATE_RANGE, "Name" => "Date Range"));
		$this->output_data['date_types'] = $date;
		
		

	}
}

