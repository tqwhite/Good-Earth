<?php

//namespace GE\Entity;

/**
*
* @author tqii
*
*
**/

class ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase {
	/**
	* @var Zend_Application
	**/
	protected $application;

	public function setUp(){
		$this->bootstrap=array($this, 'appBootstrap');
		parent::setUp();
	}

	public function appBootstrap(){
		global $application;
		$this->application=$application;
		$this->application->bootstrap();
	}

	public function tearDown(){
		parent::tearDown();
	}

} //end of class