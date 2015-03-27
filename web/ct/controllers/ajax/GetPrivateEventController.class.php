<?php

/**
 * @file
 * @brief Private Event ControllerClass
 */

namespace ct\controllers\ajax;

use util\mvc\AjaxController;
use util\superglobals\Superglobal;
use ct\models\filters\EventTypeFilter;
use ct\models\filters\RecurrenceFilter;
use ct\models\filters\AccessFilter;
use ct\models\FilterCollectionModel;

/**
 * @class GetPrivateEventController
 * @brief Class for handling the 062 request (get private events)
 */
class GetPrivateEventController extends AjaxController
{
	/**
	 * @brief Construct the GetPrivateEventController object
	 */
	public function __construct() 
	{
		parent::__construct();

		// create private event
		$event_filter = new EventTypeFilter(EventTypeFilter::TYPE_STUDENT);
		$recur_filter = new RecurrenceFilter(false, true);
		$acces_filter = new AccessFilter();

 		$filter_collection = new FilterCollectionModel();
 		$filter_collection->add_filter($event_filter);
 		$filter_collection->add_filter($recur_filter);
 		$filter_collection->add_access_filter($acces_filter);

 		$events = $filter_collection->get_events();
 		$out_events = array();

 		// format events for output
		foreach ($events as &$event) 
		{
			$f_event = array();
			$f_event['id'] = $event['Id_Event'];
			$f_event['name'] = $event['Name'];
			$f_event['recurrence'] = $event['Id_Recurrence'] > 1;
			$f_event['recurrence_type'] = $event['Id_Recur_Category'];
			$f_event['start'] = $event['Start'];
			$f_event['end'] = $event['End'];

			$out_events[] = $f_event; 
		}

		$this->add_output_data("events", $out_events);
	}
	

}


