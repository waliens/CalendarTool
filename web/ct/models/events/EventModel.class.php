<?php

	/**
	 * @file
	 * @brief Event ControllerClass
	 */

namespace ct\models\events;
use util\mvc\Model;
use util\database\Database;

use \DateTime;
use \DateInterval;

	/**
	 * @class Event
	 * @brief Class for getting event from D
	 */


	class EventModel extends Model{
			
		protected $fields; /** < @brief Array containing the differents type of data that we use when we work with event when you pass an array it must have the same keys */
		protected $fields_event;
		protected $table;
		protected $translate;
		protected  $error;

		const TEMP_DEADLINE = 1; /**< @brief Constant identifying the temporal type of event : deadline event */
		const TEMP_TIME_RANGE = 2; /**< @brief Constant identifying the temporal type of event : time range event */
		const TEMP_DATE_RANGE = 3; /**< @brief Constant identifying the temporal type of event : date range event */
		const REC_DAILY = 1;
		const REC_WEEKLY = 2;
		const REC_BIM = 3;
		const REC_MONTHLY = 4;
		const REC_YEARLY = 5;
		const REC_NEVER = 6;
		
		function __construct() {
			parent::__construct();
			
			$this->fields = array("id_event" => "int", "name" => "text", "description" => "text", "id_recurrence" => "int", "place" => "text", "id_category" => "int", "limit" => "date", "start" => "date", "end" => "date", "feedback" => "text", "workload" => "int", "practical_details" => "text", "id_owner" => "int", "public" => "bool","id_owner" => "int", "id_GlobalEvent" => "int", "categ_name_FR" => "text", "categ_name_EN" => "text");
			$this->fields_event = array("Id_Event" => "int", "Name" => "text", "Description" => "text", "Id_Recurrence" => "int", "Place" => "text", "Id_Category" => "int");
			$this->table = array();
			$this->table[0] = "event";
			$this->translate = array("id_event" => "event.Id_Event", "name" => "Name", "description" => "Description", "id_recurrence" => "Id_Recurrence", "place" => "Place", "id_category" => "Id_Category", "limit" => "Limit", "start" =>"Start", "end" => "End", "feedback" => "Feedback", "workload" => "Workload", "practical_details" => "Practical_Details", "id_GlobalEvent" => "Id_Global_Event","id_owner" => "Id_Owner", "id_owner" => "Id_Owner", "public" => "Public", "categ_name_FR" => "Name_FR", "categ_name_EN" => "Name_EN");
		}
		
		/**
		 * @brief Get the Fields from the Model
		 * @retval array $this->fields
		 */
		public function getFields() { return $this->fields; }

		/**
		 * @brief Get the table from the Model
		 * @retval array $this->table
		 */
		public function getTable() {	return $this->table;	}
		
		
		/**
		 * @brief Get an or several events from the bdd
		 * @param array String $infoData the data to identify the event
		 * @param array String $requestData the requested data for the event $id (empty = all)
		 * @retval mixed an array containing the data, false if no matching
		 */
		protected  function getData(array $infoData = null, array $requestData = null){			
			$selectClause = "*";
			$whereClause = "";
			
			if($infoData == null)
				$infoData = array();
			
			if($requestData != null && !empty($requestData))
				$selectClause = implode(", ", $requestData);

			
			if(!empty($infoData)){
				$whereClause = "WHERE ";
				$equals = array();
				foreach($infoData as $key => $value){
					array_push($equals,"".$key ." = '". $value. "'");
				}
				$whereClause .= implode(" AND ", $equals);
			}
			$query  =  "SELECT ".$selectClause." FROM event 
					 NATURAL JOIN ( 
					 	SELECT Id_Event, Start, End, 'time_range' AS DateType FROM time_range_event 
					 	UNION ALL
					 	SELECT Id_Event, DATE(Start) AS Start, DATE(End) AS End, 
					 	 	'date_range' AS DateType FROM date_range_event 
					 	UNION ALL 
					 	SELECT Id_Event, `Limit` AS Start, '' AS End, 'deadline' AS DateType FROM deadline_event 
					 ) AS time_data 
					NATURAL JOIN (
						SELECT Id_Event, '' AS Id_Owner,Id_Global_Event, 'sub_event' AS EventType,
							 Feedback, Workload, Practical_Details 
							 FROM sub_event NATURAL JOIN academic_event
              			UNION ALL 
              			SELECT Id_Event, Id_Owner,'' AS Id_Global_Event, 'indep_event' AS EventType,
              				Feedback, Workload, Practical_Details 
              				FROM independent_event NATURAL JOIN academic_event
              			UNION ALL
              			SELECT Id_Event, Id_Owner,'' AS Id_Global_Event, 'student_event' AS EventType, 
              			'' AS Feedback, '' AS Workload, '' AS Practical_Details FROM student_event
              		 ) AS type_data
  		            NATURAL JOIN (
						 SELECT Id_Category, Color, Description_EN AS Categ_Desc_EN, Description_FR 
							AS Categ_Desc_FR, Name_EN AS Categ_Name_EN, Name_FR AS Categ_Name_FR
						 	FROM event_category 
					) AS categ NATURAL JOIN recurrence NATURAL JOIN recurrence_category ".$whereClause." ;";

						
			return   $this->sql->execute_query($query);

	
		}
		
		/**
		 * @brief Get an event from the bdd (this function check the args)
		 * @param $type type of event
		 * @param $requestedData what we want to obtain (nothing for *)
		 * @param $infoData what we know about the event
		 * @retval mixed an array containing the data false otherwise
		 */
		public function getEvent (array $infoData = null,  array $requestedData = null){	
			if($infoData == null)
				$infoData = array();
						
			$info = $this->checkParams($infoData, true);
			if($requestedData ==  null)
				return $this->getData($info);
		
			$request = $this->checkParams($requestedData, false);		
			
			return $this->getData($info, $request);
		}
		
		/** 
		 * @brief check if the params given in input correspond to the type and translate into bdd rows name
		 * @param array $ar Parameters array
		 * @param boolean $ckey Check key (if not check values)
		 * @param boolean $cintegrity Check integrity
		 * @retval mixed return the array without invalids params (-1 if prooblem during cintegrity)
		 */
		protected function checkParams($ar, $ckey, $cintegrity = false){
			if($ckey)
				$arr = array_intersect_key($ar, $this->fields);
			else
				$arr = $this->array_intersect_key_val($ar, $this->fields);
			
			
			if($cintegrity){
				foreach($arr as $key => $value){
					if($this->fields[$key] == "int"){
							$arr[$key] = $this->sql->quote($value);
					}
					elseif($this->fields[$key] == "bool"){
							$arr[$key] = $this->sql->quote($value);	
					}
					elseif($this->fields[$key] == "text"){
						$arr[$key] = htmlEntities($value, ENT_QUOTES);
						$arr[$key] = nl2br($arr[$key]);
						$arr[$key] = $this->sql->quote($arr[$key]);
					}
					elseif($this->fields[$key] == "date"){
						//TODO
						$arr[$key] = $this->sql->quote($value);
					}
				
				}
						
			}
			
			$ret = array();
			if($ckey){
				foreach ($arr as $key => $value){
					if(isset($this->translate[$key]))
						$ret[$this->translate[$key]] = $value;
				}
			}
			else{
				foreach($arr as $key => $value){
					if(isset($this->translate[$value]))
						$ret[$key] = $this->translate[$value];
				}
			}
			return $ret;
			
		}

		
		/**
		 * 
		 * @brief Create an event and put it into the DB
		 * @param array $data The data provide by the user after being checked by the controller
		 * @retval mixed true if execute correctly error_info if not
		 */
		public function createEvent($data){
			$datas = $this->checkParams($data, true, true);
			if($datas == -1)
				return false;
			
			if(isset($datas['Id_Event'])){
				return false;
			}
			
			if(!isset($datas['Id_Recurrence'])){
				$datas = array_merge($datas, array("Id_Recurrence" => 1));
			}			
			
			$datas = array_intersect_key($datas, $this->fields_event);
			$a = $this->sql->insert("event", $datas);
			if($a){
				$id = intval($this->sql->last_insert_id());
				if(isset($data['limit'])){
					$limit = new DateTime($data['limit']);
					$this->setDate($id, "Deadline", $limit);
				}
				elseif(isset($data['start'])){
					$start = new DateTime($data['start']);
					$end = new DateTime($data['end']);
					if($start->format("H:i:s") == "00:00:00"){
						$this->setDate($id, "Date", $start, $end);
					
					}
					else 
						$this->setDate($id, "TimeRange", $start, $end);
				}
				return $id;
			}
			return false;
			
				
		}

		/**
		 * 
		 * @brief Update event(s) (specify by $from) data to the those specify by $to
		 * @param array $from array of elements that allow us to identy target event(s)
		 * @param array $to new data to put in the bdd 
		 * @param bool $recur Indicate if it's a recurrent modification or not
		 * @retval mixed true if execute correctly error_info or false if not
		 */
		public function modifyEvent($from, $to, $recur = false){

			
			$table = implode(" JOIN ", $this->table);
			
			$data = $this->checkParams($to, true, true);
			if($data == -1){
				return false;
			}

						
			if(!$recur) //Desolidarize recur
				$data['Id_Recurrence'] = 1;
			
				
			
			$where = $this->checkParams($from, true);
			
			$whereClause = array();
			$i = 0;
			foreach($where as $key => $value){
				if($key == "Id_Event")
					$whereClause[$i] = "event.". $key ." = ".$value.""; //removing ambiguity
				else
					$whereClause[$i] = $key ." = ".$value."";
				$i++;
			}
			
			$a = $this->sql->update($table, $data, implode(" AND ", $whereClause));
	
		}
		/**
		 * @brief 
		 * @param int $id the id of the event
		 * @param string $type the type of event (Date|Deadline|TimeRange)
		 * @param DateTime $start the start of the event (or the deadline)
		 * @param DateTime $end the end of the event 
		 * @param boolean $update if it's already set to an other value
		 * @retval mixed true if execute correctly error_info if not
		 */
		public function setDate($id, $type, $start, $end = NULL, $update = false){
			$a = false;
			switch($type){
				case "Date":
					$data = array();
					$data["Id_event"] = $this->sql->quote($id);
					$data["Start"] = $this->sql->quote($start->format("Y-m-d"));
					$data["End"] = $this->sql->quote($end->format("Y-m-d"));
					$table = "date_range_event";
					break;
				case "Deadline":
					$data = array();
					$data["Id_event"] = $this->sql->quote($id);
					$data["`Limit`"] = $this->sql->quote($start->format("Y-m-d H:i:s"));
					$table = "deadline_event";
					break;
				case "TimeRange":
					$data = array();
					$data["Id_event"] = $this->sql->quote($id);
					$data["Start"] = $this->sql->quote($start->format("Y-m-d H:i:s"));
					$data["End"] = $this->sql->quote($end->format("Y-m-d H:i:s"));
					$table = "time_range_event";
					break;
				default:
					return false;
					break;	
			}
			if($update){
				if(!is_int($id))
					return -1;
				$a = $this->sql->update($table, $data, "Id_Event=".$id);

			}
			else
				$a = $this->sql->insert($table, $data);

			if($a)
				return true;
			$this->error .= "\n Date Error";
			return false;
		}

		/**
		 * @brief Get the data of the events having the given ids
		 * @param array $ids an array containing the differents ids
		 * @retval array Multi-dimensionnal array containing the event data
		 *
		 * The rows contains the following keys : 
		 * <ul>
		 *   <li> Id_Event : id of the event </li>
		 *   <li> Name : event name </li>
		 *   <li> Description : event description </li>
		 *   <li> Place : location where the event take place (or NULL) </li>
		 *   <li> Start : start date/datetime (for deadline events, this field contains the limit datetime) </li>
		 *   <li> End : end date/datetime (for deadline events, this field contains an empty string) </li>
		 *   <li> DateType : a string specifying the date type of the event ('time_range', 'date_range' or 'deadline') </li>
		 *   <li> EventType : a string specifying the event type ('sub_event', 'indep_event' or 'student_event') </li>
		 *   <li> Color : event category color </li>
		 *   <li> Categ_Name_EN : the event category name in english </li>
		 *   <li> Categ_Name_FR : the event category name in french </li>
		 *   <li> Categ_Desc_EN : the event category description in english </li>
		 *   <li> Categ_Desc_FR : the event category description in french </li>
		 *   <li> Recur_Category_EN : the recurrence category name in english </li>
		 *   <li> Recur_Category_FR : the recurrence category name in french </li>
		 *   <li> Id_Recur_Category : the id of the recurrence category </li>
		 *   <li> Id_Recurrence : the recurrence id of the event (1 for never) </li>
		 *   <li> Id_Category : the event category id </li>
		 * </ul>
		 */
		public function getEventFromIds(array $ids)
		{
			if(count($ids) == 0)
				return array();

			// create que question mark array string : (?, ..., ?) 
			$qmark_array = array_fill(0, count($ids), "?");
			$id_array_str = "(".implode(", ", $qmark_array).")";

			$query  =  "SELECT * FROM event 
						NATURAL JOIN 
						(
						  SELECT Id_Event, Start, End, 'time_range' AS DateType 
						  FROM time_range_event 
						  WHERE Id_Event IN ".$id_array_str."
						  
						  UNION ALL

						  SELECT Id_Event, DATE(Start) AS Start, DATE(End) AS End, 'date_range' AS DateType
						  FROM date_range_event
						  WHERE Id_Event IN ".$id_array_str."

						  UNION ALL

						  SELECT Id_Event, `Limit` AS Start, '' AS End, 'deadline' AS DateType
						  FROM deadline_event
						  WHERE Id_Event IN ".$id_array_str."
						) AS time_data
						NATURAL JOIN
						(
						  SELECT Id_Event, 'sub_event' AS EventType 
						  FROM sub_event
						  WHERE Id_Event IN ".$id_array_str."

						  UNION ALL

						  SELECT Id_Event, 'indep_event' AS EventType
						  FROM independent_event
						  WHERE Id_Event IN ".$id_array_str."

						  UNION ALL

						  SELECT Id_Event, 'student_event' AS EventType
						  FROM student_event
						  WHERE Id_Event IN ".$id_array_str."
						) AS type_data
						NATURAL JOIN 
						( SELECT Id_Category, Color, Description_EN AS Categ_Desc_EN, 
								 Description_FR AS Categ_Desc_FR, Name_EN AS Categ_Name_EN, 
								 Name_FR AS Categ_Name_FR 
						  FROM event_category ) AS categ
						NATURAL JOIN recurrence
						NATURAL JOIN recurrence_category;";

			
			return  $this->sql->execute_query($query, \ct\array_dup($ids, 6));
			
		}

		/**
		 * @brief Checks whether the event having the given id is an academic event or not
		 * @param[in] int $event_id The event id
		 * @retval bool True if the event is an academic event, false otherwise
		 */
		public function is_academic_event($event_id)
		{
			$ret = $this->sql->execute_query("SELECT event_is_academic(?) AS ret;", array($event_id));
			return !!$ret[0]['ret'];
		}

		/**
		 * @brief Checks whether the event having the given id is a private event or not
		 * @param[in] int $event_id The event id
		 * @retval bool True if the event is a private event, false otherwise
		 */
		public function is_private_event($event_id)
		{
			$ret = $this->sql->execute_query("SELECT event_is_student(?) AS ret;", array($event_id));
			return !!$ret[0]['ret'];
		}

		/**
		 * @brief Checks whether the event having the given id is a subevent or not
		 * @param[in] int $event_id The event id
		 * @retval bool True if the event is a subevent, false otherwise
		 */
		public function is_sub_event($event_id)
		{
			$ret = $this->sql->execute_query("SELECT event_is_sub_event(?) AS ret;", array($event_id));
			return !!$ret[0]['ret'];
		}

		/**
		 * @brief Checks whether the event having the given id is an independent event or not
		 * @param[in] int $event_id The event id
		 * @retval bool True if the event is an independent event, false otherwise
		 */
		public function is_independent_event($event_id)
		{
			$ret = $this->sql->execute_query("SELECT event_is_independent(?) AS ret;", array($event_id));
			return !!$ret[0]['ret'];
		}

		/**
		 * @brief Checks whether the event having the given id is a deadline event or not
		 * @param[in] int $event_id The event id
		 * @retval bool True if the event is a deadline event, false otherwise
		 */
		public function is_deadline_event($event_id)
		{
			$ret = $this->sql->execute_query("SELECT event_is_deadline(?) AS ret;", array($event_id));
			return !!$ret[0]['ret'];
		}

		/**
		 * @brief Checks whether the event having the given id is a time range event or not
		 * @param[in] int $event_id The event id
		 * @retval bool True if the event is a time range event, false otherwise
		 */
		public function is_time_range_event($event_id)
		{
			$ret = $this->sql->execute_query("SELECT event_is_time_range(?) AS ret;", array($event_id));
			return !!$ret[0]['ret'];
		}

		/**
		 * @brief Checks whether the event having the given id is a date range event or not
		 * @param[in] int $event_id The event id
		 * @retval bool True if the event is a date range event, false otherwise
		 */
		public function is_date_range_event($event_id)
		{
			$ret = $this->sql->execute_query("SELECT event_is_date_range(?) AS ret;", array($event_id));
			return !!$ret[0]['ret'];
		}

		/** 
		 * @brief Checks if the given event exists
		 * @param[in] int $event_id The event id
		 * @param[in] int   $lock_mode	 One of the Model LOCKMODE_* class constant 
		 * @retval bool True if the event exists, false otherwise
		 * @note A read lock on the event table might be acquired (according to the lock_mode) 
		 */
		public function event_exists($event_id, $lock_mode=Model::LOCKMODE_NO_LOCK)
		{
			$ret = false;
			
			if($this->do_lock($lock_mode)){
				$ret = $this->sql->count("event", "Id_Event = ".$this->sql->quote($event_id)) > 0;
			}
			return $ret;
		}

		/**
		 * @brief Returns the temporal data of the given event
		 * @param[in] int $event_id The identifier of the event
		 * @param[in] int   $lock_mode	 One of the Model LOCKMODE_* class constant 
		 * @retval array Array containing the temporal data (empty array means that the event does not exist)
		 * @note The array contains a field 'Type' of which the value is one of the TEMP_* class constant. This 
		 * field identify the temporal type of the event
		 * @note This function accesses the tables time_range_event, date_range_event and deadline_event 
		 * @note According to the type the event is structured as follows :
		 * <ul>
		 *  <li>deadline : array('End' => datetime, 'Type' => ...)</li>
		 *  <li>time_range : array('Start' => datetime, 'End' => datetime, 'Type' => ...)</li>
		 *  <li>date_range : array('Start' => date, 'End' => date, 'Type' => ...)</li>
		 * </ul>
		 */
		public function get_event_temporal_data($event_id, $lock_mode=Model::LOCKMODE_NO_LOCK)
		{
			if($this->do_lock($lock_mode))
				$this->sql->lock(array("time_range_event READ", "date_range_event READ", "deadline_event READ"));

			$query  =  "SELECT Start, End, 'time_range' AS Type FROM `time_range_event` WHERE Id_Event = ? 
						UNION ALL
						SELECT Start, End, 'date_range' AS Type FROM `date_range_event` WHERE Id_Event = ?
						UNION ALL
						SELECT '' AS Start, `Limit` AS End, 'deadline' AS Type FROM `deadline_event` WHERE Id_Event = ?";

		 	$event = $this->execute_query($query, array($event_id, $event_id, $event_id));

		 	if($this->do_unlock())
				$this->sql->unlock();

		 	if(empty($event))
		 		return array();

		 	$event = $event[0];

		 	if($event['Type'] === "time_range_event")
		 		$event['Type'] = self::TEMP_TIME_RANGE;
		 	elseif($event['Type'] === "date_range_event")
		 		$event['Type'] = self::TEMP_DATE_RANGE;
		 	else
		 	{
		 		$event['Type'] = self::TEMP_DEADLINE;
		 		unset($event['Start']);
		 	}

		 	return $event;
		}

		/**
		 * @brief Change the type of the given event. The new type will be a 
		 * @param[in] int $event The event id
		 * @param[in] string $datetime The start datetime
		 * @retval True on success, false on error
		 */
		public function reset_time_type_deadline($event, $datetime)
		{
			$success = $this->delete_time_type($event);

			// insert the new deadline data
			$insert_date = array("Id_Event" => $target['event'], "Limit" => $target['proposition']);
			$success &= $this->sql->insert("deadline_event", $this->quote_all($insert_date));
		}

		/**
		 * @brief Change the type of the given event. The new type will be a 
		 * @param[in] int $event The event id
		 * @param[in] string $start The start datetime
		 * @param[in] string $end The end datetime
		 * @retval True on success, false on error
		 */
		public function reset_time_type_time_range($event, $start, $end)
		{
			$success = $this->delete_time_type($event);
			
			// insert the new time_range data
			$insert_date = array("Id_Event" => $target['event'], "Start" => $start, "End" => $end);
			$success &= $this->sql->insert("time_range_event", $this->quote_all($insert_date));
		}

		/**
		 * @brief Change the type of the given event. The new type will be a 
		 * @param[in] int $event The event id
		 * @param[in] string $start The start date
		 * @param[in] string $end The end date
		 * @retval True on success, false on error
		 */
		public function reset_time_type_date_range($event, $start, $end)
		{
			$success = $this->delete_time_type($event);
			
			// insert the new date_range event data
			$insert_date = array("Id_Event" => $target['event'], "Start" => $start, "End" => $end);
			$success &= $this->sql->insert("date_range_event", $this->quote_all($insert_date));
		}

		/**
		 * @brief Delete the event temporal type of the given event
		 * @param[in] int $event The event id
		 * @retval bool True on success, false on error
		 */
		private function delete_time_type($event)
		{
			$quoted_event = $this->sql->quote($event);
			$success = $this->sql->delete("time_range_event", "Id_Event = ".$quoted_event);
			$success &= $this->sql->delete("date_range_event", "Id_Event = ".$quoted_event);
			$success &= $this->sql->delete("deadline_event", "Id_Event = ".$quoted_event);
			return $success;
		}
		
		/**
		 * @brief get an annotation for the given student/event couple
		 * @param int $eventId The event id
		 * @param int $userId The user id 
		 * @retval mixed the annotation or false if empty
		 */
		public function get_annotation($eventId, $userId) {
			if(intval($eventId) && intval($userId)){
				$data = $this->sql->select("event_annotation", "Id_Event = ".$eventId." AND Id_Student =".$userId, array("Annotation"));
				if(isset($data[0]["Annotation"]))
					return $data[0]['Annotation'];
				else
					return false;
			}
			return false;
		}
		
		/**
		 * @brief set an annotation for the given student/event couple
		 * @param int $eventId The event id
		 * @param int $userId The user id
		 * @param string $annotation the annotation
		 * @param bool $update true if we have to perform an update false (by default)  if insert
		 * @retval mixed true if everything go perfectly false or the error_innfo from sql if not
		 */
		public function set_annotation($eventId, $userId, $annotation, $update = false) {
			$annotation_quoted = $this->sql->quote($annotation);
			 
			if(!$this->event_exists($eventId, Model::LOCKMODE_LOCK)){
				$this->error .= "\n This event does not exist";
				return false;
			}
		
			if(intval($userId) < 0){
				$this->error .= "\n Error in the  user field";
				return false;
			}
			
			if($update)
				$a = $this->sql->update("event_annotation", array("Annotation" => $annotation_quoted), "Id_Event=".$eventId." AND Id_Student=".$userId);
			else
				$a = $this->sql->insert("event_annotation", array("Annotation" => $annotation_quoted, "Id_Event" => $eventId, "Id_Student" => $userId ));
			
			return $a;
			
		}
		
		/**
		 * @brief delete an annotation for the given student/event couple
		 * @param int $eventId The event id
		 * @param int $userId The user id
		 * @param bool $update true if we have to perform an update false (by default)  if insert
		 * @retval true if everything go perfectly false if not
		 */
		public function delete_annotation($eventId, $userId){
			$event = $this->sql->quote($eventId);
			$user = $this->sql->quote($userId);
			$success = $this->sql->delete("event_annotation", "Id_Event = ".$event." AND Id_Student = ".$user);
			if($success)
				return true;
			else{
				return false;
			}
		}
		
		 	
	/**
	 * @brief get an intersection of an array based on the key of an other array (ie if the value x in the table $array is a key of the table $keyarray we keep it)
	 * @param array $array the tested array
	 * @param array $keyArray the key that we want to appear as value in the first array
	 * @retval the intersected array
	 */
 		protected function array_intersect_key_val($array, $keyArray){ //$array est un tableau dont on cherche a savoir quelles sont les valeurs en commun avec les clés de $keyarray
		$retval = array();
		foreach($array as $key => $value){
			if(array_key_exists($value, $keyArray)){
				$retval[$key] = $value;
			}
		}
		return $retval;
	}
	
	/**
	 * @brief delete an event from the DB 
	 * @param int $eventId the id of the event to delete
	 * @retval mixed true if everything was correct error_info if not
	 * @note this function only delete from the tale event but the  db is construct with the CASCADE keyword so it will delete also if the id is present in academic/sub/... event table
	 */
		public function delete_event($eventId){
			$event = $this->sql->quote($eventId);
			$success = $this->sql->delete("event", "Id_Event = ". $event);
			if($success)
				return true;
			
			$this->error .= "\n Error while deleting the event";
				return false;
		}
		
		/**
		 * @brief create different event with same data at differents interval
		 * @param array $data The date 
		 * @param enum $recurence the type of reccurence 
		 * @param DateTime $endrecurence the end date of the recurrence (exclude)
		 * @retval array the ids of the created events
		 */
		public function createEventWithRecurrence(array $data, $recurence, $endrecurence){
			switch($recurence){
				case self::REC_DAILY :
					$interval = new DateInterval("P1D");
					break;
				case self::REC_WEEKLY :
					$interval = new DateInterval("P7D");
					break;
				case self::REC_BIM :
					$interval = new DateInterval("P14D");
					break;
				case self::REC_MONTHLY :
					$interval = new DateInterval("P1M");
					break;
				case self::REC_YEARLY :
					$interval = new DateInterval("P1Y");
					break;
				default :
					$this->error .= "\n Error in the recurrence field";
					return false;
					break;
			}
			
			$typeOfDate = "";
			if(isset($data['limit'])){
				$start = new DateTime($data['limit']);
				$end = NULL;
				$typeOfDate = "Deadline";
			}
			elseif(isset($data['start'])){
				$start = new DateTime($data['start']);
				$end = new DateTime($data['end']);
				if($start->format("H:i:s") == "00:00:00")
					$typeOfDate =  "Date";
				else 
					$typeOfDate = "TimeRange";
			}
			else{
				$this->error .= "\n Error in the Date field";
				return false;
			}
		
			unset($data['start']);
			unset($data['end']);
			unset($data['limit']);
		
			$ret = $this->sql->insert("recurrence", array("Id_Recur_Category"  => $recurence));
			$recId = $this->sql->last_insert_id();
			if(!$ret){
				$this->error .= "\n Error while registering the recurrence";
				return false;
			}
			
			$data["id_recurrence"] = $recId;
				
			$retval = array();
			while($start < $endrecurence){
				$id = $this->createEvent($data);
				if(is_int($id) && $id >= 0){
					$this->setDate($id, $typeOfDate, $start, $end);
					array_push($retval, $id);
				}
				$start->add($interval);
				if($end)
					$end->add($interval);
			}
			
			return $retval;
		}
		
		public function get_error(){
			return $this->error;
		}
		
		/**
		 * @brief delete from de db all the event with a specific id recurrence
		 * @param int $idRec the id of the recurrence;
		 * @retval boolean
		 * @warning NEVER USE WITH 1
		 */
		public function deleteEventRecurrence($idRec){
			return   $this->sql->delete("recurrence", "Id_Recurrence = ". $this->sql->quote($idRec));
		}
		
		/**
		 * @brief Set the event as favorite for the user
		 * @param int $eventId
		 * @param int $userId
		 * @retval boolean
		 */
		public function addAsFavorite($eventId, $userId){
			if(!$this->event_exists($eventId, self::LOCKMODE_LOCK))
				return false;
			
			return $this->sql->insert("favorite_event", array("Id_Student" => $this->sql->quote($userId),
																"Id_Event" => $this->sql->quote($eventId)));
		}
		
		/**
		 * @brief Remove the event of the favourite list
		 * @param int $eventId
		 * @param int $userId
		 * @retval boolean
		 */
		public function removeAsFavorite($eventId, $userId){
			return  $this->sql->delete("favorite_event", "Id_Student =".$this->sql->quote($userId)
																."AND Id_Event =". $this->sql->quote($eventId));
		
		}
		
		public function isFavorite($eventId, $userId){
			$ret = $this->sql->execute_query("SELECT Id_Event AS ret FROM favorite_event WHERE Id_Event = ? AND Id_Student  = ? ", array($event_id, $userId));
			return isset($ret[0]['ret']);
		}
	/**
	 * @brief return the start recurrent of a recurrent event
	 * @param int $recurrenceId
	 * @retval false if error start of recurrence if ok
	 */
		public function getStartRecurrence($recurrenceId){
		
			$rId = $this->sql->quote($recurrenceId);
			$query = "SELECT Start FROM event 
						 NATURAL JOIN ( 
					 	SELECT  Start  FROM time_range_event 
					 	UNION ALL
					 	SELECT  DATE(Start) AS Start FROM date_range_event 
					 	UNION ALL 
					 	SELECT `Limit` AS Start FROM deadline_event 
					 ) AS time_data WHERE Id_Recurrence =".$rId." ORDER BY Start ASC LIMIT 0,1";

			return $this->sql->execute_query($query)[0];
		}

		/**
		 * @brief return the end recurrent of a recurrent event
		 * @param int $recurrenceId
		 * @retval false if error end of recurrence if ok
		 */
		public function getEndRecurrence($recurrenceId){

				
			$rId = $this->sql->quote($recurrenceId);
			$query = "SELECT End FROM event
			NATURAL JOIN (
			SELECT  End  FROM time_range_event
			UNION ALL
			SELECT  DATE(End) AS End FROM date_range_event
			UNION ALL
			SELECT `Limit` AS End FROM deadline_event
			) AS time_data WHERE Id_Recurrence =".$rId." ORDER BY End DESC LIMIT 0,1";
		
			return $this->sql->execute_query($query)[0];
		}
		
	}