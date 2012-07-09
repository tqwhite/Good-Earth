<?php

class TestControllerTest extends ControllerTestCase
{

    public function setUp()
    {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();
    }

	public function testCanTouchWebPage(){
		$this->dispatch("/test/index");

		//echo $this->getResponse()->getBody();

		$this->assertController('test');
		$this->assertAction('index');
		$this->assertResponseCode(200);

	}

    public function testInitAction()
    {
		$this->dispatch("/test/init");

		//echo $this->getResponse()->getBody();

		$this->assertController('test');
		$this->assertAction('init');
		$this->assertResponseCode(200);
    }
/*
	public function XXXtestCanGetDefaultIndexPage(){

		$this->dispatch("/test/doctrine");
		$this->assertXpathContentContains("id('message')", "default");
	}

	public function XXXtestCanSetMessageIndexPage(){
		$testMessage='hello from testMessage';
		$this->getRequest()
			->setMethod('GET')
			->setParams(array('m'=>$testMessage));

		$this->dispatch("/test/doctrine");
		$this->assertXpathContentContains("id('message')", $testMessage);
	}
//this is definitely being found and executed by phpunit
*/
}

