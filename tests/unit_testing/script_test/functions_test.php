<?php
	set_include_path("D:\\Documents\\Bitbucket\\CalendarTool");

	require_once("web\\functions.php");

	class Test extends PHPUnit_Framework_TestCase
	{
		public function testArrayColumn_empty_keys()
		{
			$array = array(array("col1" => "val00", "col2" => "val01", "col3" => "val02"),
						   array("col1" => "val01", "col2" => "val11", "col3" => "val12"));

			$keys = array();

			$this->assertEquals(array(array(), array()), ct\array_columns($array, $keys));
		}

		public function testCommonRegroup_general()
		{
			$array = array(array("col1" => "val00", "col2" => "val01", "col3" => "val02"),
						   array("col1" => "val01", "col2" => "val11", "col3" => "val12"));

			$out_array = array(array("col2" => "val01", "col3" => "val02"),
						  	   array("col2" => "val11", "col3" => "val12"));

			$keys = array("col2", "col3");

			$this->assertEquals($out_array, ct\array_columns($array, $keys));
		}
	}