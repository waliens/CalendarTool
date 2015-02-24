<?php

	namespace ct;

	// set include path to the path of the index.php file
	set_include_path(dirname(__FILE__));

	// various includes 
	require_once("functions.php");

	// init autoloading
	spl_autoload_register("ct\autoload");

	use util\entry_point\Browser;
	use util\entry_point\Ajax;
	use ct\Connection;

	$connection = Connection::get_instance();

	header('Content-Type: text/html; charset=utf-8');

	if(!isset($_GET['src']) || empty($_GET['src']))
		$src = "browser";
	else
		$src = $_GET['src'];

	switch($src)
	{
	case "ajax": 
		$entry_point = new Ajax();
		break;
	default: 
		$entry_point = new Browser();
	}

	echo $entry_point->get_controller()->get_output();