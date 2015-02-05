<?php

	namespace ct;

	header('Content-Type: text/html; charset=utf-8');

	// set include path to the path of the index.php file
	set_include_path(dirname(__FILE__));

	// various includes 
	require_once("functions.php");

	// init autoloading
	spl_autoload_register("ct\autoload");

	use util\entry_point\Browser;

	$browser = new Browser();

	echo $browser->get_controller()->get_output();
