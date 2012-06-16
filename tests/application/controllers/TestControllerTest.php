<?php

class TestControllerTest extends ControllerTestCase
{

    public function setUp()
    {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();
    }

	public function testCanTouchDoctrinePage(){
		$this->dispatch("/test/doctrine");

		//echo $this->getResponse()->getBody();

		$this->assertController('test');
		$this->assertAction('doctrine');
		$this->assertResponseCode(200);

	}

	public function testCanGetDefaultIndexPage(){

		$this->dispatch("/test/doctrine");
		$this->assertXpathContentContains("id('message')", "default");
	}

	public function testCanSetMessageIndexPage(){
		$testMessage='hello from testMessage';
		$this->getRequest()
			->setMethod('GET')
			->setParams(array('m'=>$testMessage));

		$this->dispatch("/test/doctrine");
		$this->assertXpathContentContains("id('message')", $testMessage);
	}
//this is definitely being found and executed by phpunit
}

