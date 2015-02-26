<?php

	/**
	 * @file
	 * @brief Event ControllerClass
	 */

namespace ct\models\events;
use util\mvc\Model;
use util\database\Database;


	/**
	 * @class Event
	 * @brief Class for getting event from D
	 */


	class EventModel extends Model{
			
		protected $fields;
		protected $fields_event;
		protected $table;
		protected $translate;

		const TEMP_DEADLINE = 1; /**< @brief Constant identifying the temporal type of event : deadline event */
		const TEMP_TIME_RANGE = 2; /**< @brief Constant identifying the temporal type of event : time range event */
		const TEMP_DATE_RANGE = 3; /**< @brief Constant identifying the temporal type of event : date range event */
		
		function __construct() {
			parent::__construct();
			
			$this->fields = array("id_event" => "int", "name" => "text", "description" => "text", "id_recurence" => "int", "place" => "text", "id_category" => "int", "limit" => "date", "start" => "date", "end" => "date");
			$this->fields_event = array("Id_Event" => "int", "Name" => "text", "Description" => "text", "Id_Recurence" => "int", "Place" => "text", "Id_Category" => "int");
			$this->table = array();
			$this->table[0] = "event";
			$this->translate = array("id_event" => "Id_Event", "name" => "Name", "description" => "Description", "id_recurence" => "Id_Recurrence", "place" => "Place", "id_category" => "Id_Category", "limit" => "Limit", "start" =>"Start", "end" => "End");
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
		 * @brief Get an event from the bdd
		 * @param String $tables tables to find the event (default Event)
		 * @param array String $infoData the data to identify the event
		 * @param array String $requestData the requested data for the event $id (empty = all)
		 * @retval mixed an array containing the data, false if no matching
		 */
		protected  function getData($table, array $infoData = null, array $requestData = null){

			//Build WHERE clause
			if($infoData != null && !empty($infoData)){
				$ar = array();
				$i = 0;
				
			
				foreach($infoData as $key => $value){
					$ar[$i] = $key." = ".$value;
					$i++;
				}
				
				$where = implode(" AND ", $ar);
			
				$data = $this->sql->select($table, $where, $requestData);
				return $data;
			}

		}
		
		/**
		 * @brief Get an event from the bdd (this function check the args)
		 * @param $type type of event
		 * @param $requestedData what we want to obtain (nothing for *)
		 * @param $infoData what we know about the event
		 * @param $dateType the type of event concerning the date (Date|Deadline|TimeRange)
		 * @retval mixed an array containing the data false otherwise
		 */
		public function getEvent (array $infoData = null,  array $requestedData = null, $dateType = NULL){	
			if($infoData == null)
				$infoData = array();
			
			$table = implode(" JOIN ", $this->table);
			
			
			if(isset($dateType)){
				switch($dateType){
					case "Date":
						$table = $table ." JOIN date_range_event";
						break;
					case "Deadline":
						$table = $table ." JOIN deadline_event";
						break;
					case "TimeRange":
						$table = $table . " JOIN time_range_event";
						break;
						
				}
			}
			
			
			$info = $this->checkParams($infoData, true);
			if($requestedData ==  null)
				return $this->getData($table, $info);
		
			$request = $this->checkParams($requestedData, false);		
			return $this->getData($table, $info, $request);
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
						if(!is_int($value))
							return -1;
						else
							$arr[$key] = $this->sql->quoted($value);
					}
					elseif($this->fields[$key] == "bool"){
						if(!is_bool($value))
							return -1;
						else
							$arr[$key] = $this->sql->quoted($value);	
					}
					elseif($this->fields[$key] == "text"){
						$arr[$key] = htmlEntities($value, ENT_QUOTES);
						$arr[$key] = nl2br($arr[$key]);
						$arr[$key] = $this->sql->quoted($arr[$key]);
					}
					elseif($this->fields[$key] == "date"){
						//TODO
						$arr[$key] = $this->sql->quoted($value);
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
			
			if(isset($datas['Id_Event']))
				return false;
			
			$datas = array_intersect_key($datas, $this->fields_event);

			//TODO a lock here
			$a = $this->sql->insert($this->table[0], $datas);
			if($a){
				$id = $this->sql->execute_query("SELECT Id_Event FROM event ORDER BY Id_Event DESC LIMIT 0,1", array());
				if(isset($id[0]["Id_Event"]))
					$ret = intval($id[0]["Id_Event"]);
				else 
					$ret = $this->sql->error_info();
			}
			else 
				$ret = $this->sql->error_info();
			//TODO unlock
			
			return $ret;
			
		}

		/**
		 * 
		 * @brief Update event(s) (specify by $from) data to the those specify by $to
		 * @param array $from array of elements that allow us to identy target event(s)
		 * @param array $to new data to put in the bdd 
		 * @retval mixed true if execute correctly error_info or false if not
		 */
		public function modifyEvent($from, $to){
			$table = implode(" JOIN ", $this->table);
			
			$data = $this->checkParams($to, true, true);
			if($data == -1)
				return false;
			
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
			if($a)
				return true;
			return $this->sql->error_info();
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
					$data["Id_event"] = $id;
					$data["Start"] = $start->format("Y-m-d");
					$data["End"] = $start->format("Y-m-d");
					$table = "date_range_event";
					break;
				case "Deadline":
					$data = array();
					$data["Id_event"] = $id;
					$data["Limit"] = $start->format("Y-m-d H:i:s");
					$table = "deadline_event";
					break;
				case "TimeRange":
					$data = array();
					$data["Id_event"] = $id;
					$data["Start"] = $start->format("Y-m-d H:i:s");
					$data["End"] = $start->format("Y-m-d H:i:s");
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
			return $this->sql->error_info();
		}
	/**
	 * @brief get an array of event from an array of id
	 * @param array $ids an array containing the differents ids
	 * @param string $dateType the type of the date that the event have
	 * @retval mixed array of data (false if error)
	 */
		public function getEventFromIds($ids = null, $dateType = null){
			if($ids == null)
				$ids = array();
			
			if(empty($ids))
				return -1;
			
			$table = implode(" JOIN ", $this->table);
			
			
			$id = 'event.Id_Event = ';
			$id = $id.implode(" OR event.Id_Event = ", $ids);
			
			if(isset($dateType)){
				switch($dateType){
					case "Date":
						$table = $table ." JOIN date_range_event";
						break;
					case "Deadline":
						$table = $table ." JOIN deadline_event";
						break;
					case "TimeRange":
						$table = $table . " JOIN time_range_event";
						break;
							
				}
			}
			
			return $this->sql->select($table, $id);
			
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
			if($this->do_lock())
			$ret = $this->sql->count("event", "Id_Event = ".$this->sql->quote($event_id)) > 0;
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
			if(is_int($eventId) && is_int($userId)){
				$data = $this->sql->select("event_annotation", "Id_Event = ".$eventId." AND Id_Student =".$userId, array("Annotation"));
				if(isset($data[0]["Annotation"]))
					return $data[0]['Annotation'];
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
			if(is_int($eventId) && is_int($userId)){
				if($update)
					$a = $this->sql->update("event_annotation", array("Annotation" => $annotation_quoted), "Id_Event=".$eventId." AND Id_Student=".$userId);
				else
					$a = $this->sql->insert("event_annotation", array("Annotation" => $annotation_quoted, "Id_Event" => $eventId, "Id_Student" => $userId ));
				
				if($a)
					return true;
				else
					return $this->sql->error_info();
					
			}
			return false;
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
			else
				return $this->sql->error_info();
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
			else
				return $this->sql->error_info();
		}
	}