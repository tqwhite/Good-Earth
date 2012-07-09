<?php

class SchoolControllerTest extends Zend_Test_PHPUnit_ControllerTestCase
{

    public function setUp()
    {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();
    }

    public function testIndexAction()
    {
		$this->dispatch("/School/index");

		//echo $this->getResponse()->getBody();

		$this->assertController('School');
		$this->assertAction('index');
		$this->assertResponseCode(200);
    }

    public function XXtestListAction()
    {
		$this->dispatch("/School/list");

		//echo $this->getResponse()->getBody();

		$this->assertController('test');
		$this->assertAction('index');
		$this->assertResponseCode(200);
    }


}





