<?php
	

	require_once("D:\\Documents\\Bitbucket\\CalendarTool\\web\\ct\\util\\superglobals\\Superglobal.class.php");
	require_once("D:\\Documents\\Bitbucket\\CalendarTool\\web\\ct\\util\\superglobals\\SG_Post.class.php");
	require_once("D:\\Documents\\Bitbucket\\CalendarTool\\web\\ct\\util\\superglobals\\SG_Get.class.php");
	require_once("D:\\Documents\\Bitbucket\\CalendarTool\\web\\ct\\util\\superglobals\\SG_Cookies.class.php");

	use ct\util\superglobals\Superglobal as Superglobal;
	use ct\util\superglobals\SG_Post as SG_Post;
	use ct\util\superglobals\SG_Get as SG_Get;
	use ct\util\superglobals\SG_Cookies as SG_Cookies;

	class TestSuperglobals extends PHPUnit_Framework_TestCase
	{
		public function setUp()
		{
			$_POST = array("key1" => "value1", 
							"key2" => "", 
							"key3" => " ", 
							"arr" => array("k" => "v"), 
							"arr1" => array(), 
							"arr2" => array("k" => "v", "k2" => "v2"));
			$_GET = $_POST;
		}

		public function testPost_base()
		{
			$sg_post = new SG_Post();

			$this->assertEquals(Superglobal::ERR_NOT_SET,  $sg_post->check("key4"));
			$this->assertEquals(Superglobal::ERR_EMPTY,    $sg_post->check("key2"));
			$this->assertEquals(Superglobal::ERR_EMPTY,    $sg_post->check("key3", Superglobal::CHK_ALL));
			$this->assertEquals(Superglobal::ERR_OK,       $sg_post->check("key3"));
			$this->assertEquals(Superglobal::ERR_OK,	   $sg_post->check("key1"));
		}

		public function testPost_array()
		{
			$sg_post = new SG_Post();
			
			$this->assertEquals(Superglobal::ERR_OK, 	   $sg_post->check("arr"));
			$this->assertEquals(Superglobal::ERR_EMPTY,    $sg_post->check("arr1"));
		}

		public function testPost_callback()
		{
			$sg_post = new SG_Post();

			$clback = function($str) { return strlen($str) > 3; };

			$this->assertEquals(Superglobal::ERR_OK, 	   $sg_post->check("key1", null, $clback));
			$this->assertEquals(Superglobal::ERR_CALLBACK, $sg_post->check("key3", null, $clback));

			$clback2 = function($array) { return count($array) == 2; };

			$this->assertEquals(Superglobal::ERR_EMPTY,    $sg_post->check("arr1", null, $clback2));
			$this->assertEquals(Superglobal::ERR_OK, 	   $sg_post->check("arr2", null, $clback2));
			$this->assertEquals(Superglobal::ERR_CALLBACK, $sg_post->check("arr", null, $clback2));
		}

		public function testGet_base()
		{
			$sg_get = new SG_Get();

			$this->assertEquals(Superglobal::ERR_NOT_SET,  $sg_get->check("key4"));
			$this->assertEquals(Superglobal::ERR_EMPTY,    $sg_get->check("key2"));
			$this->assertEquals(Superglobal::ERR_EMPTY,    $sg_get->check("key3", Superglobal::CHK_ALL));
			$this->assertEquals(Superglobal::ERR_OK,       $sg_get->check("key3"));
			$this->assertEquals(Superglobal::ERR_OK,	   $sg_get->check("key1"));
		}

		public function testGet_array()
		{
			$sg_get = new SG_Get();
			
			$this->assertEquals(Superglobal::ERR_OK, 	   $sg_get->check("arr"));
			$this->assertEquals(Superglobal::ERR_EMPTY,    $sg_get->check("arr1"));
		}

		public function testGet_callback()
		{
			$sg_get = new SG_Get();

			$clback = function($str) { return strlen($str) > 3; };

			$this->assertEquals(Superglobal::ERR_OK, 	   $sg_get->check("key1", null, $clback));
			$this->assertEquals(Superglobal::ERR_CALLBACK, $sg_get->check("key3", null, $clback));

			$clback2 = function($array) { return count($array) == 2; };

			$this->assertEquals(Superglobal::ERR_EMPTY,    $sg_get->check("arr1", null, $clback2));
			$this->assertEquals(Superglobal::ERR_OK, 	   $sg_get->check("arr2", null, $clback2));
			$this->assertEquals(Superglobal::ERR_CALLBACK, $sg_get->check("arr", null, $clback2));
		}
	};