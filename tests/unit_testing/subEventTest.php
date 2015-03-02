<?php

use ct\models\events\EventModel;
use ct\models\events\AcademicEventModel;

header('Content-Type: text/html; charset=utf-8');

// set include path to the path of the index.php file
set_include_path(dirname(__FILE__));

// various includes
require_once("/opt/lampp/htdocs/git/calendartool/web/functions.php");

// init autoloading
spl_autoload_register("ct\autoload");



//Event Model Test (assume there is an id_category = 1 
$test = new EventModel();
//Create
echo "<br> Create <br>";
$data = array("place" => "montef", "name" => "Cours X", "description" => "lol \n d", "id_category" => 1, "start" => "2015-03-01", "end" => "2015-03-01");
$a = $test->createEvent($data);
var_dump($a);
/*
//Updata
echo "<br> Update <br>";
$from = array("id_category" => 1);
$to = array("place" => "somewhere");
$b  = $test->modifyEvent($from, $to);
var_dump($b);

//Set date
echo "<br> Set Date <br>";
$date = new DateTime();
$date2 = new DateTime();
$date2->setDate(2016,1,1);
$c = $test->setDate(10,"Date", $date, $date2);
var_dump($c);

//Update Date
echo "<br> Update Date <br>";
$date2->setDate(2142, 1, 1);
$e = $test->setDate(10,"Date", $date, $date2, true);
var_dump($e);

//Academic Event
echo "<br> Academic Event <br>";
$model2 = new AcademicEventModel();
$data2 = array("feedback" => "pop", "workload" => 100, "practical_details" => "none", "name" => "Stuff", "description" => "why ?", "id_category" => 1, "place" => "montef");
$f = $model2->createEvent($data2);
var_dump($f);

//get events from id
echo "<br> GetEventFromIds <br>";
$ids = array(11,10);
$g = $test->getEventFromIds($ids);
var_dump($g);

//getEvents
echo "<br>GetEvent <br>";
$h = $test->getEvent(array("id_category" => 1), array("id_event"));
var_dump($h);

echo  "<br> GetEvent Academic <br>";
$i = $model2->getEvent(array("place" => "montef"));
var_dump($i);


//Annotation part
echo "<br> Select an annotation <br>";
$j = $test->get_annotation(11, 1);
var_dump($j);

echo "<br> Insert annotation <br>";
$k = $test->set_annotation(11, 1, "first");
var_dump($k);

echo "<br> Select an annotation <br>";
$j = $test->get_annotation(11, 1);
var_dump($j);

echo '<br> Update an annotation <br>';
$l = $test->set_annotation(11, 1, "up", true);
var_dump($l);

echo "<br> Select an annotation <br>";
$j = $test->get_annotation(11, 1);
var_dump($j);

echo "<br> Delete an annotation <br>";
$n = $test->delete_annotation(11, 1);
var_dump($n);

echo "<br> Select an annotation <br>";
$j = $test->get_annotation(11, 1);
var_dump($j);*/
