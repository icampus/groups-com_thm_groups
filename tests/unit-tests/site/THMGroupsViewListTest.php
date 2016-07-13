<?php

require_once JPATH_BASE . '/components/com_thm_groups/views/list/view.html.php';

class THMGroupsViewListTest extends PHPUnit_Framework_TestCase
{
	// contains the object handle of the string class
	protected $instance;

	// called before the test functions will be executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function setUp()
	{
		$this->instance = new THMGroupsViewList();
	}

	// called after the test functions are executed
	// this function is defined in PHPUnit_TestCase and overwritten
	// here
	function tearDown()
	{
		// "benutztes" Objekt entfernen
		$this->instance = null;
		// tearDown der Elternklasse aufrufen
		parent::tearDown();
	}


	/*
	// test the display($tpl = null)
	function testDisplay() {
		
		$result = $this->instance->display($tpl = null);
		var_dump($result);
		$this->assertTrue(true);
	}*/
}

?>