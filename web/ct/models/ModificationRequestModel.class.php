<?php
	
	/**
	 * @file 
	 * @brief Contains the ModificationRequestModel class
	 */

	namespace ct\models;

	include_once("functions.php");

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
		private $event_mod; /**< @brief An event model */

		const STATUS_WAITING = "waiting"; /**< @brief Status string constant : waiting */
		const STATUS_ACCEPTED = "accepted"; /**< @brief Status string constant : accepted */
		const STATUS_CANCELLED = "cancelled"; /**< @brief Status string constant : cancelled */
		const STATUS_REFUSED = "refused"; /**< @brief Status string constant : refused */
 
		const SELECT_BY_EVENT = 0; /**< Selection method for selecting request : by event */
		const SELECT_BY_USER = 1; /**< Selection method for selecting request : by user */
		const SELECT_BY_SENDER = 2; /**< Selection method for selecting request : by sender */

		/**
		 * @brief Construct the ModificationRequestModel object
		 */
		public function __construct()
		{
			parent::__construct();
			$this->req_status = array('waiting', 'accepted', 'cancelled', 'refused');
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
		 * @note Each row of the output array is mapped by the corresponding target id and
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
			// post request
			$request_data = array("Id_Event" => $data['event'], 
								  "Id_Sender" => $data['sender'],
								  "Description" => $data['description'],
								  "Status" => self::STATUS_WAITING);

			$success = true; // true if no error occurred
			$this->sql->transaction();

			$success &= !$this->sql->insert("modification_request", $this->sql->quote_all($request_data));
			
			if($success)
			{
				// post targets
				$req_id = $this->sql->last_insert_id();
				$targets = array_map(array($this, "format_target_str"), $data['targets']);
				$targets = array_map(function ($target) use ($req_id) { return array_merge(array($req_id), $target); });
				
				$success &= $this->sql->insert_batch("modification", $targets, array("Id_Request", "Id_Target", "Proposition"));
			}

			if(!$success)
				$this->sql->rollback();
			else
				$this->sql->commit();

			return $success;
		}

		/**
		 * @brief Convert a target array representation to its string representation (as in the db)
		 * @param[in] array $target The target data (see function insert_modification_request for array structure)
		 * @retval array An array containing the target id at index 0 and the target string at index 1
		 */ 
		private function format_target_str(array $target)
		{
			switch($this->targets[$target['target']]['Name'])
			{
			case "place":
				return $target;
			case "to_deadline": 
			case "to_time_range":
			case "to_date_range":
				return array($target['target'], "start:".$target['start'].",end:".$target['end']);
			case "change_date":
				return array($target['target'], $target['what'].":".$target['date']);
			case "change_time":
				return array($target['target'], $target['what'].":".$target['time']);
			default: 
				return null;
			}
		}

		/**
		 * @brief Convert a target extracted from the database to its array representation 
		 * @param[in] array $target An array containing the target (Id_Target, Proposition, Name (Target name))
		 * @retval array An array structured as a row of the targets array (see insert_modification_request), null on error
		 * @note in addition to the fields target and propsotion is added the field name referring to the target 
		 */ 
		private function format_target_arr(array $target)
		{
			// target id 
			$ret = array("target" => $target['Id_Target'], "name" => $target['Name']);

			if($this->targets[$target['Id_Target']]['Name'] === "place") // place modification
			{
				$ret['proposition'] = $target['Proposition'];
				return $ret;
			}

			// regex matched targets : so get the regex
			$this->type_checker->set_type($this->targets[$target['Id_Target']]['Type']);
			$regex = $this->type_checker->get_parameters()[0];

			// find the matches
			$matches = array();
			preg_match("#".$regex."#", $target['Proposition'], $matches);

			// fill the array 
			switch($this->targets[$target['Id_Target']]['Name'])
			{
			case "to_deadline": 
				$ret['proposition'] = $matches[1];
				$ret['name'] = "Changer une deadline";
				break;
			case "to_time_range":
				$ret['proposition'] = array("start" => $matches[1], "end" => $matches[2]);
				$ret['name'] = "Changer en un événement 'time range'";
				break;
			case "to_date_range":
				$ret['proposition'] = array("start" => $matches[1], "end" => $matches[2]);
				$ret['name'] = "Changer en un événement 'date range'";
				break;
			case "change_date":
				$ret['proposition'] = array("what" => $matches[1], "date" => $matches[2]);
				if($matches[1] === "start")
					$ret['name'] = "Changer la date de début";
				else
					$ret['name'] = "Changer la date de fin";
				break;
			case "change_time":
				$ret['proposition'] = array("what" => $matches[1], "time" => $matches[2]);
				if($matches[1] === "start")
					$ret['name'] = "Changer les date et heure de début";
				else
					$ret['name'] = "Changer les date et heure de fin";
				break;
			default: 
				return null;
			}

			return $ret;
		}

		/**
		 * @brief Return the data of the modification request having the given id
		 * @param[in] int $req_id The modification request id
		 * @retval array An array structured as the $data array of the insert_modification_request function
		 * @note In addition of the index present in the $data array of the insert_modification_request function,
		 * the following fields are added : 
		 * <ul>
		 *  <li>status : the request status (waiting, refused, cancelled, accepted)</li>
		 *  <li>request : request id</li>
		 *  <li>sender_name : the name of the sender</li>
		 *  <li>sender_surname : the surname of the sende</li>
		 *  <li>event_name : the name of the event</li>
		 * </ul>
		 * Moreover, the targets array contain a 'name' key mapping the target human-readable name
		 */
		public function get_modification_request($req_id)
		{
			$this->sql->lock(array("modification_request READ"), "modification READ");

			// get request
			$query  =  "SELECT Id_Request AS request, Id_Event AS event, event_name, Id_Sender AS sender, 
								Name AS sender_name, Surname AS sender_surname, Description AS description,
								Status AS status
						FROM modification 
						NATURAL JOIN ( SELECT Id_Event, Name AS event_name FROM Event ) as events
						INNER JOIN user ON user.Id_User = modification.Id_Sender
						WHERE Id_Request = ?;";

			$request = $this->sql->execute_query($query, array($req_id));

			if(empty($request))
				return array();

			$request = $request[0];

			// get modifications
			$query  =  "SELECT * FROM 
						( SELECT Id_Target, Proposition FROM modification WHERE Id_Request = ? ) as modif 
						NATURAL JOIN 
						( SELECT Id_Target, Name FROM modification_target ) as targets;";

			$targets = $this->sql->execute_query($query, array($req_id));
			$request['targets'] = array_map(array($this, "format_target_arr"), $targets);

			return $request;
		}

		/**
		 * @brief Selects a set of modification requests with the given criterion
		 * @param[in] int $method The criterion (one of the class SELECT_* constant)  
		 * @param[in] int $id     The id corresponding to the criterion
		 * @retval A multidimensionnal array of which each row corresponds to a request and is
		 * structured as the array returned by the get_modification_request function. Return
		 * null on error.
		 */
		public function get_modification_requests($method, $id)
		{
			// get the ids of the modification request to get
			switch($method)
			{
			case self::SELECT_BY_SENDER:
				$req_ids = $this->sql->select("modification_request", 
											  "Id_Sender = ".$this->sql->quote($id), 
											  array("Id_Request"));
			case self::SELECT_BY_EVENT:
				$req_ids = $this->sql->select("modification_request",
											  "Id_Event = ".$this->sql->quote($id),
											  array("Id_Request"));
			case self::SELECT_BY_USER:
				$query  =  "SELECT Id_Request FROM modification_request
							NATURAL JOIN
							(
							    SELECT Id_Event FROM `independent_event` WHERE Id_Owner = ?
							    UNION ALL
							    SELECT Id_Event FROM sub_event NATURAL JOIN 
							    ( SELECT Id_Global_Event FROM global_event WHERE Id_Owner = ? ) AS glob
							) as owned_event";
	
				$req_ids = $this->sql->execute_query($query, array($id, $id));
			default: return null;
			}				

			// get the formatted modif request
			$ret_array = array();

			foreach($req_ids as $req)
				$ret_array[] = $this->get_modification_request($req['Id_Request']);
			
			return $ret_array;
		}

		/**
		 * @brief Update the status of a request to the new status
		 * @param[in] int 	 $req_id 	 The request identifier 
		 * @param[in] string $new_status The new status (one of the STATUS_* class constant)
		 * @retval bool True on success, false on error
		 */
		public function update_request_status($req_id, $new_status)
		{
			if(!$this->valid_status($new_status))
				return false;

			$update_data = $this->sql->quote_all(array("Status" => $new_status));
			return $this->sql->update("modification_request", $update_data, "Id_Request = ".$this->sql->quote($req_id));
		}

		/**
		 * @brief Checks whether the given request status is valid
		 * @param[in] string $status The status string to check
		 * @retval bool True if the string is ok, false otherwise
		 */
		private function valid_status($status)
		{
			return preg_match("#^(accepted|waiting|refused|cancelled)$#", $status);
		}

		/**
		 * @brief Apply the given modification request (modifies the related event)
		 * @param[in] int $req_id The request identifier
		 * @retval bool True on success, false on error
		 */
		public function apply_modification($req_id)
		{
			$this->sql->transaction();
			$request_data = $this->get_modification_request($req_id);

			if(empty($request_data)) // modif request not found
			{
				$this->sql->rollback();
				return false;
			}

			$success = true;
			foreach($request_data['targets'] as $target)
			{
				switch($target['name'])
				{
				case "place":
					$success &= apply_place($request_data, $target);
					break;
				case "to_deadline":
					$success &= apply_to_deadline($reques_data, $target);
					break;
				case "to_time_range":
					$success &= apply_to_time_range($request_data, $target);
					break;
				case "to_date_range":
					$success &= apply_to_date_range($request_data, $target);
					break;
				case "change_time":
					$success &= apply_change_time($request_data, $target);
					break;
				case "change_date":
					$success &= apply_change_date($request_data, $target);
					break;
				default:
					$success = false;
				}
			}

			// set the status to 'accepted'
			$success &= $this->update_request_status(self::STATUS_ACCEPTED);

			if($success)
				$this->sql->commit();
			else
				$this->sql->rollback();

			return $success;
		}
		
		/**
		 * @brief Apply the given 'to_deadline' modification request (modifies the related event)
		 * @param[in] array $request The request data (as returned from the get_modification_request)
		 * @param[in] array $target  The modification data (indexes : target, proposition (an array), name).
		 * @retval bool True on success, false on error
		 * @note The proposition array must be structured as a row of the targets index of the data array in the 
		 * insert_modification_request function. 
		 * @note The field proposition must be an array structured as in the case of a 'to_deadline' request in
		 * the insert_modification_request function 
		 */
		private function apply_to_deadline(array &$request, array $target)
		{
			return $this->event_mod->reset_time_type_deadline($request['event'], $target['proposition']);
		}
		
		/**
		 * @brief Apply the given 'to_date_range' modification request (modifies the related event)
		 * @param[in] array $request The request data (as returned from the get_modification_request)
		 * @param[in] array $target  The modification data (indexes : target, proposition (an array), name).
		 * @retval bool True on success, false on error
		 * @note The proposition array must be structured as a row of the targets index of the data array in the 
		 * insert_modification_request function. 
		 * @note The field proposition must be an array structured as in the case of a 'to_date_range' request in
		 * the insert_modification_request function 
		 */
		private function apply_to_date_range(array &$request, array $target)
		{
			return $this->event_mod->reset_time_type_date_range($request['event'], 
																$target['proposition']['start'],
																$target['proposition']['end']);
		}
		
		/**
		 * @brief Apply the given 'to_time_range' modification request (modifies the related event)
		 * @param[in] array $request The request data (as returned from the get_modification_request)
		 * @param[in] array $target  The modification data (indexes : target, proposition (an array), name).
		 * @retval bool True on success, false on error
		 * @note The proposition array must be structured as a row of the targets index of the data array in the 
		 * insert_modification_request function. 
		 * @note The field proposition must be an array structured as in the case of a 'to_time_range' request in
		 * the insert_modification_request function 
		 */
		private function apply_to_time_range(array &$request, array $target)
		{
			return $this->event_mod->reset_time_type_time_range($request['event'], 
																$target['proposition']['start'],
																$target['proposition']['end']);
		}
		
		/**
		 * @brief Apply the given 'change_time' modification request (modifies the related event)
		 * @param[in] array $request The request data (as returned from the get_modification_request)
		 * @param[in] array $target  The modification data (indexes : target, proposition (an array), name).
		 * @retval bool True on success, false on error
		 * @note The proposition array must be structured as a row of the targets index of the data array in the 
		 * insert_modification_request function. 
		 * @note The field proposition must be an array structured as in the case of a 'change_time' request in
		 * the insert_modification_request function 
		 */
		private function apply_change_time(array &$request, array $target)
		{	
			switch($target['proposition']['what'])
			{
			case "start":
				$update_array = array("Start" => $target['proposition']['time']);
				$table = "time_range_event";
				break;
			case "end":
				$update_array = array("End" => $target['proposition']['time']);
				$table = "time_range_event";
				break;
			case "deadline":
				$update_array = array("Limit" => $target['proposition']['time']);
				$table = "deadline_event";
				break;
			default: return false;
			}
			
			// sql where clause
			$where = "Id_Event = ".$this->sql->quote($request['event']);

			return $this->sql->update($table, $this->sql->quote_all($update_array), $where);
		} 
		
		/**
		 * @brief Apply the given 'change_date'  request (modifies the related event)
		 * @param[in] array $request The request data (as returned from the get_modification_request)
		 * @param[in] array $target  The modification data (indexes : target, proposition (an array), name).
		 * @retval bool True on success, false on error
		 * @note The proposition array must be structured as a row of the targets index of the data array in the 
		 * insert_modification_request function. 
		 * @note The field proposition must be an array structured as in the case of a 'change_date' request in
		 * the insert_modification_request function 
		 */
		private function apply_change_date(array &$request, array $target)
		{
			switch($target['proposition']['what'])
			{
			case "start":
				$update_array = array("Start" => $target['proposition']['date']);
				break;
			case "end":
				$update_array = array("End" => $target['proposition']['date']);
				break;
			default: return false;
			}

			// SQL where clause 
			$where = "Id_Event = ".$this->sql->quote($request['event']);

			return $this->sql->update($table, $this->sql->quote_all($update_array), $where);
		}
		
		/**
		 * @brief Apply the given 'place' modification request (modifies the related event)
		 * @param[in] array $request The request data (as returned from the get_modification_request)
		 * @param[in] array $target  The modification data (indexes : target, proposition (an array), name).
		 * @retval bool True on success, false on error
		 * @note The proposition array must be structured as a row of the targets index of the data array in the 
		 * insert_modification_request function. 
		 * @note The field proposition must be an array structured as in the case of a 'place' request in
		 * the insert_modification_request function 
		 */
		private function apply_place(array &$request, array $target)
		{
			$update_data = array("Place" => $target['proposition']);
			return $this->sql->update("event", $update_data, "Id_Event = ".$this->sql->quote($request['event']));
		}
	}