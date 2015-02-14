<?php
	use util\mvc\EventModel;

header('Content-Type: text/html; charset=utf-8');

	// set include path to the path of the index.php file
	set_include_path(dirname(__FILE__));

	// various includes 
	require_once("functions.php");

	// init autoloading
	spl_autoload_register("ct\autoload");


	$test = new EventModel();
	$data = array("id_event" => 1, "name" => "e1", "description" => "ceci est un event");
	$test->createEvent($data);
	