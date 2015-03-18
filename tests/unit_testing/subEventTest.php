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
<<<<<<< HEAD
$test = new EventModel();
//Create*/
/*
echo "<br> Create <br>";
$data = array("place" => "montef", "name" => "Cours X", "description" => "lol \n d", "id_category" => 1, "start" => "2015-03-01", "end" => "2015-03-01");
=======

$test = new EventModel();/*
//Create
echo "<br> Create <br>";
$data = array("place" => "montef", "name" => "Cours X", "description" => "lol \n d", "id_category" => 1, "start" => "2015-03-01", "end" => "2015-03-01");
>>>>>>> bc965c9fa13b7accab51a8e7dbce38eb96ef9e70
$a = $test->createEvent($data);
echo $test->get_error();
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
$ids = array(1,2);
$g = $test->getEventFromIds($ids);
var_dump($g);

//getEvents
*/
echo "<br>GetEvent <br>";
$h = $test->getEvent(array("id_category" => "1"));
var_dump($h);
/*
echo  "<br> GetEvent Academic <br>";
$i = $model2->getEvent(array("place" => "montef"));
var_dump($i);
*/

//Annotation part
/*
echo "<br> Select an annotation <br>";
$j = $test->get_annotation(35, 1);
var_dump($j);

echo "<br> Insert annotation <br>";
$k = $test->set_annotation(35, 1, "first");
var_dump($k);

<<<<<<< HEAD
echo "<br> Select an annotation <br>";
$j = $test->get_annotation(35, 1);
var_dump($j);
=======
echo "<br> Select an annotation <br>";
$j = $test->get_annotation(11, 1);
var_dump($j);
>>>>>>> bc965c9fa13b7accab51a8e7dbce38eb96ef9e70

echo '<br> Update an annotation <br>';
$l = $test->set_annotation(35, 1, "up", true);
var_dump($l);

<<<<<<< HEAD
echo "<br> Select an annotation <br>";
$j = $test->get_annotation(35, 1);
var_dump($j);
=======
echo "<br> Select an annotation <br>";
$j = $test->get_annotation(11, 1);
var_dump($j);
>>>>>>> bc965c9fa13b7accab51a8e7dbce38eb96ef9e70

echo "<br> Delete an annotation <br>";
$n = $test->delete_annotation(35, 1);
var_dump($n);

<<<<<<< HEAD
echo "<br> Select an annotation <br>";
$j = $test->get_annotation(35, 1);
var_dump($j);
/*
=======
echo "<br> Select an annotation <br>";
$j = $test->get_annotation(11, 1);

var_dump($j);*/

>>>>>>> bc965c9fa13b7accab51a8e7dbce38eb96ef9e70
echo "<br> receurrence <br>";
$data = array("place" => "montef", "name" => "Cours XXX", "description" => "lol \n d", "id_category" => 1, "start" => "2015-03-01 14:15:16", "end" => "2015-03-02 15:14:12");
$end = new DateTime("2015-03-04");
$e = $test->createEventWithRecurrence($data, EventModel::REC_DAILY, $end);
echo $test->get_error();
<<<<<<< HEAD
var_dump($e);*/
=======
var_dump($e);

>>>>>>> bc965c9fa13b7accab51a8e7dbce38eb96ef9e70
