<?php
	set_include_path("D:\\Documents\\Bitbucket\\CalendarTool");
	require_once("web\\scripts\\load_data.php");

	class TestLoadData extends PHPUnit_Framework_TestCase
	{
		public function testCommonRegroup_empty()
		{
			$array = array();
			$column = "col";

			$eff_out = common_regroup($array, $column);

			$this->assertEquals(true, empty($eff_out));
		}

		public function testCommonRegroup_missing_column()
		{
			$array = array(array("col" => "val1"), array("col" => "val2"));
			$column = "col2";

			$this->assertEquals($array, common_regroup($array, $column));
		}

		public function testCommonRegroup_one_column()
		{
			$array = array(array("col" => "val1"), array("col" => "val2"));
			$out_array = array("val1" => array(), "val2" => array());
			$column = "col";

			$this->assertEquals($out_array, common_regroup($array, $column));
		}

		public function testCommonRegroup_general()
		{
			$array = array(array("col1" => "val00", "col2" => "val01", "col3" => "val02"),
						   array("col1" => "val00", "col2" => "val11", "col3" => "val12"),
						   array("col1" => "val00", "col2" => "val21", "col3" => "val22"),
						   array("col1" => "val10", "col2" => "val31", "col3" => "val32"),
						   array("col1" => "val10", "col2" => "val41", "col3" => "val42"),
						   array("col1" => "val10", "col2" => "val51", "col3" => "val52"),
						   array("col1" => "val10", "col2" => "val61", "col3" => "val62"),
						   array("col1" => "val20", "col2" => "val71", "col3" => "val72"),
						   array("col1" => "val20", "col2" => "val81", "col3" => "val82"),
						   array("col1" => "val20", "col2" => "val91", "col3" => "val92"));

			$out_array = array("val00" => array(array("col2" => "val01", "col3" => "val02"),
							   				    array("col2" => "val21", "col3" => "val22"),
							   				    array("col2" => "val11", "col3" => "val12")),
							   "val10" => array(array("col2" => "val61", "col3" => "val62"),
							   					array("col2" => "val51", "col3" => "val52"),
							   					array("col2" => "val41", "col3" => "val42"),
							   					array("col2" => "val31", "col3" => "val32")),
							   "val20" => array(array("col2" => "val91", "col3" => "val92"),
							   					array("col2" => "val71", "col3" => "val72"),
							   					array("col2" => "val81", "col3" => "val82")));
			$column = "col1";

			$this->assertEquals($out_array, common_regroup($array, $column));
		}
	}