<?php

	/** 
	 * @file
	 * @brief Contains the ViewGlobalEventController class
	 */ 

	namespace ct\controllers\ajax;

	use util\mvc\AjaxController;
	use util\superglobals\Superglobal;

	use ct\models\events\GlobalEventModel;
	use ct\models\filters\GlobalEventFilter;
	use ct\models\filters\EventTypeFilter;
	use ct\models\filters\AccessFilter;
	use ct\models\filters\RecurrenceFilter;
	use ct\models\FilterCollectionModel;

	/** 
	 * @class ViewGlobalEventController
	 * @brief A class for handling the view global event request
	 */
	class ViewGlobalEventController extends AjaxController
	{
		/** 
		 * @brief Construct the ViewGlobalEventController object and process the request
		 */
		public function __construct()
		{
			parent::__construct();

			// check the inputs : id in GET_
			if($this->sg_get->check("event", Superglobal::CHK_ALL, "\ct\is_valid_id") < 0)
			{
				$this->set_error_predefined(AjaxController::ERROR_MISSING_ID);
				return;
			}

			// format global event id
			$id_data = array("id" => $this->sg_get->value("event"));

			// instantiate models
			$glob_mod = new GlobalEventModel();

			// check access to the global event
			if(!$glob_mod->global_event_user_has_read_access($id_data))
			{
				$this->set_error_predefined(AjaxController::ERROR_ACCESS_DENIED);
				return;
			}

			// get global event data and format it
			$glob = $glob_mod->get_global_event($id_data);

			if(empty($glob))
			{
				$this->set_error_predefined(AjaxController::ERROR_MISSING_GLOBAL_EVENT);
				return;
			}

			$trans_array = array("id" => "", "ulg_id" => "id_ulg", "name_long" => "name", "name_short" => "",
								 "desc" => "description", "feedback" => "", "period" => "", "lang" => "language",
								 "acad_year" => "", "owner" => "owner_id");

			$formatted_glob = \ct\array_keys_transform($glob, $trans_array);

			// format workloads 
			$workloads = array("th" => $glob['wk_th'], "pr" => $glob['wk_pr'], "au" => $glob['wk_au'], "st" => $glob['wk_st']);
			$formatted_glob['workload'] = $workloads;

			// get pathways
			$pathways = $glob_mod->get_global_event_pathways($id_data);
			$formatted_glob['pathways'] = \ct\darray_transform($pathways, array("id" => "", "name_long" => "name", "name_short" => ""));

			// get teaching team
			$formatted_glob['team'] = $glob_mod->get_teaching_team($id_data, $glob['lang']);

			// get the subevents
			$filter_collection = new FilterCollectionModel();

			// set filters
			$event_type = new EventTypeFilter(EventTypeFilter::TYPE_SUB_EVENT);
			$glob_filter = new GlobalEventFilter(array($id_data['id']));
			$recur_filter = new RecurrenceFilter(false, true); // dont add all the occurrences of recursive events

			$filter_collection->add_filter($recur_filter);
			$filter_collection->add_filter($glob_filter);
			$filter_collection->add_filter($event_type);
			$filter_collection->add_access_filter(new AccessFilter());

			$subevents = $filter_collection->get_events();

			foreach ($subevents as &$subevent) 
			{
				$f_event = array();
				$f_event['id'] = $subevent['Id_Event'];
				$f_event['name'] = $subevent['Name'];
				$f_event['recurrence'] = $subevent['Id_Recurrence'] > 1;
				$f_event['recurrence_type'] = $subevent['Id_Recur_Category'];
				$f_event['start'] = $subevent['Start'];
				$f_event['end'] = $subevent['End'];

				$formatted_glob['subevents'][] = $f_event; 
			}
			
			// set the output 
			$this->set_output_data($formatted_glob);
		}
	}