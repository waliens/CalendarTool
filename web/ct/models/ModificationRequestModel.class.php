<?php
	
	/**
	 * @file 
	 * @brief Contains the ModificationRequestModel
	 */

	namespace ct\models;

	require_once("function.php");

	use util\mvc\Model;
	use util\TypeChecker;

	/**
	 * @class ModificationRequestModel
	 * @brief Class for handling database queries related to modification requests
	 */
	class ModificationRequestModel extends Model
	{
		private $req_status; /**< @brief Array containing the possible status for a request */
		private $targets; /**< @brief Array containing the possible modification targets' information */
		private $type_checker; /**< @brief A TypeChecker object */
		private $event_mod;

		/**
		 * @brief Construct the ModificationRequestModel object
		 */
		public function __construct()
		{
			parent::__construct();
			$this->req_status = array('sent', 'accepted', 'cancelled', 'refused');
			$this->type_checker = new TypeChecker();
			$this->populate_targets();
			$this->event_mod = new EventModel();
		}

		/**
		 * @brief Populates the $targets class variable with the database targets
		 */
		private function populate_targets()
		{
			foreach($this->get_targets() as $target)
				$this->targets[$target['Id_Target']] = $target;
		}

		/**
		 * @brief Get all the possible modification targets
		 * @retval array An array of which the rows corresponds to the targets
		 * @note The each row of the output array is mapped by the corresponding target id and
		 * is structured as follows :
		 * <ul>
		 * 	<li>Id_Target : the target id</li>
		 *  <li>Name : the target modification name</li>
		 *  <li>Type : the target type</li>
		 * </ul>
		 */
		public function get_targets()
		{
			return $this->sql->select("modification_target");
		}

		/**
		 * @brief Checks the integrity of the data of a modification request and cleans it if necessary
		 * @param[in]  array $data 		 An array containing the data to check 
		 * @param[out] array $error_desc Contains an error description for each field that wasn't valid
		 * @param[in]  int   $lock_mode	 One of the Model LOCKMODE_* class constant 
		 * @retval bool True if the data were correctly formatted, false otherwise
		 *
		 * @note The $data array should be structured as for the insert_modification_request function
		 * @note The formatting of error desc is the same as the data array 
		 * @note This function might access the following tables : event, modification_target
		 */
		public function check_modification_request_data(array &$data, array &$error_desc, $lock_mode=Model::LOCKMODE_NO_LOCK)
		{
			$error_desc = array();
			// set the lock mod for the model function call
			$sub_lock_mode = $this->get_sub_lockmode($lock_mode);

			// event should exist
			if(!is_int($data['event']) || !$this->event_mod->event_exists($data['event'], $sub_lock_mode))
				$error_desc['event'] = "Cet événement n'existe pas";
 
 			// no check for the status -> set by the model according to the action
 			// no check on the description -> can be empty

 			$targets_error = array();

 			foreach($data['targets'] as $target)
 			{
 				if(!isset($this->targets[$target['target_id']])) // check target_id
 				{
 					$error_desc['targets'] = "Au moins une modification demandée est impossible";
 					break;
 				}
 				// set a type for the type checker
 				$this->type_checker->set_type($this->targets[$target['target_id']['Type']]);

 				if(!$this->type_checker->valid_data($target['proposition'])) // check proposition string
 				{
 					$error_desc['targets'] = "Au moins une modification demandée est impossible";
 					break;
 				}

 				if(!$this->check_proposition_array($target['target_id'], $data['event'], $target['proposition'], $sub_lock_mode))
 					$error_desc['targets'] = "Au moins une modification demandée est impossible";
 			}

 			if($this->do_unlock($lock_mode))
 				$this->sql->unlock();

			return empty($error_desc);
		}

		/**
		 * @brief Check whether a modification proposition string is valid
		 * @param[in] int   $target_id   The id of the target
		 * @param[in] int   $event_id    The id of the event to modify
		 * @param[in] mixed $proposition The proposition (see insert_modification_request for the formatting)
		 * @param[in] int   $lock_mode	 One of the Model LOCKMODE_* class constant 
		 * @retval bool True if the proposition is valid, false otherwise
		 * @note If no_lock is false then read locks are acquired on the tables date_range_event, time_range_event and
		 * deadline_event.
		 */
		private function check_proposition_array($target_id, $event_id, $proposition, $lock_mode=Model::LOCKMODE_NO_LOCK)
		{
			$valid;
			// should check if the date exists and if the start and end ordered properly
			switch($this->targets[$target_id]['Name'])
			{
			case "to_deadline": 
				$valid = ct\date_exists($proposition);
				break;

			case "to_time_range":
			case "to_date_range":
				$valid = (count($proposition) == 2) 
						&& ct\date_exists($proposition['start']) 
						&& ct\date_exists($proposition['end']);
				break;

			case "change_date":

				$event = $this->event_mod->get_event_temporal_data($event_id, $sub_lock_mode);

				if($event['Type'] !== EventModel::TEMP_DATE_RANGE_EVENT) // only date_range can be modified with change_date
				{	
					$valid = false;
					break;
				}

				if(!ct\date_exists($proposition['date']))
				{
					$valid = false;
					break;
				}

				if($proposition['what'] === "start")
					$valid = ct\date_cmp($proposition['date'], $event['End']) < 0;
				else $valid = ct\date_cmp($event['Start'], $proposition['date']) < 0;

				break;

			case "change_time":

				$event = $this->event_mod->get_event_temporal_data($event_id, $sub_lock_mode);

				if($event['Type'] === EventModel::TEMP_DATE_RANGE_EVENT) // date_range cannot be modified with change_time
				{
					$valid = false;
					break;
				}

				if(!ct\date_exists($proposition['time']))
				{
					$valid = false;
					break;
				}

				if($event['Type'] === EventModel::TEMP_DEADLINE_EVENT)
				{
					$valid = true;
					break;
				}

				if($proposition['what'] === "start")
					$valid = ct\date_cmp($proposition['date'], $event['End']) < 0;
				else $valid = ct\date_cmp($event['Start'], $proposition['date']) < 0;

				break;

			default:
				$valid = false;
			}

			if($this->do_unlock($lock_mode))
				$this->sql->unlock();

			return $valid;
		}


		/**
		 * @brief Insert a new modification request into the database
		 * @param[in]  array $data 		 The modification request data
		 * @param[out] array $error_desc Contains an error description for each field that wasn't valid
		 * @param[in]  int   $lock_mode	 One of the Model LOCKMODE_* class constant 
		 *
		 * @note The $data array should be structured as follows :
		 * <ul>
		 *  <li>event : the event id</li>
		 * 	<li>sender : the sender id</li>
		 * 	<li>description : description text</li>
		 *  <li>targets : see below<li>
		 * </ul>
		 *
		 * The targets key in the $data array should map an array of which the row are also arrays :
		 * <ul>
		 * 	<li>target : target id</li>
		 * 	<li>proposition : change according to the target (see below)</li>
		 * </ul>
		 *
		 * According to the modification target, the field proposition should map :
		 * 
		 * 1. place : a string indicating the new place
		 * 2. to_date_range : an array ('start' => $start_date, 'end' => $end_date)
		 * 3. to_time_range : an array ('start' => $start_datetime, 'end' => $end_datetime)
		 * 4. to_deadline   : a string indicating the new datetime
		 * 5. change_date   : an array ('what' => ("start"|"end"), 'date' => $new_date)
		 * 6. change_time   : an array ('what' => ("start"|"end"|"deadline"), 'time' => $new_datetime)
		 * 
		 */
		public function insert_modification_request(array $data, $lock_mode=Model::LOCKMODE_NO_LOCK)
		{

		}
	}